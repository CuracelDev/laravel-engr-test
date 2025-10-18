<?php

use App\Enums\MedicalSpecialty;
use App\Enums\PriorityLevel;
use App\Http\Controllers\ProfileController;
use App\Models\Insurer;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('SubmitClaim', [
        'insurers' => Insurer::select('id', 'code', 'name')->get(),
        'specialties' => MedicalSpecialty::labels(),
        'priorityLevels' => PriorityLevel::labels(),
    ]);
})->name('submit-claim');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
