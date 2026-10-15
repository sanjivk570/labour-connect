<?php

use App\Modules\Search\Http\Controllers\Api\LabourSearchController;
use Illuminate\Support\Facades\Route;

Route::get('labour/search', LabourSearchController::class);
