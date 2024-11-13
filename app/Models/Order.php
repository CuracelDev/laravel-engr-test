<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'provider_name', 'hmo_code', 'encounter_date', 
        'submitted_date', 'batch_identifier', 'total_amount'
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
