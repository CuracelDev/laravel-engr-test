<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $casts = [
        'items' => 'array',
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
