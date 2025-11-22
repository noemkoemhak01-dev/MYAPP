<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => NotificationResource::collection($notifications),
        ]);
    }

    public function unreadCount(Request $request)
    {
        $count = $request->user()->notifications()->unread()->count();

        return response()->json([
            'success' => true,
            'unread_count' => $count,
        ]);
    }

    public function markAsRead(Request $request, int $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->notifications()->unread()->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
        ]);
    }

    public function destroy(Request $request, int $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification removed',
        ]);
    }
}
