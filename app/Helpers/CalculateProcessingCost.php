<?php

namespace App\Helpers;

use App\Models\Claim;
use App\Models\Insurer;
use Carbon\Carbon;

class CalculateProcessingCost
{
    public function execute(Insurer $insurer, Claim $claim, Carbon $batchDate): float
    {
        $baseCost = $insurer->base_processing_cost;

        $dayOfMonth = $batchDate->day;
        $timeMultiplier = 0.20 + (($dayOfMonth - 1) / 29) * 0.30;

        $specialtyMultiplier = $insurer->getSpecialtyEfficiency($claim->specialty);

        $priorityMultiplier = 1.0 + (($claim->priority_level - 1) * 0.1);

        $valueMultiplier = 1.0;
        if ($claim->total_amount > 10000) {
            $valueMultiplier = 1.5;
        } elseif ($claim->total_amount > 5000) {
            $valueMultiplier = 1.3;
        } elseif ($claim->total_amount > 1000) {
            $valueMultiplier = 1.1;
        }

        $processingCost = $baseCost
            * (1 + $timeMultiplier)
            * $specialtyMultiplier
            * $priorityMultiplier
            * $valueMultiplier;

        return round($processingCost, 2);
    }
}
