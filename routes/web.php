<?php

use App\Http\Controllers\Web\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('orders.create');
});

Route::prefix('orders')->name('orders.')->group(function () {

    Route::get('/create', [OrderController::class, 'create'])->name('create');

    Route::post('/orders/check-stock', [OrderController::class, 'checkStock'])->name('check-stock');

    Route::post('/', [OrderController::class, 'store'])->name('store');

    Route::get('/{order}', [OrderController::class, 'show'])->name('show');

    Route::get('/history/{customer:email}', [OrderController::class, 'history'])->name('history');
});

// API routes for order operations
use App\Http\Controllers\Api\OrderApiController;

Route::prefix('api')->group(function () {
    Route::post('/orders', [OrderApiController::class, 'create']);
    Route::get('/orders/history', [OrderApiController::class, 'history']);
    Route::get('/products/low-stock', [OrderApiController::class, 'lowStock']);
});