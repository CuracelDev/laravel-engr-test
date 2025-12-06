<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClaimRequest;
use App\Models\Claim;
use App\Models\Insurer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClaimController extends Controller
{
    public function store(StoreClaimRequest $request)
    {
        $validated = $request->validated();
        
        $insurer = Insurer::where('code', $validated['insurer_code'])->firstOrFail();

        // Calculate total amount from items to ensure accuracy server-side
        $totalAmount = 0;
        foreach ($validated['items'] as $item) {
            $totalAmount += $item['unit_price'] * $item['quantity'];
        }

        $claim = DB::transaction(function () use ($validated, $insurer, $totalAmount) {
            $claim = Claim::create([
                'provider_name' => $validated['provider_name'],
                'insurer_id' => $insurer->id,
                'encounter_date' => $validated['encounter_date'],
                'submission_date' => now(), // Defaults to current date/time
                'priority' => $validated['priority'],
                'specialty' => $validated['specialty'],
                'amount' => $totalAmount,
                'status' => 'pending'
            ]);

            foreach ($validated['items'] as $item) {
                $claim->items()->create([
                    'name' => $item['name'],
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['unit_price'] * $item['quantity'],
                ]);
            }

            return $claim;
        });

        return response()->json([
            'message' => 'Claim submitted successfully',
            'claim_id' => $claim->id
        ], 201);
    }
}
