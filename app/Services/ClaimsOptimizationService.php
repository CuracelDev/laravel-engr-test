<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Claim;
use App\Models\Insurer;
use Illuminate\Database\Eloquent\Collection;

class ClaimsOptimizationService
{
    protected ?Collection $processable_claims;

    public function __construct(public Insurer $insurer, public User $provider, public Collection $claims)
    {
    }

    public function getClaimsThatCanFitIntoABatch()
    {
        $min_batch_size = $this->insurer->min_batch_size;
        $max_batch_size = $this->insurer->max_batch_size;
        $actual_batch_size = $this->claims->count();

        if ($min_batch_size > $actual_batch_size) {
            $this->processable_claims = null;
            return $this;
        }

        if ($actual_batch_size > $max_batch_size) {
            $this->processable_claims = $this->claims->sortByDesc('priority_level')->take($max_batch_size);
            return $this;
        }

        $this->processable_claims = $this->claims;
        return $this;
    }

    public function getClaimsThatCanFitIntoABatchWithDailyLimit()
    {
        if (is_null($this->processable_claims)) {
            return $this;
        }
        $daily_capacity = $this->insurer->daily_capacity;
        $actual_batch_size = $this->processable_claims->count();
        $total_claims_batched_today_across_all_providers = Claim::where('insurer_id', $this->insurer->id)->whereDate('processed_at', now()->toDateString())->whereNotNull('claim_batch_id')->count(); 

        if (($actual_batch_size + $total_claims_batched_today_across_all_providers) > $daily_capacity) {
            $this->processable_claims = $this->processable_claims->sortByDesc('priority_level')->take(abs($daily_capacity - $total_claims_batched_today_across_all_providers));
        }
        return $this;
    }

    public function calculateClaimsCost()
    {
        if (is_null($this->processable_claims)) {
            return $this;
        }

        $this->processable_claims = $this->processable_claims->map(function ($claim) {
            $preferred_claim_processing_date = match($this->insurer->batch_date_preference) {
                'submission' => Carbon::parse($claim['submission_date']),
                'encounter' => Carbon::parse($claim['encounter_date']),
            };

            $claim_weight = 0.1 * log($claim['total_amount'] ?? 1);
            $date_multiplier = $this->getDayInflationMultiplier($preferred_claim_processing_date);
            $priority_multiplier = $this->getPriorityMultiplier($claim->priority_level);
            $speciality_multiplier = $this->getSpecialityMultiplier($this->insurer, $claim->specialty ?? '');
           
            $claim_cost = (1 + $date_multiplier + $priority_multiplier + $speciality_multiplier + $claim_weight);
            $claim['processing_cost'] = $claim_cost;
            return $claim;
        });
        return $this;
    }

    public function process(): array
    {
        if (is_null($this->processable_claims)) {
            return [];
        }

        return [
            'processable_claims' => $this->processable_claims,
            'processing_cost' => $this->processable_claims->sum('processing_cost'),
        ];
    }

    public function getDayInflationMultiplier(Carbon $preferred_date)
    {
        return 0.20 + (0.30 * ($preferred_date->day - 1) / 29);
    }

    public function getPriorityMultiplier(int $priority)
    {
        return 0.05 * ($priority - 1);
    }

    public function getSpecialityMultiplier(Insurer $insurer, string $specialty)
    {
        return $insurer->specialty_efficiencies[$specialty] ?? 1.0;
    }
}