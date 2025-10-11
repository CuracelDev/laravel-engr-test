<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ClaimService;

class ClaimController extends Controller
{
    protected $claimService;

    public function __construct(ClaimService $claimService)
    {
        $this->claimService = $claimService;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'insurer_code' => 'required|string|exists:insurers,code',
            'provider_name' => 'required|string',
            'encounter_date' => 'required|date',
            'specialty' => 'required|string',
            'priority_level' => 'required|integer|min:1|max:5',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
        ]);
        
        $result = $this->claimService->createClaimWithItems($validated);

        return response()->json([
            'message' => 'Claim submitted successfully',
            'data' => $result
        ], 201);
    }
}
