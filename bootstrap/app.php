<?php

use App\Http\Middleware\EnsurePatient;
use App\Http\Middleware\EnsureStaff;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Resolve the request locale (session-backed) for every web request.
        $middleware->web(append: [
            SetLocale::class,
        ]);

        $middleware->alias([
            'staff' => EnsureStaff::class,
            'patient' => EnsurePatient::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
