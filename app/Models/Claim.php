<?php

namespace App\Models;

use App\Models\User;
use App\Models\Insurer;
use App\Models\ClaimItem;
use App\Models\ClaimBatch;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Claim extends Model
{
    use HasFactory;

    protected $table = 'claims';

    protected $guarded = ['id'];

    protected $dates = ['submission_date', 'encounter_date'];

    public function insurer(): BelongsTo
    {
        return $this->belongsTo(Insurer::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ClaimItem::class, 'claim_id');
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(ClaimBatch::class);
    }

    public static function getClaimsSummary(?User $provider = null): array
    {
        $provider ??= auth()->user();
        $cache_salt = $provider?->id . '_claims_summary';
        return Cache::remember($cache_salt, now()->addDay(), function () use ($provider): array {
            $claims_summary = self::where('provider_id', $provider?->id)
            ->selectRaw('SUM(total_amount) as total_amount')
            ->selectRaw('SUM(CASE WHEN processed_at IS NULL THEN 1 ELSE 0 END) as pending_batches_count')
            ->selectRaw('SUM(CASE WHEN processed_at IS NOT NULL THEN 1 ELSE 0 END) as processed_batches_count')
            ->selectRaw('COUNT(*) as total_claims_count')
            ->first()
            ->toArray();

            return [
                'total_amount' => $claims_summary['total_amount'] ?? 0,
                'pending_batches_count' => $claims_summary['pending_batches_count'] ?? 0,
                'processed_batches_count' => $claims_summary['processed_batches_count'] ?? 0,
                'total_claims_count' => $claims_summary['total_claims_count'] ?? 0,
            ];
        });
    }
}
