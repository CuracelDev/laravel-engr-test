<?php

use App\Http\Controllers\ClaimController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('claims')->group(function () {
    Route::post('/', [ClaimController::class, 'store']);
    Route::get('/insurers', [ClaimController::class, 'getInsurers']);
});
