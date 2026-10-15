<?php

namespace App\Modules\Auth\Providers;

use App\Modules\Auth\Repositories\Contracts\OtpRepositoryInterface;
use App\Modules\Auth\Repositories\Contracts\UserRepositoryInterface;
use App\Modules\Auth\Repositories\Eloquent\OtpRepository;
use App\Modules\Auth\Repositories\Eloquent\UserRepository;
use App\Modules\Auth\Services\Contracts\AuthServiceInterface;
use App\Modules\Auth\Services\Contracts\OtpServiceInterface;
use App\Modules\Auth\Services\AuthService;
use App\Modules\Auth\Services\OtpService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(OtpRepositoryInterface::class, OtpRepository::class);
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
        $this->app->bind(OtpServiceInterface::class, OtpService::class);
    }

    public function boot(): void
    {
        Route::middleware('api')->prefix('api')
            ->group(__DIR__.'/../routes.php');
    }
}
