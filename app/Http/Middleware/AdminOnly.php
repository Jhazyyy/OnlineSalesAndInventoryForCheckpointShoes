<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to restrict access to admins only
 * Supports simultaneous multi-user access
 */
class AdminOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!$request->user()) {
            return redirect()->route('login')
                ->with('error', 'Please login to access this page.');
        }

        // Check if user has admin role
        if (!$request->user()->hasRole('admin')) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        return $next($request);
    }
}
