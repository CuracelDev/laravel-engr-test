<?php

namespace App\Services;

use App\Enums\BatchStatus;
use App\Enums\ClaimStatus;
use App\Enums\DatePreference;
use App\Models\Batch;
use App\Models\Claim;
use App\Models\Insurer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClaimBatchingService
{
    protected ProcessingCostCalculator $costCalculator;

    public function __construct(ProcessingCostCalculator $costCalculator)
    {
        $this->costCalculator = $costCalculator;
    }

    /**
     * Assign a claim to the optimal batch
     * Considers a 4-day window for optimization
     */
    public function assignOptimalBatch(Claim $claim): Batch
    {
        $insurer = $claim->insurer;
        
        // Determine which date to use for batching based on insurer preference
        $baseDate = $insurer->date_preference === DatePreference::ENCOUNTER 
            ? Carbon::parse($claim->encounter_date)
            : Carbon::parse($claim->submission_date);

        // Consider today + 4 days window
        $today = Carbon::today();
        $startDate = $baseDate->greaterThan($today) ? $baseDate : $today;
        $endDate = $startDate->copy()->addDays(4);

        $bestBatch = null;
        $lowestCost = PHP_FLOAT_MAX;

        // Evaluate each possible date in the window
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $candidateBatch = $this->getOrCreateBatch($claim->provider_name, $insurer, $date);
            
            // Check if batch meets constraints
            if (!$this->canAddClaimToBatch($candidateBatch, $insurer)) {
                continue;
            }

            // Calculate cost for this batch date
            $cost = $this->costCalculator->calculateCost($claim, $insurer, $date);

            if ($cost < $lowestCost) {
                $lowestCost = $cost;
                $bestBatch = $candidateBatch;
            }
        }

        // If no batch found (all violated constraints), use the first available date
        if (!$bestBatch) {
            $bestBatch = $this->getOrCreateBatch($claim->provider_name, $insurer, $startDate);
        }

        // Assign claim to batch
        $claim->batch_id = $bestBatch->id;
        $claim->processing_cost = $lowestCost !== PHP_FLOAT_MAX ? $lowestCost : null;
        $claim->status = ClaimStatus::BATCHED;
        $claim->save();

        // Update batch totals
        $bestBatch->updateTotals();

        Log::info("Claim {$claim->id} assigned to batch {$bestBatch->identifier} with cost {$lowestCost}");

        return $bestBatch;
    }

    /**
     * Re-optimize all pending and batched claims that haven't been sent to insurers
     * This is called by the optimizer command
     */
    public function optimizeAllClaims(): array
    {
        $stats = [
            'optimized' => 0,
            'moved' => 0,
            'errors' => 0,
            'skipped' => 0,
        ];

        DB::beginTransaction();
        
        try {
            // Get all claims that are not yet sent to insurers
            // Only include claims with status pending/batched AND in batches with status pending/ready
            $claims = Claim::whereIn('status', [ClaimStatus::PENDING, ClaimStatus::BATCHED])
                ->where(function($query) {
                    // Include claims with no batch (pending)
                    $query->whereNull('batch_id')
                        // OR claims in batches that are pending or ready (not notified)
                        ->orWhereHas('batch', function($batchQuery) {
                            $batchQuery->whereIn('status', [BatchStatus::PENDING, BatchStatus::READY]);
                        });
                })
                ->with(['insurer', 'batch'])
                ->get();

            foreach ($claims as $claim) {
                // Double-check the claim is eligible for re-optimization
                if ($claim->batch && $claim->batch->status === BatchStatus::NOTIFIED) {
                    $stats['skipped']++;
                    continue;
                }

                $originalBatchId = $claim->batch_id;
                
                // Re-assign to optimal batch
                $newBatch = $this->assignOptimalBatch($claim);
                
                if ($originalBatchId && $originalBatchId !== $newBatch->id) {
                    $stats['moved']++;
                    
                    // Update the old batch totals
                    $oldBatch = Batch::find($originalBatchId);
                    if ($oldBatch) {
                        $oldBatch->updateTotals();
                    }
                }
                
                $stats['optimized']++;
            }

            // Check which batches are ready to be notified
            $this->markReadyBatches();

            DB::commit();

            Log::info("Batch optimization completed", $stats);

        } catch (\Exception $e) {
            DB::rollBack();
            $stats['errors']++;
            Log::error("Batch optimization failed: " . $e->getMessage());
        }

        return $stats;
    }

    /**
     * Check if a claim can be added to a batch without violating constraints
     */
    protected function canAddClaimToBatch(Batch $batch, Insurer $insurer): bool
    {
        // Cannot add claims to batches that have been notified to insurers
        if ($batch->status === BatchStatus::NOTIFIED) {
            return false;
        }

        // Check max batch size
        if ($batch->total_claims >= $insurer->max_batch_size) {
            return false;
        }

        // Check daily capacity for this date
        $claimsOnDate = Claim::where('insurer_id', $insurer->id)
            ->whereHas('batch', function($query) use ($batch) {
                $query->where('batch_date', $batch->batch_date);
            })
            ->count();

        if ($claimsOnDate >= $insurer->daily_capacity) {
            return false;
        }

        return true;
    }

    /**
     * Get existing batch or create a new one
     */
    protected function getOrCreateBatch(string $providerName, Insurer $insurer, Carbon $date): Batch
    {
        $identifier = Batch::generateIdentifier($providerName, $date->toDateString());

        return Batch::firstOrCreate(
            ['identifier' => $identifier],
            [
                'insurer_id' => $insurer->id,
                'batch_date' => $date,
                'status' => BatchStatus::PENDING,
            ]
        );
    }

    /**
     * Mark batches as ready if they meet the minimum batch size
     */
    protected function markReadyBatches(): int
    {
        $readyCount = 0;

        $batches = Batch::where('status', BatchStatus::PENDING)
            ->with('insurer')
            ->get();

        foreach ($batches as $batch) {
            if ($batch->total_claims >= $batch->insurer->min_batch_size) {
                $batch->status = BatchStatus::READY;
                $batch->optimized_at = now();
                $batch->save();
                $readyCount++;
            }
        }

        return $readyCount;
    }

    /**
     * Optimize claims for a specific insurer
     * Only processes claims that haven't been sent to insurers
     */
    public function optimizeClaimsForInsurer(int $insurerId): array
    {
        $stats = [
            'optimized' => 0,
            'moved' => 0,
            'errors' => 0,
            'skipped' => 0,
        ];

        DB::beginTransaction();
        
        try {
            // Get all claims for this insurer that haven't been sent to insurers
            // Only include claims with status pending/batched AND in batches with status pending/ready
            $claims = Claim::where('insurer_id', $insurerId)
                ->whereIn('status', [ClaimStatus::PENDING, ClaimStatus::BATCHED])
                ->where(function($query) {
                    // Include claims with no batch (pending)
                    $query->whereNull('batch_id')
                        // OR claims in batches that are pending or ready (not notified)
                        ->orWhereHas('batch', function($batchQuery) {
                            $batchQuery->whereIn('status', [BatchStatus::PENDING, BatchStatus::READY]);
                        });
                })
                ->with(['insurer', 'batch'])
                ->get();

            foreach ($claims as $claim) {
                // Double-check the claim is eligible for re-optimization
                if ($claim->batch && $claim->batch->status === BatchStatus::NOTIFIED) {
                    $stats['skipped']++;
                    continue;
                }

                $originalBatchId = $claim->batch_id;
                
                // Re-assign to optimal batch
                $newBatch = $this->assignOptimalBatch($claim);
                
                if ($originalBatchId && $originalBatchId !== $newBatch->id) {
                    $stats['moved']++;
                    
                    // Update the old batch totals
                    $oldBatch = Batch::find($originalBatchId);
                    if ($oldBatch) {
                        $oldBatch->updateTotals();
                    }
                }
                
                $stats['optimized']++;
            }

            // Mark batches as ready for this insurer
            $this->markReadyBatchesForInsurer($insurerId);

            DB::commit();

            Log::info("Batch optimization completed for insurer {$insurerId}", $stats);

        } catch (\Exception $e) {
            DB::rollBack();
            $stats['errors']++;
            Log::error("Batch optimization failed for insurer {$insurerId}: " . $e->getMessage());
        }

        return $stats;
    }

    /**
     * Mark batches as ready for a specific insurer
     */
    protected function markReadyBatchesForInsurer(int $insurerId): int
    {
        $readyCount = 0;

        $batches = Batch::where('insurer_id', $insurerId)
            ->where('status', BatchStatus::PENDING)
            ->with('insurer')
            ->get();

        foreach ($batches as $batch) {
            if ($batch->total_claims >= $batch->insurer->min_batch_size) {
                $batch->status = BatchStatus::READY;
                $batch->optimized_at = now();
                $batch->save();
                $readyCount++;
            }
        }

        return $readyCount;
    }

    /**
     * Get batches that are ready but not yet notified for a specific insurer
     */
    public function getReadyBatchesForInsurer(int $insurerId)
    {
        return Batch::where('insurer_id', $insurerId)
            ->where('status', BatchStatus::READY)
            ->whereNull('notified_at')
            ->get();
    }
}

