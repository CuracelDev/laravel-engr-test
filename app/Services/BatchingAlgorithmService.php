<?php

namespace App\Services;

use App\Models\Claim;
use App\Models\Insurer;
use App\Models\Batch;
use Carbon\Carbon;

class BatchingAlgorithmService
{
    public function batchClaims(Claim $claim, Insurer $insurer)
    {
        // Sorting
        $sortedClaims = Claim::where('insurer_id', $insurer->id)
            ->orderBy('priority_level', 'desc')
            ->orderBy('specialty_type', 'asc')
            ->orderBy('monetary_value', 'asc')
            ->get();

        // Batching
        $batches = [];
        foreach ($sortedClaims as $claim) {
            $batch = $this->assignClaimToBatch($claim, $insurer);
            $batches[] = $batch;
        }

        // Cost Optimization
        $optimizedBatches = $this->optimizeBatchCosts($batches, $insurer);

        return $optimizedBatches;
    }

    private function assignClaimToBatch(Claim $claim, Insurer $insurer)
    {
        // Find the next available batch for the insurer
        $nextBatch = $this->findNextAvailableBatch($insurer);

        // If no batch is available, create a new one
        if (!$nextBatch) {
            $nextBatch = $this->createNewBatch($insurer);
        }

        // Assign the claim to the batch
        try {
            $nextBatch->claims()->attach($claim);
        } catch (\Throwable $th) {
            //throw $th;
        }

        // Update the batch's total monetary value
        $nextBatch->total_monetary_value += $claim->monetary_value;
        $nextBatch->save();

        return $nextBatch;
    }

    private function findNextAvailableBatch(Insurer $insurer)
    {
        // Find the next available batch for the insurer
        // based on the daily processing capacity limits
        $nextBatch = Batch::where('insurer_id', $insurer->id)
            ->where('batch_date', Carbon::today())
            ->where('total_monetary_value', '<', $insurer->daily_capacity)
            ->first();

        return $nextBatch;
    }

    private function createNewBatch(Insurer $insurer)
    {
        $formatted_date = date('M d Y');
        // Create a new batch for the insurer
        $batch = new Batch();
        $batch->insurer_id = $insurer->id;
        $batch->name = $insurer->name . " " . $formatted_date;
        $batch->batch_date = Carbon::today();
        $batch->total_monetary_value = 0;
        $batch->save();

        return $batch;
    }

    private function optimizeBatchCosts($batches, Insurer $insurer)
    {
        // Sort batches by batch date
        usort($batches, function ($a, $b) {
            return $a->batch_date <=> $b->batch_date;
        });

        // Initialize total cost
        $totalCost = 0;

        // Iterate through batches
        foreach ($batches as $batch) {
            // Calculate batch cost based on insurer's processing cost
            // and batch's total monetary value
            $batchCost = $this->calculateBatchCost($batch, $insurer);

            // Add batch cost to total cost
            $totalCost += $batchCost;

            // Update batch's cost
            $batch->total_monetary_value = $batchCost;
            $batch->save();
        }

        return $batches;
    }

    private function calculateBatchCost(Batch $batch, Insurer $insurer)
    {
        // Calculate batch cost based on insurer's processing cost
        // and batch's total monetary value
        $processingCost = $insurer->processing_cost;
        $batchMonetaryValue = $batch->total_monetary_value;
        $batchCost = $processingCost * $batchMonetaryValue;

        // Apply time-of-month adjustment to batch cost
        $batchCost *= $this->getTimeOfMonthAdjustment($batch->batch_date);

        // Apply specialty-type adjustment to batch cost
        $batchCost *= $this->getSpecialtyTypeAdjustment($batch->claims->first()->specialty_type);

        // Apply priority-level adjustment to batch cost
        $batchCost *= $this->getPriorityLevelAdjustment($batch->claims->first()->priority_level);

        return $batchCost;
    }

    private function getTimeOfMonthAdjustment($batchDate)
    {
        // Apply time-of-month adjustment based on batch date
        // For example, increase cost by 20% on the 1st and 50% on the 30th
        $dayOfMonth = $batchDate->day;
        if ($dayOfMonth <= 10) {
            return 1.2; // 20% increase
        } elseif ($dayOfMonth <= 20) {
            return 1.3; // 30% increase
        } elseif ($dayOfMonth <= 30) {
            return 1.5; // 50% increase
        } else {
            return 1.0; // No adjustment
        }
    }

    private function getSpecialtyTypeAdjustment($specialtyType)
    {
        // Apply specialty-type adjustment based on specialty type
        switch ($specialtyType) {
            case 'cardiology':
                return 1.1; // 10% increase
            case 'oncology':
                return 1.2; // 20% increase
            default:
                return 1.0; // No adjustment
        }
    }

    private function getPriorityLevelAdjustment($priorityLevel)
    {
        // Apply priority-level adjustment based on priority level
        switch ($priorityLevel) {
            case 1:
                return 1.2; // 20% increase
            case 2:
                return 1.1; // 10% increase
            default:
                return 1.0; // No adjustment
        }
    }
}
