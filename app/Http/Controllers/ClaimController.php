<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Insurer;
use App\Models\Claim;
use App\Services\ClaimBatchingService;
use App\Http\Requests\StoreClaimRequest;
use Illuminate\Http\JsonResponse;


class ClaimController extends Controller
{
    public function store(StoreClaimRequest $request, ClaimBatchingService $batcher): JsonResponse
    {
        $claim = $batcher->createAndBatch($request);

        return response()->json([
            'message' => 'Claim submitted and batched',
            'processing_cost' => $claim->processing_cost,
            'batch_id' => $claim->batch_id,
        ], 201);
    }
}
