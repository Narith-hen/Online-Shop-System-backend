<?php

namespace App\Http\Controllers\Api;

use App\Helpers\SocketHelper;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class OrderController extends Controller
{
    /**
     * List orders for the authenticated user
     */
    public function index(Request $request)
    {
        $orders = Order::with(['user', 'items.product'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(15);

        return response()->json($orders);
    }

    /**
     * Show a single order for the authenticated user
     */
    public function show(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $order->load(['user', 'items.product']);

        return response()->json($order);
    }

    /**
     * Cancel an order (only if status is pending)
     */
    public function cancel(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($order->status !== 'pending') {
            return response()->json(['message' => 'Only pending orders can be cancelled.'], 422);
        }

        $order->update(['status' => 'cancelled']);

        try {
            $notif = Notification::create([
                'title'   => 'Order #' . $order->id . ' Cancelled',
                'message' => 'Your order of $' . number_format($order->total, 2) . ' has been cancelled.',
                'type'    => 'news',
                'link'    => '/orders/' . $order->id . '/receipt',
            ]);
            $notif->reads()->attach($request->user()->id, ['read_at' => null]);

            SocketHelper::notification([
                'id'         => $notif->id,
                'title'      => $notif->title,
                'message'    => $notif->message,
                'type'       => $notif->type,
                'link'       => $notif->link,
                'created_at' => $notif->created_at->toIso8601String(),
                'user_id'    => $request->user()->id,
            ]);
        } catch (\Throwable $e) {
            // Don't block cancellation if notification fails
        }

        return response()->json(['message' => 'Order cancelled successfully.', 'order' => $order->fresh(['user', 'items.product'])]);
    }
}
