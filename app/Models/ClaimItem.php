<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClaimItem extends Model
{
    protected $fillable = [
        'claim_id',
        'name',
        'unit_price',
        'quantity',
        'subtotal',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];


    public function claim(): BelongsTo
    {
        return $this->belongsTo(Claim::class);
    }


    public function calculateSubtotal(): void
    {
        $this->subtotal = $this->unit_price * $this->quantity;
    }

    /**
     * Boot method to auto-calculate subtotal.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($claimItem) {
            $claimItem->calculateSubtotal();
        });
    }
}
