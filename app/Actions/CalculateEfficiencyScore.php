<?php

namespace App\Actions;

use Illuminate\Support\Carbon;
use Lorisleiva\Actions\Concerns\AsAction;

class CalculateEfficiencyScore
{
    use AsAction;

    public function handle(float $cost, bool $meetsMinimum, Carbon $date, Carbon $preferredDate): float
    {
        // Prepare weights. Higher weight means more importance.
        // The sum of weights must be equal to 1.
        $costWeight = 0.5;
        $batchWeight = 0.3;
        $dateWeight = 0.2;

        $costFactor = $this->calculateCostFactor($cost);
        $batchFactor = $meetsMinimum ? 100 : 50;
        $dateFactor = $this->calculateDateFactor($date, $preferredDate);

        return ($costFactor * $costWeight) + ($batchFactor * $batchWeight) + ($dateFactor * $dateWeight);
    }

    private function calculateCostFactor(float $cost): float
    {
        // Cost factor based on processing costs.
        // The lower the cost, the higher the factor.
        return match (true) {
            $cost < 5000 => 100,
            $cost < 10000 => 80,
            $cost < 15000 => 60,
            $cost < 20000 => 40,
            default => 20,
        };
    }

    private function calculateDateFactor(Carbon $date, Carbon $preferredDate): float
    {
        $daysDiff = (int) $date->diffInDays($preferredDate, true);

        // The closer to the preferred date, the higher the factor.
        return match ($daysDiff) {
            0 => 100,
            1 => 90,
            2 => 80,
            3 => 70,
            4 => 60,
            5 => 50,
            default => 30,
        };
    }
}
