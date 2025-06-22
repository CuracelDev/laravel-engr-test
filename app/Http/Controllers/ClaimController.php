<?php

namespace App\Http\Controllers;

use App\Actions\SubmitClaimAction;
use App\Http\Requests\StoreClaimRequest;
use App\Models\Claim;
use App\Models\Insurer;
use App\Services\BatchOptimizerService;
use App\Services\BestInsurerResolverService;
use Exception;
use Illuminate\Http\Request;

class ClaimController extends Controller
{
    /**
     * @throws Exception
     */
    public function store(StoreClaimRequest $request)
    {

        $data = $request->validated();

        $comparison = app(BestInsurerResolverService::class)->resolveWithComparison($data);

        $claim = app(SubmitClaimAction::class)->execute($data);

        try {
            app(BatchOptimizerService::class)->handle($claim);
        } catch (\App\Exception\BatchingLimitReachedException $e) {
            return response()->json([
                'message' => 'Claim submitted but could not be batched.',
                'error' => $e->getMessage(),
                'action_required' => 'Please try again tomorrow or contact admin.',
            ], 422);
        }
        // Optional: Notify insurer (later)
        // Mail::to(...)->send(...)
//        dd($comparison);
        return response()->json([
            'message' => 'Claim submitted successfully.',
            'claim_id' => $claim->id,
            ...$comparison
        ]);
    }

}
