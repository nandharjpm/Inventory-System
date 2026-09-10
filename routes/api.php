<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/orders', [OrderApiController::class, 'create']);
Route::get('/orders/history', [OrderApiController::class, 'history']);
Route::get('/products/low-stock', [OrderApiController::class, 'lowStock']);
