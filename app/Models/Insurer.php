<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Insurer extends Model
{
    use HasFactory;

    protected $table = 'insurers';

    protected $fillable = [
        'name',
        'code',
        'base_processing_cost',
        'daily_capacity',
        'min_batch_size',
        'max_batch_size',
        'date_preference',
        'specialty_efficiency_multipliers',
    ];

    protected $casts = [
        'base_processing_cost' => 'decimal:2',
        'daily_capacity' => 'integer',
        'min_batch_size' => 'integer',
        'max_batch_size' => 'integer',
        'specialty_efficiency_multipliers' => 'array',
    ];

    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class);
    }

    /**
     * Get the specialty efficiency multiplier for a given specialty
     */
    public function getSpecialtyMultiplier(string $specialty): float
    {
        $multipliers = $this->specialty_efficiency_multipliers ?? [];
        return $multipliers[$specialty] ?? 1.0;
    }
} 