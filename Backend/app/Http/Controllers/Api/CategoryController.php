<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::withCount('products')->get();
        return response()->json([
            'success' => true,
            'data'    => $categories
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
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

    /**
     * Display the specified resource.
     */
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

    /**
     * Update the specified resource in storage.
     */
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

    /**
     * Remove the specified resource from storage.
     */
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