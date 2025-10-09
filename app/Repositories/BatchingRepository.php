<?php

namespace App\Repositories;

use \Log;
use App\Models\Batch;
use App\Models\Claim;
use App\Models\Insurer;
use App\Notifications\BatchNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class BatchingRepository
{
    /**
     * 
     * This repository tries to decide which batch (group of claims) a new claim should belong to.
     *  Each insurer prefers:
     *  not too many small batches (higher fixed cost)
     *  not too large or costly batches (time, complexity)
     *  certain specialties or priorities are cheaper or more expensive to process.
     *  So this code tries to minimize the insurer’s expected processing cost each time a claim arrives.
     * 
     * 
     * Assign a single claim to an optimal existing or new batch for the insurer,
     * minimizing expected processing cost while respecting capacity & batch size constraints.
     */
    public function assignClaimToBatch(Claim $claim): ?Batch
    {
        $insurer = $claim->insurer()->first();
        $provider = $claim->provider()->first();

        // pick grouping date based on insurer preference
        $batchDate = $insurer->batch_date_pref === 'encounter'
            ? $claim->encounter_date
            : $claim->submission_date;


        // Here we look for existing batches that: 
        // belong to the same insurer,
        // same provider (hospital/clinic),
        // and same date bucket (either by encounter date or submission date, depending on insurer),
        // these are “candidate” batches where we could possibly add the new claim.
        $candidates = Batch::where('insurer_id', $insurer->id)
            ->where('provider_id', $provider->id)
            ->where('batch_date', $batchDate)
            ->lockForUpdate() 
            ->get();

        // compute cost of adding claim to each candidate batch (marginal cost)
        $bestBatch = null;
        $bestDelta = null;

        foreach ($candidates as $batch) {
            // skip if batch claim count is greater than or equal to the insurer max batch size
            if ($batch->claim_count >= $insurer->max_batch_size) continue; // can't add

            // This function estimates how much “extra processing cost” will be incurred if this claim is added to the current batch.
            // We’ll choose the batch with the smallest delta
            $delta = $this->marginalProcessingCost($insurer, $batch, $claim);
            if (is_null($bestDelta) || $delta < $bestDelta) {
                $bestDelta = $delta;
                $bestBatch = $batch;
            }
        }

        // If no candidate or all bad, consider creating or merging batches
        if (!$bestBatch) {
            // If there are other small existing batches for that date, consider merging
            $smallBatches = $candidates->filter(function($b) use ($insurer) {
                return $b->claim_count < $insurer->min_batch_size;
            })->sortByDesc('claim_count');

            // try to find a small batch where adding this claim will reach min or is feasible
            foreach ($smallBatches as $b) {
                if ($b->claim_count + 1 <= $insurer->max_batch_size) {
                    $bestBatch = $b;
                    break;
                }
            }
        }

        // if still none, create a new batch
        if (!$bestBatch) {
            $batch = Batch::create([
                'batch_identifier' => sprintf('%s %s', $provider->name, $batchDate),
                'provider_id' => $provider->id,
                'insurer_id' => $insurer->id,
                'batch_date' => $batchDate,
                'claim_count' => 0,
                'total_amount' => 0,
            ]);
            $bestBatch = $batch;
        }

        // attach claim to bestBatch and update totals
        DB::transaction(function() use ($bestBatch, $claim, $insurer) {
            $claim->batch_id = $bestBatch->id;
            $claim->save();

            $bestBatch->claim_count += 1;
            $bestBatch->total_amount = bcadd($bestBatch->total_amount, $claim->total_amount, 2);
            $bestBatch->save();
        });

        // notify insurer of new batch or update
        $this->notifyInsurer($insurer, $bestBatch, $claim);

        return $bestBatch->fresh();
    }

    /**
     * Estimate marginal processing cost of adding $claim to $batch under $insurer rules.
     * This is an approximate cost function; the system should store real coefficients for
     * each insurer to be tuned later.
     */
    protected function marginalProcessingCost(Insurer $insurer, Batch $batch, Claim $claim): float
    {
        // Time factor (processing costs increase from 20% on day 1 to 50% on day 30 linearly)
        $day = (int) Carbon::parse($batch->batch_date)->day;

        // Start at 0.20 (20%),
        // then add a tiny bit each day until you reach 0.50 (50%) by the last day.
        $timeFactor = 0.20 + (($day - 1) / 29) * (0.50 - 0.20);

        // Specialty factor: lower if insurer efficient for that specialty
        $specEff = $insurer->specialty_efficiency[$claim->specialty] ?? 1.0;

    
        // priority multiplier
        $priorityMult = $insurer->priority_cost_multiplier[$claim->priority_level] ?? (1 + 0.05 * ($claim->priority_level - 1));

        // value factor: a simple non-linear factor (scale by log(1+value))
        $valueFactor = 1.0 + log10(max(1, $claim->total_amount)) * 0.01;

        $base = 1.0; // base processing cost unit
        $cost = $base * (1 + $timeFactor) * $specEff * $priorityMult * $valueFactor;

        // adding to existing batch may have small amortization benefit:
        // we reduce cost a bit if batch already exists (fixed cost amortization)
        $amortization = $batch->claim_count > 0 ? (1 - min(0.15, 0.01 * $batch->claim_count)) : 1.0;
        return $cost * $amortization;
    }

    protected function notifyInsurer(Insurer $insurer, Batch $batch, $claim)
    {
        if (!$insurer->email) return;
        try {
             Notification::route('mail', $insurer->email)
            ->notify(new BatchNotification($batch, $claim));
        } catch (\Throwable $e) {
            Log::error("Failed to send batch notification: ". $e->getMessage());
        }
    }
}