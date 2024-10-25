<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Provider;
use App\Models\Order;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
        'hmo_code',
        'provider_id',
        'name',
        'criteria_type',
    ];

   
    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
