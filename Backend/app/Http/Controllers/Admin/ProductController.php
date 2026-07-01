<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('price', 'like', "%{$search}%")
                  ->orWhere('stock', 'like', "%{$search}%")
                  ->orWhereHas('category', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereRaw("CASE WHEN is_active = 1 THEN 'active' ELSE 'inactive' END LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') LIKE ?", ["%{$search}%"]);
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Stock status filter (from card clicks)
        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'low') {
                $query->where('stock', '<=', 5)->where('is_active', true);
            }
        }

        $perPage = isset($_COOKIE['per_page']) ? min(25, max(5, (int) $_COOKIE['per_page'])) : 10;
        $products = $query->latest()->paginate($perPage)->onEachSide(1)->appends($request->except('per_page'));
        $categories = Category::all();

        // Stock metrics
        $lowStockCount = Product::where('is_active', true)->where('stock', '<=', 5)->count();
        $totalUnitsLeft = Product::where('is_active', true)->sum('stock');
        $totalUnitsSold = OrderItem::sum('quantity');

        $firstOrder = Order::orderBy('created_at')->first();
        if ($firstOrder) {
            $daysActive = max(1, Carbon::now()->diffInDays($firstOrder->created_at));
            $totalRevenue = Order::where('status', 'completed')->sum('total');
            $dailyEarnings = round($totalRevenue / $daysActive, 2);
        } else {
            $dailyEarnings = 0;
        }

        return view('admin.products.index', compact('products', 'categories', 'lowStockCount', 'totalUnitsLeft', 'totalUnitsSold', 'dailyEarnings'));
    }

    public function create()
    {
        $categories = Category::all();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['categories' => $categories]);
        }

        return view('admin.products.create', compact('categories'));
    }

    public function show(Product $product)
    {
        $product->load('category');

        return view('admin.products.show', compact('product'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_url' => 'nullable|string|max:2048',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        } elseif ($request->filled('image_url')) {
            $validated['image'] = $request->image_url;
        } elseif ($request->boolean('remove_image')) {
            $validated['image'] = null;
        }

        $product = Product::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Product created successfully.']);
        }

        return redirect()->route('admin.products.show', $product)->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'product' => [
                    'id'          => $product->id,
                    'name'        => $product->name,
                    'price'       => $product->price,
                    'stock'       => $product->stock,
                    'category_id' => $product->category_id,
                    'is_active'   => $product->is_active,
                    'image_url'   => $product->image_url,
                ],
                'categories' => $categories,
            ]);
        }

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_url' => 'nullable|string|max:2048',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            if ($product->image && !str_starts_with($product->image, 'http') && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        } elseif ($request->filled('image_url')) {
            if ($product->image && !str_starts_with($product->image, 'http') && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->image_url;
        } elseif ($request->boolean('remove_image')) {
            if ($product->image && !str_starts_with($product->image, 'http') && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = null;
        }

        $product->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Product updated successfully.']);
        }

        return redirect()->route('admin.products.show', $product)->with('success', 'Product updated successfully.');
    }

    public function destroy(Request $request, Product $product)
    {
        if (!Hash::check($request->password, auth()->user()->password)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Incorrect password.'], 403);
            }
            return back()->with('error', 'Incorrect password.');
        }

        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Product deleted successfully.']);
        }

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
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
        $products = Product::whereIn('id', $ids)->get();
        foreach ($products as $product) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $product->delete();
        }
        return response()->json(['success' => true, 'message' => count($products) . ' product(s) deleted.']);
    }
}
