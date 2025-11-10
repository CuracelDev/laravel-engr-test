<?php

use App\Actions\GetInsurerByCode;
use App\Actions\GetPriorities;
use App\Actions\GetSpecialties;
use App\Actions\SubmitClaim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/claims', SubmitClaim::class)->name('claims.store');

Route::get('/insurers/{code}', GetInsurerByCode::class)->name('insurers.by-code');

// Miscellaneous routes
Route::get('/specialties', GetSpecialties::class)->name('specialties.index');
Route::get('/priorities', GetPriorities::class)->name('priorities.index');
