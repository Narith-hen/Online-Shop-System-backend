<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WishlistItem;
use App\Models\Product;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class WishlistController extends Controller
{
    #[OA\Get(
        path: '/api/wishlist',
        summary: 'Get authenticated user wishlist',
        security: [['sanctum' => []]],
        tags: ['Wishlist'],
        responses: [
            new OA\Response(response: 200, description: 'Wishlist items'),
        ]
    )]
    public function index(Request $request)
    {
        $wishlistItems = WishlistItem::with('product.category')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'items' => $wishlistItems,
            'count' => $wishlistItems->count(),
        ]);
    }

    #[OA\Post(
        path: '/api/wishlist',
        summary: 'Add product to wishlist',
        security: [['sanctum' => []]],
        tags: ['Wishlist'],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'product_id', type: 'integer'),
            ]
        )),
        responses: [
            new OA\Response(response: 201, description: 'Added to wishlist'),
            new OA\Response(response: 409, description: 'Already in wishlist'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $exists = WishlistItem::where('user_id', $request->user()->id)
            ->where('product_id', $validated['product_id'])
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Product is already in your wishlist.'], 409);
        }

        WishlistItem::create([
            'user_id' => $request->user()->id,
            'product_id' => $validated['product_id'],
        ]);

        return response()->json(['message' => 'Added to wishlist.'], 201);
    }

    #[OA\Delete(
        path: '/api/wishlist/{wishlistItem}',
        summary: 'Remove product from wishlist',
        security: [['sanctum' => []]],
        tags: ['Wishlist'],
        parameters: [
            new OA\Parameter(name: 'wishlistItem', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Removed from wishlist'),
            new OA\Response(response: 403, description: 'Forbidden'),
        ]
    )]
    public function destroy(Request $request, WishlistItem $wishlistItem)
    {
        if ($wishlistItem->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $wishlistItem->delete();

        return response()->json(['message' => 'Removed from wishlist.']);
    }

    #[OA\Post(
        path: '/api/wishlist/toggle',
        summary: 'Toggle product in wishlist (add or remove)',
        security: [['sanctum' => []]],
        tags: ['Wishlist'],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'product_id', type: 'integer'),
            ]
        )),
        responses: [
            new OA\Response(response: 200, description: 'Toggled'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function toggle(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $item = WishlistItem::where('user_id', $request->user()->id)
            ->where('product_id', $validated['product_id'])
            ->first();

        if ($item) {
            $item->delete();
            return response()->json(['message' => 'Removed from wishlist.', 'in_wishlist' => false]);
        }

        WishlistItem::create([
            'user_id' => $request->user()->id,
            'product_id' => $validated['product_id'],
        ]);

        return response()->json(['message' => 'Added to wishlist.', 'in_wishlist' => true], 201);
    }
}
