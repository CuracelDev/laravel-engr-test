<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\ClaimItem;
use App\Models\Insurer;
use App\Services\ClaimBatchingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ClaimController extends Controller
{
    public function __construct(
        private ClaimBatchingService $batchingService
    ) {
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'insurer_code' => 'required|string|exists:insurers,code',
            'provider_name' => 'required|string|max:255',
            'provider_email' => 'required|email|max:255',
            'encounter_date' => 'required|date',
            'specialty' => 'required|string|max:255',
            'priority_level' => 'required|in:low,medium,high',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $insurer = Insurer::where('code', $request->insurer_code)->firstOrFail();
            
            $total = 0;
            foreach ($request->items as $item) {
                $total += $item['quantity'] * $item['price'];
            }

            $claim = Claim::create([
                'insurer_id' => $insurer->id,
                'provider_name' => $request->provider_name,
                'provider_email' => $request->provider_email,
                'claim_reference_code' => Claim::generateReferenceCode(),
                'encounter_date' => $request->encounter_date,
                'submission_date' => now(),
                'specialty' => $request->specialty,
                'priority_level' => $request->priority_level,
                'claim_total' => round($total, 2),
                'status' => 'pending',
            ]);

            foreach ($request->items as $item) {
                ClaimItem::create([
                    'claim_id' => $claim->id,
                    'name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => round($item['quantity'] * $item['price'], 2),
                ]);
            }

            $batch = $this->batchingService->processClaim($claim);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Claim submitted successfully',
                'data' => [
                    'claim' => [
                        'id' => $claim->id,
                        'reference_code' => $claim->claim_reference_code,
                        'total' => $claim->claim_total,
                        'status' => $claim->status,
                    ],
                    'batch' => [
                        'id' => $batch->id,
                        'code' => $batch->batch_code,
                        'date' => $batch->batch_date->format('Y-m-d'),
                        'claim_count' => $batch->claim_count,
                    ],
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit claim',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred',
            ], 500);
        }
    }

    public function getInsurers(): JsonResponse
    {
        $insurers = Insurer::select('id', 'code', 'name')->get();
        
        return response()->json([
            'success' => true,
            'data' => $insurers,
        ]);
    }
}
