<?php

namespace App\Http\Controllers\Api;

use App\Services\SocketService;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use OpenApi\Attributes as OA;

class OrderController extends Controller
{
    #[OA\Get(
        path: '/api/orders',
        summary: 'List orders for authenticated user',
        security: [['sanctum' => []]],
        tags: ['Orders'],
        responses: [
            new OA\Response(response: 200, description: 'Paginated list of orders'),
        ]
    )]
    public function index(Request $request)
    {
        $orders = Order::with(['user', 'items.product'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(15);

        return response()->json($orders);
    }

    #[OA\Get(
        path: '/api/orders/{order}',
        summary: 'Get order details',
        security: [['sanctum' => []]],
        tags: ['Orders'],
        parameters: [
            new OA\Parameter(name: 'order', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Order details'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function show(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $order->load(['user', 'items.product']);

        return response()->json($order);
    }

    #[OA\Post(
        path: '/api/orders/{order}/cancel',
        summary: 'Cancel a pending order',
        security: [['sanctum' => []]],
        tags: ['Orders'],
        parameters: [
            new OA\Parameter(name: 'order', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Order cancelled'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 422, description: 'Only pending orders can be cancelled'),
        ]
    )]
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

            SocketService::notification([
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
