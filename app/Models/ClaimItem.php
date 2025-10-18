<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClaimItem extends Model
{
    use HasFactory;

    protected $table = 'claim_items';

    protected $fillable = [
        'claim_id',
        'name',
        'unit_price',
        'quantity',
        'subtotal',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'quantity' => 'integer',
        'subtotal' => 'decimal:2',
    ];

    public function claim(): BelongsTo
    {
        return $this->belongsTo(Claim::class);
    }

    /**
     * Calculate and set the subtotal
     */
    public function calculateSubtotal(): void
    {
        $this->subtotal = $this->unit_price * $this->quantity;
    }
}

