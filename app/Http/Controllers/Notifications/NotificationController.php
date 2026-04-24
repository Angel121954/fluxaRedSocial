<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Profile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $profile = Profile::where('user_id', Auth::id())->first();

        return view('notifications.index', [
            'profile' => $profile,
        ]);
    }

    public function list(Request $request): JsonResponse
    {
        $filter = $request->query('filter', 'all');

        $query = Notification::forUser(Auth::id())->orderBy('created_at', 'desc');

        if ($filter !== 'all') {
            $query->where('type', $filter);
        }

        $notifications = $query->limit(50)->get();

        return response()->json([
            'notifications' => $notifications->map(function ($n) {
                return [
                    'id' => $n->id,
                    'type' => $n->type,
                    'title' => $n->title,
                    'body' => $n->body,
                    'link' => $n->link,
                    'is_read' => $n->is_read,
                    'created_at' => $n->created_at->toIso8601String(),
                    'from_user' => $n->fromUser ? [
                        'id' => $n->fromUser->id,
                        'name' => $n->fromUser->name,
                        'username' => $n->fromUser->username,
                        'avatar_url' => $n->fromUser->avatar_url,
                    ] : null,
                ];
            }),
            'unread_count' => Notification::unreadCount(Auth::id()),
        ]);
    }

    public function unread(): JsonResponse
    {
        return response()->json([
            'count' => Notification::unreadCount(Auth::id()),
        ]);
    }

    public function markAsRead(Notification $notification): JsonResponse
    {
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $notification->markAsRead();

        return response()->json(['message' => 'Marked as read']);
    }

    public function markAllAsRead(): JsonResponse
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['message' => 'All marked as read']);
    }
}