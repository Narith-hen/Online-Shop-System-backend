@extends('MainLayout')

@section('title', 'Categories')
@section('page_title', 'Categories')
@section('page_subtitle', 'Manage your store categories')

@section('content')
<div class="space-y-6">

    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">All Categories</h2>
        </div>
        <button onclick="openCreateModal()"
           class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-lg font-medium transition">
            <i class="fas fa-plus"></i>
            Add New Category
        </button>
    </div>

    <!-- Search & Filters -->
    <div class="bg-white rounded-xl shadow-sm p-4">
        <form method="GET" action="{{ route('admin.categories.index') }}" class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by name or description..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
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
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.categories.index') }}" class="px-5 py-2 text-gray-600 hover:text-gray-900 transition">
                    <i class="fas fa-times mr-1"></i> Clear
                </a>
            @endif
        </form>
    </div>

    <div id="table-container" class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600 w-16">ID</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Name</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Description</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Status</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Created At</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody id="table-body" class="divide-y divide-gray-200">
                @forelse($categories as $index => $category)
                <tr class="hover:bg-gray-50" data-id="{{ $category->id }}">
                    <td class="px-6 py-4 text-gray-700 font-medium">#{{ $index + 1 }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            @if($category->image_url)
                                <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="w-10 h-10 object-cover rounded">
                            @else
                                <div class="w-10 h-10 bg-gray-200 rounded flex items-center justify-center">
                                    <i class="fas fa-tag text-gray-400"></i>
                                </div>
                            @endif
                            <span class="font-medium text-gray-800">{{ $category->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-600 text-sm">
                        {{ Str::limit($category->description ?? 'No description', 80) }}
                    </td>
                    <td class="px-6 py-4">
                        @if($category->is_active)
                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Active</span>
                        @else
                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">Inactive</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $category->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <a href="{{ route('admin.categories.show', $category->id) }}" class="text-green-600 hover:text-green-800 transition">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button onclick="openEditModal({{ $category->id }})" class="text-blue-600 hover:text-blue-800 transition">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteItem({{ $category->id }})" class="text-red-600 hover:text-red-800 transition">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-16 text-center"><i class="fas fa-folder-open text-5xl text-gray-300 mb-4"></i><p class="text-gray-500">No categories yet</p><button onclick="openCreateModal()" class="text-blue-600 hover:underline mt-3 inline-block">Create your first category</button></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div id="pagination-container">
        @if($categories->hasPages())
            <div class="flex justify-end mt-4">{{ $categories->links() }}</div>
        @endif
    </div>

</div>

<!-- CREATE MODAL -->
<div id="create-modal" class="fixed inset-0 z-50 hidden items-center justify-center" style="display: none;">
    <div class="absolute inset-0 bg-black opacity-50" onclick="closeCreateModal()"></div>
    <div class="relative bg-white rounded-lg shadow-xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-5 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Create Category</h3>
            <button onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-xl"></i></button>
        </div>
        <div class="p-5">
            <div id="create-errors"></div>
            <form id="create-form" onsubmit="submitCreateForm(event)" enctype="multipart/form-data" class="space-y-5">
                <div class="space-y-4">
                    <div><label class="block text-sm font-semibold text-gray-700 mb-2">Category Name <span class="text-red-500">*</span></label><input type="text" name="name" id="create-name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required></div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Image</label>
                        <div class="relative flex items-center border border-gray-300 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-blue-500">
                            <label class="cursor-pointer bg-gray-200 px-5 py-2.5 text-sm font-medium text-gray-400 border-r border-gray-300">Choose file<input type="file" name="image" accept="image/*" class="hidden" onchange="updateFileName('create-file-name',this)"></label>
                            <div id="create-file-name" class="flex-1 px-4 py-2.5 text-sm text-gray-700 truncate">No file chosen</div>
                        </div>
                        <div id="create-preview" class="mt-3 hidden"><img id="create-preview-img" class="w-20 h-20 object-cover rounded-lg border border-gray-300"></div>
                    </div>
                    <div><label class="block text-sm font-semibold text-gray-700 mb-2">Description</label><textarea name="description" id="create-description" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea></div>
                    <div class="flex items-center gap-2"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" id="create-is_active" checked class="rounded border-gray-300 text-blue-600"><label for="create-is_active" class="text-sm font-medium text-gray-700">Active</label></div>
                </div>
                <div class="flex items-center gap-3 pt-4"><button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-lg">Create</button><button type="button" onclick="closeCreateModal()" class="text-gray-600 hover:text-gray-900">Cancel</button></div>
            </form>
        </div>
    </div>
</div>

<!-- EDIT MODAL -->
<div id="edit-modal" class="fixed inset-0 z-50 hidden items-center justify-center" style="display: none;">
    <div class="absolute inset-0 bg-black opacity-50" onclick="closeEditModal()"></div>
    <div class="relative bg-white rounded-lg shadow-xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-5 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Edit Category</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-xl"></i></button>
        </div>
        <div class="p-5">
            <div id="edit-errors"></div>
            <form id="edit-form" onsubmit="submitEditForm(event)" enctype="multipart/form-data" class="space-y-5">
                <div class="space-y-4">
                    <div><label class="block text-sm font-semibold text-gray-700 mb-2">Category Name <span class="text-red-500">*</span></label><input type="text" name="name" id="edit-name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required></div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Image</label>
                        <div class="relative flex items-center border border-gray-300 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-blue-500">
                            <label class="cursor-pointer bg-gray-200 px-5 py-2.5 text-sm font-medium text-gray-400 border-r border-gray-300">Choose file<input type="file" name="image" accept="image/*" class="hidden" onchange="updateFileName('edit-file-name',this)"></label>
                            <div id="edit-file-name" class="flex-1 px-4 py-2.5 text-sm text-gray-700 truncate">No file chosen</div>
                        </div>
                        <div id="edit-preview" class="mt-3 hidden"><img id="edit-preview-img" class="w-20 h-20 object-cover rounded-lg border border-gray-300"></div>
                    </div>
                    <div><label class="block text-sm font-semibold text-gray-700 mb-2">Description</label><textarea name="description" id="edit-description" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea></div>
                    <div class="flex items-center gap-2"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" id="edit-is_active" class="rounded border-gray-300 text-blue-600"><label for="edit-is_active" class="text-sm font-medium text-gray-700">Active</label></div>
                </div>
                <div class="flex items-center gap-3 pt-4"><button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-lg">Update</button><button type="button" onclick="closeEditModal()" class="text-gray-600 hover:text-gray-900">Cancel</button></div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let editingCatId = null;
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const storeUrl = '{{ route("admin.categories.store") }}';
const createUrl = '{{ route("admin.categories.create") }}';

function showModal(id) { document.getElementById(id).style.display = 'flex'; }
function hideModal(id) { document.getElementById(id).style.display = 'none'; }
function showErrors(id, errors) {
    let h = '<div class="p-4 bg-red-100 text-red-700 border border-red-300 rounded-lg mb-4"><ul class="list-disc list-inside text-sm">';
    for (const k in errors) { (Array.isArray(errors[k]) ? errors[k] : [errors[k]]).forEach(m => { h += '<li>' + m + '</li>'; }); }
    document.getElementById(id).innerHTML = h + '</ul></div>';
}
function clearErrors(id) { document.getElementById(id).innerHTML = ''; }
function updateFileName(displayId, input) { document.getElementById(displayId).textContent = input.files.length > 0 ? input.files[0].name : 'No file chosen'; }

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
        const newPag = doc.querySelector('#pagination-container');
        if (newBody) document.querySelector('#table-body').innerHTML = newBody.innerHTML;
        if (newPag) document.querySelector('#pagination-container').innerHTML = newPag.innerHTML;
    } catch (e) { window.location.reload(); }
}

function populateParentSelect(selectId, cats, selectedId) {
    const s = document.getElementById(selectId);
    s.innerHTML = '<option value="">None (top-level)</option>';
    cats.forEach(cat => { s.innerHTML += `<option value="${cat.id}" ${cat.id == selectedId ? 'selected' : ''}>${cat.name}</option>`; });
}

// ====== CREATE ======
async function openCreateModal() {
    document.getElementById('create-form').reset();
    document.getElementById('create-file-name').textContent = 'No file chosen';
    document.getElementById('create-preview').classList.add('hidden');
    clearErrors('create-errors');
    try { const r = await fetch(createUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } }); } catch (e) {}
    showModal('create-modal');
}
function closeCreateModal() { hideModal('create-modal'); }
async function submitCreateForm(e) {
    e.preventDefault(); clearErrors('create-errors');
    const fd = new FormData(document.getElementById('create-form')); fd.append('_token', csrfToken);
    try {
        const res = await fetch(storeUrl, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }, body: fd });
        if (res.ok) { closeCreateModal(); showToast('Category created successfully.', 'success'); await refreshTable(); }
        else { const d = await res.json(); showErrors('create-errors', d.errors || { general: [d.message || 'Error'] }); }
    } catch (e) { showErrors('create-errors', { general: ['Network error.'] }); }
}

// ====== EDIT ======
async function openEditModal(catId) {
    editingCatId = catId; clearErrors('edit-errors');
    document.getElementById('edit-file-name').textContent = 'No file chosen';
    document.getElementById('edit-preview').classList.add('hidden');
    try {
        const res = await fetch(`/admin/categories/${catId}/edit`, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });
        if (res.ok) {
            const d = await res.json();
            document.getElementById('edit-name').value = d.category.name;
            document.getElementById('edit-description').value = d.category.description || '';
            document.getElementById('edit-is_active').checked = d.category.is_active;
            if (d.category.image_url) { document.getElementById('edit-preview-img').src = d.category.image_url; document.getElementById('edit-preview').classList.remove('hidden'); }
        }
    } catch (e) { showErrors('edit-errors', { general: ['Failed to load data.'] }); return; }
    showModal('edit-modal');
}
function closeEditModal() { hideModal('edit-modal'); editingCatId = null; }
async function submitEditForm(e) {
    e.preventDefault(); if (!editingCatId) return; clearErrors('edit-errors');
    const fd = new FormData(document.getElementById('edit-form')); fd.append('_method', 'PUT');
    try {
        const res = await fetch(`/admin/categories/${editingCatId}`, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }, body: fd });
        if (res.ok) { closeEditModal(); showToast('Category updated successfully.', 'success'); await refreshTable(); }
        else { const d = await res.json(); showErrors('edit-errors', d.errors || { general: [d.message || 'Error'] }); }
    } catch (e) { showErrors('edit-errors', { general: ['Network error.'] }); }
}

// ====== DELETE ======
async function deleteItem(id) {
    if (!confirm('Are you sure you want to delete this category?')) return;
    try {
        const res = await fetch(`/admin/categories/${id}`, { method: 'DELETE', headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken } });
        if (res.ok) { showToast('Category deleted successfully.', 'success'); await refreshTable(); }
        else { const d = await res.json(); showToast(d.message || 'Delete failed.', 'error'); }
    } catch (e) { showToast('Network error.', 'error'); }
}
</script>
@endpush
