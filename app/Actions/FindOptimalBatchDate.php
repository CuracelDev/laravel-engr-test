<?php

namespace App\Actions;

use App\Enums\BatchStatus;
use App\Enums\DatePreference;
use App\Models\Batch;
use App\Models\Claim;
use App\Models\Insurer;
use Illuminate\Support\Carbon;
use Lorisleiva\Actions\Concerns\AsAction;

class FindOptimalBatchDate
{
    use AsAction;

    public function handle(Claim $claim, Insurer $insurer): Carbon
    {
        $batchDate = $this->getBatchDate($claim, $insurer);

        // Look ahead up to 15 days or end of month (whichever is sooner)
        $maxDate = min($batchDate->copy()->addDays(15), $batchDate->copy()->endOfMonth());

        $possibleDates = [];
        $currentDate = $batchDate->copy();

        // Evaluate each possible date
        while ($currentDate <= $maxDate) {
            $batchIdentifier = Batch::generateIdentifier($claim->provider_name, $currentDate);

            // Check for an existing batch
            $existingBatch = Batch::where('batch_identifier', $batchIdentifier)
                ->where('insurer_id', $insurer->id)
                ->where('status', BatchStatus::PENDING)
                ->first();

            // Check batch size constraints
            $currentBatchSize = $existingBatch ? $existingBatch->total_claims : 0;

            // Skip if existing batch will exceed max size
            if ($existingBatch && ($currentBatchSize + 1) > $insurer->max_batch_size) {
                $currentDate->addDay();

                continue;
            }

            // Get the insurer's total claims for the day
            $dailyTotalClaims = Batch::where('insurer_id', $insurer->id)
                ->whereDate('processing_date', $currentDate)
                ->sum('total_claims');

            // Skip if the daily capacity limit will be exceeded
            if ($dailyTotalClaims + 1 > $insurer->daily_capacity_limit) {
                $currentDate->addDay();

                continue;
            }

            // Calculate processing cost for this date
            $cost = CalculateClaimCost::run($claim, $insurer, $currentDate);

            $newBatchSize = $currentBatchSize + 1;
            $meetsMinimum = $newBatchSize >= $insurer->min_batch_size;

            $possibleDates[] = [
                'date' => $currentDate->copy(),
                'efficiency_score' => CalculateEfficiencyScore::run($cost, $meetsMinimum, $currentDate, $batchDate),
            ];

            $currentDate->addDay();
        }

        if (empty($possibleDates)) {
            return $batchDate; // No valid dates found, return the original date
        }

        // Sort possible dates by efficiency score from highest to lowest
        usort($possibleDates, function ($a, $b) {
            return $b['efficiency_score'] <=> $a['efficiency_score'];
        });

        return $possibleDates[0]['date'];
    }

    private function getBatchDate(Claim $claim, Insurer $insurer): Carbon
    {
        $date = $insurer->date_preference === DatePreference::ENCOUNTER
            ? $claim->encounter_date
            : $claim->submission_date;

        // If the preferred date is in the past, use today's date
        // This ensures batches are not scheduled in the past in cases where the encounter date is preferred to submission date
        return $date->isPast() ? Carbon::today() : $date;
    }
}
