<?php

namespace App\Modules\Labour\Providers;

use App\Modules\Labour\Repositories\Contracts\LabourProfileRepositoryInterface;
use App\Modules\Labour\Repositories\Contracts\LabourRepositoryInterface;
use App\Modules\Labour\Repositories\Eloquent\LabourProfileRepository;
use App\Modules\Labour\Repositories\Eloquent\LabourRepository;
use App\Modules\Labour\Services\Contracts\LabourServiceInterface;
use App\Modules\Labour\Services\LabourService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class LabourServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(LabourRepositoryInterface::class, LabourRepository::class);
        $this->app->bind(LabourProfileRepositoryInterface::class, LabourProfileRepository::class);
        $this->app->bind(LabourServiceInterface::class, LabourService::class);
    }

    public function boot(): void
    {
        Route::middleware('api')->prefix('api')->group(__DIR__.'/../routes.php');
    }
}
