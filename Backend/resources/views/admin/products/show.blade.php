@extends('MainLayout')

@section('title', $product->name)
@section('page_title', $product->name)
@section('page_subtitle', 'Product details')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <a href="{{ route('admin.products.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-blue-600 transition">
            <i class="fas fa-arrow-left"></i> Back to Products
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.products.edit', $product->id) }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                <i class="fas fa-edit"></i> Edit Product
            </a>
            <button onclick="deleteProduct({{ $product->id }})" class="inline-flex items-center gap-2 bg-white border border-red-200 text-red-600 hover:bg-red-50 px-4 py-2 rounded-lg text-sm font-medium transition">
                <i class="fas fa-trash"></i> Delete
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Product Summary -->
        <div class="card p-5">
            <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-info-circle text-blue-500"></i> Product Summary
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500 text-sm">Name</span>
                    <span class="font-semibold text-gray-900 text-sm">{{ $product->name }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500 text-sm">Category</span>
                    <span class="font-semibold text-gray-900 text-sm">{{ $product->category->name ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500 text-sm">Price</span>
                    <span class="font-bold text-blue-600">${{ number_format($product->price, 2) }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-500 text-sm">Status</span>
                    @if($product->is_active)
                        <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-full text-xs font-medium border border-emerald-200">Active</span>
                    @else
                        <span class="px-2.5 py-1 bg-red-50 text-red-600 rounded-full text-xs font-medium border border-red-200">Inactive</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Stock Info -->
        <div class="card p-5">
            <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-warehouse text-emerald-500"></i> Stock Info
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500 text-sm">Current Stock</span>
                    @if($product->stock > 0)
                        <span class="font-bold text-emerald-600">{{ $product->stock }} units</span>
                    @else
                        <span class="font-bold text-red-600">Out of stock</span>
                    @endif
                </div>
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500 text-sm">Product ID</span>
                    <span class="font-semibold text-gray-900 text-sm">#{{ $product->id }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-500 text-sm">Created</span>
                    <span class="font-semibold text-gray-900 text-sm">{{ $product->created_at->format('M d, Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Image -->
        <div class="card p-5">
            <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-image text-purple-500"></i> Image
            </h3>
            @if($product->image_url)
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-44 object-cover rounded-xl border border-gray-200">
            @else
                <div class="w-full h-44 bg-gray-50 rounded-xl flex items-center justify-center border border-dashed border-gray-300">
                    <div class="text-center text-gray-400">
                        <i class="fas fa-box text-4xl mb-2"></i>
                        <p class="text-sm">No image</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function deleteProduct(id) {
        showDeleteModal('Product', id, '/admin/products/' + id, { redirectUrl: '{{ route("admin.products.index") }}' });
    }
</script>
@endpush
