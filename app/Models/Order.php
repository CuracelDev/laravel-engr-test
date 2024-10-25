<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\OrderItem;
use App\Models\Batch;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id',
        'batch_id',
        'encounter_date',
        'total',
    ];

    
    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

   
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
