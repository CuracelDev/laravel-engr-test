<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Claim extends Model
{
    use HasFactory;

    protected $table = 'claims';

    protected $fillable = [
        'provider_name',
        'insurer_code',
        'encounter_date',
        'submission_date',
        'priority_level',
        'specialty',
        'total_amount',
        'status',
        'batch_id',
    ];

    protected $casts = [
        'encounter_date' => 'date',
        'submission_date' => 'date',
        'total_amount' => 'decimal:2',
        'priority_level' => 'integer',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(ClaimItem::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function insurer(): BelongsTo
    {
        return $this->belongsTo(Insurer::class, 'insurer_code', 'code');
    }

    /**
     * processing cost for the claim based on insurer configuration.
     */
    public function calculateProcessingCost(): float
    {
        $insurer = $this->insurer;
        if (! $insurer || ! $insurer->configuration) {
            return 0;
        }

        $config = $insurer->configuration;

        $baseCost = $this->total_amount * 0.02;

        $dayOfMonth = $this->batch ? $this->batch->batch_date->day : now()->day;
        $timeMultiplier = 0.20 + (($dayOfMonth - 1) / 29) * 0.30;

        $specialtyEfficiency = $config->specialty_efficiency[$this->specialty] ?? 1.0;

        $priorityMultiplier = 1 + (($this->priority_level - 1) * 0.25);

        $valueMultiplier = 1 + ($this->total_amount * $config->value_multiplier);

        return $baseCost * $timeMultiplier * $specialtyEfficiency * $priorityMultiplier * $valueMultiplier;
    }
}
