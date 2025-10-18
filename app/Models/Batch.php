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
        'identifier',
        'insurer_id',
        'batch_date',
        'total_claims',
        'total_amount',
        'status',
        'optimized_at',
        'notified_at',
    ];

    protected $casts = [
        'batch_date' => 'date',
        'total_claims' => 'integer',
        'total_amount' => 'decimal:2',
        'optimized_at' => 'datetime',
        'notified_at' => 'datetime',
    ];

    public function insurer(): BelongsTo
    {
        return $this->belongsTo(Insurer::class);
    }

    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class);
    }

    /**
     * Update batch totals from claims
     */
    public function updateTotals(): void
    {
        $this->total_claims = $this->claims()->count();
        $this->total_amount = $this->claims()->sum('total_amount');
        $this->save();
    }

    /**
     * Generate batch identifier from provider name and date
     */
    public static function generateIdentifier(string $providerName, string $date): string
    {
        return trim($providerName) . ' ' . $date;
    }
}

