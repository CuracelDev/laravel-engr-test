<?php

namespace App\Services;

use App\Enums\PriorityLevel;
use App\Models\Claim;
use App\Models\Insurer;
use Carbon\Carbon;

class ProcessingCostCalculator
{
    /**
     * Calculate the processing cost for a claim with a specific insurer on a specific date
     * 
     * Formula:
     * cost = (base_cost + (total_amount/1000 * 10)) × priority_multiplier × specialty_multiplier × time_of_month_multiplier
     * 
     * Where:
     * - base_cost: Insurer's base processing cost
     * - total_amount/1000 * 10: For every 1000 units, add 10 to the cost
     * - priority_multiplier: 1x for priority 1, 2x for priority 2, etc.
     * - specialty_multiplier: Insurer's efficiency for specific specialty
     * - time_of_month_multiplier: 0.20 + (day/30 * 0.30) = ranges from 20% to 50%
     */
    public function calculateCost(Claim $claim, Insurer $insurer, Carbon $batchDate): float
    {
        // Base cost + monetary value cost
        $baseCost = $insurer->base_processing_cost;
        $monetaryValueCost = ($claim->total_amount / 1000) * 10;
        $subtotal = $baseCost + $monetaryValueCost;

        // Priority multiplier
        $priorityMultiplier = PriorityLevel::getMultiplier($claim->priority_level);

        // Specialty multiplier
        $specialtyMultiplier = $insurer->getSpecialtyMultiplier($claim->specialty);

        // Time of month multiplier (20% to 50% based on day of month)
        $timeMultiplier = $this->calculateTimeOfMonthMultiplier($batchDate);

        // Calculate final cost
        $cost = $subtotal * $priorityMultiplier * $specialtyMultiplier * $timeMultiplier;

        return round($cost, 2);
    }

    /**
     * Calculate the time of month multiplier
     * 
     * Returns a value between 0.20 (20%) and 0.50 (50%)
     * Day 1 = 20%, Day 30/31 = 50%
     */
    private function calculateTimeOfMonthMultiplier(Carbon $date): float
    {
        $dayOfMonth = $date->day;
        $daysInMonth = $date->daysInMonth;

        // Linear interpolation from 0.20 to 0.50
        $multiplier = 0.20 + (($dayOfMonth - 1) / ($daysInMonth - 1)) * 0.30;

        return round($multiplier, 4);
    }

    /**
     * Calculate total processing cost for a batch of claims
     */
    public function calculateBatchCost(array $claims, Insurer $insurer, Carbon $batchDate): float
    {
        $totalCost = 0;

        foreach ($claims as $claim) {
            $totalCost += $this->calculateCost($claim, $insurer, $batchDate);
        }

        return round($totalCost, 2);
    }
}

