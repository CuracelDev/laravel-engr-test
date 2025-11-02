<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
        'insurer_id',
        'batch_code',
        'batch_date',
        'claim_count',
        'total_value',
        'processing_cost',
        'notified',
        'notified_at',
    ];

    protected $casts = [
        'batch_date' => 'date',
        'claim_count' => 'integer',
        'total_value' => 'decimal:2',
        'processing_cost' => 'decimal:2',
        'notified' => 'boolean',
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
}
