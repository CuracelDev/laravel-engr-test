<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Claim;
use App\Models\Insurer;
use App\Services\BatchingAlgorithmService;
use Illuminate\Support\Facades\DB;

class ClaimsController extends Controller
{
    public function index(Request $request, BatchingAlgorithmService $batchingAlgorithmService)
    {
        // Create claim
        // $claim = Claim::create($request->all());

        // Get insurer
        $insurer = Insurer::where('code', $request->insurer_code)->get();

        if (!$insurer) {
            return "Insurer not found";
        }

        $insurer = $insurer[0];        

        DB::beginTransaction();

        $claim = Claim::firstOrCreate([
            'insurer_id' => $insurer->id,
            'encounter_date' => $request->encounter_date,
            'submission_date' => date('Y-m-d'),
            'priority_level' => $request->priority_level,
            'specialty_type' => $request->specialty,
            'monetary_value' => $request->total,
            'items' => json_encode($request->items),
        ]);

        try {
            $batchingAlgorithmService->batchClaims($claim, $insurer);

            DB::commit();
            return "Successfully batched claims";
        } catch (\Exception $e) {
            DB::rollback();
            return "Failed to batch claims";
        }


        // Notify insurer via email
        // ...

        return response()->json(['message' => 'Claim submitted successfully']);
    }
}
