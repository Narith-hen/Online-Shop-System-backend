@extends('MainLayout')

@section('title', $category->name)
@section('page_title', $category->name)
@section('page_subtitle', 'Category details and products')

@section('content')

<div class="space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('admin.categories.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
            <i class="fas fa-arrow-left mr-1"></i> Back to Categories
        </a>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.categories.edit', $category->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                <i class="fas fa-edit mr-1"></i> Edit Category
            </a>
            <button onclick="deleteCategory({{ $category->id }})" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                <i class="fas fa-trash mr-1"></i> Delete
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Category Summary</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">Name</span>
                    <span class="font-semibold text-gray-900">{{ $category->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">Status</span>
                    @if($category->is_active)
                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-medium">Active</span>
                    @else
                        <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-medium">Inactive</span>
                    @endif
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">Products</span>
                    <span class="font-bold text-lg text-blue-600">{{ $category->products_count }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">Created</span>
                    <span class="font-semibold text-gray-900">{{ $category->created_at->format('M d, Y') }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Description</h3>
            <p class="text-sm text-gray-600 leading-relaxed">{{ $category->description ?? 'No description provided.' }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Image</h3>
            @if($category->image_url)
                <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="w-full h-40 object-cover rounded-lg border border-gray-200">
            @else
                <div class="w-full h-40 bg-gray-100 rounded-lg flex items-center justify-center">
                    <div class="text-center text-gray-400">
                        <i class="fas fa-tag text-4xl mb-2"></i>
                        <p class="text-sm">No image</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-800">Products in this Category</h3>
            <a href="{{ route('admin.products.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                <i class="fas fa-plus mr-1"></i> Add Product
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="text-left py-3 px-6 font-semibold text-gray-700 text-sm">Product</th>
                        <th class="text-left py-3 px-6 font-semibold text-gray-700 text-sm">Price</th>
                        <th class="text-left py-3 px-6 font-semibold text-gray-700 text-sm">Stock</th>
                        <th class="text-left py-3 px-6 font-semibold text-gray-700 text-sm">Status</th>
                        <th class="text-right py-3 px-6 font-semibold text-gray-700 text-sm">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    @if($product->image_url)
                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-10 h-10 object-cover rounded">
                                    @else
                                        <div class="w-10 h-10 bg-gray-200 rounded flex items-center justify-center">
                                            <i class="fas fa-box text-gray-400"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $product->name }}</p>
                                        <p class="text-xs text-gray-500">ID: {{ $product->id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-sm text-gray-600">${{ number_format($product->price, 2) }}</td>
                            <td class="py-4 px-6 text-sm text-gray-600">{{ $product->stock }}</td>
                            <td class="py-4 px-6">
                                @if($product->is_active)
                                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Active</span>
                                @else
                                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">Inactive</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('admin.products.edit', $product->id) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 px-6 text-center text-gray-500">
                                <i class="fas fa-box-open text-4xl mb-3"></i>
                                <p class="text-sm">No products in this category yet</p>
                                <a href="{{ route('admin.products.create') }}" class="text-blue-600 hover:underline text-sm mt-2 inline-block">Add your first product</a>
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
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function showToast(msg, type) {
        const existing = document.getElementById('inline-toast');
        if (existing) existing.remove();
        const toast = document.createElement('div');
        toast.id = 'inline-toast';
        toast.className = 'fixed bottom-4 right-4 z-[9999] px-5 py-3 rounded-lg shadow-lg text-white font-medium transition-all '
            + (type === 'success' ? 'bg-emerald-500' : 'bg-red-500');
        toast.textContent = msg;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 2500);
    }

    async function deleteCategory(id) {
        if (!confirm('Are you sure you want to delete this category?')) return;
        try {
            const res = await fetch(`/admin/categories/${id}`, {
                method: 'DELETE',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
            });
            if (res.ok) {
                showToast('Category deleted successfully.', 'success');
                setTimeout(() => { window.location.href = '{{ route("admin.categories.index") }}'; }, 1000);
            } else {
                const d = await res.json();
                showToast(d.message || 'Delete failed.', 'error');
            }
        } catch (e) { showToast('Network error.', 'error'); }
    }
</script>
@endpush
