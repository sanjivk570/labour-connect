<?php

namespace App\Modules\Search\Providers;

use App\Modules\Search\Repositories\Contracts\LabourSearchRepositoryInterface;
use App\Modules\Search\Repositories\Eloquent\LabourSearchRepository;
use App\Modules\Search\Services\Contracts\LabourSearchServiceInterface;
use App\Modules\Search\Services\LabourSearchService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class SearchServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(LabourSearchRepositoryInterface::class, LabourSearchRepository::class);
        $this->app->bind(LabourSearchServiceInterface::class, LabourSearchService::class);
    }

    public function boot(): void
    {
        Route::middleware('api')->prefix('api')->group(__DIR__.'/../routes.php');
    }
}
