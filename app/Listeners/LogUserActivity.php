<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Spatie\Activitylog\Models\Activity;

class LogUserActivity
{
    /**
     * Handle user login events.
     */
    public function handleLogin(Login $event): void
    {
        if (class_exists(Activity::class) && $event->user) {
            activity()
                ->causedBy($event->user)
                ->performedOn($event->user)
                ->withProperties(['ip' => request()->ip(), 'user_agent' => request()->userAgent()])
                ->log('login');
        }
    }

    /**
     * Handle user logout events.
     */
    public function handleLogout(Logout $event): void
    {
        if (class_exists(Activity::class) && $event->user) {
            activity()
                ->causedBy($event->user)
                ->performedOn($event->user)
                ->withProperties(['ip' => request()->ip()])
                ->log('logout');
        }
    }

    /**
     * Handle password reset events.
     */
    public function handlePasswordReset(PasswordReset $event): void
    {
        if (class_exists(Activity::class) && $event->user) {
            activity()
                ->causedBy($event->user)
                ->performedOn($event->user)
                ->withProperties(['ip' => request()->ip()])
                ->log('password_reset');
        }
    }

    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe($events): array
    {
        return [
            Login::class => 'handleLogin',
            Logout::class => 'handleLogout',
            PasswordReset::class => 'handlePasswordReset',
        ];
    }
}
