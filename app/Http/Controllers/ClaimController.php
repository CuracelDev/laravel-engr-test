<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\Insurer;
use App\Models\Specialty;
use App\Mail\ClaimSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ClaimController extends Controller
{
    public function submitClaim(Request $request)
    {
        // Validate the incoming data
        $validatedData = $request->validate([
            'insurer_code' => 'required|string|exists:insurers,code',
            'provider_name' => 'required|string',
            'encounter_date' => 'required|date',
            'specialty' => 'required|string',
            'priority_level' => 'required|integer|min:1|max:5',
            'items' => 'required|array',
            'items.*.name' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unitPrice' => 'required|numeric|min:0',
        ]);

        // Find the insurer using the insurer code (no need for user_id)
        $insurer = Insurer::where('code', $validatedData['insurer_code'])->first();

        // Ensure insurer exists, else return an error
        if (!$insurer) {
            return response()->json([
                'message' => 'Insurer not found.',
            ], 404);
        }

        // Create the claim record (no need for user_id)
        $claim = Claim::create([
            'insurer_id' => $insurer->id,  // Only associating with insurer
            'provider_name' => $validatedData['provider_name'],
            'encounter_date' => $validatedData['encounter_date'],
            'specialty' => $validatedData['specialty'],
            'priority_level' => $validatedData['priority_level'],
            'total_amount' => 0,  // Will be updated later based on items
        ]);

        // Create claim items and calculate total amount
        $totalAmount = 0;
        foreach ($validatedData['items'] as $item) {
            $claimItem = $claim->items()->create([
                'name' => $item['name'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unitPrice'],
                'subtotal' => $item['quantity'] * $item['unitPrice'],
            ]);
            $totalAmount += $claimItem->subtotal;
        }

        // Update total amount for the claim
        $claim->total_amount = $totalAmount;
        $claim->save();

        // Apply batching logic to minimize processing costs
        $batch = $this->batchClaims($claim, $insurer);

        // Send email notification to the insurer
        // if ($insurer) {
        //     Mail::to($insurer->email)->send(new ClaimSubmitted($claim));
        // }

        return response()->json([
            'message' => 'Claim submitted successfully!',
            'claim' => $claim,
        ], 201);
    }


    /**
     * Get all claims for the authenticated user.
     */
    public function getClaims(Request $request)
    {
        // Fetch claims for the authenticated user
        $claims = $request->user()->claims()->get();

        return response()->json([
            'claims' => $claims,
        ]);
    }

    private function batchClaims($claim, $insurer)
    {
        // Fetch the encounter date and extract the day of the month for processing cost logic
        $dayOfMonth = (int) date('d', strtotime($claim->encounter_date));

        // Apply cost multiplier based on the day of the month
        $costMultiplier = 0.2 + 0.01 * $dayOfMonth;

        // Fetch the specialty multiplier from the specialties table
        $specialty = Specialty::where('name', $claim->specialty)->first();
        $specialtyMultiplier = $specialty ? $specialty->processing_cost_multiplier : 1;

        // Priority level-based multiplier (higher priority costs more)
        $priorityMultiplier = 1 + ($claim->priority_level - 1) * 0.1;

        // Monetary value multiplier (higher claim value costs more)
        $valueMultiplier = 1 + ($claim->total_amount / 1000) * 0.05;

        // Combine all multipliers to calculate the final processing cost for this claim
        $finalProcessingCost = $costMultiplier * $specialtyMultiplier * $priorityMultiplier * $valueMultiplier;

        // Initialize the batch
        $batch = [
            'batch_name' => 'Batch for ' . $claim->provider_name . ' on ' . $claim->encounter_date,
            'claims' => [$claim],  // Initially, just the submitted claim
            'total_cost' => $claim->total_amount * $finalProcessingCost,
        ];

        // Ensure the batch size is within the allowed range
        if (count($batch['claims']) < $insurer->min_batch_size) {
            // Handle case where batch size is too small (e.g., wait for more claims or reject)
            return response()->json([
                'message' => 'Batch size is too small to process.',
            ], 400);
        }

        // If the batch exceeds max size, split into multiple batches
        if (count($batch['claims']) > $insurer->max_batch_size) {
            $batch['claims'] = array_chunk($batch['claims'], $insurer->max_batch_size);
            $batch['total_cost'] = array_sum(array_map(function ($batchItem) use ($finalProcessingCost) {
                return $batchItem[0]->total_amount * $finalProcessingCost;
            }, $batch['claims']));
        }

        return $batch;
    }


    /**
     * Get the processing cost multiplier based on the claim's specialty.
     */
    private function getSpecialtyMultiplier($specialty, $insurer)
    {
        // This could be based on a mapping of specialty to processing cost for each insurer
        $specialtyCosts = [
            'Cardiology' => 1,  // Default value, no change
            'Orthopedics' => 1.2, // Higher processing cost
            // Add other specialties and their respective multipliers
        ];

        return $specialtyCosts[$specialty] ?? 1;  // Default to 1 if the specialty isn't defined
    }

    // Calculate processing cost multiplier based on the day of the month
    private function getProcessingCostMultiplier($encounterDate, $insurer)
    {
        $dayOfMonth = (int) date('d', strtotime($encounterDate));
        $costMultiplier = 0.2 + 0.01 * $dayOfMonth;  // Example of cost increasing 1% per day

        return $costMultiplier;
    }

    // Check if the batch respects the insurer’s batch size limits
    private function checkBatchSizeLimits(&$batch, $insurer)
    {
        $batchSize = count($batch['claims']);
        $minBatchSize = $insurer->min_batch_size;
        $maxBatchSize = $insurer->max_batch_size;

        if ($batchSize < $minBatchSize) {
            // If batch size is too small, adjust it (this is just a simple example, can be refined)
            while ($batchSize < $minBatchSize) {
                $batch['claims'][] = $batch['claims'][0];  // Duplicate claims (for simplicity)
                $batchSize++;
            }
        }

        if ($batchSize > $maxBatchSize) {
            // If batch size exceeds max limit, split into multiple batches
            $this->splitBatch($batch, $maxBatchSize);
        }
    }

    // Split batch into multiple smaller batches if needed
    private function splitBatch(&$batch, $maxBatchSize)
    {
        $batches = [];
        $claims = $batch['claims'];

        while (count($claims) > $maxBatchSize) {
            $batches[] = array_splice($claims, 0, $maxBatchSize);
        }

        // Add the remaining claims as the last batch
        if (count($claims) > 0) {
            $batches[] = $claims;
        }

        $batch['claims'] = $batches;
    }

    // Example optimization for batch processing (could be expanded further)
    private function optimizeBatching(&$batch)
    {
        // Implement further optimization techniques based on priority, value, etc.
        usort($batch['claims'], function ($a, $b) {
            return $a['priority_level'] <=> $b['priority_level'];
        });
    }



}
