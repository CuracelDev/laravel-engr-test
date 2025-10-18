<?php

namespace App\Http\Controllers;

use App\Enums\ClaimStatus;
use App\Http\Requests\SubmitClaimRequest;
use App\Jobs\OptimizeBatches;
use App\Models\Batch;
use App\Models\Claim;
use App\Models\ClaimItem;
use App\Models\Insurer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClaimController extends Controller
{
    /**
     * Submit a new claim
     */
    public function store(SubmitClaimRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Find the insurer
            $insurer = Insurer::where('code', $request->insurer_code)->firstOrFail();

            // Create the claim
            $claim = Claim::create([
                'insurer_id' => $insurer->id,
                'provider_name' => $request->provider_name,
                'encounter_date' => $request->encounter_date,
                'submission_date' => now()->toDateString(),
                'priority_level' => $request->priority_level,
                'specialty' => $request->specialty,
                'total_amount' => 0, // Will be calculated from items
                'status' => ClaimStatus::PENDING,
            ]);

            // Create claim items
            $totalAmount = 0;
            foreach ($request->items as $itemData) {
                $subtotal = $itemData['unit_price'] * $itemData['quantity'];
                
                ClaimItem::create([
                    'claim_id' => $claim->id,
                    'name' => $itemData['name'],
                    'unit_price' => $itemData['unit_price'],
                    'quantity' => $itemData['quantity'],
                    'subtotal' => $subtotal,
                ]);

                $totalAmount += $subtotal;
            }

            // Update claim total amount
            $claim->total_amount = $totalAmount;
            $claim->save();

            DB::commit();

            // Dispatch job to optimize batches for this insurer
            OptimizeBatches::dispatch($insurer->id);

            Log::info("Claim {$claim->id} submitted successfully for insurer {$insurer->code}");

            return response()->json([
                'success' => true,
                'message' => 'Claim submitted successfully',
                'data' => [
                    'claim_id' => $claim->id,
                    'total_amount' => $claim->total_amount,
                    'status' => $claim->status,
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error submitting claim: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error submitting claim: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all batches with their claims
     */
    public function getBatches(): JsonResponse
    {
        try {
            $batches = Batch::with(['insurer:id,code,name', 'claims:id,batch_id,provider_name,total_amount,status,priority_level,specialty'])
                ->orderBy('batch_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($batch) {
                    return [
                        'id' => $batch->id,
                        'identifier' => $batch->identifier,
                        'insurer' => [
                            'code' => $batch->insurer->code,
                            'name' => $batch->insurer->name,
                        ],
                        'batch_date' => $batch->batch_date->format('Y-m-d'),
                        'total_claims' => $batch->total_claims,
                        'total_amount' => number_format($batch->total_amount, 2),
                        'status' => $batch->status,
                        'optimized_at' => $batch->optimized_at?->format('Y-m-d H:i:s'),
                        'notified_at' => $batch->notified_at?->format('Y-m-d H:i:s'),
                        'claims' => $batch->claims->map(function ($claim) {
                            return [
                                'id' => $claim->id,
                                'provider_name' => $claim->provider_name,
                                'total_amount' => number_format($claim->total_amount, 2),
                                'status' => $claim->status,
                                'priority_level' => $claim->priority_level,
                                'specialty' => $claim->specialty,
                            ];
                        }),
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $batches,
            ]);

        } catch (\Exception $e) {
            Log::error("Error fetching batches: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error fetching batches: ' . $e->getMessage(),
            ], 500);
        }
    }
}

