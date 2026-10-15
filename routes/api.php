<?php

use App\Modules\Common\Http\Controllers\Api\LocalizationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/localization/strings', [LocalizationController::class, 'strings']);

Route::get('/health', function () {
    return response()->json([
        'app' => config('app.name'),
        'status' => 'ok',
    ]);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
