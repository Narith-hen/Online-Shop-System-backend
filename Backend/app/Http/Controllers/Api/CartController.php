<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class CartController extends Controller
{
    #[OA\Get(
        path: '/api/cart',
        summary: 'Get authenticated user cart',
        security: [['sanctum' => []]],
        tags: ['Cart'],
        responses: [
            new OA\Response(response: 200, description: 'Cart items with subtotal'),
        ]
    )]
    public function index(Request $request)
    {
        $cartItems = CartItem::with('product.category')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        $subtotal = $cartItems->sum(fn ($item) => ($item->product?->price ?? 0) * $item->quantity);

        return response()->json([
            'items' => $cartItems,
            'count' => $cartItems->sum('quantity'),
            'subtotal' => round($subtotal, 2),
        ]);
    }

    #[OA\Post(
        path: '/api/cart',
        summary: 'Add item to cart',
        security: [['sanctum' => []]],
        tags: ['Cart'],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'product_id', type: 'integer'),
                new OA\Property(property: 'quantity', type: 'integer'),
            ]
        )),
        responses: [
            new OA\Response(response: 200, description: 'Cart updated'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
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

    #[OA\Put(
        path: '/api/cart/{cartItem}',
        summary: 'Update cart item quantity',
        security: [['sanctum' => []]],
        tags: ['Cart'],
        parameters: [
            new OA\Parameter(name: 'cartItem', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'quantity', type: 'integer'),
            ]
        )),
        responses: [
            new OA\Response(response: 200, description: 'Cart updated'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function update(Request $request, CartItem $cartItem)
    {
        if ($cartItem->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $product = $cartItem->product;

        if (!$product || !$product->is_active) {
            return response()->json(['message' => 'This product is no longer available.'], 422);
        }

        if ($validated['quantity'] > $product->stock) {
            return response()->json(['message' => "Only {$product->stock} units available."], 422);
        }

        $cartItem->update(['quantity' => $validated['quantity']]);

        return $this->index($request);
    }

    #[OA\Delete(
        path: '/api/cart/{cartItem}',
        summary: 'Remove item from cart',
        security: [['sanctum' => []]],
        tags: ['Cart'],
        parameters: [
            new OA\Parameter(name: 'cartItem', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Cart updated'),
            new OA\Response(response: 403, description: 'Forbidden'),
        ]
    )]
    public function destroy(Request $request, CartItem $cartItem)
    {
        if ($cartItem->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $cartItem->delete();

        return $this->index($request);
    }

    #[OA\Post(
        path: '/api/cart/clear',
        summary: 'Clear all items from cart',
        security: [['sanctum' => []]],
        tags: ['Cart'],
        responses: [
            new OA\Response(response: 200, description: 'Cart cleared'),
        ]
    )]
    public function clear(Request $request)
    {
        CartItem::where('user_id', $request->user()->id)->delete();

        return response()->json(['message' => 'Cart cleared.', 'items' => [], 'count' => 0, 'subtotal' => 0]);
    }
}
