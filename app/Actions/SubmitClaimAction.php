<?php

namespace App\Actions;

use App\Mail\BatchReadyMail;
use App\Models\Batch;
use App\Models\Claim;
use App\Models\ClaimItem;
use App\Models\Insurer;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class SubmitClaimAction
{
    public function handle(Request $request)
    {
        // Validate claim data with insurer code lookup
        $validated = $request->validate([
            // 'insurer_code' => 'required|string|exists:insurers,code',
            'provider_name' => 'required|string',
            'encounter_date' => 'required|date',
            'specialty' => 'required|string',
            'priority_level' => 'required|integer|min:1|max:5',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
        ], [
            // 'insurer_code.exists' => 'The insurer code does not exist in our system.',
            'items.required' => 'At least one item is required.',
            'items.*.unit_price.min' => 'Unit price must be a positive number.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
        ]);
        $insurer = Insurer::where('code', $request->insurer_code)->first();

        if (!$insurer) {
            return response()->json([
                'errors' => ['insurer_code' => 'The selected insurer code is invalid.']
            ], 422);
        }

        // Calculate total amount
        $totalAmount = collect($validated['items'])->sum(function ($item) {
            return $item['unit_price'] * $item['quantity'];
        });

        $claim = DB::transaction(function () use ($validated, $insurer, $totalAmount) {
            // Create claim
            $claim = Claim::create([
                'provider_name' => $validated['provider_name'],
                'insurer_id' => $insurer->id,
                'encounter_date' => $validated['encounter_date'],
                'submission_date' => now(),
                'specialty' => $validated['specialty'],
                'priority_level' => $validated['priority_level'],
                'total_amount' => $totalAmount,
            ]);

            // Create claim items
            foreach ($validated['items'] as $itemData) {
                ClaimItem::create([
                    'claim_id' => $claim->id,
                    'name' => $itemData['name'],
                    'unit_price' => $itemData['unit_price'],
                    'quantity' => $itemData['quantity'],
                    'subtotal' => $itemData['unit_price'] * $itemData['quantity'],
                ]);
            }
            // Assign to optimal batch
            $this->assignToOptimalBatch($claim);

            return $claim;
        });

        return $claim;
    }

    private function assignToOptimalBatch(Claim $claim)
    {
        $insurer = $claim->insurer;
        if (!$insurer)
            return;
        $batchDate = ($insurer->batch_by === 'encounter') ? $claim->encounter_date : $claim->submission_date;

        // Check if batch exists and has capacity
        $existingBatch = Batch::where('insurer_id', $insurer->id)
            ->where('provider_name', $claim->provider_name)
            ->where('processing_date', $batchDate)
            ->get();
        // Cost function: base processing cost percent depends on day of month
        $day = (int) date('j', strtotime($batchDate));
        $basePct = 0.20 + ($day - 1) * ((0.50 - 0.20) / 29); // 20% -> 50% linearly

        // Specialty efficiency
        $specEff = 1.0;
        if (
            !empty($insurer->specialty_efficiency) &&
            isset($insurer->specialty_efficiency[$claim->specialty])
        ) {
            $specEff = floatval($insurer->specialty_efficiency[$claim->specialty]);
        }

        // Priority & amount multipliers
        $priorityFactor = 1 + ($claim->priority_level - 1) * 0.1;
        $amountFactor = log($claim->total_amount + 1);

        $claimCost = $basePct * $specEff * $priorityFactor * $amountFactor;
        // Evaluate best batch
        $best = null;
        $bestMarginal = null;

        foreach ($existingBatch as $b) {
            if ($b->count + 1 > $insurer->max_batch_size) {
                continue; // would exceed capacity
            }

            // Marginal cost — depends on how full batch already is
            $marginal = $claimCost * (1 + ($b->total_amount / max(1, $insurer->daily_capacity)));

            if ($best === null || $marginal < $bestMarginal) {
                $best = $b;
                $bestMarginal = $marginal;
            }
        }

        // Cost to create new batch = claimCost + overhead (5%)
        $newBatchCost = $claimCost * 1.05;

        if ($best && $bestMarginal <= $newBatchCost) {
            // Attach claim to best batch
            $this->addClaimToBatch($claim, $best);
        } else {
            // Create a new batch
            $batch = Batch::create([
                'insurer_id' => $insurer->id,
                'provider_name' => $claim->provider_name,
                'processing_date' => $batchDate,
                'total_cost' => $claim->total_amount,
                'claim_count' => 1,
            ]);

            $claim->batch_id = $batch->id;
            $claim->save();


            // Notify insurer (queued)
            if ($insurer->email) {
                Mail::to($insurer->email)->queue(new BatchReadyMail($batch));
            }
            Log::info("Created new batch ID {$batch->id} for claim {$claim->id}");
        }
    }

    protected function addClaimToBatch(Claim $claim, Batch $batch)
    {
        $batch->total_cost += $claim->total_amount;
        $batch->claim_count += 1;
        $batch->save();

        $claim->batch_id = $batch->id;
        $claim->save();
        // Notify insurer only if batch meets min batch size or is full
        $insurer = $claim->insurer;
        if ($insurer && $insurer->email) {
            if (
                $batch->claim_count >= $insurer->min_batch_size ||
                $batch->claim_count >= $insurer->max_batch_size
            ) {
                Mail::to($insurer->email)->queue(new BatchReadyMail($batch));
                Log::info("Batch {$batch->id} reached threshold; notification queued.");
            }
        } else {

            Log::info("Batch {$batch->id} reached threshold; email does not exit.");
        }
    }
}
