<?php

namespace App\Http\Controllers\Api;

use App\Helpers\SocketHelper;
use App\Http\Controllers\Controller;
use App\Mail\OrderReceipt;
use App\Models\CartItem;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    /**
     * Place an order from the authenticated user's cart.
     */
    public function checkout(Request $request)
    {
        $cartItems = CartItem::with('product')
            ->where('user_id', $request->user()->id)
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Your cart is empty.'], 422);
        }

        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|string|in:aba,acleda',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Please select a payment method.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $errors = [];
        foreach ($cartItems as $item) {
            if (! $item->product || ! $item->product->is_active) {
                $errors[] = "'{$item->product->name}' is no longer available.";
            } elseif ($item->quantity > $item->product->stock) {
                $errors[] = "'{$item->product->name}' only has {$item->product->stock} units available.";
            }
        }

        if (! empty($errors)) {
            return response()->json(['message' => 'Some items cannot be ordered.', 'errors' => $errors], 422);
        }

        $order = DB::transaction(function () use ($cartItems, $request) {
            $subtotal = $cartItems->sum(fn ($item) => $item->product->price * $item->quantity);
            $tax = round($subtotal * 0.1, 2);
            $total = round($subtotal + $tax, 2);

            $order = Order::create([
                'user_id'        => $request->user()->id,
                'total'          => $total,
                'status'         => 'pending',
                'payment_method' => $request->payment_method,
                'payment_status' => 'unpaid',
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item->product_id,
                    'quantity'   => $item->quantity,
                    'price'      => $item->product->price,
                    'status'     => 'pending',
                ]);

                $item->product->decrement('stock', $item->quantity);
            }

            CartItem::where('user_id', $request->user()->id)->delete();

            return $order;
        });

        $order->load(['user', 'items.product']);

        try {
            Mail::to($order->user->email)->send(new OrderReceipt($order));
        } catch (\Throwable $e) {
            // Log email failure but don't block the order
        }

        try {
            $notif = Notification::create([
                'title'   => 'Order #' . $order->id . ' Placed',
                'message' => 'Your order of $' . number_format($order->total, 2) . ' has been placed successfully.',
                'type'    => 'news',
                'link'    => '/orders/' . $order->id . '/receipt',
            ]);
            $notif->reads()->attach($request->user()->id, ['read_at' => null]);

            $adminIds = User::whereHas('role', fn ($q) => $q->where('name', 'admin'))
                ->pluck('id');
            $notif->reads()->attach($adminIds->toArray(), ['read_at' => null]);

            SocketHelper::notification([
                'id'         => $notif->id,
                'title'      => $notif->title,
                'message'    => $notif->message,
                'type'       => $notif->type,
                'link'       => $notif->link,
                'created_at' => $notif->created_at->toIso8601String(),
                'user_id'    => $request->user()->id,
            ]);

            SocketHelper::push('admin-notification', [
                'id'         => $notif->id,
                'title'      => 'New Order #' . $order->id,
                'message'    => 'Customer ' . $order->user->name . ' placed an order of $' . number_format($order->total, 2),
                'type'       => 'order',
                'link'       => '/admin/orders/' . $order->id . '/edit',
                'created_at' => $notif->created_at->toIso8601String(),
                'order_id'   => $order->id,
                'customer'   => $order->user->name,
                'total'      => $order->total,
            ]);

            SocketHelper::cartUpdate();
        } catch (\Throwable $e) {
            // Don't block order if notification fails
        }

        return response()->json([
            'message' => 'Order placed successfully.',
            'order' => $order,
        ], 201);
    }

    /**
     * Upload payment proof for an order.
     */
    public function uploadProof(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($order->payment_status !== 'unpaid') {
            return response()->json(['message' => 'Payment already submitted.'], 422);
        }

        $validator = Validator::make($request->all(), [
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Please upload a valid payment screenshot (jpg/png, max 5MB).',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $path = $request->file('payment_proof')->store('payments', 'public');

        $order->update([
            'payment_proof' => $path,
            'payment_status' => 'pending_verification',
        ]);

        $order->load(['user', 'items.product']);

        return response()->json([
            'message' => 'Payment proof uploaded. Awaiting verification.',
            'order'   => $order,
        ]);
    }
}
