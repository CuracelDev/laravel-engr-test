<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'claim_id',
        'name',
        'unit_price',
        'quantity',
        'subtotal',
    ];

    /**
     * Each Claim Item belongs to one Claim.
     */
    public function claim()
    {
        return $this->belongsTo(Claim::class);
    }

    /**
     * Automatically calculate subtotal before saving.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->subtotal = $item->unit_price * $item->quantity;
        });
    }
}
