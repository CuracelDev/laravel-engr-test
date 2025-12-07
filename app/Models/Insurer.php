<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insurer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'min_batch_size',
        'max_batch_size',
        'daily_processing_capacity',
        'date_preference',
        'specialty_factors',
        'email',
    ];

    protected $casts = [
        'specialty_factors' => 'array',
    ];

    public function claims()
    {
        return $this->hasMany(Claim::class);
    }

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }
}