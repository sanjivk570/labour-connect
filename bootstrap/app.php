<?php

use App\Modules\Common\Http\Middleware\EnsureRole;
use App\Modules\Common\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Resolve the active locale for every API request (Accept-Language / ?locale / user).
        $middleware->api(prepend: [
            SetLocale::class,
        ]);

        $middleware->alias([
            'role' => EnsureRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Render OTP exceptions as structured JSON responses.
        $exceptions->render(function (\App\Modules\Auth\Exceptions\InvalidOtpException $e) {
            return response()->json([
                'success' => false,
                'message' => __('api.'.$e->getMessage()),
            ], 422);
        });

        $exceptions->render(function (\App\Modules\Auth\Exceptions\OtpExpiredException $e) {
            return response()->json([
                'success' => false,
                'message' => __('api.'.$e->getMessage()),
            ], 422);
        });
    })->create();