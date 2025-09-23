<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InsurerConfiguration extends Model
{
    use HasFactory;

    protected $table = 'insurer_configurations';

    protected $fillable = [
        'insurer_code',
        'daily_capacity',
        'min_batch_size',
        'max_batch_size',
        'date_preference',
        'specialty_efficiency',
        'priority_multiplier',
        'value_multiplier',
    ];

    protected $casts = [
        'daily_capacity' => 'integer',
        'min_batch_size' => 'integer',
        'max_batch_size' => 'integer',
        'specialty_efficiency' => 'array',
        'priority_multiplier' => 'decimal:2',
        'value_multiplier' => 'decimal:4',
    ];

    public function insurer(): BelongsTo
    {
        return $this->belongsTo(Insurer::class, 'insurer_code', 'code');
    }

    public function getSpecialtyEfficiency(string $specialty): float
    {
        return $this->specialty_efficiency[$specialty] ?? 1.0;
    }

    public function isValidBatchSize(int $size): bool
    {
        return $size >= $this->min_batch_size && $size <= $this->max_batch_size;
    }
}
