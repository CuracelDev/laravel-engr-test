<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\Claim;
use App\Models\Insurer;
use App\Support\ClaimCostEvaluator;
use Carbon\Carbon;

class BestInsurerResolverService
{

    /**
     * Get insurer recommendation with cost comparison
     *
     * @param array $data Original request data (must include items, specialty, etc.)
     * @return array {
     *      selected_insurer: array,
     *      recommended_insurer: array|null
     * }
     */
    public function resolveWithComparison(array $data): array
    {
        // Step 1: Get selected insurer
        $selectedInsurer = Insurer::where('code', $data['insurer_code'])->firstOrFail();

        // Step 2: Calculate total amount
        $total = collect($data['items'])->sum(fn($item) =>
            $item['unit_price'] * $item['quantity']
        );

        // Step 3: Build payload for evaluation
        $claimPayload = [
            ...$data,
            'total_amount' => $total,
            'submission_date' => now(),
        ];

        // Step 4: Find best insurer
        $bestInsurer = $this->resolve($claimPayload);
//dd($bestInsurer);
        // Step 5: Calculate costs
        $selectedCost = ClaimCostEvaluator::fromArray($claimPayload, $selectedInsurer);
        $recommendedCost = $bestInsurer
            ? ClaimCostEvaluator::fromArray($claimPayload, $bestInsurer)
            : null;

        return [
            'selected_insurer' => [
                'name' => $selectedInsurer->name,
                'code' => $selectedInsurer->code,
                'estimated_cost' => round($selectedCost, 2),
            ],
            'recommended_insurer' => $bestInsurer && $bestInsurer->id !== $selectedInsurer->id
                ? [
                    'name' => $bestInsurer->name,
                    'code' => $bestInsurer->code,
                    'estimated_cost' => round($recommendedCost, 2),
                ]
                : null,
        ];
    }

    /**
     * Finds the cheapest valid insurer for a given claim dataset.
     *
     * @param array $claimData ['specialty', 'priority_level', 'encounter_date', 'submission_date', 'total_amount', 'provider_name']
     * @return Insurer|null
     */
    public function resolve(array $claimData): ?Insurer
    {
        $eligibleInsurers = Insurer::all();
        $lowestCost = null;
        $bestInsurer = null;

        foreach ($eligibleInsurers as $insurer) {
            // Step 1: Determine which date to use based on the insurer's preference
            $batchDate = $insurer->batching_date_preference === 'encounter'
                ? $claimData['encounter_date']
                : $claimData['submission_date'];

            // Step 2: Check daily claim capacity
            $dailyCount = Claim::where('insurer_id', $insurer->id)
                ->whereDate('created_at', $batchDate)
                ->count();

            if ($dailyCount >= $insurer->daily_capacity) {
                continue; // This insurer has already hit capacity for the day
            }

            // Step 3: Check batch size constraint
            $batchName = $claimData['provider_name'] . ' ' . Carbon::parse($batchDate)->toFormattedDateString();

            $batch = Batch::where('insurer_id', $insurer->id)
                ->where('name', $batchName)
                ->first();

            if ($batch) {
                $batchSize = $batch->claims()->count();
                if ($batchSize >= $insurer->max_batch_size) {
                    continue; // Batch is already full
                }
            }

            // Step 4: Estimate processing cost
            $cost = ClaimCostEvaluator::fromArray(
                $claimData,
                $insurer
            );

            if (is_null($lowestCost) || $cost < $lowestCost) {
                $lowestCost = $cost;
                $bestInsurer = $insurer;
            }
        }

        return $bestInsurer;
    }

}
