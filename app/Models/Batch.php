<?php

namespace App\Models;

use App\Enums\BatchStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Batch extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'batch_date' => 'date',
            'total_claims' => 'integer',
            'total_amount' => 'decimal:2',
            'total_processing_cost' => 'decimal:2',
            'status' => BatchStatus::class,
            'completed_at' => 'datetime',
            'processing_date' => 'datetime',
        ];
    }

    public function insurer(): BelongsTo
    {
        return $this->belongsTo(Insurer::class);
    }

    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class);
    }

    public static function generateIdentifier(string $providerName, Carbon $date): string
    {
        return strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $providerName)).'_'.$date->format('Y-m-d');
    }

    public function recalculateStatistics(): void
    {
        $this->total_claims = $this->claims()->count();
        $this->total_amount = $this->claims()->sum('total_amount') ?? 0;
        $this->total_processing_cost = $this->claims()->sum('processing_cost') ?? 0;

        $this->save();
    }
}
