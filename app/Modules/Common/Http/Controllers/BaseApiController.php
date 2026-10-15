<?php

namespace App\Modules\Common\Http\Controllers;

use App\Modules\Common\Traits\ApiResponse;
use Illuminate\Routing\Controller;

abstract class BaseApiController extends Controller
{
    use ApiResponse;
}
