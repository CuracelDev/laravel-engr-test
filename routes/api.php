<?php

use App\Http\Controllers\BatchController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\InsurerController;
use App\Http\Controllers\SpecialtyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('claims', ClaimController::class)->only(['store', 'index', 'show']);

Route::get('/insurers', [InsurerController::class, 'index']);
Route::get('/specialties', [SpecialtyController::class, 'index']);

Route::apiResource('batches', BatchController::class)->only(['index', 'show']);
