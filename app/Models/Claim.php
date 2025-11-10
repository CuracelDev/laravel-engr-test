<?php

namespace App\Models;

use App\Enums\ClaimStatus;
use App\Enums\PriorityLevel;
use App\Enums\Specialty;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Claim extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'encounter_date' => 'date',
            'submission_date' => 'date',
            'total_amount' => 'decimal:2',
            'specialty' => Specialty::class,
            'status' => ClaimStatus::class,
            'priority_level' => PriorityLevel::class,
        ];
    }

    public function items(): Claim|HasMany
    {
        return $this->hasMany(ClaimItem::class, 'claim_id', 'id');
    }

    public function insurer(): BelongsTo
    {
        return $this->belongsTo(Insurer::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }
}
