<?php

use App\Actions\OptimizeBatchingAction;
use App\Http\Controllers\ProfileController;
use App\Models\Claim;
use App\Models\Insurer;
use App\Models\Specialty;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    $insurers = Insurer::get();
    $specialities = Specialty::get();
    return Inertia::render('SubmitClaim', [
        'insurers' => $insurers,
        'specialities' => $specialities,
    ]);
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
