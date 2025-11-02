<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Claim extends Model
{
    use HasFactory;

    protected $fillable = [
        'insurer_id',
        'batch_id',
        'provider_name',
        'provider_email',
        'claim_reference_code',
        'encounter_date',
        'submission_date',
        'specialty',
        'priority_level',
        'claim_total',
        'status',
    ];

    protected $casts = [
        'encounter_date' => 'date',
        'submission_date' => 'date',
        'claim_total' => 'decimal:2',
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

    public static function generateReferenceCode(): string
    {
        return 'CLM-' . strtoupper(uniqid());
    }
} 