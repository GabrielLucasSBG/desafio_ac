<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TransactionController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    Route::prefix('transactions')->group(function () {
        Route::post('/transfer', [TransactionController::class, 'transfer']);
        Route::post('/deposit', [TransactionController::class, 'deposit']);
        Route::post('/reverse', [TransactionController::class, 'reverse']);
        Route::get('/history', [TransactionController::class, 'history']);
    });
});
