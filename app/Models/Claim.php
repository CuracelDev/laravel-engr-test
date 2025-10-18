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
        'insurer_id',
        'provider_name',
        'encounter_date',
        'submission_date',
        'priority_level',
        'specialty',
        'total_amount',
        'batch_id',
        'processing_cost',
        'status',
    ];

    protected $casts = [
        'encounter_date' => 'date',
        'submission_date' => 'date',
        'priority_level' => 'integer',
        'total_amount' => 'decimal:2',
        'processing_cost' => 'decimal:2',
    ];

    public function insurer(): BelongsTo
    {
        return $this->belongsTo(Insurer::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ClaimItem::class);
    }

    /**
     * Calculate and update the total amount from claim items
     */
    public function calculateTotalAmount(): void
    {
        $this->total_amount = $this->items()->sum('subtotal');
        $this->save();
    }
} 