<?php

use App\Actions\SubmitClaimAction;
use App\Http\Controllers\ClaimController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::post('/submit-claim', function (Request $request, SubmitClaimAction $submitClaimAction) {

//     try {
//         $claim = $submitClaimAction->handle($request);
//         return response()->json(['claim' => $claim, 'message' => 'success'], 200);
//     } catch (\Exception $e) {
//         return response()->json(['error' => $e->getMessage()], $e->getCode());
//     }
// });
