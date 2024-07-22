<?php

use App\Actions\SubmitOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/order/store', SubmitOrder::class)->name('order.store');
