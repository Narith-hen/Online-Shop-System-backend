<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ReviewController extends Controller
{
    #[OA\Get(
        path: '/api/products/{product}/reviews',
        summary: 'Get all reviews for a product',
        tags: ['Reviews'],
        parameters: [
            new OA\Parameter(name: 'product', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Product reviews with average rating'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
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

    #[OA\Post(
        path: '/api/products/{product}/reviews',
        summary: 'Submit a review for a product',
        security: [['sanctum' => []]],
        tags: ['Reviews'],
        parameters: [
            new OA\Parameter(name: 'product', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'rating', type: 'integer', description: 'Rating 1-5'),
                new OA\Property(property: 'comment', type: 'string'),
            ]
        )),
        responses: [
            new OA\Response(response: 201, description: 'Review submitted'),
            new OA\Response(response: 409, description: 'Already reviewed'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
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
