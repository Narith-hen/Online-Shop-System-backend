@extends('MainLayout')

@section('title', 'Products')
@section('page_title', 'Products')
@section('page_subtitle', 'Manage your store products')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">All Products</h2>
        </div>
        <button onclick="openCreateModal()"
            class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-lg font-medium transition">
            <i class="fas fa-plus"></i>
            Add New Product
        </button>
    </div>

    <!-- Search & Filters -->
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('admin.products.index') }}" class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by product name or category..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <select name="category_id" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <button type="submit" class="px-5 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition">
                <i class="fas fa-search mr-1"></i> Filter
            </button>
            @if(request()->hasAny(['search', 'category_id', 'status']))
                <a href="{{ route('admin.products.index') }}" class="px-5 py-2 text-gray-600 hover:text-gray-900 transition">
                    <i class="fas fa-times mr-1"></i> Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Main Table -->
    <div class="card bg-white rounded-lg overflow-hidden">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">Product Name</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">Category</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">Price</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">Stock</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">Status</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">Actions</th>
                        </tr>
                    </thead>
                     <tbody id="table-body">
                        @forelse($products ?? [] as $product)
                            <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $product->image_url ?? 'https://via.placeholder.com/40x40?text=Product' }}" alt="{{ $product->name }}" class="w-10 h-10 rounded object-cover">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $product->name }}</p>
                                            <p class="text-xs text-gray-500">ID: #{{ $product->id }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-600">{{ $product->category->name ?? 'N/A' }}</td>
                                <td class="py-3 px-4 text-sm font-semibold text-gray-900">${{ number_format($product->price ?? 0, 2) }}</td>
                                <td class="py-3 px-4 text-sm">
                                    @if(($product->stock ?? 0) > 0)
                                        <span class="text-green-600">{{ $product->stock }} in stock</span>
                                    @else
                                        <span class="text-red-600">Out of stock</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-sm">
                                    @if($product->is_active)
                                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-medium">Active</span>
                                    @else
                                        <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-xs font-medium">Inactive</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-sm">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.products.show', $product->id) }}" class="text-green-600 hover:text-green-800">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button onclick="openEditModal({{ $product->id }})" class="text-blue-600 hover:text-blue-900 font-medium">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteProduct({{ $product->id }})" class="text-red-600 hover:text-red-900 font-medium">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 px-4 text-center text-gray-500">
                                    <i class="fas fa-inbox text-3xl mb-2"></i>
                                    <p class="text-sm mt-2">No products found</p>
                                    <button onclick="openCreateModal()" class="text-blue-600 hover:text-blue-700 text-sm font-medium mt-2 inline-block">
                                        Create your first product
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- ==================== CREATE MODAL ==================== -->
<div id="create-modal" class="fixed inset-0 z-50 hidden items-center justify-center" style="display: none;">
    <div class="absolute inset-0 bg-black opacity-50" onclick="closeCreateModal()"></div>
    <div class="relative bg-white rounded-lg shadow-xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-5 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Create Product</h3>
            <button onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-5">
            <div id="create-errors"></div>
            <form id="create-form" onsubmit="submitCreateForm(event)" enctype="multipart/form-data" class="space-y-5">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="create-name"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Category <span class="text-red-500">*</span></label>
                        <select name="category_id" id="create-category_id"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Select category</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Price <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" min="0" name="price" id="create-price"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Stock <span class="text-red-500">*</span></label>
                            <input type="number" min="0" name="stock" id="create-stock"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Image</label>
                        <div class="relative flex items-center border border-gray-300 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-blue-500">
                            <label class="cursor-pointer bg-gray-200 px-5 py-2.5 text-sm font-medium text-gray-400 border-r border-gray-300">
                                Choose file
                                <input type="file" name="image" accept="image/*" class="hidden"
                                    onchange="updateFileName('create-file-name', this)">
                            </label>
                            <div id="create-file-name" class="flex-1 px-4 py-2.5 text-sm text-gray-700 truncate">No file chosen</div>
                        </div>
                        <div id="create-preview" class="mt-3 hidden">
                            <img id="create-preview-img" class="w-20 h-20 object-cover rounded-lg border border-gray-300">
                        </div>
                    </div>
                    <div class="flex items-center gap-2"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" id="create-is_active" checked class="rounded border-gray-300 text-blue-600"><label for="create-is_active" class="text-sm font-medium text-gray-700">Active</label></div>
                </div>
                <div class="flex items-center gap-3 pt-4">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-lg">Create</button>
                    <button type="button" onclick="closeCreateModal()" class="text-gray-600 hover:text-gray-900">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==================== EDIT MODAL ==================== -->
<div id="edit-modal" class="fixed inset-0 z-50 hidden items-center justify-center" style="display: none;">
    <div class="absolute inset-0 bg-black opacity-50" onclick="closeEditModal()"></div>
    <div class="relative bg-white rounded-lg shadow-xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-5 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Edit Product</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-5">
            <div id="edit-errors"></div>
            <form id="edit-form" onsubmit="submitEditForm(event)" enctype="multipart/form-data" class="space-y-5">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="edit-name"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Category <span class="text-red-500">*</span></label>
                        <select name="category_id" id="edit-category_id"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Select category</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Price <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" min="0" name="price" id="edit-price"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Stock <span class="text-red-500">*</span></label>
                            <input type="number" min="0" name="stock" id="edit-stock"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Image</label>
                        <div class="relative flex items-center border border-gray-300 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-blue-500">
                            <label class="cursor-pointer bg-gray-200 px-5 py-2.5 text-sm font-medium text-gray-400 border-r border-gray-300">
                                Choose file
                                <input type="file" name="image" accept="image/*" class="hidden"
                                    onchange="updateFileName('edit-file-name', this)">
                            </label>
                            <div id="edit-file-name" class="flex-1 px-4 py-2.5 text-sm text-gray-700 truncate">No file chosen</div>
                        </div>
                        <div id="edit-preview" class="mt-3 hidden">
                            <img id="edit-preview-img" class="w-20 h-20 object-cover rounded-lg border border-gray-300">
                        </div>
                    </div>
                    <div class="flex items-center gap-2"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" id="edit-is_active" class="rounded border-gray-300 text-blue-600"><label for="edit-is_active" class="text-sm font-medium text-gray-700">Active</label></div>
                </div>
                <div class="flex items-center gap-3 pt-4">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-lg">Update</button>
                    <button type="button" onclick="closeEditModal()" class="text-gray-600 hover:text-gray-900">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let editingProdId = null;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function showModal(id) { document.getElementById(id).style.display = 'flex'; }
    function hideModal(id) { document.getElementById(id).style.display = 'none'; }

    function showErrors(containerId, errors) {
        const c = document.getElementById(containerId);
        let h = '<div class="p-4 bg-red-100 text-red-700 border border-red-300 rounded-lg mb-4"><ul class="list-disc list-inside text-sm">';
        for (const k in errors) {
            const items = Array.isArray(errors[k]) ? errors[k] : [errors[k]];
            items.forEach(m => { h += '<li>' + m + '</li>'; });
        }
        h += '</ul></div>';
        c.innerHTML = h;
    }

    function clearErrors(id) { document.getElementById(id).innerHTML = ''; }

    function updateFileName(displayId, input) {
        document.getElementById(displayId).textContent = input.files.length > 0 ? input.files[0].name : 'No file chosen';
    }

    function populateCatSelect(selectId, cats, selectedId) {
        const s = document.getElementById(selectId);
        s.innerHTML = '<option value="">Select category</option>';
        cats.forEach(cat => {
            const sel = cat.id == selectedId ? 'selected' : '';
            s.innerHTML += `<option value="${cat.id}" ${sel}>${cat.name}</option>`;
        });
    }

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

    async function refreshTable() {
        try {
            const res = await fetch(window.location.href);
            const html = await res.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newBody = doc.querySelector('#table-body');
            if (newBody) document.querySelector('#table-body').innerHTML = newBody.innerHTML;
        } catch (e) { window.location.reload(); }
    }

    // ============ CREATE MODAL ============
    async function openCreateModal() {
        document.getElementById('create-form').reset();
        document.getElementById('create-file-name').textContent = 'No file chosen';
        document.getElementById('create-preview').classList.add('hidden');
        clearErrors('create-errors');

        try {
            const res = await fetch('{{ route("admin.products.create") }}', {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            if (res.ok) {
                const data = await res.json();
                populateCatSelect('create-category_id', data.categories, null);
            }
        } catch (e) {}

        showModal('create-modal');
    }

    function closeCreateModal() { hideModal('create-modal'); }

    async function submitCreateForm(e) {
        e.preventDefault();
        clearErrors('create-errors');
        const fd = new FormData(document.getElementById('create-form'));
        fd.append('_token', csrfToken);

        try {
            const res = await fetch('{{ route("admin.products.store") }}', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: fd
            });
            if (res.ok) { closeCreateModal(); showToast('Product created successfully.', 'success'); await refreshTable(); }
            else { const d = await res.json(); showErrors('create-errors', d.errors || { general: [d.message || 'Error'] }); }
        } catch (e) { showErrors('create-errors', { general: ['Network error.'] }); }
    }

    // ============ EDIT MODAL ============
    async function openEditModal(prodId) {
        editingProdId = prodId;
        clearErrors('edit-errors');
        document.getElementById('edit-file-name').textContent = 'No file chosen';
        document.getElementById('edit-preview').classList.add('hidden');

        try {
            const res = await fetch(`/admin/products/${prodId}/edit`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            if (res.ok) {
                const data = await res.json();
                document.getElementById('edit-name').value = data.product.name;
                document.getElementById('edit-price').value = data.product.price;
                document.getElementById('edit-stock').value = data.product.stock;
                document.getElementById('edit-is_active').checked = data.product.is_active;
                populateCatSelect('edit-category_id', data.categories, data.product.category_id);

                if (data.product.image_url) {
                    document.getElementById('edit-preview-img').src = data.product.image_url;
                    document.getElementById('edit-preview').classList.remove('hidden');
                }
            }
        } catch (e) {
            showErrors('edit-errors', { general: ['Failed to load data.'] });
            return;
        }

        showModal('edit-modal');
    }

    function closeEditModal() { hideModal('edit-modal'); editingProdId = null; }

    async function submitEditForm(e) {
        e.preventDefault();
        if (!editingProdId) return;
        clearErrors('edit-errors');
        const fd = new FormData(document.getElementById('edit-form'));
        fd.append('_method', 'PUT');
        fd.append('_token', csrfToken);

        try {
            const res = await fetch(`/admin/products/${editingProdId}`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: fd
            });
            if (res.ok) { closeEditModal(); showToast('Product updated successfully.', 'success'); await refreshTable(); }
            else { const d = await res.json(); showErrors('edit-errors', d.errors || { general: [d.message || 'Error'] }); }
        } catch (e) { showErrors('edit-errors', { general: ['Network error.'] }); }
    }

    // ============ DELETE ============
    async function deleteProduct(id) {
        if (!confirm('Are you sure you want to delete this product?')) return;
        try {
            const res = await fetch(`/admin/products/${id}`, {
                method: 'DELETE',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
            });
            if (res.ok) { showToast('Product deleted successfully.', 'success'); await refreshTable(); }
            else { const d = await res.json(); showToast(d.message || 'Delete failed.', 'error'); }
        } catch (e) { showToast('Network error.', 'error'); }
    }
</script>
@endpush
