<?php

namespace App\Http\Controllers\Admin;

use App\Services\SocketService;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use OpenApi\Attributes as OA;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $totalOrders = 0;
        $pendingOrders = 0;
        $completedOrders = 0;
        $totalRevenue = 0;
        $orders = collect();

        if (Schema::hasTable('orders')) {
            $query = Order::with('user');

            // Search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%")
                      ->orWhere('total', 'like', "%{$search}%")
                      ->orWhere('status', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($q2) use ($search) {
                          $q2->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                      })
                      ->orWhereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') LIKE ?", ["%{$search}%"]);
                });
            }

            // Status filter
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $aggregates = Order::selectRaw('COUNT(*) as total_orders, SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_orders, SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_orders, SUM(total) as total_revenue')->first();
            $totalOrders = $aggregates->total_orders;
            $pendingOrders = $aggregates->pending_orders;
            $completedOrders = $aggregates->completed_orders;
            $totalRevenue = $aggregates->total_revenue ?? 0;
            $perPage = isset($_COOKIE['per_page']) ? min(25, max(5, (int) $_COOKIE['per_page'])) : 10;
            $orders = $query->latest()->paginate($perPage)->appends($request->except('per_page'));
        }

        return view('admin.orders.index', [
            'totalOrders' => $totalOrders,
            'pendingOrders' => $pendingOrders,
            'completedOrders' => $completedOrders,
            'totalRevenue' => $totalRevenue,
            'orders' => $orders,
        ]);
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product']);

        return view('admin.orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $order->load(['user', 'items.product']);

        return view('admin.orders.edit', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,processing,shipped,completed,cancelled',
        ]);

        if ($request->has('payment_status')) {
            $validated['payment_status'] = $request->validate([
                'payment_status' => 'required|string|in:unpaid,pending_verification,verified,failed',
            ])['payment_status'];
        }

        $oldStatus = $order->status;
        $order->update($validated);

        if ($oldStatus !== $order->status) {
            try {
                $notif = Notification::create([
                    'title'   => 'Order #' . $order->id . ' ' . ucfirst($order->status),
                    'message' => 'Your order status has been updated from "' . ucfirst($oldStatus) . '" to "' . ucfirst($order->status) . '".',
                    'type'    => 'news',
                    'link'    => '/orders/' . $order->id . '/receipt',
                ]);
                $notif->reads()->attach($order->user_id, ['read_at' => null]);

                SocketService::notification([
                    'id'         => $notif->id,
                    'title'      => $notif->title,
                    'message'    => $notif->message,
                    'type'       => $notif->type,
                    'link'       => $notif->link,
                    'created_at' => $notif->created_at->toIso8601String(),
                    'user_id'    => $order->user_id,
                ]);
            } catch (\Throwable $e) {
                // Don't block the update if notification fails
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Order status updated successfully.']);
        }

        return redirect()->route('admin.orders.index')->with('success', 'Order status updated successfully.');
    }

    public function verifyPayment(Request $request, Order $order)
    {
        $validated = $request->validate([
            'payment_status' => 'required|string|in:verified,failed',
        ]);

        $order->update($validated);

        try {
            $notif = Notification::create([
                'title'   => 'Payment ' . ucfirst($validated['payment_status']) . ' for Order #' . $order->id,
                'message' => $validated['payment_status'] === 'verified'
                    ? 'Your payment for order #' . $order->id . ' has been verified.'
                    : 'Your payment for order #' . $order->id . ' was rejected. Please contact support.',
                'type'    => 'news',
                'link'    => '/orders/' . $order->id . '/receipt',
            ]);
            $notif->reads()->attach($order->user_id, ['read_at' => null]);

            SocketService::notification([
                'id'         => $notif->id,
                'title'      => $notif->title,
                'message'    => $notif->message,
                'type'       => $notif->type,
                'link'       => $notif->link,
                'created_at' => $notif->created_at->toIso8601String(),
                'user_id'    => $order->user_id,
            ]);
        } catch (\Throwable $e) {
            // Don't block verification if notification fails
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Payment ' . $validated['payment_status'] . ' successfully.']);
        }

        return redirect()->route('admin.orders.show', $order->id)->with('success', 'Payment ' . $validated['payment_status'] . ' successfully.');
    }

    #[OA\Get(
        path: '/api/admin/orders/stats',
        summary: 'Get order statistics',
        security: [['sanctum' => []]],
        tags: ['Admin'],
        responses: [
            new OA\Response(response: 200, description: 'Order stats'),
        ]
    )]
    public function apiStats()
    {
        if (!Schema::hasTable('orders')) {
            return response()->json(['success' => true, 'data' => [
                'total_orders' => 0,
                'pending_orders' => 0,
                'completed_orders' => 0,
                'total_revenue' => 0,
            ]]);
        }

        $aggregates = Order::selectRaw('COUNT(*) as total_orders, SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_orders, SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_orders, SUM(total) as total_revenue')->first();

        return response()->json(['success' => true, 'data' => [
            'total_orders' => $aggregates->total_orders ?? 0,
            'pending_orders' => $aggregates->pending_orders ?? 0,
            'completed_orders' => $aggregates->completed_orders ?? 0,
            'total_revenue' => $aggregates->total_revenue ?? 0,
        ]]);
    }

    #[OA\Get(
        path: '/api/admin/orders/earnings',
        summary: 'Get earnings data for charts',
        security: [['sanctum' => []]],
        tags: ['Admin'],
        parameters: [
            new OA\Parameter(name: 'period', in: 'query', description: 'Period (daily, weekly, monthly)', schema: new OA\Schema(type: 'string', default: 'monthly')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Earnings data with labels and values'),
        ]
    )]
    public function apiEarnings()
    {
        if (!Schema::hasTable('orders')) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $period = request('period', 'monthly');
        $now = Carbon::now();

        switch ($period) {
            case 'weekly':
                $data = Order::where('status', '!=', 'cancelled')
                    ->where('created_at', '>=', $now->copy()->subWeeks(7)->startOfWeek())
                    ->selectRaw("YEARWEEK(created_at, 1) as period, SUM(total) as total")
                    ->groupBy('period')
                    ->orderBy('period')
                    ->get()
                    ->keyBy('period');

                $labels = [];
                $values = [];
                for ($i = 7; $i >= 0; $i--) {
                    $date = $now->copy()->subWeeks($i)->startOfWeek();
                    $key = $date->isoWeekYear() . str_pad($date->isoWeek(), 2, '0', STR_PAD_LEFT);
                    $labels[] = 'Week ' . $date->format('M d');
                    $values[] = (float) ($data[$key]->total ?? 0);
                }
                break;

            case 'daily':
                $data = Order::where('status', '!=', 'cancelled')
                    ->where('created_at', '>=', $now->copy()->subDays(13)->startOfDay())
                    ->selectRaw("DATE(created_at) as period, SUM(total) as total")
                    ->groupBy('period')
                    ->orderBy('period')
                    ->get()
                    ->keyBy('period');

                $labels = [];
                $values = [];
                for ($i = 13; $i >= 0; $i--) {
                    $date = $now->copy()->subDays($i);
                    $key = $date->format('Y-m-d');
                    $labels[] = $date->format('D, M d');
                    $values[] = (float) ($data[$key]->total ?? 0);
                }
                break;

            default: // monthly
                $data = Order::where('status', '!=', 'cancelled')
                    ->where('created_at', '>=', $now->copy()->subMonths(6)->startOfMonth())
                    ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as period, SUM(total) as total")
                    ->groupBy('period')
                    ->orderBy('period')
                    ->get()
                    ->keyBy('period');

                $labels = [];
                $values = [];
                for ($i = 6; $i >= 0; $i--) {
                    $date = $now->copy()->subMonths($i);
                    $key = $date->format('Y-m');
                    $labels[] = $date->format('M');
                    $values[] = (float) ($data[$key]->total ?? 0);
                }
                break;
        }

        return response()->json(['success' => true, 'data' => [
            'labels' => $labels,
            'values' => $values,
        ]]);
    }

    public function destroy(Order $order)
    {
        $order->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Order deleted successfully.']);
        }

        return redirect()->route('admin.orders.index')->with('success', 'Order deleted successfully.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No items selected.'], 400);
        }
        Order::whereIn('id', $ids)->delete();
        return response()->json(['success' => true, 'message' => count($ids) . ' order(s) deleted.']);
    }
}
