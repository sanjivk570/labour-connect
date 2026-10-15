<?php

namespace App\Modules\Catalogue\Providers;

use App\Modules\Catalogue\Repositories\Contracts\CategoryRepositoryInterface;
use App\Modules\Catalogue\Repositories\Contracts\SkillRepositoryInterface;
use App\Modules\Catalogue\Repositories\Eloquent\CategoryRepository;
use App\Modules\Catalogue\Repositories\Eloquent\SkillRepository;
use App\Modules\Catalogue\Services\Contracts\CatalogueServiceInterface;
use App\Modules\Catalogue\Services\CatalogueService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CatalogueServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(SkillRepositoryInterface::class, SkillRepository::class);
        $this->app->bind(CatalogueServiceInterface::class, CatalogueService::class);
    }

    public function boot(): void
    {
        Route::middleware('api')->prefix('api')->group(__DIR__.'/../routes.php');
    }
}
