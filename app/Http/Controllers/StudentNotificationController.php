<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentNotificationController extends Controller
{
    /**
     * Get notifications for the authenticated student
     */
    public function getNotifications()
    {
        $user = Auth::user();

        $notifications = Notification::forUser($user->id)
                                   ->recent()
                                   ->orderBy('created_at', 'desc')
                                   ->limit(10)
                                   ->get();

        $unreadCount = Notification::forUser($user->id)->unread()->count();

        return response()->json([
            'notifications' => $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'type' => $notification->type,
                    'icon' => $notification->icon_class,
                    'badge_class' => $notification->badge_class,
                    'is_read' => $notification->is_read,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'action_url' => $notification->data['action_url'] ?? null,
                ];
            }),
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Notification $notification)
    {
        $user = Auth::user();

        if ($notification->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $user = Auth::user();

        Notification::forUser($user->id)
                   ->unread()
                   ->update([
                       'is_read' => true,
                       'read_at' => now(),
                   ]);

        return response()->json(['success' => true]);
    }

    /**
     * Show all notifications page
     */
    public function showAll()
    {
        $user = Auth::user();

        $notifications = Notification::forUser($user->id)
                                   ->orderBy('created_at', 'desc')
                                   ->paginate(20);

        $viewData = [
            'meta_title' => 'Notifications | Lexa University',
            'meta_desc' => 'View all your notifications',
            'meta_image' => url('logo.png'),
            'notifications' => $notifications,
        ];

        return view('student.notifications.index', $viewData);
    }
}
