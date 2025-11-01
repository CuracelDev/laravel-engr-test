<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Insurer extends Model
{
    protected $fillable = [
        'code','name','notify_email',
        'daily_capacity','min_batch_size','max_batch_size',
        'date_preference',
        'time_cost_min','time_cost_max',
        'specialty_multipliers','priority_multipliers',
        'value_cost_slope','is_active'
    ];

    protected $casts = [
        'specialty_multipliers' => 'array',
        'priority_multipliers'  => 'array',
        'is_active'             => 'boolean',
        'time_cost_min'         => 'decimal:4',
        'time_cost_max'         => 'decimal:4',
        'value_cost_slope'      => 'decimal:6',
    ];

    public function batches(): HasMany { return $this->hasMany(Batch::class); }
    public function claims(): HasMany { return $this->hasMany(Claim::class); }
}