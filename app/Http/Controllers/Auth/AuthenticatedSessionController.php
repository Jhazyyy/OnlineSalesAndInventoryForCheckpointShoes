<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Update login tracking
        /** @var User $user */
        $user = Auth::user();
        if ($user) {
            $user->increment('login_count');
            $user->update([
                'last_login_at' => now(),
            ]);

            // Log successful login
            AuditLog::logAction(
                AuditLog::ACTION_LOGIN,
                AuditLog::MODULE_AUTH,
                "User '{$user->name}' logged in successfully.",
                User::class,
                $user->id,
                $user->name,
                null,
                null,
                AuditLog::SEVERITY_INFO
            );
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();
        
        // Log logout before ending session
        if ($user) {
            AuditLog::logAction(
                AuditLog::ACTION_LOGOUT,
                AuditLog::MODULE_AUTH,
                "User '{$user->name}' logged out.",
                User::class,
                $user->id,
                $user->name,
                null,
                null,
                AuditLog::SEVERITY_INFO
            );
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
