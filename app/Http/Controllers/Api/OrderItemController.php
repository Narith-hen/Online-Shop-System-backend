<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderItemController extends Controller
{
    public function cancel(Request $request, Order $order, OrderItem $item)
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($item->order_id !== $order->id) {
            return response()->json(['message' => 'Item does not belong to this order.'], 422);
        }

        if ($item->status !== 'pending') {
            return response()->json(['message' => 'Only pending items can be cancelled.'], 422);
        }

        DB::transaction(function () use ($order, $item) {
            $item->update(['status' => 'cancelled']);
            $item->product()->increment('stock', $item->quantity);

            $newTotal = $order->items()
                ->where('status', '!=', 'cancelled')
                ->where('status', '!=', 'returned')
                ->sum(DB::raw('quantity * price'));

            $tax = round($newTotal * 0.1, 2);
            $order->update(['total' => round($newTotal + $tax, 2)]);

            $remaining = $order->items()->where('status', 'pending')->count();
            if ($remaining === 0) {
                $order->update(['status' => 'cancelled']);
            }
        });

        $order->load(['user', 'items.product']);

        return response()->json([
            'message' => 'Item cancelled successfully.',
            'order'   => $order,
        ]);
    }

    public function returnItem(Request $request, Order $order, OrderItem $item)
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($item->order_id !== $order->id) {
            return response()->json(['message' => 'Item does not belong to this order.'], 422);
        }

        if (!in_array($item->status, ['delivered', 'completed'])) {
            return response()->json(['message' => 'Only delivered items can be returned.'], 422);
        }

        DB::transaction(function () use ($order, $item) {
            $item->update(['status' => 'returned']);
            $item->product()->increment('stock', $item->quantity);

            $newTotal = $order->items()
                ->where('status', '!=', 'cancelled')
                ->where('status', '!=', 'returned')
                ->sum(DB::raw('quantity * price'));

            $tax = round($newTotal * 0.1, 2);
            $order->update(['total' => round($newTotal + $tax, 2)]);
        });

        $order->load(['user', 'items.product']);

        return response()->json([
            'message' => 'Return requested successfully.',
            'order'   => $order,
        ]);
    }

    public function reorder(Request $request, Order $order, OrderItem $item)
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($item->order_id !== $order->id) {
            return response()->json(['message' => 'Item does not belong to this order.'], 422);
        }

        $product = $item->product;
        if (!$product || !$product->is_active) {
            return response()->json(['message' => 'This product is no longer available.'], 422);
        }

        $existing = CartItem::where('user_id', $request->user()->id)
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            $newQty = $existing->quantity + $item->quantity;
            if ($newQty > $product->stock) {
                return response()->json(['message' => "Only {$product->stock} units available."], 422);
            }
            $existing->update(['quantity' => $newQty]);
        } else {
            CartItem::create([
                'user_id'    => $request->user()->id,
                'product_id' => $product->id,
                'quantity'   => $item->quantity,
            ]);
        }

        return response()->json(['message' => 'Item added to cart.']);
    }
}
