<?php

namespace App\View\Composers;

use App\Models\Notification;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class NotificationComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $unreadCount = 0;
        
        if (Auth::check()) {
            $unreadCount = Notification::whereNull('read_at')
                ->where(function($q) {
                    $q->whereNull('user_id')
                      ->orWhere('user_id', Auth::id());
                })
                ->count();
        } else {
            $unreadCount = Notification::whereNull('read_at')
                ->whereNull('user_id')
                ->count();
        }
        
        $view->with('unreadNotificationCount', $unreadCount);
    }
}
