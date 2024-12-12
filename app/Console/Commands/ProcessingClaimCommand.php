<?php

namespace App\Console\Commands;

use App\Mail\ClaimsStatusMail;
use App\Models\Claim;
use App\Models\Insurer;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ProcessingClaimCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:claims';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Processing Claim';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $insurerClaims = Claim::query()
            ->with('insurer')
            ->where('processed', 0)
            ->where('status', 'pending')
            ->whereDate('processed_date', Carbon::today())
            ->orderBy('created_at')
            ->get()
            ->groupBy('insurer_code');

        foreach ($insurerClaims as $insurerCode => $claims) {
            $insurer = Insurer::where('code', $insurerCode)->first();
            if (!$insurer){
                continue;
            }
            $minBatchSize = $insurer->min_batch_size;
            $maxBatchSize = $insurer->max_batch_size;

            $totalClaims = $claims->count();

            if ($totalClaims < $minBatchSize) {
                $claims->each(function ($claim) {
                    $claim->update(['processed_date' => Carbon::tomorrow()]);
                });

                $this->sendEmailToInsurer(
                    $insurer,
                    'Claims Moved to Next Day',
                    $claims,
                    []
                );
                $this->info("Insurer {$insurer->name} - Claims moved to the next day: {$totalClaims} claims.");
            } elseif ($totalClaims >= $minBatchSize && $totalClaims <= $maxBatchSize) {
                $claims->each(function ($claim) {
                    $claim->update([
                        'processed' => 1,
                        'status' => 'processed'
                    ]);
                });

                $this->sendEmailToInsurer(
                    $insurer,
                    'Claims Processed',
                    [],
                    $claims
                );
                $this->info("Insurer {$insurer->name} - All claims processed: {$totalClaims} claims.");
            } else {
                $claimsToProcess = $claims->take($maxBatchSize);
                $claimsToMove = $claims->skip($maxBatchSize);

                $claimsToProcess->each(function ($claim) {
                    $claim->update([
                        'processed' => 1,
                        'status' => 'processed'
                    ]);
                });

                $claimsToMove->each(function ($claim) {
                    $claim->update(['processed_date' => Carbon::tomorrow()]);
                });

                $this->sendEmailToInsurer(
                    $insurer,
                    'Claims Partially Processed',
                    $claimsToMove,
                    $claimsToProcess
                );
                $this->info("Insurer {$insurer->name} - Claims partially processed: {$claimsToProcess->count()} claims processed, {$claimsToMove->count()} claims moved.");
            }
        }
        $this->info('Claim processing completed.');
    }

    /**
     * Send an email to the insurer with details about processed and moved claims.
     *
     * @param  \App\Models\Insurer  $insurer
     * @param  string  $subject
     * @param  \Illuminate\Support\Collection  $movedClaims
     * @param  \Illuminate\Support\Collection  $processedClaims
     * @return void
     */
    protected function sendEmailToInsurer($insurer, $subject, $movedClaims, $processedClaims)
    {
        $emailData = [
            'insurerName' => $insurer->name,
            'insurerEmail' => $insurer->email,
            'movedClaims' => $movedClaims,
            'processedClaims' => $processedClaims,
            'totalMoved' => count($movedClaims),
            'totalProcessed' => count($processedClaims),
        ];
        Mail::queue(new ClaimsStatusMail($subject, $emailData));
    }

}
