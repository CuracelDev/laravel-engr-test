<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Claim extends Model
{
    use HasFactory;

    protected $table = 'claims';

    protected $fillable = [
        'provider_name',
        'insurer_id',
        'encounter_date',
        'submission_date',
        'specialty',
        'priority_level',
        'total_amount',
        'batch_id',
    ];

    protected $casts = [
        'encounter_date' => 'date',
        'submission_date' => 'date',
    ];
    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    /**
     * Get the insurer that owns the claim.
     */
    public function insurer(): BelongsTo
    {
        return $this->belongsTo(Insurer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ClaimItem::class);
    }

    /**
     * Get the batch that the claim belongs to.
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }

    public function calculateTotalAmount(): float
    {
        return $this->items->sum('subtotal');
    }
}
