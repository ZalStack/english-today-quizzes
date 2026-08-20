<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        $unreadCount = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        if (Auth::user()->isHR()) {
            return view('hr.notifications.index', compact('notifications', 'unreadCount'));
        }

        return view('employee.notifications.index', compact('notifications', 'unreadCount'));
    }

    public function getUnreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    public function getLatest()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'is_read' => $notification->is_read,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'icon' => $notification->getTypeIcon(),
                    'color' => $notification->getTypeColor(),
                ];
            });

        $totalUnread = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        $totalNotifications = Notification::where('user_id', Auth::id())->count();

        return response()->json([
            'notifications' => $notifications,
            'total_unread' => $totalUnread,
            'total_notifications' => $totalNotifications,
            'has_more' => $totalNotifications > 5,
        ]);
    }

    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['success' => true]);
    }
}
