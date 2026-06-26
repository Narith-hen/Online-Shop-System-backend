@extends('MainLayout')

@section('title', $product->name)
@section('page_title', $product->name)
@section('page_subtitle', 'Product details')

@section('content')

<div class="space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('admin.products.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
            <i class="fas fa-arrow-left mr-1"></i> Back to Products
        </a>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.products.edit', $product->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                <i class="fas fa-edit mr-1"></i> Edit Product
            </a>
            <button onclick="deleteProduct({{ $product->id }})" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                <i class="fas fa-trash mr-1"></i> Delete
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Product Summary</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">Name</span>
                    <span class="font-semibold text-gray-900">{{ $product->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">Category</span>
                    <span class="font-semibold text-gray-900">{{ $product->category->name ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">Price</span>
                    <span class="font-bold text-lg text-blue-600">${{ number_format($product->price, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">Status</span>
                    @if($product->is_active)
                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-medium">Active</span>
                    @else
                        <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-medium">Inactive</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Stock Info</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">Current Stock</span>
                    @if($product->stock > 0)
                        <span class="font-bold text-lg text-green-600">{{ $product->stock }} units</span>
                    @else
                        <span class="font-bold text-lg text-red-600">Out of stock</span>
                    @endif
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">Product ID</span>
                    <span class="font-semibold text-gray-900">#{{ $product->id }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">Created</span>
                    <span class="font-semibold text-gray-900">{{ $product->created_at->format('M d, Y') }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Image</h3>
            @if($product->image_url)
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-40 object-cover rounded-lg border border-gray-200">
            @else
                <div class="w-full h-40 bg-gray-100 rounded-lg flex items-center justify-center">
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

    async function deleteProduct(id) {
        if (!confirm('Are you sure you want to delete this product?')) return;
        try {
            const res = await fetch(`/admin/products/${id}`, {
                method: 'DELETE',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
            });
            if (res.ok) {
                showToast('Product deleted successfully.', 'success');
                setTimeout(() => { window.location.href = '{{ route("admin.products.index") }}'; }, 1000);
            } else {
                const d = await res.json();
                showToast(d.message || 'Delete failed.', 'error');
            }
        } catch (e) { showToast('Network error.', 'error'); }
    }
</script>
@endpush
