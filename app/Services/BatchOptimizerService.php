<?php

namespace App\Services;

use App\Exception\BatchingLimitReachedException;
use App\Mail\NewClaimNotification;
use App\Models\Batch;
use App\Models\Claim;
use App\Support\ClaimCostEvaluator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class BatchOptimizerService
{

    /**
     * Handle batching logic for a given claim.
     * This method ensures that the claim is grouped into the most optimal batch
     * based on the insurer's constraints (date preference, batch size, daily capacity).
     */
    public function handle(Claim $claim): void
    {
        $insurer = $claim->insurer;

        // STEP 1: Determine the batch date based on the insurer’s preference
        $batchDate = $insurer->batching_date_preference === 'encounter'
            ? Carbon::parse($claim->encounter_date)
            : Carbon::parse($claim->submission_date);

        // STEP 2: Check insurer's total claims on this day
        $claimsToday = Claim::where('insurer_id', $insurer->id)
            ->whereDate('created_at', $batchDate)
            ->count();

        if ($claimsToday >= $insurer->daily_capacity) {
            throw new BatchingLimitReachedException($insurer->name, $batchDate->toDateString());
        }

        // STEP 3: Create a human-friendly name for the batch
        // Format: "ProviderName Jan 21, 2025"
        $baseBatchName = "{$claim->provider_name} " . $batchDate->toFormattedDateString();
        $batchName = $baseBatchName;
        $batchIndex = 1;

        $batch = Batch::where('insurer_id', $insurer->id)
            ->where('name', $batchName)
            ->first();

        while ($batch && $batch->claims()->count() >= $insurer->max_batch_size) {
            // Try the next version of the batch name
            $batchName = "{$baseBatchName} ({$batchIndex})";
            $batch = Batch::where('insurer_id', $insurer->id)
                ->where('name', $batchName)
                ->first();
            $batchIndex++;
        }

        if (!$batch) {
            // Create a new batch if none exists or all are full
            $batch = Batch::create([
                'name' => $batchName,
                'insurer_id' => $insurer->id,
                'batch_date' => $batchDate,
                'total_cost' => 0,
            ]);
        }

        // STEP 4: Attach the claim to the batch
        $claim->batch_id = $batch->id;
        $claim->save();

        // STEP 8: Recalculate the total cost of the batch
        $totalCost = $batch->claims->sum(function ($claim) use ($insurer) {
            return ClaimCostEvaluator::fromModel(
                $claim,
                $insurer
            );
        });

        $batch->update(['total_cost' => $totalCost]);
        $notificationEmail = $claim->insurer->notification_email ?? null;

        if ($notificationEmail) {
            Mail::to($notificationEmail)->queue(new NewClaimNotification($claim));
        }
    }

    private function createBatch($batchName, $insurer_id, $batchDate){
        return Batch::create([
            'name' => $batchName,
            'insurer_id' => $insurer_id,
            'batch_date' => $batchDate,
            'total_cost' => 0, // will be updated after assigning
        ]);
    }
}
