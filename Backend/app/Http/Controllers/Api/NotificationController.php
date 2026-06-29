<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

class NotificationController extends Controller
{
    #[OA\Get(
        path: '/api/notifications',
        summary: 'List notifications for authenticated user',
        security: [['sanctum' => []]],
        tags: ['Notifications'],
        responses: [
            new OA\Response(response: 200, description: 'List of notifications with unread count'),
        ]
    )]
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

    #[OA\Post(
        path: '/api/notifications/{notification}/read',
        summary: 'Mark a notification as read',
        security: [['sanctum' => []]],
        tags: ['Notifications'],
        parameters: [
            new OA\Parameter(name: 'notification', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Marked as read'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
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

    #[OA\Post(
        path: '/api/notifications/read-all',
        summary: 'Mark all notifications as read',
        security: [['sanctum' => []]],
        tags: ['Notifications'],
        responses: [
            new OA\Response(response: 200, description: 'All marked as read'),
        ]
    )]
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

    #[OA\Post(
        path: '/api/notifications/toggle',
        summary: 'Toggle notification subscription',
        security: [['sanctum' => []]],
        tags: ['Notifications'],
        responses: [
            new OA\Response(response: 200, description: 'Subscription toggled'),
        ]
    )]
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
