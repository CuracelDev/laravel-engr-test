<?php

namespace App\Models;

use App\Models\Batch;
use App\Models\Claim;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insurer extends Model
{
    use HasFactory;

    protected $table = 'insurers';

     
    protected $fillable = [
        'code','name','email','specialty_efficiency','priority_cost_multiplier',
        'daily_capacity','min_batch_size','max_batch_size','batch_date_pref'
    ];

    protected $casts = [
        'specialty_efficiency' => 'array',
        'priority_cost_multiplier' => 'array',
    ];


    public function claims() { return $this->hasMany(Claim::class); }
    public function batches() { return $this->hasMany(Batch::class); }
} 