<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\NotificationResource;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    use HttpResponses;
    public function index(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(15);

        return NotificationResource::collection($notifications);
    }

    public function unreadCount(Request $request)
    {
        $count = $request->user()->unreadNotifications()->count();

        return $this->success(['unread_count' => $count], null, 200);
    }

    public function markAsRead(Request $request, $notification)
    {
        $notification = $request->user()->notifications()->findOrFail($notification);
        $notification->markAsRead();

        return $this->success(null, 'Notification marked as read');
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return $this->success(null, 'All notifications marked as read');
    }

    public function destroy(Request $request, $notification)
    {
        $notification = $request->user()->notifications()->findOrFail($notification);
        $notification->delete();

        return $this->success(null, 'Notification deleted');
    }
}
