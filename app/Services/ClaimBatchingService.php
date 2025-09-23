<?php

namespace App\Services;

use App\Mail\BatchNotification;
use App\Models\Batch;
use App\Models\Claim;
use App\Models\Insurer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class ClaimBatchingService
{
    /**
     * process claim
     */
    public function processClaim(Claim $claim): Batch
    {
        $insurer = $claim->insurer()->with('configuration')->first();

        if (! $insurer || ! $insurer->configuration) {
            throw new \Exception("Insurer configuration not found for code: {$claim->insurer_code}");
        }

        $config = $insurer->configuration;

        $batchDate = $config->date_preference === 'encounter'
            ? $claim->encounter_date
            : $claim->submission_date;

        $optimalBatch = $this->findOptimalBatch($claim, $batchDate, $config);

        if ($optimalBatch) {

            $claim->batch_id = $optimalBatch->id;
            $claim->save();

            $optimalBatch->updateTotals();
        } else {
            // Create new batch
            $optimalBatch = $this->createNewBatch($claim, $batchDate);
        }

        $this->evaluateBatchForProcessing($optimalBatch);

        return $optimalBatch;
    }

    /**
     * ind the optimal batch for a claim
     */
    protected function findOptimalBatch(Claim $claim, Carbon $batchDate, $config): ?Batch
    {

        $candidateBatches = Batch::where([
            'provider_name' => $claim->provider_name,
            'insurer_code' => $claim->insurer_code,
            'batch_date' => $batchDate,
            'status' => 'pending',
        ])->get();

        if ($candidateBatches->isEmpty()) {
            return null;
        }

        $bestBatch = null;
        $lowestCostIncrease = PHP_FLOAT_MAX;

        foreach ($candidateBatches as $batch) {

            if (! $this->canAddClaimToBatch($batch, $claim, $config)) {
                continue;
            }

            $currentCost = $batch->processing_cost;
            $newCost = $this->calculateBatchCostWithClaim($batch, $claim);
            $costIncrease = $newCost - $currentCost;

            $batchEfficiency = $batch->claims_count / $config->max_batch_size;
            $adjustedCostIncrease = $costIncrease * (1 - ($batchEfficiency * 0.1));

            if ($adjustedCostIncrease < $lowestCostIncrease) {
                $lowestCostIncrease = $adjustedCostIncrease;
                $bestBatch = $batch;
            }
        }

        return $bestBatch;
    }

    /**
     * check if a claim can be added to a batch
     */
    protected function canAddClaimToBatch(Batch $batch, Claim $claim, $config): bool
    {
        if ($batch->claims_count >= $config->max_batch_size) {
            return false;
        }

        $insurer = $batch->insurer;
        if (! $insurer->canProcessMoreClaims()) {
            return false;
        }

        return true;
    }

    /**
     * calculate batch processing cost with new claim
     */
    protected function calculateBatchCostWithClaim(Batch $batch, Claim $claim): float
    {
        $totalCost = $batch->processing_cost;

        $claimCost = $this->calculateClaimProcessingCost($claim, $batch->batch_date);

        $newBatchSize = $batch->claims_count + 1;
        $bulkDiscount = $this->calculateBulkDiscount($newBatchSize);

        return ($totalCost + $claimCost) * (1 - $bulkDiscount);
    }

    /**
     * Calculate processing cost for a specific claim
     */
    protected function calculateClaimProcessingCost(Claim $claim, Carbon $batchDate): float
    {
        $insurer = $claim->insurer;
        $config = $insurer->configuration;

        $baseCost = $claim->total_amount * 0.02;

        $dayOfMonth = $batchDate->day;
        $timeMultiplier = 0.20 + (($dayOfMonth - 1) / 29) * 0.30;

        $specialtyEfficiency = $config->getSpecialtyEfficiency($claim->specialty);

        $priorityMultiplier = 1 + (($claim->priority_level - 1) * 0.25);

        $valueMultiplier = 1 + ($claim->total_amount * $config->value_multiplier);

        return $baseCost * $timeMultiplier * $specialtyEfficiency * $priorityMultiplier * $valueMultiplier;
    }

    /**
     * calculate bulk processing discount
     */
    protected function calculateBulkDiscount(int $batchSize): float
    {
        if ($batchSize >= 20) {
            return 0.15;
        }
        if ($batchSize >= 10) {
            return 0.10;
        }
        if ($batchSize >= 5) {
            return 0.05;
        }

        return 0;
    }

    /**
     * create a new batch for the claim
     */
    protected function createNewBatch(Claim $claim, Carbon $batchDate): Batch
    {
        $batchIdentifier = Batch::generateIdentifier($claim->provider_name, $batchDate->toDateString());

        $batch = Batch::create([
            'provider_name' => $claim->provider_name,
            'insurer_code' => $claim->insurer_code,
            'batch_date' => $batchDate,
            'batch_identifier' => $batchIdentifier,
            'claims_count' => 1,
            'total_amount' => $claim->total_amount,
            'processing_cost' => $this->calculateClaimProcessingCost($claim, $batchDate),
            'status' => 'pending',
        ]);

        $claim->batch_id = $batch->id;
        $claim->save();

        return $batch;
    }

    /**
     * evaluate if a batch can be processed
     */
    protected function evaluateBatchForProcessing(Batch $batch): void
    {
        $config = $batch->insurer->configuration;

        $shouldProcess = false;
        $reason = '';

        if ($batch->claims_count >= $config->min_batch_size) {
            $shouldProcess = true;
            $reason = 'minimum_size_met';
        }

        if ($batch->claims_count >= $config->max_batch_size) {
            $shouldProcess = true;
            $reason = 'maximum_size_reached';
        }

        if ($this->isEndOfDay()) {
            $shouldProcess = true;
            $reason = 'end_of_day';
        }

        if ($this->isCostEfficient($batch)) {
            $shouldProcess = true;
            $reason = 'cost_efficient';
        }

        if ($shouldProcess) {
            $this->processBatch($batch, $reason);
        }
    }

    /**
     * check if its end of processing day
     */
    protected function isEndOfDay(): bool
    {
        $currentHour = now()->hour;

        return $currentHour >= 17;
    }

    /**
     * check if batch processing is cost efficient
     */
    protected function isCostEfficient(Batch $batch): bool
    {
        if ($batch->claims_count < 3) {
            return false;
        }

        $averageCostPerClaim = $batch->processing_cost / $batch->claims_count;
        $baselineAverageCost = $batch->total_amount * 0.03;

        return $averageCostPerClaim <= $baselineAverageCost;
    }

    /**
     * process batch
     */
    protected function processBatch(Batch $batch, string $reason): void
    {
        $batch->status = 'processing';
        $batch->processed_at = now();
        $batch->save();

        $batch->claims()->update(['status' => 'processing']);

        $this->notifyInsurer($batch, $reason);
    }

    /**
     * Send email notification to insurer it will be in log for now
     */
    protected function notifyInsurer(Batch $batch, string $reason): void
    {
        try {

            $insurerEmail = $this->getInsurerEmail($batch->insurer_code);

            Mail::to($insurerEmail)->send(new BatchNotification($batch, $reason));
        } catch (\Exception $e) {

            \Log::error('Failed to send batch notification: '.$e->getMessage());
        }
    }

    protected function getInsurerEmail(string $insurerCode): string
    {
        $emails = [
            'INS-A' => 'processing@insurer-a.com',
            'INS-B' => 'claims@insurer-b.com',
            'INS-C' => 'batch@insurer-c.com',
            'INS-D' => 'operations@insurer-d.com',
        ];

        return $emails[$insurerCode] ?? 'default@insurer.com';
    }

    /**
     * Optimize existing batches
     */
    public function optimizeExistingBatches(): void
    {
        $pendingBatches = Batch::where('status', 'pending')
            ->where('batch_date', '>=', now()->subDays(7))
            ->get()
            ->groupBy(['insurer_code', 'provider_name', 'batch_date']);

        foreach ($pendingBatches as $insurerBatches) {
            foreach ($insurerBatches as $providerBatches) {
                foreach ($providerBatches as $dateBatches) {
                    $this->optimizeBatchGroup($dateBatches);
                }
            }
        }
    }

    /**
     * Optimize a group of batches for the same provider-insurer-date
     */
    protected function optimizeBatchGroup($batches): void
    {
        if (count($batches) <= 1) {
            return;
        }

        $firstBatch = $batches->first();
        $config = $firstBatch->insurer->configuration;

        $totalClaims = $batches->sum('claims_count');
        $totalAmount = $batches->sum('total_amount');
        $currentTotalCost = $batches->sum('processing_cost');

        $optimalBatchCount = ceil($totalClaims / $config->max_batch_size);

        if ($optimalBatchCount < count($batches)) {
            $this->mergeBatches($batches->toArray(), $optimalBatchCount);
        }
    }

    protected function mergeBatches(array $batches, int $targetBatchCount): void
    {
        // merging batches will go here in real scenerio
    }
}
