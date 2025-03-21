<?php

namespace App\Actions;

use App\Models\Claim;
use App\Models\Batch;
use App\Models\Insurer;
use Carbon\Carbon;

class BatchClaims
{
    public function handle(Claim $claim)
    {
        $insurer = Insurer::find($claim->insurer_code);

        $batchDate = $this->getBatchDate($claim, $insurer);

        $batch = Batch::firstOrCreate([
            'provider_name' => $claim->provider_name,
            'batch_date' => $batchDate,
            'insurer_code' => $claim->insurer_code,
        ]);

        if (!$this->isWithinDailyCapacity($batch, $insurer)) {
          
            $batchDate = Carbon::parse($batchDate)->addDay()->toDateString();
            $batch = Batch::create([
                'provider_name' => $claim->provider_name,
                'batch_date' => $batchDate,
                'insurer_code' => $claim->insurer_code,
            ]);
        }

        if (!$this->isWithinBatchSize($batch, $insurer)) {
          
            $batch = Batch::create([
                'provider_name' => $claim->provider_name,
                'batch_date' => $batchDate,
                'insurer_code' => $claim->insurer_code,
            ]);
        }

        $batch->claims()->save($claim);

        $batch->total_processing_cost += $this->calculateProcessingCost($claim, $insurer);
        $batch->save();

        return $batch;
    }

    private function getBatchDate(Claim $claim, Insurer $insurer)
    {
        return $insurer->preferred_date_type === 'encounter'
            ? $claim->encounter_date
            : $claim->submission_date;
    }

    private function isWithinDailyCapacity(Batch $batch, Insurer $insurer)
    {

        $totalClaimsOnDate = Batch::where('insurer_code', $insurer->code)
            ->where('batch_date', $batch->batch_date)
            ->withCount('claims')
            ->get()
            ->sum('claims_count');

        return ($totalClaimsOnDate + 1) <= $insurer->daily_capacity;
    }

    private function isWithinBatchSize(Batch $batch, Insurer $insurer)
    {
        $batchSize = $batch->claims()->count();
        return ($batchSize + 1) >= $insurer->min_batch_size && ($batchSize + 1) <= $insurer->max_batch_size;
    }

    private function calculateProcessingCost(Claim $claim, Insurer $insurer)
    {

        $baseCost = 100;

        $dayOfMonth = Carbon::parse($claim->submission_date)->day;
        $timeOfMonthFactor = 0.2 + ($dayOfMonth / 30) * 0.3;

        $specialtyEfficiency = $insurer->specialty_efficiency[$claim->specialty] ?? 1;

        $priorityFactor = 1 + ($claim->priority_level * 0.1);

        $valueFactor = 1 + ($claim->total_value * 0.001);

        return $baseCost * $timeOfMonthFactor * $specialtyEfficiency * $priorityFactor * $valueFactor;
    }
}