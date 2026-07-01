<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use OpenApi\Attributes as OA;

class StatsController extends Controller
{
    #[OA\Get(
        path: '/api/stats',
        summary: 'Get public storefront stats (product/customer counts, average rating)',
        tags: ['Stats'],
        responses: [
            new OA\Response(response: 200, description: 'Storefront stats'),
        ]
    )]
    public function index()
    {
        $products = Product::where('is_active', true)->count();
        $customers = User::whereHas('role', fn ($q) => $q->where('name', 'customer'))->count();
        $rating = round((float) (Review::avg('rating') ?? 0), 1);
        $ordersDelivered = Order::where('status', 'completed')->count();

        return response()->json([
            'products' => $products,
            'customers' => $customers,
            'rating' => $rating,
            'reviews_count' => Review::count(),
            'orders_delivered' => $ordersDelivered,
        ]);
    }
}
