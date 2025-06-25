<?php

namespace App\Models;

use App\Models\User;
use App\Models\ClaimBatch;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Services\ClaimsOptimizationService;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Insurer extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'insurers';

    protected $guarded = ['id'];

    protected $casts = [
        'specialty_efficiencies' => 'array'
    ];

    public function batches(): HasMany
    {
        return $this->hasMany(ClaimBatch::class, 'insurer_id');
    }

    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class, 'insurer_id');
    }

    public function batchClaimsForProcessing(User $provider, Collection $claims): ?ClaimBatch
    {
        $get_optimized_claims = (new ClaimsOptimizationService($this, $provider, $claims))
            ->getClaimsThatCanFitIntoABatch()
            ->getClaimsThatCanFitIntoABatchWithDailyLimit()
            ->calculateClaimsCost()
            ->process();

        if (empty($get_optimized_claims)) {
            return null;
        }

        $batch = DB::transaction(function () use ($get_optimized_claims, $provider) {
            $batch_key = $provider->name . " " . now()->format('M j Y');
            $batch = $this->batches()->create([
                'provider_id' => $provider->id,
                'batch_date' => now(),
                'processing_cost' => $get_optimized_claims['processing_cost'],
                'key' => $batch_key
            ]);

            $batchable_claims = $get_optimized_claims['processable_claims'];
            Claim::whereIn('id', $batchable_claims->pluck('id'))
                ->update([
                    'claim_batch_id' => $batch->id,
                    'processed_at' => now(),
                ]);
            return $batch;
        });

        return $batch;
    }
}
