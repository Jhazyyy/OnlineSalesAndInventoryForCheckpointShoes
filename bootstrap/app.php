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
    ->withMiddleware(function (Middleware $middleware): void {
        // Register role and permission middleware
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'admin' => \App\Http\Middleware\AdminOnly::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        ]);
        
        // Ensure CSRF token validation is enabled
        $middleware->validateCsrfTokens(except: [
            // Add any routes that should be excluded from CSRF protection
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'CSRF token mismatch. Please refresh and try again.'], 419);
            }
            
            // If on login page, redirect to login route to refresh the token
            if ($request->is('login')) {
                return redirect()->route('login')->with('error', 'Your session has expired. Please try logging in again.');
            }
            
            return redirect()->back()->withInput($request->except('password', '_token'))->with('error', 'Your session has expired. Please try again.');
        });
    })->create();
