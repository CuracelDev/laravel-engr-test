<?php

namespace App\Services;

use App\Mail\BatchReadyNotification;
use App\Models\Batch;
use App\Models\Claim;
use App\Models\Insurer;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ClaimBatchingService
{
    /**
     * Process a claim and assign it to the optimal batch
     */
    public function processClaim(Claim $claim): Batch
    {
        $insurer = $claim->insurer;
        $batchDate = $this->getBatchDate($claim, $insurer);
        $batch = $this->findOrCreateOptimalBatch($claim, $insurer, $batchDate);
        
        $claim->update([
            'batch_id' => $batch->id,
            'status' => 'processed',
        ]);
        
        $this->updateBatchTotals($batch);
        $batch->refresh();
        $this->checkAndNotifyBatch($batch);
        
        return $batch;
    }

    /**
     * Determine batch date based on insurer preference
     */
    private function getBatchDate(Claim $claim, Insurer $insurer): Carbon
    {
        if ($insurer->batch_by === 'submission_date') {
            return Carbon::parse($claim->submission_date);
        }
        
        return Carbon::parse($claim->encounter_date);
    }

    /**
     * Find or create the optimal batch for a claim
     */
    private function findOrCreateOptimalBatch(Claim $claim, Insurer $insurer, Carbon $batchDate): Batch
    {
        $existingBatches = Batch::where('insurer_id', $insurer->id)
            ->whereDate('batch_date', $batchDate)
            ->where('notified', false)
            ->get();

        $bestBatch = null;
        $lowestCost = PHP_FLOAT_MAX;

        foreach ($existingBatches as $batch) {
            if ($batch->claim_count >= $insurer->max_batch_size) {
                continue;
            }

            $projectedCost = $this->calculateBatchCost($batch, $claim, $insurer);
            
            if ($projectedCost < $lowestCost) {
                $lowestCost = $projectedCost;
                $bestBatch = $batch;
            }
        }

        if ($bestBatch && $bestBatch->claim_count < $insurer->max_batch_size) {
            return $bestBatch;
        }

        $batchesToday = Batch::where('insurer_id', $insurer->id)
            ->whereDate('batch_date', $batchDate)
            ->count();

        if ($batchesToday >= $insurer->daily_capacity) {
            $batchDate = $this->findNextAvailableDate($insurer, $batchDate);
        }

        return Batch::create([
            'insurer_id' => $insurer->id,
            'batch_code' => $this->generateBatchCode($insurer),
            'batch_date' => $batchDate,
            'claim_count' => 0,
            'total_value' => 0,
            'processing_cost' => 0,
            'notified' => false,
        ]);
    }

    /**
     * Calculate processing cost for a batch with a new claim
     */
    private function calculateBatchCost(Batch $batch, Claim $claim, Insurer $insurer): float
    {
        $baseCost = $insurer->base_cost;
        $monthsFromBase = Carbon::parse($batch->batch_date)->diffInMonths(now()->startOfMonth());
        $dateMultiplier = 1 + ($insurer->monthly_cost_increase * $monthsFromBase);
        $priorityMultiplier = $insurer->getPriorityMultiplier($claim->priority_level);
        $specialtyEfficiency = $insurer->getSpecialtyEfficiency($claim->specialty);
        $valueTierCost = $insurer->getValueTierCost($claim->claim_total);
        
        $claimCost = $baseCost * $dateMultiplier * $priorityMultiplier * $valueTierCost;
        $claimCost = $claimCost / $specialtyEfficiency;
        
        return $batch->processing_cost + $claimCost;
    }

    /**
     * Find next available date within capacity
     */
    private function findNextAvailableDate(Insurer $insurer, Carbon $startDate): Carbon
    {
        $date = $startDate->copy();
        $maxDays = 30;
        
        for ($i = 0; $i < $maxDays; $i++) {
            $batchesOnDate = Batch::where('insurer_id', $insurer->id)
                ->whereDate('batch_date', $date)
                ->count();
            
            if ($batchesOnDate < $insurer->daily_capacity) {
                return $date;
            }
            
            $date->addDay();
        }
        
        return $date;
    }

    /**
     * Update batch totals after adding a claim
     */
    private function updateBatchTotals(Batch $batch): void
    {
        $claims = $batch->claims;
        
        $batch->update([
            'claim_count' => $claims->count(),
            'total_value' => $claims->sum('claim_total'),
            'processing_cost' => $this->calculateTotalBatchCost($batch),
        ]);
    }

    /**
     * Calculate total cost for a complete batch
     */
    private function calculateTotalBatchCost(Batch $batch): float
    {
        $insurer = $batch->insurer;
        $baseCost = $insurer->base_cost;
        $monthsFromBase = Carbon::parse($batch->batch_date)->diffInMonths(now()->startOfMonth());
        $dateMultiplier = 1 + ($insurer->monthly_cost_increase * $monthsFromBase);
        $totalCost = 0;
        
        foreach ($batch->claims as $claim) {
            $priorityMultiplier = $insurer->getPriorityMultiplier($claim->priority_level);
            $specialtyEfficiency = $insurer->getSpecialtyEfficiency($claim->specialty);
            $valueTierCost = $insurer->getValueTierCost($claim->claim_total);
            
            $claimCost = $baseCost * $dateMultiplier * $priorityMultiplier * $valueTierCost;
            $claimCost = $claimCost / $specialtyEfficiency;
            $totalCost += $claimCost;
        }
        
        return round($totalCost, 2);
    }

    /**
     * Generate unique batch code
     */
    private function generateBatchCode(Insurer $insurer): string
    {
        return strtoupper($insurer->code) . '-' . Carbon::now()->format('Ymd') . '-' . strtoupper(Str::random(6));
    }

    /**
     * Check if batch is ready for notification (reached min size)
     */
    public function isBatchReady(Batch $batch): bool
    {
        return $batch->claim_count >= $batch->insurer->min_batch_size;
    }

    /**
     * Get batches ready for notification
     */
    public function getReadyBatches(): Collection
    {
        return Batch::where('notified', false)
            ->with(['insurer', 'claims'])
            ->get()
            ->filter(function ($batch) {
                return $this->isBatchReady($batch);
            });
    }

    /**
     * Check if batch is ready and send notification
     */
    private function checkAndNotifyBatch(Batch $batch): void
    {
        $batch->refresh();
        
        if (!$batch->insurer->email) {
            Log::warning('Cannot send batch notification: Insurer has no email', [
                'batch_id' => $batch->id,
                'batch_code' => $batch->batch_code,
                'insurer_id' => $batch->insurer_id,
            ]);
            return;
        }
        
        if ($batch->notified) {
            Log::info('Batch already notified, skipping', [
                'batch_id' => $batch->id,
                'batch_code' => $batch->batch_code,
            ]);
            return;
        }
        
        if (!$this->isBatchReady($batch)) {
            Log::info('Batch not ready yet', [
                'batch_id' => $batch->id,
                'batch_code' => $batch->batch_code,
                'claim_count' => $batch->claim_count,
                'min_batch_size' => $batch->insurer->min_batch_size,
            ]);
            return;
        }
        
        try {
            Mail::to($batch->insurer->email)->send(new BatchReadyNotification($batch));
            
            $batch->update([
                'notified' => true,
                'notified_at' => now(),
            ]);
            
            Log::info('Batch notification sent successfully', [
                'batch_id' => $batch->id,
                'batch_code' => $batch->batch_code,
                'sent_to' => $batch->insurer->email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send batch notification', [
                'batch_id' => $batch->id,
                'batch_code' => $batch->batch_code,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
