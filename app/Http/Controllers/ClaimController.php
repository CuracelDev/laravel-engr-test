<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClaimRequest;
use App\Models\Claim;
use App\Models\Insurer;
use App\Services\ClaimService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClaimController extends Controller
{
    protected ClaimService $claimService;

    public function __construct(ClaimService $claimService)
    {
        $this->claimService = $claimService;
    }

    public function store(StoreClaimRequest $request): JsonResponse
    {
        try {
            $claim = $this->claimService->submit($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Claim submitted successfully',
                'data' => [
                    'claim_id' => $claim->id,
                    'batch_id' => $claim->batch?->id,
                    'batch_identifier' => $claim->batch?->batch_identifier,
                    'total_amount' => $claim->total_amount,
                    'estimated_processing_cost' => $claim->calculateProcessingCost(),
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process claim: '.$e->getMessage(),
            ], 422);
        }
    }

    public function show(Claim $claim): JsonResponse
    {
        $claim->load(['items', 'batch', 'insurer']);

        return response()->json([
            'success' => true,
            'data' => $claim,
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $query = Claim::with(['items', 'batch', 'insurer']);

        if ($request->has('provider_name')) {
            $query->where('provider_name', 'like', '%'.$request->provider_name.'%');
        }
        if ($request->has('insurer_code')) {
            $query->where('insurer_code', $request->insurer_code);
        }
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('specialty')) {
            $query->where('specialty', $request->specialty);
        }

        $claims = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $claims,
        ]);
    }

    public function getInsurers(): JsonResponse
    {
        $insurers = Insurer::with('configuration')->get();

        return response()->json([
            'success' => true,
            'data' => $insurers,
        ]);
    }

    public function getSpecialties(): JsonResponse
    {
        $specialties = [
            'cardiology' => 'Cardiology',
            'orthopedics' => 'Orthopedics',
            'neurology' => 'Neurology',
            'general' => 'General Medicine',
            'pediatrics' => 'Pediatrics',
        ];

        return response()->json([
            'success' => true,
            'data' => $specialties,
        ]);
    }
}
