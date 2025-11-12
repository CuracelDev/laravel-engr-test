<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Insurer Model
 * 
 * Represents an insurance company that processes healthcare claims.
 * Each insurer has unique cost multipliers and processing constraints.
 * 
 * @property int $id
 * @property string $name Insurer company name
 * @property string $code Unique identifier (e.g., INS-A)
 * @property array $specialty_costs Cost multipliers by specialty
 * @property array $priority_costs Cost multipliers by priority level
 * @property float $value_cost_multiplier Cost per dollar of claim value
 * @property int $daily_capacity Maximum claims processable per day
 * @property int $min_batch_size Minimum claims required to process batch
 * @property int $max_batch_size Maximum claims allowed in single batch
 * @property string $date_preference Date to use for batching (encounter/submission)
 * @property string $email Email for batch processing notifications
 */
class Insurer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'specialty_costs', 'priority_costs', 
        'value_cost_multiplier', 'daily_capacity', 'min_batch_size', 
        'max_batch_size', 'date_preference', 'email'
    ];

    protected $casts = [
        'specialty_costs' => 'array',
        'priority_costs' => 'array',
        'value_cost_multiplier' => 'decimal:4',
    ];

    /**
     * Get all claims for this insurer
     */
    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class);
    }

    /**
     * Calculate processing cost for a claim
     * 
     * Formula: Base Cost × Specialty Multiplier × Priority Multiplier × Value Multiplier
     * 
     * @param Claim $claim The claim to calculate cost for
     * @param int $dayOfMonth Day of month (1-30) for time-based cost
     * @return float Calculated processing cost
     */
    public function calculateProcessingCost(Claim $claim, int $dayOfMonth): float
    {
        // Base cost increases linearly from 20% to 50% throughout the month
        $baseCost = 20 + (($dayOfMonth - 1) / 29) * 30;
        
        // Specialty multiplier
        $specialtyMultiplier = $this->specialty_costs[$claim->specialty] ?? 1.0;
        
        // Priority multiplier
        $priorityMultiplier = $this->priority_costs[$claim->priority_level] ?? 1.0;
        
        // Value multiplier
        $valueMultiplier = 1 + ($claim->total_amount * $this->value_cost_multiplier);
        
        return $baseCost * $specialtyMultiplier * $priorityMultiplier * $valueMultiplier;
    }
} 