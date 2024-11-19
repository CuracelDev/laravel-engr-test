<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'claim_id',
        'name',
        'unit_price',
        'qty',
    ];
    
    /**
     * Define the inverse relationship with Claim.
     */
    public function claim()
    {
        return $this->belongsTo(Claim::class);
    }
}
