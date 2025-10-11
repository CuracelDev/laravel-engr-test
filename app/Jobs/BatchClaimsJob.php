<?php

namespace App\Jobs;

use App\Models\Claim;
use App\Models\Batch;
use App\Models\Insurer;
use App\Mail\NewBatchCreatedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class BatchClaimsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $insurerId;

    public function __construct($insurerId)
    {
        $this->insurerId = $insurerId;
    }

    public function handle()
    {
        $insurer = Insurer::find($this->insurerId);

        $claims = Claim::where('insurer_id', $insurer->id)
            ->whereNull('batch_id')
            ->orderBy('priority_level', 'desc')
            ->orderBy('total_amount', 'desc')
            ->get();

        $grouped = $claims->groupBy(function ($claim) {
            return $claim->provider_name . '||' . $claim->encounter_date;
        });
        
        foreach ($grouped as $key => $claimsGroup) {
            [$provider, $date] = explode('||', $key);
            // dd($provider, $date);
            if ($claimsGroup->count() < 2) continue;

            $batch = Batch::create([
                'insurer_id' => $insurer->id,
                'provider_name' => $provider,
                'batch_date' => $date,
                'total_value' => $claimsGroup->sum('total_amount'),
                'status' => 'pending'
            ]);

            foreach ($claimsGroup as $claim) {
                $claim->update(['batch_id' => $batch->id]);
            }
            // Notify Insurer
            Mail::to($insurer->email)->queue(new NewBatchCreatedMail($batch));
        }
    }
}
