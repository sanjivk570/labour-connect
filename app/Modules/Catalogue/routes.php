<?php

use App\Modules\Catalogue\Http\Controllers\Api\CatalogueController;
use Illuminate\Support\Facades\Route;

Route::get('categories', [CatalogueController::class, 'categories']);
Route::get('categories/{categoryId}/skills', [CatalogueController::class, 'skills']);
