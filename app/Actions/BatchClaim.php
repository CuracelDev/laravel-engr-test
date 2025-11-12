<?php

namespace App\Actions;

use App\Models\Claim;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Batch Claim Action
 * 
 * Assigns a claim to a batch and calculates its processing cost.
 * 
 * Batching Logic:
 * - Batch ID = Provider Name + Date (based on insurer preference)
 * - Processing cost calculated using multi-factor formula
 * - Cost factors: time of month, specialty, priority, claim value
 */
class BatchClaim
{
    use AsAction;

    /**
     * Assign claim to batch and calculate processing cost
     * 
     * @param Claim $claim The claim to batch
     * @return void
     */
    public function handle(Claim $claim): void
    {
        $batchDate = $claim->getBatchDateForInsurer();
        $batchId = $claim->generateBatchId();
        $dayOfMonth = $batchDate->day;
        
        $processingCost = $claim->insurer->calculateProcessingCost($claim, $dayOfMonth);
        
        $claim->update([
            'batch_id' => $batchId,
            'batch_date' => $batchDate,
            'processing_cost' => $processingCost,
        ]);
    }
}