<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\Claim;
use App\Models\Insurer;
use Carbon\Carbon;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class BatchingService
{
    public function __construct(private DatabaseManager $db) {}

    /**
     * Creates/assigns batch for claim according to insurer constraints and returns [Batch $batch, float $processingCost]
     */
    public function assignToOptimalBatch(Insurer $insurer, Claim $claim): array
    {
        $batchDate = $this->resolveBatchDate($insurer, $claim);
        $batch = $this->findAcceptingBatch($insurer, $claim->provider_name, $batchDate);

        // If no suitable batch or max size reached, create a new one
        if (!$batch || $batch->claims_count >= $insurer->max_batch_size) {
            $batch = $this->createBatch($insurer, $claim->provider_name, $batchDate);
        }

        // Enforce insurer daily capacity (sum of claims assigned today across batches)
        $today = Carbon::today();
        $assignedToday = Claim::whereHas('batch', fn($q) => $q->whereDate('created_at', $today))
                        ->where('insurer_id', $insurer->id)
                        ->count();

        if ($assignedToday + 1 > $insurer->daily_capacity) {
            throw new RuntimeException("Daily capacity exceeded for insurer {$insurer->code}");
        }

        // Compute processing cost for this claim
        $processingCost = $this->computeProcessingCost(
            $insurer,
            $claim->total_value,
            $claim->specialty,
            (int)$claim->priority_level,
            Carbon::parse($claim->submission_date)
        );

        // attach
        $claim->batch_id = $batch->id;
        $claim->processing_cost = $processingCost;
        $claim->status = 'batched';
        $claim->save();

        // update batch rollups
        $batch->increment('claims_count', 1);
        $batch->increment('total_amount', $claim->total_value);

        return [$batch, $processingCost];
    }

    private function resolveBatchDate(Insurer $insurer, Claim $claim): string
    {
        return $insurer->date_preference === 'encounter'
            ? Carbon::parse($claim->encounter_date)->toDateString()
            : Carbon::parse($claim->submission_date)->toDateString();
    }

    private function findAcceptingBatch(Insurer $insurer, string $providerName, string $batchDate): ?Batch
    {
        return Batch::where('insurer_id', $insurer->id)
            ->where('provider_name', $providerName)
            ->whereDate('batch_date', $batchDate)
            ->where('status', 'queued')
            ->where('claims_count', '<', $insurer->max_batch_size)
            ->orderBy('id', 'asc')
            ->first();
    }

    private function createBatch(Insurer $insurer, string $providerName, string $batchDate): Batch
    {
        $seq = Batch::where('insurer_id', $insurer->id)
            ->where('provider_name', $providerName)
            ->whereDate('batch_date', $batchDate)
            ->count() + 1;

        $code = $this->makeBatchCode($providerName, $batchDate, $seq);

        return Batch::create([
            'insurer_id'    => $insurer->id,
            'provider_name' => $providerName,
            'batch_date'    => $batchDate,
            'batch_code'    => $code,
            'claims_count'  => 0,
            'total_amount'  => 0,
            'status'        => 'queued',
        ]);
    }

    private function makeBatchCode(string $providerName, string $date, int $seq): string
    {
        $d = Carbon::parse($date);
        return sprintf('%s %s #%d', $providerName, $d->format('M j Y'), $seq);
    }

    public function computeProcessingCost(
        Insurer $insurer,
        float $totalValue,
        string $specialty,
        int $priority,
        Carbon $submissionDate
    ): float {
        $day = min(30, max(1, (int)$submissionDate->day));
        $tMin = (float)$insurer->time_cost_min; // 0.20
        $tMax = (float)$insurer->time_cost_max; // 0.50
        $timeFactor = $tMin + ($tMax - $tMin) * (($day - 1) / 29.0);

        $specMap = $insurer->specialty_multipliers ?? [];
        $prioMap = $insurer->priority_multipliers ?? [];

        $specFactor = isset($specMap[$specialty]) ? (float)$specMap[$specialty] : 1.0;
        $prioFactor = isset($prioMap[(string)$priority]) ? (float)$prioMap[(string)$priority] : 1.0;

        $valueFactor = (float)$insurer->value_cost_slope * (float)$totalValue;

        $base = (float)$totalValue * $timeFactor * $specFactor * $prioFactor;
        $cost = $base + $valueFactor;

        return round($cost, 2);
    }
}