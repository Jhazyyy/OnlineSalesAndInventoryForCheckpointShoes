<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationsController extends Controller
{
    /**
     * Display the notifications page
     */
    public function index(Request $request)
    {
        // Get filter from request
        $filter = $request->get('filter', 'all');
        
        // Build the query for notifications
        $query = Notification::query();
        
        // Apply user-specific or global notifications
        // Global notifications have user_id = null
        if (Auth::check()) {
            $query->where(function($q) {
                $q->whereNull('user_id')
                  ->orWhere('user_id', Auth::id());
            });
        } else {
            $query->whereNull('user_id');
        }
        
        // Apply filters
        if ($filter !== 'all') {
            switch ($filter) {
                case 'unread':
                    $query->whereNull('read_at');
                    break;
                case 'inventory':
                    $query->where('type', 'LIKE', 'inventory.%');
                    break;
                case 'orders':
                    $query->where(function($q) {
                        $q->where('type', 'LIKE', 'sales.%')
                          ->orWhere('type', 'LIKE', 'purchases.%');
                    });
                    break;
                case 'system':
                    $query->where('type', 'LIKE', 'system.%');
                    break;
                default:
                    $query->where('type', 'LIKE', $filter . '.%');
                    break;
            }
        }
        
        // Get paginated notifications
        $notifications = $query->latest()
            ->paginate(20)
            ->withQueryString();
        
        // Get counts for filters
        $counts = $this->getNotificationCounts();
        
        return view('notifications.index', [
            'filter' => $filter,
            'notifications' => $notifications,
            'counts' => $counts,
        ]);
    }
    
    /**
     * Mark a notification as read
     */
    public function markAsRead($id)
    {
        try {
            $notification = Notification::findOrFail($id);
            
            // Check if notification belongs to current user or is global
            if ($notification->user_id !== null && $notification->user_id !== Auth::id()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            
            $notification->markAsRead();
            
            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read',
                'unread_count' => $this->getUnreadCount()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to mark notification as read: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to mark as read'], 500);
        }
    }
    
    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        try {
            $query = Notification::whereNull('read_at');
            
            if (Auth::check()) {
                $query->where(function($q) {
                    $q->whereNull('user_id')
                      ->orWhere('user_id', Auth::id());
                });
            } else {
                $query->whereNull('user_id');
            }
            
            $query->update(['read_at' => now()]);
            
            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read',
                'unread_count' => 0
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to mark all notifications as read: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to mark all as read'], 500);
        }
    }
    
    /**
     * Delete a notification
     */
    public function destroy($id)
    {
        try {
            $notification = Notification::findOrFail($id);
            
            // Check if notification belongs to current user or is global
            if ($notification->user_id !== null && $notification->user_id !== Auth::id()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            
            $notification->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Notification deleted',
                'unread_count' => $this->getUnreadCount()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete notification: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete notification'], 500);
        }
    }
    
    /**
     * Delete all notifications for the current user
     */
    public function destroyAll()
    {
        try {
            $query = Notification::query();
            
            if (Auth::check()) {
                $query->where(function($q) {
                    $q->whereNull('user_id')
                      ->orWhere('user_id', Auth::id());
                });
            } else {
                $query->whereNull('user_id');
            }
            
            $deletedCount = $query->delete();
            
            return response()->json([
                'success' => true,
                'message' => "All notifications deleted ({$deletedCount} removed)",
                'unread_count' => 0
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete all notifications: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete all notifications'], 500);
        }
    }
    
    /**
     * Get unread notification count
     */
    public function getUnreadCount()
    {
        $query = Notification::whereNull('read_at');
        
        if (Auth::check()) {
            $query->where(function($q) {
                $q->whereNull('user_id')
                  ->orWhere('user_id', Auth::id());
            });
        } else {
            $query->whereNull('user_id');
        }
        
        return $query->count();
    }
    
    /**
     * Get notification counts by category
     */
    private function getNotificationCounts()
    {
        $baseQuery = Notification::query();
        
        if (Auth::check()) {
            $baseQuery->where(function($q) {
                $q->whereNull('user_id')
                  ->orWhere('user_id', Auth::id());
            });
        } else {
            $baseQuery->whereNull('user_id');
        }
        
        return [
            'all' => (clone $baseQuery)->count(),
            'unread' => (clone $baseQuery)->whereNull('read_at')->count(),
            'inventory' => (clone $baseQuery)->where('type', 'LIKE', 'inventory.%')->count(),
            'orders' => (clone $baseQuery)->where(function($q) {
                $q->where('type', 'LIKE', 'sales.%')
                  ->orWhere('type', 'LIKE', 'purchases.%');
            })->count(),
            'system' => (clone $baseQuery)->where('type', 'LIKE', 'system.%')->count(),
        ];
    }
    
    /**
     * API endpoint to get latest notifications (for real-time updates)
     */
    public function getLatest(Request $request)
    {
        $limit = $request->get('limit', 10);
        
        $query = Notification::query();
        
        if (Auth::check()) {
            $query->where(function($q) {
                $q->whereNull('user_id')
                  ->orWhere('user_id', Auth::id());
            });
        } else {
            $query->whereNull('user_id');
        }
        
        $notifications = $query->latest()
            ->limit($limit)
            ->get();
        
        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $this->getUnreadCount()
        ]);
    }
}
