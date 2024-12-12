<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Insurer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'insurers';

    protected $fillable = [
        'name',
        'code',
        'email',
        'specialty_efficiency',
        'daily_capacity_limit',
        'min_batch_size',
        'max_batch_size',
        'min_processing_cost_percentage',
        'max_processing_cost_percentage',
        'batch_preference',
        'high_monetary_value',
    ];
    public function claims()
    {
        return $this->hasMany(Claim::class);
    }
}
