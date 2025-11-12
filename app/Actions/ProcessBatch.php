<?php

namespace App\Actions;

use App\Models\Insurer;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Process Batch Action
 * 
 * Evaluates and processes claim batches based on insurer constraints.
 * 
 * Processing Criteria:
 * - Batch meets minimum size requirement
 * - AND (Batch reaches maximum size OR Batch is from previous day)
 * 
 * When processed:
 * - Marks all claims in batch as processed
 * - Sends email notification to insurer
 */
class ProcessBatch
{
    use AsAction;

    /**
     * Process a batch if it meets criteria
     * 
     * @param Insurer $insurer The insurer processing the batch
     * @param string $batchId Batch identifier
     * @param mixed $batchDate Date of the batch
     * @return bool True if batch was processed, false otherwise
     */
    public function handle(Insurer $insurer, string $batchId, $batchDate): bool
    {
        $batchClaims = $insurer->claims()
            ->where('batch_id', $batchId)
            ->where('processed', false)
            ->get();

        if (!$this->shouldProcess($insurer, $batchClaims, $batchDate)) {
            return false;
        }

        $batchClaims->each->update(['processed' => true]);
        
        NotifyInsurer::run($insurer, $batchClaims, $batchId);
        
        return true;
    }

    /**
     * Determine if batch should be processed
     * 
     * @param Insurer $insurer The insurer
     * @param Collection $claims Claims in the batch
     * @param mixed $batchDate Date of the batch
     * @return bool True if should process
     */
    private function shouldProcess(Insurer $insurer, Collection $claims, $batchDate): bool
    {
        $size = $claims->count();
        return $size >= $insurer->min_batch_size && 
               ($size >= $insurer->max_batch_size || $batchDate->lt(now()->toDateString()));
    }
}