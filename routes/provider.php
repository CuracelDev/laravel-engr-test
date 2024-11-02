<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('/provider')->group(function () {
    Route::post('/order', [OrderController::class, 'store'])->name('provider.store');
})->middleware('auth');
