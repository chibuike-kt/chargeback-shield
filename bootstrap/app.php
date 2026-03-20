<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // Global middleware — applies to all requests
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        // Middleware aliases
        $middleware->alias([
            'merchant.api'  => \App\Http\Middleware\ValidateMerchantApiKey::class,
            'rate.limit'    => \App\Http\Middleware\RateLimitApiRequests::class,
            'log.api'       => \App\Http\Middleware\LogApiRequests::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
