<?php

use App\Http\Controllers\BatchController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;


Route::get('/', function () {
    return Redirect::route('claim.create');
    // return Inertia::render('SubmitOrder');
});

Route::resource('claim', ClaimController::class)->middleware([HandlePrecognitiveRequests::class])->only([
    'create',
    'store'
]);;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [BatchController::class, 'index'])->name('dashboard');
    Route::resource('batch', BatchController::class)->middleware([HandlePrecognitiveRequests::class])->only([
        'show',
        'update',
    ]);
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
