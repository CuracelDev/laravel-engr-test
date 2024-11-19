<?php

namespace App\Actions;

use App\Notifications\SendNotificationEmail;
use DateTime;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class OptimizeBatchingAction{
    use AsAction;

    public function handle($claims) {
        $totalProcessingCost = $claims[0]->insurer->total_processing_cost;
        $amountForTheDay = DailyMaxProcessingCostAction::run($totalProcessingCost);

        // calculate processing cost 
        $finalcost = 0;
        foreach ($claims as $key =>  $claim) {
            $claim['processing_cost'] = CalculateProcessingCostAction::run($claim);   
            $finalcost += $claim->total_value;
        }

        $claims = $this->sortByPriorityAndMonitaryValue($claims->toArray());
        #$this->checkDailyLimitAndSortByDate($claims, $amountForTheDay);
        $this->sortWithMinMax($claims);
    }

    public function sortByPriorityAndMonitaryValue($claimsForDate)
    {
        // Sort claims by priority and monetary value (descending order)
        usort($claimsForDate, function($a, $b) {
            return $b['priority'] - $a['priority'] ?: $b['total_value'] - $a['total_value'];
        });

        return $claimsForDate;
    }

    public function checkDailyLimitAndSortByDate($claims, $expectedTotal)
    {
        $totalProcessingCost = array_sum(array_column($claims, 'processing_cost'));

        // Remove claims until the total is within the expected range
        while ($totalProcessingCost > $expectedTotal) {
            // Remove the claim with the lowest priority (last in sorted array)
            array_pop($claims);

            // Recalculate the total processing cost
            $totalProcessingCost = array_sum(array_column($claims, 'processing_cost'));
        }

        return $claims;
    }

    public function sortWithMinMax($claims)
    {
        // Fetch maximum and minimum batch size from the insurer
        $maxBatchSize = $claims[0]->insurer->maximum_num_of_batch;
        $minBatchSize = $claims[0]->insurer->minimum_num_of_batch;
    
        // Determine which date to use for grouping claims
        $datePreference = $claims[0]['insurer']['date_preference'] === 'encounter' 
            ? 'encounter_date' 
            : 'created_at';
        
        // Group claims by provider and date
        $batches = $this->groupClaimsByProviderAndDate($claims, $datePreference);
    
        // Loop through each grouped batch and split if needed
        foreach ($batches as $providerDate => $claimsInBatch) {
            $numClaims = count($claimsInBatch);
    
            // If the number of claims exceeds the max batch size, split into smaller batches
            if ($numClaims > $maxBatchSize) {
                $splitBatches = array_chunk($claimsInBatch, $maxBatchSize);
    
                // Insert each split batch into the job_batches table
                foreach ($splitBatches as $splitBatch) {
                    $this->insertIntoJobBatches($splitBatch);
                }
            } else {
                // If the batch size is within the limit, insert the batch as it is
                $this->insertIntoJobBatches($claimsInBatch);
            }
            $claims->notify(new SendNotificationEmail());
        }
    }
    
    public function groupClaimsByProviderAndDate($claims, $datePreference) 
    {
        $groupedClaims = [];
        
        // Group claims by provider name and the selected date
        foreach ($claims as $claim) {
            $date = $claim[$datePreference];
            $providerDate = $claim['provider_name'] . ' ' . $date;
    
            if (!isset($groupedClaims[$providerDate])) {
                $groupedClaims[$providerDate] = [];
            }
            $groupedClaims[$providerDate][] = $claim;
        }
        
        return $groupedClaims;
    }

    public function insertIntoJobBatches($claimsBatch)
    {
    DB::table('job_batches')->create([
            'name' => $claimsBatch,
            'total_jobs' => count($claimsBatch),
        ]);
    
        foreach ($claimsBatch['claims'] as $claim) {
            $claimsBatch->claims()->attach($claim->id);
        }
    }
    
}