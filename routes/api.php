<?php

use App\Http\Controllers\ClaimController;
use Illuminate\Support\Facades\Route;



Route::post('/claims', [ClaimController::class, 'store'])->name('api.claims.store');

