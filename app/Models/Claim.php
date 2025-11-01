<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Claim extends Model
{
    protected $fillable = [
        'insurer_id','batch_id','provider_name',
        'encounter_date','submission_date',
        'specialty','priority_level','items_count',
        'total_value','processing_cost','status'
    ];

    protected $casts = [
        'encounter_date'  => 'date',
        'submission_date' => 'date',
        'total_value'     => 'decimal:2',
        'processing_cost' => 'decimal:2',
    ];

    public function insurer(): BelongsTo { return $this->belongsTo(Insurer::class); }
    public function batch(): BelongsTo   { return $this->belongsTo(Batch::class); }
    public function items(): HasMany     { return $this->hasMany(ClaimItem::class); }
}