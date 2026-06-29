<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereRaw("CASE WHEN is_active = 1 THEN 'active' ELSE 'inactive' END LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') LIKE ?", ["%{$search}%"]);
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $perPage = isset($_COOKIE['per_page']) ? min(25, max(5, (int) $_COOKIE['per_page'])) : 10;
        $categories = $query->latest()->paginate($perPage)->appends($request->except('per_page'));
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return view('admin.categories.create');
    }

    public function show(Category $category)
    {
        $products = $category->products()->latest()->paginate(15);

        return view('admin.categories.show', compact('category', 'products'));
    }

    public function store(StoreCategoryRequest $request)
    {
        $validated = $request->validated();
        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        } elseif ($request->filled('image_url')) {
            $validated['image'] = $request->image_url;
        } elseif ($request->boolean('remove_image')) {
            $validated['image'] = null;
        }

        $category = Category::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Category created successfully.']);
        }

        return redirect()->route('admin.categories.show', $category)
            ->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'category' => [
                    'id'          => $category->id,
                    'name'        => $category->name,
                    'description' => $category->description,
                    'is_active'   => $category->is_active,
                    'image_url'   => $category->image_url,
                ],
            ]);
        }

        return view('admin.categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $validated = $request->validated();
        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            if ($category->image && !str_starts_with($category->image, 'http') && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }
            $validated['image'] = $request->file('image')->store('categories', 'public');
        } elseif ($request->filled('image_url')) {
            if ($category->image && !str_starts_with($category->image, 'http') && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }
            $validated['image'] = $request->image_url;
        } elseif ($request->boolean('remove_image')) {
            if ($category->image && !str_starts_with($category->image, 'http') && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }
            $validated['image'] = null;
        }

        $category->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Category updated successfully.']);
        }

        return redirect()->route('admin.categories.show', $category)
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Request $request, Category $category)
    {
        if (!Hash::check($request->password, auth()->user()->password)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Incorrect password.'], 403);
            }
            return back()->with('error', 'Incorrect password.');
        }

        if ($category->image && Storage::disk('public')->exists($category->image)) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Category deleted successfully.']);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }

    public function bulkDestroy(Request $request)
    {
        if (!Hash::check($request->password, auth()->user()->password)) {
            return response()->json(['success' => false, 'message' => 'Incorrect password.'], 403);
        }

        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No items selected.'], 400);
        }
        $categories = Category::whereIn('id', $ids)->get();
        foreach ($categories as $category) {
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }
            $category->delete();
        }
        return response()->json(['success' => true, 'message' => count($categories) . ' category(ies) deleted.']);
    }
}
