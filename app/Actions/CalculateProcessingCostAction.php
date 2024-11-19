<?php

namespace App\Actions;

use DateTime;
use Lorisleiva\Actions\Concerns\AsAction;

class CalculateProcessingCostAction {
    use AsAction;

    public function handle($claim)
    {
        $efficiency = (int)$claim['specialtyEfficiency']['efficiency'];
        $priority = (int)$claim['priority'];
        $totalValue = (float)$claim['total_value'];
        
        // Determine the date based on the insurer's date preference
        $date = $claim['insurer']['date_preference'] === 'encounter' 
            ? $claim['encounter_date'] 
            : $claim['created_at'];
        
        // Calculate proximity weight
        $currentDate = new DateTime();
        $claimDate = new DateTime($date);
        $daysDifference = $currentDate->diff($claimDate)->days;
        $proximityWeight = $daysDifference <= 10 ? 3 : ($daysDifference <= 20 ? 2 : 1);
    
        // Compute processing cost
        return ($efficiency * 5) + ($priority * 10) + ($totalValue * 0.01) + $proximityWeight;

    }


}