<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->alias([
            'auth.customer'   => \App\Http\Middleware\EnsureCustomerIsAuthenticated::class,
            'session.timeout' => \App\Http\Middleware\SessionTimeout::class,
            'two-factor'      => \App\Http\Middleware\EnsureTwoFactorVerified::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->booted(function () {
        // Rate limiting: max 5 login attempts per minute per IP
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('customer-login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // 5 wrong 2FA codes per 10 minutes per user — a 6-digit code must not be brute-forceable
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinutes(10, 5)->by(optional($request->user())->id ?: $request->ip());
        });
    })->create();
