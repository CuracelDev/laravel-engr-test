<?php

namespace App\Helpers;

use App\Models\Batch;
use App\Models\Claim;
use App\Models\Insurer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BatchClaimHelper
{
    protected CalculateProcessingCost $calculateCost;

    public function __construct(CalculateProcessingCost $calculateCost)
    {
        $this->calculateCost = $calculateCost;
    }

    public function execute(Claim $claim): Batch
    {
        return DB::transaction(function () use ($claim) {
            $insurer = $claim->insurer;

            $batchDate = $this->determineBatchDate($insurer, $claim);

            $optimalBatchDate = $this->findOptimalBatchDate(
                $insurer,
                $claim,
                $batchDate
            );

            $batch = $this->getOrCreateBatch(
                $insurer,
                $claim->provider_name,
                $optimalBatchDate
            );

            if (!$this->canAddToBatch($batch, $insurer)) {
                $nextDate = $optimalBatchDate->copy()->addDay();
                $batch = $this->getOrCreateBatch(
                    $insurer,
                    $claim->provider_name,
                    $nextDate
                );
            }

            $processingCost = $this->calculateCost->execute(
                $insurer,
                $claim,
                $batch->batch_date
            );

            $claim->update([
                'batch_id' => $batch->id,
                'status' => 'batched',
            ]);

            $batch->increment('claim_count');
            $batch->increment('total_amount', $claim->total_amount);
            $batch->increment('processing_cost', $processingCost);

            return $batch;
        });
    }

    protected function determineBatchDate(Insurer $insurer, Claim $claim): Carbon
    {
        if ($insurer->date_preference === 'encounter') {
            return Carbon::parse($claim->encounter_date);
        }

        return Carbon::parse($claim->submission_date);
    }

    protected function findOptimalBatchDate(
        Insurer $insurer,
        Claim $claim,
        Carbon $preferredDate
    ): Carbon {
        $testDate = $preferredDate->copy();
        $lowestCost = PHP_FLOAT_MAX;
        $optimalDate = $testDate->copy();

        for ($i = 0; $i < 15; $i++) {
            $cost = $this->calculateCost->execute($insurer, $claim, $testDate);

            $batch = Batch::where('insurer_id', $insurer->id)
                ->where('provider_name', $claim->provider_name)
                ->where('batch_date', $testDate->format('Y-m-d'))
                ->first();

            if ($batch) {
                if ($batch->claim_count < $insurer->max_batch_size) {
                    $batchEfficiency = $batch->claim_count / $insurer->max_batch_size;
                    $adjustedCost = $cost * (1 - ($batchEfficiency * 0.1));

                    if ($adjustedCost < $lowestCost) {
                        $lowestCost = $adjustedCost;
                        $optimalDate = $testDate->copy();
                    }
                }
            } else {
                if ($cost < $lowestCost) {
                    $lowestCost = $cost;
                    $optimalDate = $testDate->copy();
                }
            }

            if ($testDate->day <= 5) {
                break;
            }

            $testDate->subDay();
        }

        return $optimalDate;
    }

    protected function getOrCreateBatch(
        Insurer $insurer,
        string $providerName,
        Carbon $batchDate
    ): Batch {
        return Batch::firstOrCreate(
            [
                'insurer_id' => $insurer->id,
                'provider_name' => $providerName,
                'batch_date' => $batchDate->format('Y-m-d'),
            ],
            [
                'claim_count' => 0,
                'total_amount' => 0,
                'processing_cost' => 0,
                'status' => 'pending',
            ]
        );
    }

    protected function canAddToBatch(Batch $batch, Insurer $insurer): bool
    {
        return $batch->claim_count < $insurer->max_batch_size;
    }
}
