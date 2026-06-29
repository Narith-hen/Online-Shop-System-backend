<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    #[OA\Get(
        path: '/api/products',
        summary: 'List all active products',
        description: 'Public. Supports search, category filter, stock filter, and pagination.',
        tags: ['Products'],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', description: 'Search by name or description', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'category_id', in: 'query', description: 'Filter by category ID', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'in_stock', in: 'query', description: 'Filter only in-stock products (1 or 0)', schema: new OA\Schema(type: 'integer', enum: [0, 1])),
            new OA\Parameter(name: 'per_page', in: 'query', description: 'Items per page (max 100)', schema: new OA\Schema(type: 'integer', default: 20)),
            new OA\Parameter(name: 'page', in: 'query', description: 'Page number', schema: new OA\Schema(type: 'integer', default: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Paginated list of products'),
        ]
    )]
    public function index(Request $request)
    {
        $query = Product::with('category')->where('is_active', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->boolean('in_stock')) {
            $query->where('stock', '>', 0);
        }

        $perPage = min((int) $request->input('per_page', 20), 100);
        $products = $query->latest()->paginate($perPage);
        return ProductResource::collection($products)->response();
    }

    #[OA\Post(
        path: '/api/admin/products',
        summary: 'Create a new product',
        security: [['sanctum' => []]],
        tags: ['Products'],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'price', type: 'number', format: 'float'),
                new OA\Property(property: 'stock', type: 'integer'),
                new OA\Property(property: 'category_id', type: 'integer'),
                new OA\Property(property: 'is_active', type: 'boolean'),
                new OA\Property(property: 'description', type: 'string'),
            ]
        )),
        responses: [
            new OA\Response(response: 201, description: 'Product created'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(Request $request)
    {
        // dd($request->all());
        // Requirement C: Explicit Input Validation Matrix via Validator::make()
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'is_active'   => 'boolean',
            'category_id' => 'required|exists:categories,id',
        ]);
        // Graceful error pipeline triggering a 422 standard response structure
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation parameters failed.',
                'errors'  => $validator->errors()
            ], 422);
        }
        $validated = $validator->validated();
        $validated['is_active'] = $request->boolean('is_active', true);
        // File Upload Pipeline
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $validated['image'] = $path;
        }
        $product = Product::create($validated);
        $product->load('category');
        return (new ProductResource($product))->response()->setStatusCode(201);
    }

    #[OA\Get(
        path: '/api/products/{product}',
        summary: 'Get product details',
        tags: ['Products'],
        parameters: [
            new OA\Parameter(name: 'product', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Product details'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function show(Product $product)
    {
        $product->load('category');
        return new ProductResource($product);
    }

    #[OA\Put(
        path: '/api/admin/products/{product}',
        summary: 'Update a product',
        security: [['sanctum' => []]],
        tags: ['Products'],
        parameters: [
            new OA\Parameter(name: 'product', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'price', type: 'number', format: 'float'),
                new OA\Property(property: 'stock', type: 'integer'),
                new OA\Property(property: 'category_id', type: 'integer'),
                new OA\Property(property: 'is_active', type: 'boolean'),
                new OA\Property(property: 'description', type: 'string'),
            ]
        )),
        responses: [
            new OA\Response(response: 200, description: 'Product updated'),
            new OA\Response(response: 422, description: 'Validation error'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function update(Request $request, Product $product)
    {
        $rules = [
            'name'        => 'sometimes|required|string|max:255',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'price'       => 'sometimes|required|numeric|min:0',
            'stock'       => 'sometimes|required|integer|min:0',
            'is_active'   => 'boolean',
            'category_id' => 'sometimes|required|exists:categories,id',
        ];

        if (!$request->hasFile('image')) {
            $rules['image'] = 'nullable';
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation parameters failed.',
                'errors'  => $validator->errors()
            ], 422);
        }
        $validated = $validator->validated();

        if ($request->has('is_active')) {
            $validated['is_active'] = $request->boolean('is_active');
        }

        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $path = $request->file('image')->store('products', 'public');
            $validated['image'] = $path;
        }

        $product->update($validated);
        $product->load('category');
        return new ProductResource($product);
    }

    #[OA\Delete(
        path: '/api/admin/products/{product}',
        summary: 'Delete a product',
        security: [['sanctum' => []]],
        tags: ['Products'],
        parameters: [
            new OA\Parameter(name: 'product', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Product deleted'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function destroy(Product $product)
    {
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();
        return response()->json([
            'success' => true,
            'message' => 'Product and linked assets successfully removed.'
        ], 200);
    }

    #[OA\Get(
        path: '/api/admin/products/stats',
        summary: 'Get product statistics by category',
        security: [['sanctum' => []]],
        tags: ['Products'],
        responses: [
            new OA\Response(response: 200, description: 'Product stats'),
        ]
    )]
    public function apiStats()
    {
        $stats = Product::join('categories', 'products.category_id', '=', 'categories.id')
            ->selectRaw('categories.name as category, COUNT(*) as count')
            ->groupBy('categories.name')
            ->get();

        return response()->json(['success' => true, 'data' => $stats]);
    }
}