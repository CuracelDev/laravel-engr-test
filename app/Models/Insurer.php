<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Insurer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'email',
        'min_batch_size',
        'max_batch_size',
        'daily_capacity',
        'batch_by',
        'specialty_efficiency',
        'priority_multipliers',
        'value_tiers',
        'base_cost',
        'monthly_cost_increase',
    ];

    protected $casts = [
        'specialty_efficiency' => 'array',
        'priority_multipliers' => 'array',
        'value_tiers' => 'array',
        'min_batch_size' => 'integer',
        'max_batch_size' => 'integer',
        'daily_capacity' => 'integer',
        'base_cost' => 'decimal:2',
        'monthly_cost_increase' => 'decimal:2',
    ];

    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class);
    }

    public function getPriorityMultiplier(string $priority): float
    {
        $multipliers = $this->priority_multipliers ?? ['low' => 0.8, 'medium' => 1.0, 'high' => 1.5];
        return $multipliers[$priority] ?? 1.0;
    }

    public function getSpecialtyEfficiency(string $specialty): float
    {
        $efficiencies = $this->specialty_efficiency ?? [];
        return $efficiencies[$specialty] ?? 1.0;
    }

    public function getValueTierCost(float $value): float
    {
        $tiers = $this->value_tiers ?? [];
        foreach ($tiers as $tier) {
            if ($value >= ($tier['min'] ?? 0) && $value <= ($tier['max'] ?? PHP_FLOAT_MAX)) {
                return $tier['cost_multiplier'] ?? 1.0;
            }
        }
        return 1.0;
    }
} 