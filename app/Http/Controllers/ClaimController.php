<?php

namespace App\Http\Controllers;

use App\Actions\ClaimAction;
use App\Http\Requests\StoreClaimRequest;
use Inertia\Inertia;
 

class ClaimController extends Controller
{
    public function create()
    {
        return Inertia::render('Claims/SubmitClaim', [
            'insurers' => \App\Models\Insurer::all()
        ]);
    }

    public function store(StoreClaimRequest $request, ClaimAction $action)
    {
        $claim = $action->execute($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Claim saved and batched successfully',
            'data' => [
                'claim' => $claim,
                'batch' => $claim->batch ?? null,
            ]
        ], 201);
    }
}
