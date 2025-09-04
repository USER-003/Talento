<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Trust Koyeb / reverse proxy; use framework defaults for X-Forwarded-* headers
        $middleware->trustProxies(at: '*');
        $middleware->web([
            \App\Http\Middleware\UpdateUserOnlineStatus::class,
        ]);
    })
    ->withExceptions(function () {
        //
    })->create();
