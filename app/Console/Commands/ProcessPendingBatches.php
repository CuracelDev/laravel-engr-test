<?php

namespace App\Console\Commands;

use App\Actions\ProcessBatch;
use App\Models\Claim;
use App\Models\Insurer;
use Illuminate\Console\Command;

/**
 * Process Pending Batches Command
 * 
 * Console command to manually process pending claim batches.
 * 
 * Usage:
 *   php artisan claims:process-batches
 * 
 * Can be scheduled to run daily:
 *   $schedule->command('claims:process-batches')->daily();
 * 
 * Processes batches that meet criteria:
 * - Minimum batch size reached
 * - AND (Maximum size reached OR batch from previous day)
 */
class ProcessPendingBatches extends Command
{
    protected $signature = 'claims:process-batches';
    protected $description = 'Process pending claim batches that meet processing criteria';

    public function handle()
    {
        $this->info('Processing pending batches...');

        $processedBatches = 0;

        foreach (Insurer::all() as $insurer) {
            $batches = Claim::where('insurer_id', $insurer->id)
                ->where('processed', false)
                ->whereNotNull('batch_id')
                ->get()
                ->groupBy('batch_id');
            
            foreach ($batches as $batchId => $claims) {
                if (ProcessBatch::run($insurer, $batchId, $claims->first()->batch_date)) {
                    $this->info("Processed batch: {$batchId} ({$claims->count()} claims)");
                    $processedBatches++;
                }
            }
        }

        $this->info("Processed {$processedBatches} batches.");
    }
}