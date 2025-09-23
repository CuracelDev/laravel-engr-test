<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Batch extends Model
{
    use HasFactory;

    protected $table = 'batches';

    protected $fillable = [
        'provider_name',
        'insurer_code',
        'batch_date',
        'batch_identifier',
        'claims_count',
        'total_amount',
        'processing_cost',
        'status',
        'processed_at',
    ];

    protected $casts = [
        'batch_date' => 'date',
        'claims_count' => 'integer',
        'total_amount' => 'decimal:2',
        'processing_cost' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    /**
     * batch claims
     */
    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class);
    }

    /**
     *  batch insurer
     */
    public function insurer(): BelongsTo
    {
        return $this->belongsTo(Insurer::class, 'insurer_code', 'code');
    }

    /**
     * automatically generate batch identifier
     */
    public static function generateIdentifier(string $providerName, string $date): string
    {
        return $providerName.' '.date('M j Y', strtotime($date));
    }

    /**
     * total batch claims processing cost
     */
    public function calculateTotalProcessingCost(): float
    {
        return $this->claims->sum(function ($claim) {
            return $claim->calculateProcessingCost();
        });
    }

    /**
     * batch totals update respect to claims
     */
    public function updateTotals(): void
    {
        $this->claims_count = $this->claims()->count();
        $this->total_amount = $this->claims()->sum('total_amount');
        $this->processing_cost = $this->calculateTotalProcessingCost();
        $this->save();
    }
}
