<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Batch extends Model
{
    protected $fillable = [
        'provider_name',
        'insurer_id',
        'processing_date',
        'total_cost',
        'claim_count',
    ];

    protected $casts = [
        'processing_date' => 'date',
        'claim_count' => 'integer',
    ];


    public function insurer(): BelongsTo
    {
        return $this->belongsTo(Insurer::class);
    }

    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class, 'batch_id');
    }

    /**
     * Calculate total cost for the batch.
     */
    public function calculateTotalCost(): float
    {
        return $this->claims->sum(function ($claim) {
            return app(ClaimCostCalculator::class)->calculateCost($claim, $this->insurer);
        });
    }

    /**
     * Update statistics.
     */
    public function updateStatistics(): void
    {
        $this->claim_count = $this->claims()->count();
        $this->total_cost = $this->calculateTotalCost();
        $this->save();
    }
}
