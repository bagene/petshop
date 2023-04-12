<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth.api')->group(function () {
    // test route for auth middleware
    Route::get('/user', function () {
        return response()->json(auth()->user());
    });
    Route::patch('/orders/{order}/payments', [OrderController::class, 'payOrder']);
    Route::get('/payments/{payment}', [PaymentController::class, 'get']);
});

