<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimItem extends Model
{
    use HasFactory;
    protected $table = 'claim_items';
    protected $fillable = [
        'claim_id', 'name', 'quantity', 'unit_price', 'subtotal'
    ];



}
