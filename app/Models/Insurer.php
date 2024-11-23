<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insurer extends Model
{
    use HasFactory;

    protected $table = 'insurers';

    protected $fillable = [
        'name',
        'code',
        'processing_cost',
        'daily_capacity',
        'min_batch_size',
        'max_batch_size',
    ];

    public function claims()
    {
        return $this->hasMany(Claim::class);
    }
} 