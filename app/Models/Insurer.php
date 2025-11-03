<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Insurer extends Model
{
    use HasFactory;

    protected $table = 'insurers';


    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'email',
        'daily_capacity',
        'min_batch_size',
        'max_batch_size',
        'specialty_type',
        'priority_level',
    ];

    protected $casts = [
        'daily_capacity' => 'integer',
        'min_batch_size' => 'integer',
        'max_batch_size' => 'integer',
        'specialty_type' => 'array',
        'priority_level' => 'array',
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
     * Get specialty efficiency factor.
     */
    public function getSpecialtyType(string $specialty): float
    {
        return $this->specialty_type[$specialty] ?? 1.0;
    }

    /**
     * Get priority cost multiplier.
     */
    public function getPriorityLevel(int $priority): float
    {
        return $this->priority_level[$priority] ?? 1.0;
    }
}
