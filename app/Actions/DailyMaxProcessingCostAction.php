<?php

namespace App\Actions;

use App\Jobs\ComputeClaimJob;
use App\Models\Claim;
use App\Models\Insurer;
use App\Models\InsurerSpecialtyEfficiency;
use DateTime;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Validation\ValidationException;

class DailyMaxProcessingCostAction {
    use AsAction;

    public function handle(String $mainBalance)
    {
        $maxAllowedPercentage = 50; // Total percentage limit for the month
        $startPercentage = 20; // Starting percentage on the 1st day of the month

        $currentDate = new DateTime(); 
        $totalDaysInMonth = $currentDate->format('t'); // Total days in the current month
        $currentDay = $currentDate->format('j'); // Current day of the month

        // Calculate the total percentage to be distributed per day
        $remainingPercentage = $maxAllowedPercentage - $startPercentage;
        $dailyIncrement = $remainingPercentage / ($totalDaysInMonth - 1);

        // Calculate the percentage for the current day
        $dailyPercentage = $startPercentage + ($dailyIncrement * ($currentDay - 1));

        // Adjust percentage to ensure the sum across the month is capped at maxAllowedPercentage
        $adjustedDailyPercentage = $dailyPercentage * ($maxAllowedPercentage / (($startPercentage + $remainingPercentage) * $totalDaysInMonth / 100));

        // amount that can be spent as of today.
        return ((round($adjustedDailyPercentage, 2)/100)* $mainBalance);
    }
}