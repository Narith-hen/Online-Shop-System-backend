<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Get all reviews for a specific product.
     */
    public function index(Product $product)
    {
        $reviews = Review::with('user')
            ->where('product_id', $product->id)
            ->latest()
            ->paginate(15);

        $aggregates = Review::where('product_id', $product->id)
            ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as total_reviews')
            ->first();

        return response()->json([
            'average_rating' => round((float) ($aggregates->avg_rating ?? 0), 1),
            'total_reviews' => $aggregates->total_reviews ?? 0,
            'reviews' => $reviews,
        ]);
    }

    /**
     * Submit a review for a product (authenticated user, one review per product).
     */
    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $existing = Review::where('user_id', $request->user()->id)
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'You have already reviewed this product.'], 409);
        }

        $review = Review::create([
            'user_id' => $request->user()->id,
            'product_id' => $product->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return response()->json([
            'message' => 'Review submitted.',
            'review' => $review->load('user'),
        ], 201);
    }
}
