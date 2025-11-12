<?php

use App\Actions\SubmitClaim;
use App\Actions\BatchClaim;
use App\Actions\ProcessBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/claims', function (Request $request) {
    try {
        $claim = SubmitClaim::run($request->all());
        BatchClaim::run($claim);
        ProcessBatch::run($claim->insurer, $claim->batch_id, $claim->batch_date);
        
        return response()->json([
            'message' => 'Claim submitted successfully',
            'claim_id' => $claim->id,
            'batch_id' => $claim->batch_id
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json(['errors' => $e->errors()], 422);
    }
});
