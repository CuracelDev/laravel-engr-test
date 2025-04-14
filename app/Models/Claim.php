<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Claim extends Model
{
    use HasFactory;

    protected $table = 'claims';
    protected $fillable = [
        'name',
        'insurer_id',
        'date',
        'speciality',
        'priority_level',
        'sub_total',
        'status'
    ];

    const PRIORITES = [1, 2, 3, 4, 5];
    const SPECIALTIES = ["cardiology", "orthopedics", "neurology", "oncology"];
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSED = 'processed';

    /**
     * Get the items for the claims.
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    /**
     * Get the insurer that owns the claim.
     */
    public function insurer(): BelongsTo
    {
        return $this->belongsTo(Insurer::class);
    }
}
