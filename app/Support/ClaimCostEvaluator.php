<?php

namespace App\Support;

use App\Models\Claim;
use App\Models\Insurer;
use Carbon\Carbon;

class ClaimCostEvaluator
{
    /**
     * Estimate processing cost for a saved Claim model
     */
    public static function fromModel(Claim $claim, Insurer $insurer): float
    {
        $batchDate = $insurer->batching_date_preference === 'encounter'
            ? Carbon::parse($claim->encounter_date)
            : Carbon::parse($claim->submission_date);

        $timeCost = 0.2 + ($batchDate->day - 1) *  (30 * 0.3);
        $specialtyEff = $insurer->specialty_efficiency[$claim->specialty] ?? 1.5;

        $priorityPenalty = $claim->priority_level * 0.1;

        return $claim->total_amount * ($timeCost + $specialtyEff + $priorityPenalty);
    }

    /**
     * Estimate processing cost from raw claim data (array)
     */
    public static function fromArray(array $claim, Insurer $insurer): float
    {
        $batchDate = $insurer->batching_date_preference === 'encounter'
            ? Carbon::parse($claim['encounter_date'])
            : Carbon::parse($claim['submission_date']);

        $timeCost = 0.2 + ($batchDate->day - 1) *  (30 * 0.3);
        $specialtyEff = $insurer->specialty_efficiency[$claim['specialty']] ?? 1.5;
//        dd($specialtyEff);
        $priorityPenalty = $claim['priority_level'] * 0.1;

        return $claim['total_amount'] * ($timeCost + $specialtyEff + $priorityPenalty);
    }
}
