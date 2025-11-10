<?php

namespace App\Actions;

use App\Models\Claim;
use App\Models\Insurer;
use Illuminate\Support\Carbon;
use Lorisleiva\Actions\Concerns\AsAction;

class CalculateClaimCost
{
    use AsAction;

    public function handle(Claim $claim, Insurer $insurer, Carbon $processingDate): float
    {
        $baseCost = $insurer->base_processing_cost;

        // 1. Time-based multiplier
        $dayOfMonth = $processingDate->day;
        $daysInMonth = $processingDate->daysInMonth;
        $timeMultiplier = $insurer->getTimeOfMonthMultiplier($dayOfMonth, $daysInMonth);

        // 2. Specialty multiplier
        $specialtyMultiplier = $insurer->getSpecialtyMultiplier($claim->specialty);

        // 3. Priority multiplier
        $priorityMultiplier = $insurer->getPriorityMultiplier($claim->priority_level);

        // 4. Claim value multiplier
        $valueMultiplier = $insurer->getValueMultiplier($claim->total_amount);

        // Calculate final cost
        $processingCost = $baseCost * $timeMultiplier * $specialtyMultiplier * $priorityMultiplier * $valueMultiplier;

        return round($processingCost, 2);
    }
}
