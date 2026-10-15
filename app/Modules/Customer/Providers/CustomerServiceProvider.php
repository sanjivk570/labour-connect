<?php

namespace App\Modules\Customer\Providers;

use App\Modules\Customer\Repositories\Contracts\CustomerRepositoryInterface;
use App\Modules\Customer\Repositories\Eloquent\CustomerRepository;
use App\Modules\Customer\Services\Contracts\CustomerServiceInterface;
use App\Modules\Customer\Services\CustomerService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CustomerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CustomerRepositoryInterface::class, CustomerRepository::class);
        $this->app->bind(CustomerServiceInterface::class, CustomerService::class);
    }

    public function boot(): void
    {
        Route::middleware('api')->prefix('api')->group(__DIR__.'/../routes.php');
    }
}
