<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Claim;
use App\Models\Batch;

use App\Actions\BatchClaims;

use Illuminate\Support\Facades\Mail;
use App\Mail\InsurerNotification;

use Illuminate\Support\Facades\Log;
use Exception;

class ClaimController extends Controller
{
    //
public function store(Request $request)
{
    try {
        $validated = $request->validate([
            'insurer_code' => 'required|exists:insurers,code',
            'provider_name' => 'required|string',
            'encounter_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'specialty' => 'required|string',
            'priority_level' => 'required|integer|between:1,5',
        ]);

        $totalValue = collect($validated['items'])->sum(function ($item) {
            return $item['unit_price'] * $item['quantity'];
        });

        $validated['total_value'] = $totalValue;
        $validated['submission_date'] = now();

        $claim = Claim::create($validated);

        foreach ($validated['items'] as $item) {
            $claim->items()->create([
                'item_name' => $item['name'],
                'unit_price' => $item['unit_price'],
                'quantity' => $item['quantity'],
                'subtotal' => $item['unit_price'] * $item['quantity'],
            ]);
        }

        $batch = (new BatchClaims)->handle($claim);

        $this->notifyInsurer($batch);

        return response()->json([
            'message' => 'Claim submitted and batched successfully!',
            'batch_id' => $batch->id,
        ], 201);

    } catch (Exception $e) {
    
        Log::error('Error submitting claim', [
            'error' => $e->getMessage(),
        ]);

        return response()->json([
            'message' => 'An error occurred while submitting the claim. Please try again later.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

private function notifyInsurer(Batch $batch)
{
    try {
        $insurer = $batch->insurer;
        $insurer['email'] = 'ibrahimmalik85@gmail.com'; 

        Mail::to($insurer->email)->send(new InsurerNotification($batch));
        Log::info('Insurer notification email sent successfully', ['batch_id' => $batch->id]);

    } catch (Exception $e) {
        
        Log::error('Error sending insurer notification email', [
            'error' => $e->getMessage(),
        ]);

      
    }
}
}
