<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // List latest notifications for the current user (or global if user_id is null)
    public function index(Request $request)
    {
        $user = Auth::user();

        $notifications = Notification::where(function ($q) use ($user) {
            $q->whereNull('user_id');
            if ($user) {
                $q->orWhere('user_id', $user->id);
            }
        })->orderBy('created_at', 'desc')->limit(20)->get();

        return response()->json($notifications);
    }

    // Count unread notifications
    public function unreadCount()
    {
        $user = Auth::user();

        $count = Notification::whereNull('read_at')
            ->where(function ($q) use ($user) {
                $q->whereNull('user_id');
                if ($user) {
                    $q->orWhere('user_id', $user->id);
                }
            })->count();

        return response()->json(['unread' => $count]);
    }

    // Mark a notification as read
    public function markRead(Request $request, $id)
    {
        $notification = Notification::findOrFail($id);
        $notification->read_at = now();
        $notification->save();

        return response()->json(['ok' => true]);
    }

    // Create a notification (internal use)
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'title' => 'required|string',
            'message' => 'nullable|string',
            'level' => 'nullable|string',
            'link' => 'nullable|string',
            'type' => 'nullable|string',
        ]);

        $notification = Notification::create($data);

        return response()->json($notification, 201);
    }
}
