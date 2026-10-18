<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\KeepAdminsInAdminInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => EnsureUserHasRole::class,
            'admin.interface' => KeepAdminsInAdminInterface::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (TokenMismatchException $exception, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Sesi sudah kedaluwarsa. Silakan muat ulang halaman dan coba lagi.'], 419);
            }

            return redirect()->route('login')->with('status', 'Sesi sudah kedaluwarsa. Silakan login kembali.');
        });
    })->create();
