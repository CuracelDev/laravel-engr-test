<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\Claim;
use App\Models\Insurer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Notifications\BatchSentNotification;
use Illuminate\Support\Facades\Notification;

class BatchingService
{
    public function batchAll()
    {
        $insurers = Insurer::all();
        foreach ($insurers as $insurer) {
            $this->processInsurer($insurer);
        }
    }

    public function processInsurer(Insurer $insurer)
    {
        Log::info("Starting batching for Insurer: {$insurer->name}");

        // 1. Get Pending Claims
        $pendingClaims = $insurer->claims()->where('status', 'pending')->with('items')->get();

        if ($pendingClaims->isEmpty()) {
            return;
        }

        // 2. Group Claims into "Potential Batches" (Provider + Relevant Date)
        $groupedClaims = $pendingClaims->groupBy(function ($claim) use ($insurer) {
            $date = $insurer->date_preference === 'encounter' 
                ? $claim->encounter_date 
                : $claim->submission_date;
            
            // Key: ProviderName|Date
            return $claim->provider_name . '|' . $date;
        });

        $potentialBatches = [];

        foreach ($groupedClaims as $key => $claims) {
            list($providerName, $date) = explode('|', $key);

            // Constraint: Min Batch Size
            if ($claims->count() < $insurer->min_batch_size) {
                // Too small to batch yet. Skip.
                continue;
            }

            // TODO: Max Batch Size handling. 
            // If > max_batch_size, we should split. 
            // For now, let's just make multiple chunks if needed.
            $chunks = $claims->chunk($insurer->max_batch_size);

            foreach ($chunks as $index => $chunk) {
                $batchCostScore = $this->calculateBatchCostScore($chunk, $insurer);
                $potentialBatches[] = [
                    'provider_name' => $providerName,
                    'date' => $date,
                    'claims' => $chunk,
                    'count' => $chunk->count(),
                    'score' => $batchCostScore
                ];
            }
        }

        // 3. Sort Potential Batches by "Score" (Descending)
        // We want to process the 'most expensive' batches TODAY to avoid tomorrow's higher rates.
        usort($potentialBatches, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        // 4. Select Batches up to Daily Capacity
        $capacityRemaining = $insurer->daily_processing_capacity;
        $batchesCreated = 0;

        foreach ($potentialBatches as $pb) {
            if ($capacityRemaining <= 0) {
                break;
            }

            // Do we have enough capacity for this whole batch?
            // If we only have space for 5, but batch is 10, can we take partial? 
            // "Batches are identified using Provider... Date". 
            // Strictly speaking, splitting a "Provider-Date" group might break the "unique batch" concept if not careful.
            // But we already chunked by max_size. 
            // If remaining capacity < chunk size, we probably shouldn't process it (as it would violate min_size or splitting rules).
            // Let's assume strict fit: Must fit entirely.
            if ($pb['count'] > $capacityRemaining) {
                continue; 
            }

            $this->createBatch($insurer, $pb);
            $capacityRemaining -= $pb['count'];
            $batchesCreated++;
        }

        Log::info("Created {$batchesCreated} batches for {$insurer->name}. Remaining Capacity: {$capacityRemaining}");
    }

    private function calculateBatchCostScore($claims, Insurer $insurer)
    {
        // Score = Sum of individual claim scores
        // Claim Score = Value * SpecialtyFactor * PriorityFactor
        // (We omit TimeFactor here because it's constant for all claims processed TODAY)
        
        $score = 0;
        $specialtyFactors = $insurer->specialty_factors ?? []; // json decoded array

        foreach ($claims as $claim) {
            $specialtyFactor = $specialtyFactors[strtolower($claim->specialty)] ?? 1.0;
            $priorityFactor = $claim->priority; // 1-5. Higher priority = 'costlier' to delay? Or just higher processing cost? 
            // Prompt: "higher priority claims cost more to process"
            // Prompt: "higher value claims cost more to process"
            // Prompt: "Time of month... processing costs increase"
            // 
            // IF we process today: Cost = Base * TodayFactor * Specialty * Priority * Value
            // IF we process tomorrow: Cost = Base * TomorrowFactor * Specialty * Priority * Value
            // Saving = Base * (Tomorrow - Today) * Specialty * Priority * Value
            // We want to Maximize SAVING. 
            // Since (Tomorrow - Today) is positive constant, we maximize (Specialty * Priority * Value).
            
            $claimScore = $claim->amount * $specialtyFactor * $priorityFactor;
            $score += $claimScore;
        }

        return $score;
    }

    private function createBatch(Insurer $insurer, $batchData)
    {
        DB::transaction(function () use ($insurer, $batchData) {
            $batch = Batch::create([
                'insurer_id' => $insurer->id,
                'batch_date' => $batchData['date'],
                'name' => $batchData['provider_name'] . ' ' . $batchData['date'],
                'total_claims' => $batchData['count'],
                'total_amount' => $batchData['claims']->sum('amount'),
            ]);



            foreach ($batchData['claims'] as $claim) {
                $claim->update([
                    'status' => 'batched',
                    'batch_id' => $batch->id
                ]);
            }

            // Queue Email Notification
            // We use notify() on the Notifiable trait (Insurer should probably have it, or we use Notification facade to custom email)
            Notification::route('mail', $insurer->email)
                ->notify(new BatchSentNotification($batch));

            Log::info("Batch created: {$batch->name} with {$batch->total_claims} claims.");
        });
    }
}
