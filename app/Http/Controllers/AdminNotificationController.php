<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminNotificationController extends Controller
{
    /**
     * Display admin notifications page
     */
 // Update AdminNotificationController.php - index method
public function index(Request $request)
{
    try {
        $perPage = $request->get('per_page', 20); // Default 20 per page
        $perPage = in_array($perPage, [10, 20, 50, 100]) ? $perPage : 20; // Validate per_page

        $query = Notification::forUser(Auth::id())
                            ->orderBy('created_at', 'desc');

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by read status
        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->where('is_read', false);
            } elseif ($request->status === 'read') {
                $query->where('is_read', true);
            }
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $notifications = $query->paginate($perPage)->withQueryString();

        // Get statistics
        $stats = [
            'total' => Notification::forUser(Auth::id())->count(),
            'unread' => Notification::forUser(Auth::id())->unread()->count(),
            'today' => Notification::forUser(Auth::id())->whereDate('created_at', today())->count(),
            'this_week' => Notification::forUser(Auth::id())->where('created_at', '>=', now()->startOfWeek())->count(),
        ];

        $viewData = [
            'meta_title' => 'Notifications | Admin',
            'meta_desc' => 'Manage your admin notifications',
            'meta_image' => url('logo.png'),
            'notifications' => $notifications,
            'stats' => $stats,
            'filters' => $request->only(['type', 'status', 'search', 'per_page']),
        ];

        return view('admin.notifications.index', $viewData);

    } catch (\Exception $e) {
        Log::error('Failed to load admin notifications page', [
            'error' => $e->getMessage(),
            'admin_id' => Auth::id(),
        ]);

        return back()->with('error', 'Failed to load notifications page.');
    }
}

    /**
     * Get notifications for AJAX requests (header dropdown)
     */
    public function getNotifications(Request $request)
    {
        try {
            $limit = $request->get('limit', 10);

            $notifications = Notification::forUser(Auth::id())
                                        ->orderBy('created_at', 'desc')
                                        ->limit($limit)
                                        ->get();

            $unreadCount = Notification::getUnreadCountForUser(Auth::id());

            return response()->json([
                'success' => true,
                'notifications' => $notifications->map(function($notification) {
                    return [
                        'id' => $notification->id,
                        'title' => $notification->title,
                        'message' => $notification->message,
                        'type' => $notification->type,
                        'icon' => $notification->icon_class,
                        'badge_class' => $notification->badge_class,
                        'is_read' => $notification->is_read,
                        'time_ago' => $notification->time_ago,
                        'action_url' => $notification->data['action_url'] ?? null,
                        'created_at' => $notification->created_at->format('Y-m-d H:i:s'),
                    ];
                }),
                'unread_count' => $unreadCount,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get admin notifications', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load notifications'
            ], 500);
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, Notification $notification)
    {
        try {
            // Ensure the notification belongs to the current admin
            if ($notification->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $notification->markAsRead();

            Log::info('Admin notification marked as read', [
                'notification_id' => $notification->id,
                'admin_id' => Auth::id(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notification marked as read'
                ]);
            }

            return back()->with('success', 'Notification marked as read.');

        } catch (\Exception $e) {
            Log::error('Failed to mark admin notification as read', [
                'notification_id' => $notification->id,
              'error' => $e->getMessage(),
                'admin_id' => Auth::id(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to mark notification as read'
                ], 500);
            }

            return back()->with('error', 'Failed to mark notification as read.');
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        try {
            $count = Notification::markAllAsReadForUser(Auth::id());

            Log::info('All admin notifications marked as read', [
                'count' => $count,
                'admin_id' => Auth::id(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Marked {$count} notifications as read",
                    'count' => $count
                ]);
            }

            return back()->with('success', "Marked {$count} notifications as read.");

        } catch (\Exception $e) {
            Log::error('Failed to mark all admin notifications as read', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to mark all notifications as read'
                ], 500);
            }

            return back()->with('error', 'Failed to mark all notifications as read.');
        }
    }

    /**
     * Delete notification
     */
    public function delete(Request $request, Notification $notification)
    {
        try {
            // Ensure the notification belongs to the current admin
            if ($notification->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $notification->delete();

            Log::info('Admin notification deleted', [
                'notification_id' => $notification->id,
                'admin_id' => Auth::id(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notification deleted successfully'
                ]);
            }

            return back()->with('success', 'Notification deleted successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to delete admin notification', [
                'notification_id' => $notification->id,
                'error' => $e->getMessage(),
                'admin_id' => Auth::id(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete notification'
                ], 500);
            }

            return back()->with('error', 'Failed to delete notification.');
        }
    }

    /**
     * Bulk delete notifications
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'notification_ids' => 'required|array',
            'notification_ids.*' => 'exists:notifications,id',
        ]);

        try {
            $notifications = Notification::whereIn('id', $request->notification_ids)
                                       ->where('user_id', Auth::id())
                                       ->get();

            $count = $notifications->count();

            foreach ($notifications as $notification) {
                $notification->delete();
            }

            Log::info('Bulk admin notifications deleted', [
                'count' => $count,
                'notification_ids' => $request->notification_ids,
                'admin_id' => Auth::id(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Deleted {$count} notifications successfully",
                    'count' => $count
                ]);
            }

            return back()->with('success', "Deleted {$count} notifications successfully.");

        } catch (\Exception $e) {
            Log::error('Failed to bulk delete admin notifications', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete notifications'
                ], 500);
            }

            return back()->with('error', 'Failed to delete notifications.');
        }
    }

    /**
     * Clear all read notifications
     */
    public function clearRead(Request $request)
    {
        try {
            $count = Notification::where('user_id', Auth::id())
                                ->where('is_read', true)
                                ->delete();

            Log::info('All read admin notifications cleared', [
                'count' => $count,
                'admin_id' => Auth::id(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Cleared {$count} read notifications",
                    'count' => $count
                ]);
            }

            return back()->with('success', "Cleared {$count} read notifications.");

        } catch (\Exception $e) {
            Log::error('Failed to clear read admin notifications', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to clear read notifications'
                ], 500);
            }

            return back()->with('error', 'Failed to clear read notifications.');
        }
    }

    /**
     * Send notification to all admins
     */
    public function sendToAllAdmins(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'type' => 'required|in:info,success,warning,danger',
            'icon' => 'nullable|string|max:50',
        ]);

        try {
            $notifications = Notification::createForAllAdmins(
                $request->title,
                $request->message,
                $request->type,
                [
                    'icon' => $request->icon,
                    'data' => [
                        'sent_by' => Auth::user()->name,
                        'sent_at' => now()->format('Y-m-d H:i:s'),
                    ]
                ]
            );

            Log::info('Notification sent to all admins', [
                'title' => $request->title,
                'type' => $request->type,
                'sent_by' => Auth::id(),
                'recipients_count' => count($notifications),
            ]);

            return back()->with('success', 'Notification sent to all admins successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to send notification to all admins', [
                'error' => $e->getMessage(),
                'sent_by' => Auth::id(),
            ]);

            return back()->with('error', 'Failed to send notification.');
        }
    }

    /**
     * Get notification statistics
     */
    public function getStats(Request $request)
    {
        try {
            $userId = Auth::id();

            $stats = [
                'total' => Notification::forUser($userId)->count(),
                'unread' => Notification::forUser($userId)->unread()->count(),
                'today' => Notification::forUser($userId)->whereDate('created_at', today())->count(),
                'this_week' => Notification::forUser($userId)->where('created_at', '>=', now()->startOfWeek())->count(),
                'this_month' => Notification::forUser($userId)->where('created_at', '>=', now()->startOfMonth())->count(),
                'by_type' => [
                    'info' => Notification::forUser($userId)->where('type', 'info')->count(),
                    'success' => Notification::forUser($userId)->where('type', 'success')->count(),
                    'warning' => Notification::forUser($userId)->where('type', 'warning')->count(),
                    'danger' => Notification::forUser($userId)->where('type', 'danger')->count(),
                ],
                'recent' => Notification::getRecentForUser($userId, 5)->map(function($notification) {
                    return [
                        'id' => $notification->id,
                        'title' => $notification->title,
                        'type' => $notification->type,
                        'is_read' => $notification->is_read,
                        'time_ago' => $notification->time_ago,
                    ];
                }),
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get notification stats', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load statistics'
            ], 500);
        }
    }
}
