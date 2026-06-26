<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WishlistItem;
use App\Models\Product;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * Get authenticated user's wishlist with product details.
     */
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

    /**
     * Add a product to the wishlist.
     */
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

    /**
     * Remove a product from the wishlist.
     */
    public function destroy(Request $request, WishlistItem $wishlistItem)
    {
        if ($wishlistItem->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $wishlistItem->delete();

        return response()->json(['message' => 'Removed from wishlist.']);
    }

    /**
     * Toggle a product in the wishlist (add or remove).
     */
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
