<?php

use App\Http\Controllers\Web\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('orders.create');
});

Route::prefix('orders')->name('orders.')->group(function () {

    // New order screen
    Route::get('/create', [OrderController::class, 'create'])
        ->name('create');

    // Store order from Blade frontend
    Route::post('/', [OrderController::class, 'store'])
        ->name('store');

    // Order details
    Route::get('/{order}', [OrderController::class, 'show'])
        ->name('show');

    // Customer order history
    Route::get('/history/{customer:email}', [OrderController::class, 'history'])
        ->name('history');
});