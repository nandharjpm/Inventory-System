<?php

use App\Http\Controllers\Api\OrderApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('orders')->group(function () {
    Route::post('/', [OrderApiController::class, 'create']);
    Route::post('/check-stock', [OrderApiController::class, 'checkStock']);
    Route::get('/history', [OrderApiController::class, 'history']);
    Route::get('/history/{customer:email}', [OrderApiController::class, 'history']);
    Route::get('/customers', [OrderApiController::class, 'customers']);
    Route::get('/{order}', [OrderApiController::class, 'show'])->whereNumber('order');
});

Route::get('/products/low-stock', [OrderApiController::class, 'lowStock']);
