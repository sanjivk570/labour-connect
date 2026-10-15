<?php

use App\Modules\Customer\Http\Controllers\Api\CustomerProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'role:customer'])->prefix('customer')->group(function () {
    Route::get('profile', [CustomerProfileController::class, 'show']);
    Route::put('profile', [CustomerProfileController::class, 'update']);
});
