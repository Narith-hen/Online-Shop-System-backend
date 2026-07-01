@extends('MainLayout')

@section('title', 'Products')
@section('page_title', 'Products')
@section('page_subtitle', 'Manage your store products')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-bold text-gray-800">All Products</h2>
                <span class="px-2.5 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">{{ $products->total() ?? 0 }} total</span>
            </div>
        </div>
        <button onclick="openCreateModal()"
            class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium transition shadow-sm hover:shadow-md">
            <i class="fas fa-plus"></i>
            Add New Product
        </button>
    </div>

    <!-- Stock Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ request()->fullUrlWithQuery(['stock_status' => request('stock_status') === 'low' ? null : 'low']) }}"
           class="card p-4 block transition-all {{ request('stock_status') === 'low' ? 'ring-2 ring-red-300 shadow-md' : 'hover:shadow-md' }}">
            <div class="flex items-center justify-between mb-1">
                <p class="text-gray-500 text-sm font-medium">Almost Gone</p>
                <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-500 text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-red-600">{{ $lowStockCount }}</p>
            <p class="text-xs text-gray-400 mt-0.5">products with low stock</p>
        </a>
        <div class="card p-4">
            <div class="flex items-center justify-between mb-1">
                <p class="text-gray-500 text-sm font-medium">Units Left</p>
                <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-warehouse text-blue-500 text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-blue-600">{{ $totalUnitsLeft }}</p>
            <p class="text-xs text-gray-400 mt-0.5">total stock available</p>
        </div>
        <div class="card p-4">
            <div class="flex items-center justify-between mb-1">
                <p class="text-gray-500 text-sm font-medium">Units Sold</p>
                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                    <i class="fas fa-chart-line text-emerald-500 text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-emerald-600">{{ $totalUnitsSold }}</p>
            <p class="text-xs text-gray-400 mt-0.5">total sold all time</p>
        </div>
        <div class="card p-4">
            <div class="flex items-center justify-between mb-1">
                <p class="text-gray-500 text-sm font-medium">Daily Earnings</p>
                <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-purple-500 text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-purple-600">${{ number_format($dailyEarnings, 2) }}</p>
            <p class="text-xs text-gray-400 mt-0.5">avg per day</p>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="card p-4">
        <form method="GET" action="{{ route('admin.products.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-[200px]">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search products..."
                           class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
            </div>
            <select name="category_id" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                <option value="">All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition text-sm font-medium">
                <i class="fas fa-search mr-1"></i> Filter
            </button>
            @if(request()->hasAny(['search', 'category_id', 'status', 'stock_status']))
                <a href="{{ route('admin.products.index') }}" class="px-4 py-2 text-gray-500 hover:text-gray-700 transition text-sm font-medium">
                    <i class="fas fa-times mr-1"></i> Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full" id="products-table">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left py-3 px-4 w-10"><input type="checkbox" class="bulk-select-all rounded border-gray-300"></th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-600 text-sm">#</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-600 text-sm">Product</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-600 text-sm hide-tablet">Category</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-600 text-sm">Price</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-600 text-sm">Stock</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-600 text-sm">Status</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-600 text-sm">Actions</th>
                    </tr>
                </thead>
                <tbody id="table-body" class="divide-y divide-gray-100">
                    @forelse($products ?? [] as $product)
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="py-3 px-4"><input type="checkbox" class="bulk-checkbox rounded border-gray-300" data-id="{{ $product->id }}"></td>
                            <td class="py-3 px-4 text-sm text-gray-500 font-medium">#{{ $products->firstItem() + $loop->index }}</td>
                            <td class="py-3 px-4 min-w-[200px] max-w-[300px]">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $product->image_url ?? 'https://via.placeholder.com/40x40?text=P' }}" alt="{{ $product->name }}" class="w-10 h-10 rounded-lg object-cover border border-gray-200 shrink-0">
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $product->name }}</p>
                                        <p class="text-xs text-gray-500">ID: #{{ $product->id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4 hide-tablet">
                                <span class="px-2.5 py-1 bg-gray-100 text-gray-600 rounded-md text-xs font-medium">{{ $product->category->name ?? 'N/A' }}</span>
                            </td>
                            <td class="py-3 px-4 text-sm font-semibold text-gray-900">${{ number_format($product->price ?? 0, 2) }}</td>
                            <td class="py-3 px-4">
                                @if(($product->stock ?? 0) > 5)
                                    <span class="text-sm font-medium text-emerald-600">{{ $product->stock }} in stock</span>
                                @elseif(($product->stock ?? 0) > 0)
                                    <span class="text-sm font-medium text-amber-600">{{ $product->stock }} low stock</span>
                                @else
                                    <span class="text-sm font-medium text-red-600">Out of stock</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if($product->is_active)
                                    <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-full text-xs font-medium border border-emerald-200">Active</span>
                                @else
                                    <span class="px-2.5 py-1 bg-gray-100 text-gray-500 rounded-full text-xs font-medium border border-gray-200">Inactive</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.products.show', $product->id) }}" class="p-1.5 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button onclick="openEditModal({{ $product->id }})" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteProduct({{ $product->id }})" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-16 text-center">
                                <div class="text-gray-300 mb-3"><i class="fas fa-box text-5xl"></i></div>
                                <p class="text-gray-500 font-medium">No products found</p>
                                <button onclick="openCreateModal()" class="mt-2 text-blue-600 hover:text-blue-700 text-sm font-medium">Create your first product</button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(isset($products) && method_exists($products, 'links') && $products->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">{{ $products->links() }}</div>
        @endif
    </div>

</div>

<!-- CREATE MODAL -->
<div id="create-modal" class="modal-overlay">
    <div class="modal-backdrop" onclick="closeCreateModal()"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3>Create Product</h3>
            <button onclick="closeCreateModal()" class="modal-close"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div id="create-errors"></div>
            <form id="create-form" onsubmit="submitCreateForm(event)" enctype="multipart/form-data" class="space-y-4">@csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="create-name" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Category <span class="text-red-500">*</span></label>
                    <select name="category_id" id="create-category_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" required>
                        <option value="">Select category</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Price <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" min="0" name="price" id="create-price" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Stock <span class="text-red-500">*</span></label>
                        <input type="number" min="0" name="stock" id="create-stock" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Image</label>
                    <div id="create-image-box" class="border border-gray-200 rounded-lg p-3 space-y-3 bg-gray-50/50">
                        <div id="create-preview-box" class="flex items-center justify-center bg-white rounded-lg border border-dashed border-gray-300 overflow-hidden relative hidden" style="min-height:80px">
                            <img id="create-preview-img" src="" alt="Preview" class="max-h-24 max-w-full object-contain p-2" onerror="this.parentElement.classList.add('hidden')">
                            <button type="button" onclick="clearModalImage('create-preview-box', 'create-preview-img', 'create-image-url', 'create-file-name')"
                                class="absolute top-1 right-1 w-5 h-5 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center text-[10px] transition shadow-sm" title="Remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <input type="hidden" name="remove_image" id="create-remove-image" value="0">
                        <div class="space-y-1.5">
                            <label class="text-xs font-medium text-gray-500">Paste image URL</label>
                            <input type="url" name="image_url" id="create-image-url"
                                placeholder="https://example.com/image.jpg"
                                oninput="previewModalUrl('create-preview-img', 'create-preview-box', this.value)"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="flex items-center gap-2">
                            <hr class="flex-1 border-gray-200"><span class="text-xs text-gray-400">OR</span><hr class="flex-1 border-gray-200">
                        </div>
                        <div class="flex items-center gap-3">
                            <label class="cursor-pointer px-3 py-2 bg-white hover:bg-gray-100 border border-gray-300 rounded-lg text-sm font-medium text-gray-600 transition flex items-center gap-1.5">
                                <i class="fas fa-cloud-upload-alt text-blue-500 text-xs"></i> Upload
                                <input type="file" name="image" accept="image/*" class="hidden" onchange="handleModalFile('create-file-name', 'create-preview-img', 'create-preview-box', this)">
                            </label>
                            <span id="create-file-name" class="text-sm text-gray-500">No file</span>
                        </div>
                    </div>
                </div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" id="create-is_active" checked class="rounded border-gray-300 text-blue-600">
                    <span class="text-sm font-medium text-gray-700">Active</span>
                </label>
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg text-sm transition">Create</button>
                    <button type="button" onclick="closeCreateModal()" class="px-5 py-2.5 text-gray-600 hover:text-gray-800 font-medium text-sm">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- EDIT MODAL -->
<div id="edit-modal" class="modal-overlay">
    <div class="modal-backdrop" onclick="closeEditModal()"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Product</h3>
            <button onclick="closeEditModal()" class="modal-close"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div id="edit-errors"></div>
            <form id="edit-form" onsubmit="submitEditForm(event)" enctype="multipart/form-data" class="space-y-4">@csrf @method('PUT')
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="edit-name" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Category <span class="text-red-500">*</span></label>
                    <select name="category_id" id="edit-category_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" required>
                        <option value="">Select category</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Price <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" min="0" name="price" id="edit-price" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Stock <span class="text-red-500">*</span></label>
                        <input type="number" min="0" name="stock" id="edit-stock" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Image</label>
                    <div id="edit-image-box" class="border border-gray-200 rounded-lg p-3 space-y-3 bg-gray-50/50">
                        <div id="edit-preview-box" class="flex items-center justify-center bg-white rounded-lg border border-dashed border-gray-300 overflow-hidden relative hidden" style="min-height:80px">
                            <img id="edit-preview-img" src="" alt="Preview" class="max-h-24 max-w-full object-contain p-2" onerror="this.parentElement.classList.add('hidden')">
                            <button type="button" onclick="clearModalImage('edit-preview-box', 'edit-preview-img', 'edit-image-url', 'edit-file-name')"
                                class="absolute top-1 right-1 w-5 h-5 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center text-[10px] transition shadow-sm" title="Remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <input type="hidden" name="remove_image" id="edit-remove-image" value="0">
                        <div class="space-y-1.5">
                            <label class="text-xs font-medium text-gray-500">Paste image URL</label>
                            <input type="url" name="image_url" id="edit-image-url"
                                placeholder="https://example.com/image.jpg"
                                oninput="previewModalUrl('edit-preview-img', 'edit-preview-box', this.value)"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="flex items-center gap-2">
                            <hr class="flex-1 border-gray-200"><span class="text-xs text-gray-400">OR</span><hr class="flex-1 border-gray-200">
                        </div>
                        <div class="flex items-center gap-3">
                            <label class="cursor-pointer px-3 py-2 bg-white hover:bg-gray-100 border border-gray-300 rounded-lg text-sm font-medium text-gray-600 transition flex items-center gap-1.5">
                                <i class="fas fa-cloud-upload-alt text-blue-500 text-xs"></i> Upload
                                <input type="file" name="image" accept="image/*" class="hidden" onchange="handleModalFile('edit-file-name', 'edit-preview-img', 'edit-preview-box', this)">
                            </label>
                            <span id="edit-file-name" class="text-sm text-gray-500">No file</span>
                        </div>
                    </div>
                </div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" id="edit-is_active" class="rounded border-gray-300 text-blue-600">
                    <span class="text-sm font-medium text-gray-700">Active</span>
                </label>
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg text-sm transition">Update</button>
                    <button type="button" onclick="closeEditModal()" class="px-5 py-2.5 text-gray-600 hover:text-gray-800 font-medium text-sm">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>initBulk('products-table', '{{ route("admin.products.bulk-destroy") }}');</script>
@endsection

@push('scripts')
<script>
    var editingProdId = null;
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function showModal(id) { document.getElementById(id).classList.add('active'); }
    function hideModal(id) { document.getElementById(id).classList.remove('active'); }

    function showErrors(containerId, errors) {
        var c = document.getElementById(containerId);
        var h = '<div class="p-3 bg-red-50 border border-red-200 rounded-lg mb-4"><ul class="list-disc list-inside text-sm text-red-700">';
        for (var k in errors) {
            (Array.isArray(errors[k]) ? errors[k] : [errors[k]]).forEach(function(m) { h += '<li>' + m + '</li>'; });
        }
        c.innerHTML = h + '</ul></div>';
    }
    function clearErrors(id) { document.getElementById(id).innerHTML = ''; }

    function previewModalUrl(imgId, boxId, url) {
        var img = document.getElementById(imgId);
        var box = document.getElementById(boxId);
        url = url.trim();
        if (url) { img.src = url; box.classList.remove('hidden'); clearRemove(imgId); }
        else { img.src = ''; box.classList.add('hidden'); }
    }
    function handleModalFile(nameId, imgId, boxId, input) {
        var name = document.getElementById(nameId);
        var img = document.getElementById(imgId);
        var box = document.getElementById(boxId);
        if (input.files && input.files[0]) {
            name.textContent = input.files[0].name;
            clearRemove(imgId);
            var reader = new FileReader();
            reader.onload = function(e) { img.src = e.target.result; box.classList.remove('hidden'); };
            reader.readAsDataURL(input.files[0]);
        } else { name.textContent = 'No file'; }
    }
    function clearRemove(imgId) {
        var prefix = imgId.includes('create') ? 'create' : 'edit';
        document.getElementById(prefix + '-remove-image').value = '0';
    }
    function clearModalImage(boxId, imgId, urlId, nameId) {
        document.getElementById(boxId).classList.add('hidden');
        document.getElementById(imgId).src = '';
        document.getElementById(urlId).value = '';
        document.getElementById(nameId).textContent = 'No file';
        var prefix = imgId.includes('create') ? 'create' : 'edit';
        document.getElementById(prefix + '-remove-image').value = '1';
        var fi = document.querySelector('#' + prefix + '-form input[type="file"]');
        if (fi) fi.value = '';
    }

    function populateCatSelect(selectId, cats, selectedId) {
        var s = document.getElementById(selectId);
        s.innerHTML = '<option value="">Select category</option>';
        cats.forEach(function(cat) {
            s.innerHTML += '<option value="' + cat.id + '" ' + (cat.id == selectedId ? 'selected' : '') + '>' + cat.name + '</option>';
        });
    }

    async function refreshTable() {
        try {
            var res = await fetch(window.location.href);
            var html = await res.text();
            var doc = new DOMParser().parseFromString(html, 'text/html');
            var newBody = doc.querySelector('#table-body');
            if (newBody) document.querySelector('#table-body').innerHTML = newBody.innerHTML;
        } catch (e) { console.error(e); }
    }

    // ===== CREATE =====
    async function openCreateModal() {
        document.getElementById('create-form').reset();
        document.getElementById('create-file-name').textContent = 'No file';
        document.getElementById('create-preview-box').classList.add('hidden');
        document.getElementById('create-image-url').value = '';
        clearErrors('create-errors');
        try {
            var res = await fetch('{{ route("admin.products.create") }}', { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });
            if (res.ok) { var data = await res.json(); populateCatSelect('create-category_id', data.categories, null); }
        } catch (e) {}
        showModal('create-modal');
    }
    function closeCreateModal() { hideModal('create-modal'); }
    async function submitCreateForm(e) {
        e.preventDefault(); clearErrors('create-errors');
        var fd = new FormData(document.getElementById('create-form')); fd.append('_token', csrfToken);
        try {
            var res = await fetch('{{ route("admin.products.store") }}', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }, body: fd });
            if (res.ok) { closeCreateModal(); showToast('Product created successfully.', 'success'); adminNavigate(window.location.href); }
            else { var d = await res.json(); showErrors('create-errors', d.errors || { general: [d.message || 'Error'] }); }
        } catch (e) { showErrors('create-errors', { general: ['Network error.'] }); }
    }

    // ===== EDIT =====
    async function openEditModal(prodId) {
        editingProdId = prodId; clearErrors('edit-errors');
        document.getElementById('edit-file-name').textContent = 'No file';
        document.getElementById('edit-preview-box').classList.add('hidden');
        document.getElementById('edit-image-url').value = '';
        var fi = document.querySelector('#edit-form input[type="file"]'); if (fi) fi.value = '';
        try {
            var res = await fetch('/admin/products/' + prodId + '/edit', { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });
            if (res.ok) {
                var data = await res.json();
                document.getElementById('edit-name').value = data.product.name;
                document.getElementById('edit-price').value = data.product.price;
                document.getElementById('edit-stock').value = data.product.stock;
                document.getElementById('edit-is_active').checked = data.product.is_active;
                populateCatSelect('edit-category_id', data.categories, data.product.category_id);
                if (data.product.image_url) {
                    document.getElementById('edit-preview-img').src = data.product.image_url;
                    document.getElementById('edit-preview-box').classList.remove('hidden');
                }
            }
        } catch (e) { showErrors('edit-errors', { general: ['Failed to load data.'] }); return; }
        showModal('edit-modal');
    }
    function closeEditModal() { hideModal('edit-modal'); editingProdId = null; }
    async function submitEditForm(e) {
        e.preventDefault(); if (!editingProdId) return; clearErrors('edit-errors');
        var fd = new FormData(document.getElementById('edit-form')); fd.append('_method', 'PUT'); fd.append('_token', csrfToken);
        try {
            var res = await fetch('/admin/products/' + editingProdId, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }, body: fd });
            if (res.ok) { closeEditModal(); showToast('Product updated successfully.', 'success'); adminNavigate(window.location.href); }
            else { var d = await res.json(); showErrors('edit-errors', d.errors || { general: [d.message || 'Error'] }); }
        } catch (e) { showErrors('edit-errors', { general: ['Network error.'] }); }
    }

    // ===== DELETE =====
    function deleteProduct(id) { showDeleteModal('Product', id, '/admin/products/' + id); }

</script>
@endpush
