<?php

use App\Modules\Labour\Http\Controllers\Api\LabourProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'role:labour'])->prefix('labour')->group(function () {
    Route::get('profile', [LabourProfileController::class, 'show']);
    Route::put('registration/step/{step}', [LabourProfileController::class, 'saveStep'])
        ->whereNumber('step');
    Route::patch('availability/today', [LabourProfileController::class, 'updateToday']);
    Route::get('dashboard', [LabourProfileController::class, 'dashboard']);
});
