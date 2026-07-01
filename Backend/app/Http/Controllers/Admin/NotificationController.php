<?php

namespace App\Http\Controllers\Admin;

use App\Services\SocketService;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = Notification::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') LIKE ?", ["%{$search}%"]);
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $perPage = isset($_COOKIE['per_page']) ? min(25, max(5, (int) $_COOKIE['per_page'])) : 10;
        $notifications = $query->latest()->paginate($perPage)->onEachSide(1)->appends($request->except('per_page'));

        $totalNotifications = Notification::count();
        $totalReads = DB::table('notification_reads')->count();
        $uniqueRecipients = DB::table('notification_reads')->distinct('user_id')->count('user_id');
        $totalUnread = DB::table('notification_reads')->whereNull('read_at')->count();

        return view('admin.notifications.index', compact(
            'notifications', 'totalNotifications', 'totalReads', 'uniqueRecipients', 'totalUnread'
        ));
    }

    public function create()
    {
        return view('admin.notifications.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'message' => 'nullable|string',
            'type'    => 'required|string|in:news,alert,update',
            'link'    => 'nullable|string|max:500',
        ]);

        $notification = Notification::create($validated);

        $notifyAll = $request->boolean('notify_all', true);

        if ($notifyAll) {
            $users = User::where('notifications_enabled', true)->get();
            $now = now();
            $rows = $users->map(fn ($user) => [
                'notification_id' => $notification->id,
                'user_id'         => $user->id,
                'read_at'         => null,
                'created_at'      => $now,
                'updated_at'      => $now,
            ]);
            DB::table('notification_reads')->insertOrIgnore($rows->toArray());

            try {
                SocketService::notification([
                    'id'         => $notification->id,
                    'title'      => $notification->title,
                    'message'    => $notification->message,
                    'type'       => $notification->type,
                    'link'       => $notification->link,
                    'created_at' => $notification->created_at->toIso8601String(),
                ]);
            } catch (\Throwable $e) {
                // Don't block if socket fails
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Notification created successfully.']);
        }

        return redirect()->route('admin.notifications.index')->with('success', 'Notification created successfully.');
    }

    public function show(Notification $notification)
    {
        $reads = DB::table('notification_reads')
            ->where('notification_id', $notification->id)
            ->join('users', 'notification_reads.user_id', '=', 'users.id')
            ->select('users.name', 'users.email', 'notification_reads.read_at')
            ->get();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'notification' => $notification,
                'reads' => $reads,
            ]);
        }

        return view('admin.notifications.show', compact('notification', 'reads'));
    }

    public function destroy(Notification $notification)
    {
        DB::table('notification_reads')->where('notification_id', $notification->id)->delete();
        $notification->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Notification deleted successfully.']);
        }

        return redirect()->route('admin.notifications.index')->with('success', 'Notification deleted successfully.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No items selected.'], 400);
        }
        DB::table('notification_reads')->whereIn('notification_id', $ids)->delete();
        Notification::whereIn('id', $ids)->delete();
        return response()->json(['success' => true, 'message' => count($ids) . ' notification(s) deleted.']);
    }
}
