<?php

namespace App\Actions;

use App\Enums\ClaimStatus;
use App\Mail\ClaimAddedToBatchNotification;
use App\Models\Claim;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Lorisleiva\Actions\Concerns\AsAction;

class AssignClaimToBatch
{
    use AsAction;

    public function handle(Claim $claim): void
    {
        DB::transaction(function () use ($claim) {
            $insurer = $claim->insurer;

            // Find the optimal batch date
            $optimalDate = FindOptimalBatchDate::run($claim, $insurer);

            // Calculate processing cost
            $processingCost = CalculateClaimCost::run($claim, $insurer, $optimalDate);

            // Find or create a new batch
            $batch = FindOrCreateBatch::run($insurer, $claim->provider_name, $optimalDate);

            // Update claim
            $claim->update([
                'batch_id' => $batch->id,
                'processing_cost' => $processingCost,
                'status' => ClaimStatus::BATCHED,
            ]);

            $batch->recalculateStatistics();

            // Send notification that claim was added to batch
            Mail::to($batch->insurer->email)->send(new ClaimAddedToBatchNotification($claim, $batch));
        }, 3);
    }
}
