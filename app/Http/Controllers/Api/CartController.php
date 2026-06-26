<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Get authenticated user's cart with product details.
     */
    public function index(Request $request)
    {
        $cartItems = CartItem::with('product.category')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        $subtotal = $cartItems->sum(fn ($item) => $item->product->price * $item->quantity);

        return response()->json([
            'items' => $cartItems,
            'count' => $cartItems->sum('quantity'),
            'subtotal' => round($subtotal, 2),
        ]);
    }

    /**
     * Add a product to the cart (or increment quantity).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if (! $product->is_active) {
            return response()->json(['message' => 'This product is not available.'], 422);
        }

        $cartItem = CartItem::where('user_id', $request->user()->id)
            ->where('product_id', $validated['product_id'])
            ->first();

        if ($cartItem) {
            $newQty = $cartItem->quantity + ($validated['quantity'] ?? 1);
            if ($newQty > $product->stock) {
                return response()->json(['message' => "Only {$product->stock} units available."], 422);
            }
            $cartItem->update(['quantity' => $newQty]);
        } else {
            CartItem::create([
                'user_id' => $request->user()->id,
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'] ?? 1,
            ]);
        }

        return $this->index($request);
    }

    /**
     * Update cart item quantity.
     */
    public function update(Request $request, CartItem $cartItem)
    {
        if ($cartItem->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $product = $cartItem->product;

        if ($validated['quantity'] > $product->stock) {
            return response()->json(['message' => "Only {$product->stock} units available."], 422);
        }

        $cartItem->update(['quantity' => $validated['quantity']]);

        return $this->index($request);
    }

    /**
     * Remove an item from the cart.
     */
    public function destroy(Request $request, CartItem $cartItem)
    {
        if ($cartItem->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $cartItem->delete();

        return $this->index($request);
    }

    /**
     * Clear all items from the authenticated user's cart.
     */
    public function clear(Request $request)
    {
        CartItem::where('user_id', $request->user()->id)->delete();

        return response()->json(['message' => 'Cart cleared.', 'items' => [], 'count' => 0, 'subtotal' => 0]);
    }
}
