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
        'email',
        'daily_capacity',
        'min_batch_size',
        'max_batch_size',
        'date_preference',
        'specialty_efficiency',
        'base_processing_cost',
    ];

    protected $casts = [
        'specialty_efficiency' => 'array',
        'base_processing_cost' => 'decimal:2',
    ];

    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class);
    }

    public function getSpecialtyEfficiency(string $specialty): float
    {
        return $this->specialty_efficiency[$specialty] ?? 1.0;
    }
} 