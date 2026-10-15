<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // MySQL 5.7/8 on older WAMP setups errors on long utf8mb4 index keys.
        Schema::defaultStringLength(191);
    }
}