<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return inertia('Welcome');
});

Route::get('/SubmitClaim', function () {
    return inertia('SubmitClaim');
});

Route::get('/Specialty', function () {
    return inertia('Specialty');
});

Route::get('/Provider', function () {
    return inertia('Provider');
});

Route::get('/Insurer', function () {
    return inertia('Insurer');
});

Route::get('/ClaimBatch', function () {
    return inertia('ClaimBatch');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';