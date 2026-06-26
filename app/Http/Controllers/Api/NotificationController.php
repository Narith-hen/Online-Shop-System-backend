<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = Notification::whereHas('reads', function ($q) use ($request) {
            $q->where('user_id', $request->user()->id);
        })->latest()->take(20)->get();

        $reads = DB::table('notification_reads')
            ->where('user_id', $request->user()->id)
            ->whereIn('notification_id', $notifications->pluck('id'))
            ->pluck('read_at', 'notification_id');

        $data = $notifications->map(function ($n) use ($reads) {
            return [
                'id'         => $n->id,
                'title'      => $n->title,
                'message'    => $n->message,
                'type'       => $n->type,
                'link'       => $n->link,
                'created_at' => $n->created_at,
                'read'       => $reads->get($n->id) !== null,
            ];
        });

        $unreadCount = $data->where('read', false)->count();

        return response()->json([
            'data'         => $data,
            'unread_count' => $unreadCount,
        ]);
    }

    public function markRead(Request $request, Notification $notification)
    {
        DB::table('notification_reads')->updateOrInsert(
            [
                'notification_id' => $notification->id,
                'user_id'         => $request->user()->id,
            ],
            ['read_at' => now()]
        );

        return response()->json(['message' => 'Marked as read.']);
    }

    public function markAllRead(Request $request)
    {
        $ids = Notification::whereDoesntHave('reads', function ($q) use ($request) {
            $q->where('user_id', $request->user()->id);
        })->pluck('id');

        $now = now();
        $rows = $ids->map(fn ($id) => [
            'notification_id' => $id,
            'user_id'         => $request->user()->id,
            'read_at'         => $now,
            'created_at'      => $now,
            'updated_at'      => $now,
        ]);

        DB::table('notification_reads')->insertOrIgnore($rows->toArray());

        return response()->json(['message' => 'All marked as read.']);
    }

    public function toggleSubscription(Request $request)
    {
        $user = $request->user();
        $user->update(['notifications_enabled' => !$user->notifications_enabled]);

        return response()->json([
            'message' => $user->notifications_enabled ? 'Notifications enabled.' : 'Notifications disabled.',
            'enabled' => $user->notifications_enabled,
        ]);
    }
}
