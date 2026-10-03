<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()
            ->notifications()
            ->paginate(15);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(Notification $notification)
    {
        abort_if($notification->user_id !== auth()->id(), 403);

        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return redirect($notification->url ?? route('notifications.index'));
    }

    public function markAllAsRead()
    {
        auth()->user()->notifications()
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return back()->with('success', 'All notifications marked as read.');
    }
}