<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ClaimController;
use App\Http\Controllers\Api\InsurerController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/claims', [ClaimController::class, 'store']);
Route::get('/insurers', [InsurerController::class, 'index']);

// Route::get('/claims', function () {
//     return response()->json([
//         'message' => 'server',
//     ]);
// });