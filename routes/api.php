<?php

use App\Http\Controllers\BatchClaimController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\InsurerController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\SpecialtyController;

Route::controller(ProviderController::class)->group(function () {
    Route::get('/list_all_provider', 'list_all_provider');
    Route::post('/save_provider', 'save_provider');
});

Route::controller(InsurerController::class)->group(function () {
    Route::get('/list_all_insurer', 'list_all_insurer');
    Route::post('/save_insurer', 'save_insurer');
});

Route::controller(SpecialtyController::class)->group(function () {
    Route::get('/list_all_specialty', 'list_all_specialty');
    Route::post('/save_specialty', 'save_specialty');
});

Route::controller(ClaimController::class)->group(function () {
    Route::post('/process_claim', 'process_claim');
});

Route::controller(BatchClaimController::class)->group(function () {
    Route::get('/get_all_batched', 'get_all_batched');
    Route::get('/process_batches', 'process_batches');
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');