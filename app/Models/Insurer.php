<?php

namespace App\Models;

use App\Enums\DatePreference;
use App\Enums\PriorityLevel;
use App\Enums\Specialty;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Insurer extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'date_preference' => DatePreference::class,
            'specialty_cost_multipliers' => 'array',
            'priority_cost_multipliers' => 'array',
            'claim_value_multipliers' => 'array',
            'base_processing_cost' => 'decimal:2',
        ];
    }

    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class);
    }

    public function getSpecialtyMultiplier(Specialty $specialty): float
    {
        return $this->specialty_cost_multipliers[$specialty->value] ?? 1.0;
    }

    public function getPriorityMultiplier(PriorityLevel $priority): float
    {
        return $this->priority_cost_multipliers[$priority->value] ?? 1.0;
    }

    public function getTimeOfMonthMultiplier(float $dayOfMonth, float $daysInMonth): float
    {
        $start = $this->time_of_month_multiplier_start;
        $end = $this->time_of_month_multiplier_end;

        return $start + (($dayOfMonth - 1) * ($end - $start) / ($daysInMonth - 1));
    }

    public function getValueMultiplier(float $claimValue): float
    {
        $valueRanges = $this->claim_value_multipliers;
        $defaultMultiplier = 1.0;

        if (empty($valueRanges)) {
            return $defaultMultiplier;
        }

        usort($valueRanges, fn ($a, $b) => $a['min'] <=> $b['min']);

        foreach ($valueRanges as $range) {
            $min = $range['min_value'] ?? 0;
            $max = $range['max_value'] ?? PHP_FLOAT_MAX;
            $multiplier = $range['multiplier'] ?? $defaultMultiplier;

            if ($claimValue >= $min && $claimValue < $max) {
                return $multiplier;
            }
        }

        return $defaultMultiplier;
    }
}
