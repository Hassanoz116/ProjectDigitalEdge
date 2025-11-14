<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications for authenticated user
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Get unread count
        $unreadCount = $user->unreadNotifications->count();
        
        // Get notifications with pagination
        $perPage = $request->get('per_page', 15);
        
        if ($request->get('unread_only', false)) {
            $notifications = $user->unreadNotifications()->paginate($perPage);
        } else {
            $notifications = $user->notifications()->paginate($perPage);
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'unread_count' => $unreadCount,
                'notifications' => $notifications,
            ]
        ], 200);
    }
    
    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, string $id)
    {
        $user = $request->user();
        
        $notification = $user->notifications()->where('id', $id)->first();
        
        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found'
            ], 404);
        }
        
        $notification->markAsRead();
        
        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ], 200);
    }
    
    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        $user = $request->user();
        $user->unreadNotifications->markAsRead();
        
        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ], 200);
    }
}
