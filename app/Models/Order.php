<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['hmo_code', 'provider_name', 'encounter_date', 'total_price'];
    protected $casts = [
        'encounter_date' => 'datetime',
    ];

    public function hmo()
    {
        return $this->belongsTo(HMO::class, 'hmo_code', 'code');
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }
}
