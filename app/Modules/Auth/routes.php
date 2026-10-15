<?php

use App\Modules\Auth\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('otp', [AuthController::class, 'sendOtp']);
    Route::post('otp/verify', [AuthController::class, 'verifyOtp']);
    Route::post('social', [AuthController::class, 'socialLogin']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('role', [AuthController::class, 'selectRole']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });
});
