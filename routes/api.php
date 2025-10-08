<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\SpecialtyController;


Route::prefix('v1')->group(function () {

    // Submit claim (no authentication required)
    Route::post('/submit-claim', [ClaimController::class, 'submitClaim'])->name('claims.submit');
    Route::get('/specialties', [SpecialtyController::class, 'index'])->name('specialties.index');
});

// Authenticated Routes (accessible only by authenticated users)
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {

    // Fetch claims for the authenticated user
    Route::get('/claims', [ClaimController::class, 'getClaims'])
        ->name('claims.index');

    // Add more routes here that need authentication
});
