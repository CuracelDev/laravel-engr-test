<?php

namespace App\Http\Controllers;

use App\Helpers\SubmitClaimHelper;
use App\Http\Requests\StoreClaimRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClaimController extends Controller
{
    protected SubmitClaimHelper $submitClaim;

    public function __construct(SubmitClaimHelper $submitClaim)
    {
        $this->submitClaim = $submitClaim;
    }

    public function store(StoreClaimRequest $request): JsonResponse
    {
        try {
            $claim = $this->submitClaim->execute($request->validated());

            return response()->json([
                'message' => 'Claim submitted and batched successfully',
                'data' => [
                    'claim_id' => $claim->id,
                    'status' => $claim->status,
                    'batch_id' => $claim->batch_id,
                    'total_amount' => $claim->total_amount,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to submit claim',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
