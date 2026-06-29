@extends('MainLayout')

@section('title', $category->name)
@section('page_title', $category->name)
@section('page_subtitle', 'Category details and products')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-blue-600 transition">
            <i class="fas fa-arrow-left"></i> Back to Categories
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.categories.edit', $category->id) }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                <i class="fas fa-edit"></i> Edit Category
            </a>
            <button onclick="deleteCategory({{ $category->id }})" class="inline-flex items-center gap-2 bg-white border border-red-200 text-red-600 hover:bg-red-50 px-4 py-2 rounded-lg text-sm font-medium transition">
                <i class="fas fa-trash"></i> Delete
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="card p-5">
            <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-tag text-blue-500"></i> Category Summary
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500 text-sm">Name</span>
                    <span class="font-semibold text-gray-900 text-sm">{{ $category->name }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500 text-sm">Status</span>
                    @if($category->is_active)
                        <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-full text-xs font-medium border border-emerald-200">Active</span>
                    @else
                        <span class="px-2.5 py-1 bg-red-50 text-red-600 rounded-full text-xs font-medium border border-red-200">Inactive</span>
                    @endif
                </div>
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500 text-sm">Products</span>
                    <span class="font-bold text-lg text-blue-600">{{ $category->products_count }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-500 text-sm">Created</span>
                    <span class="font-semibold text-gray-900 text-sm">{{ $category->created_at->format('M d, Y') }}</span>
                </div>
            </div>
        </div>

        <div class="card p-5">
            <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-align-left text-emerald-500"></i> Description
            </h3>
            <p class="text-sm text-gray-600 leading-relaxed">{{ $category->description ?? 'No description provided.' }}</p>
        </div>

        <div class="card p-5">
            <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-image text-purple-500"></i> Image
            </h3>
            @if($category->image_url)
                <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="w-full h-40 object-cover rounded-xl border border-gray-200">
            @else
                <div class="w-full h-40 bg-gray-50 rounded-xl flex items-center justify-center border border-dashed border-gray-300">
                    <div class="text-center text-gray-400">
                        <i class="fas fa-tag text-4xl mb-2"></i>
                        <p class="text-sm">No image</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-boxes text-indigo-500"></i> Products in this Category
            </h3>
            <a href="{{ route('admin.products.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                <i class="fas fa-plus"></i> Add Product
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left py-3 px-5 font-semibold text-gray-600 text-sm">Product</th>
                        <th class="text-left py-3 px-5 font-semibold text-gray-600 text-sm">Price</th>
                        <th class="text-left py-3 px-5 font-semibold text-gray-600 text-sm">Stock</th>
                        <th class="text-left py-3 px-5 font-semibold text-gray-600 text-sm">Status</th>
                        <th class="text-right py-3 px-5 font-semibold text-gray-600 text-sm">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="py-3 px-5">
                                <div class="flex items-center gap-3">
                                    @if($product->image_url)
                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-9 h-9 object-cover rounded-lg border border-gray-200">
                                    @else
                                        <div class="w-9 h-9 bg-gray-100 rounded-lg flex items-center justify-center border border-gray-200">
                                            <i class="fas fa-box text-gray-400"></i>
                                        </div>
                                    @endif
                                    <span class="text-sm font-medium text-gray-900">{{ $product->name }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-5 text-sm text-gray-600">${{ number_format($product->price, 2) }}</td>
                            <td class="py-3 px-5 text-sm text-gray-600">{{ $product->stock }}</td>
                            <td class="py-3 px-5">
                                @if($product->is_active)
                                    <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-full text-xs font-medium border border-emerald-200">Active</span>
                                @else
                                    <span class="px-2.5 py-1 bg-red-50 text-red-600 rounded-full text-xs font-medium border border-red-200">Inactive</span>
                                @endif
                            </td>
                            <td class="py-3 px-5 text-right">
                                <a href="{{ route('admin.products.edit', $product->id) }}" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center">
                                <div class="text-gray-300 mb-2"><i class="fas fa-box-open text-4xl"></i></div>
                                <p class="text-gray-500 text-sm">No products in this category yet</p>
                                <a href="{{ route('admin.products.create') }}" class="mt-2 inline-block text-blue-600 hover:text-blue-700 text-sm font-medium">Add your first product</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function deleteCategory(id) {
        showDeleteModal('Category', id, '/admin/categories/' + id, { redirectUrl: '{{ route("admin.categories.index") }}' });
    }
</script>
@endpush
