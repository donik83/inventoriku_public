<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // == TAMBAH PENDAFTARAN ALIAS SPATIE DI SINI ==
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
        // ==========================================

        // Pastikan kod middleware sedia ada (jika ada) seperti di bawah dikekalkan
        // Contoh:
        // $middleware->validateCsrfTokens(except: [
        //     'stripe/*',
        // ]);
        // $middleware->web(append: [ ... ]);
        // $middleware->api(prepend: [ ... ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
