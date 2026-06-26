<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SocketHelper;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

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
                      ->orWhereHas('user', function ($q2) use ($search) {
                          $q2->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                      });
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
            $orders = $query->latest()->paginate(10)->withQueryString();
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

                SocketHelper::notification([
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

    public function destroy(Order $order)
    {
        $order->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Order deleted successfully.']);
        }

        return redirect()->route('admin.orders.index')->with('success', 'Order deleted successfully.');
    }
}
