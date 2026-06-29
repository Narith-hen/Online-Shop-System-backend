<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

class CategoryController extends Controller
{
    #[OA\Get(
        path: '/api/categories',
        summary: 'List all categories with product counts',
        tags: ['Categories'],
        responses: [
            new OA\Response(response: 200, description: 'List of categories'),
        ]
    )]
    public function index()
    {
        $categories = Category::withCount('products')->get();
        return CategoryResource::collection($categories);
    }

    #[OA\Post(
        path: '/api/admin/categories',
        summary: 'Create a new category',
        security: [['sanctum' => []]],
        tags: ['Categories'],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'description', type: 'string'),
                new OA\Property(property: 'is_active', type: 'boolean'),
            ]
        )),
        responses: [
            new OA\Response(response: 201, description: 'Category created'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(Request $request)
    {
        // dd(2);
        // Direct basic validation as requested for cleaner structural evaluation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $category = Category::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully.',
            'data' => $category
        ], 201);
    }

    #[OA\Get(
        path: '/api/categories/{category}',
        summary: 'Get a category with its products',
        tags: ['Categories'],
        parameters: [
            new OA\Parameter(name: 'category', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'per_page', in: 'query', description: 'Items per page', schema: new OA\Schema(type: 'integer', default: 20)),
            new OA\Parameter(name: 'page', in: 'query', description: 'Page number', schema: new OA\Schema(type: 'integer', default: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Category with products'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function show(Request $request, string $id)
    {
        $perPage = min((int) $request->input('per_page', 20), 100);
        $category = Category::withCount('products')->findOrFail($id);
        $products = $category->products()->latest()->paginate($perPage);
        $category->setRelation('products', $products);
        return response()->json([
            'success' => true,
            'data'    => $category
        ], Response::HTTP_OK);
    }

    #[OA\Put(
        path: '/api/admin/categories/{category}',
        summary: 'Update a category',
        security: [['sanctum' => []]],
        tags: ['Categories'],
        parameters: [
            new OA\Parameter(name: 'category', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'description', type: 'string'),
                new OA\Property(property: 'is_active', type: 'boolean'),
            ]
        )),
        responses: [
            new OA\Response(response: 200, description: 'Category updated'),
            new OA\Response(response: 422, description: 'Validation error'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function update(Request $request, string $id)
    {
        $category = Category::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $category->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully.',
            'data' => $category
        ], Response::HTTP_OK);
    }

    #[OA\Delete(
        path: '/api/admin/categories/{category}',
        summary: 'Delete a category',
        security: [['sanctum' => []]],
        tags: ['Categories'],
        parameters: [
            new OA\Parameter(name: 'category', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Category deleted'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function destroy(string $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }
        $category->delete();
        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully.'
        ], Response::HTTP_OK);
    }
}