<?php

namespace App\Models;

use App\Enums\BatchStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $casts = [
        'status' => BatchStatusEnum::class
    ];

    public function hmo()
    {
        return $this->belongsTo(HMO::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
