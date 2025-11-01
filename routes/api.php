<?php

use Illuminate\Http\Request;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\SystemController;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/claims', [ClaimController::class, 'store']);
Route::get('/insurers', [SystemController::class, 'insurers']);
Route::get('/batches',  [SystemController::class, 'batches']);
