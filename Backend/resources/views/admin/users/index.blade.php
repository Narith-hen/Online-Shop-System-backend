@extends('MainLayout')

@section('title', 'Users')
@section('page_title', 'Users')
@section('page_subtitle', 'Manage registered users')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-bold text-gray-800">All Users</h2>
                <span class="px-2.5 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">{{ $users->total() ?? 0 }} total</span>
            </div>
        </div>
        <button onclick="openCreateModal()"
            class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium transition shadow-sm hover:shadow-md">
            <i class="fas fa-plus"></i>
            Add New User
        </button>
    </div>

    <div class="card p-4">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-[200px]">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search users..." class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
            </div>
            <select name="role" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                <option value="">All Roles</option>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition text-sm font-medium">
                <i class="fas fa-search mr-1"></i> Filter
            </button>
            @if(request()->hasAny(['search', 'role']))
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 text-gray-500 hover:text-gray-700 transition text-sm font-medium">
                    <i class="fas fa-times mr-1"></i> Clear
                </a>
            @endif
        </form>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full" id="users-table">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-4 py-3 w-10"><input type="checkbox" class="bulk-select-all rounded border-gray-300"></th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">#</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">User</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 hide-tablet">Email</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Role</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Created</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody id="table-body" class="divide-y divide-gray-100">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50/80 transition">
                        <td class="px-4 py-3"><input type="checkbox" class="bulk-checkbox rounded border-gray-300" data-id="{{ $user->id }}"></td>
                        <td class="px-4 py-3 text-sm text-gray-500 font-medium">#{{ $users->firstItem() + $loop->index }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if($user->avatar_url)
                                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-9 h-9 object-cover rounded-full border border-gray-200">
                                @elseif($user->provider_avatar)
                                    <img src="{{ $user->provider_avatar }}" alt="{{ $user->name }}" class="w-9 h-9 object-cover rounded-full border border-gray-200">
                                @else
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">{{ substr($user->name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <span class="font-medium text-gray-800 text-sm">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500 hide-tablet">
                            {{ $user->email }}
                            @if($user->provider)
                                <span class="ml-1.5 px-2 py-0.5 text-[10px] bg-gray-100 text-gray-500 rounded-full font-medium">{{ ucfirst($user->provider) }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($user->isAdmin())
                                <span class="px-2.5 py-1 bg-purple-50 text-purple-700 rounded-full text-xs font-medium border border-purple-200">Admin</span>
                            @else
                                <span class="px-2.5 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-medium border border-blue-200">Customer</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($user->is_blocked)
                                <span class="px-2.5 py-1 bg-red-50 text-red-600 rounded-full text-xs font-medium border border-red-200">Blocked</span>
                            @else
                                <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-full text-xs font-medium border border-emerald-200">Active</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $user->created_at->format('M d, Y') }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="p-1.5 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button onclick="openEditModal({{ $user->id }})" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                @if(!$user->isAdmin())
                                <button onclick="toggleBlock({{ $user->id }})" class="p-1.5 {{ $user->is_blocked ? 'text-emerald-500 hover:text-emerald-600 hover:bg-emerald-50' : 'text-orange-400 hover:text-orange-600 hover:bg-orange-50' }} rounded-lg transition" title="{{ $user->is_blocked ? 'Unblock' : 'Block' }}">
                                    <i class="fas {{ $user->is_blocked ? 'fa-unlock' : 'fa-ban' }}"></i>
                                </button>
                                @endif
                                @if(auth()->id() !== $user->id && !$user->isAdmin())
                                <button onclick="deleteUser({{ $user->id }})" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-16 text-center">
                            <div class="text-gray-300 mb-3"><i class="fas fa-users text-5xl"></i></div>
                            <p class="text-gray-500 font-medium">No users found</p>
                            <button onclick="openCreateModal()" class="mt-2 text-blue-600 hover:text-blue-700 text-sm font-medium">Create your first user</button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div id="pagination-container">
            @if($users->hasPages())
                <div class="px-4 py-3 border-t border-gray-100">{{ $users->links() }}</div>
            @endif
        </div>
    </div>

</div>

<!-- CREATE MODAL -->
<div id="create-modal" class="modal-overlay">
    <div class="modal-backdrop" onclick="closeCreateModal()"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3>Create User</h3>
            <button onclick="closeCreateModal()" class="modal-close"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div id="create-errors"></div>
            <form id="create-form" onsubmit="submitCreateForm(event)" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="create-name" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="create-email" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" id="create-password" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Role <span class="text-red-500">*</span></label>
                    <select name="role_id" id="create-role_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" required>
                        <option value="">Select a role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                </div>
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
            <h3>Edit User</h3>
            <button onclick="closeEditModal()" class="modal-close"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div id="edit-errors"></div>
            <form id="edit-form" onsubmit="submitEditForm(event)" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="edit-name" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="edit-email" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Password <span class="text-gray-400 font-normal">(leave empty to keep current)</span></label>
                    <input type="password" name="password" id="edit-password" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Role <span class="text-red-500">*</span></label>
                    <select name="role_id" id="edit-role_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" required>
                        <option value="">Select a role</option>
                    </select>
                </div>
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg text-sm transition">Update</button>
                    <button type="button" onclick="closeEditModal()" class="px-5 py-2.5 text-gray-600 hover:text-gray-800 font-medium text-sm">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>initBulk('users-table', '{{ route("admin.users.bulk-destroy") }}');</script>
@endsection

@push('scripts')
<script>
    var roles = @json($roles);
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var editingUserId = null;

    function showModal(id) { document.getElementById(id).classList.add('active'); }
    function hideModal(id) { document.getElementById(id).classList.remove('active'); }

    function showErrors(containerId, errors) {
        var c = document.getElementById(containerId);
        var h = '<div class="p-3 bg-red-50 border border-red-200 rounded-lg mb-4"><ul class="list-disc list-inside text-sm text-red-700">';
        for (var k in errors) { (Array.isArray(errors[k]) ? errors[k] : [errors[k]]).forEach(function(m) { h += '<li>' + m + '</li>'; }); }
        c.innerHTML = h + '</ul></div>';
    }
    function clearErrors(id) { document.getElementById(id).innerHTML = ''; }

    async function refreshTable() {
        try {
            var res = await fetch(window.location.href);
            var html = await res.text();
            var doc = new DOMParser().parseFromString(html, 'text/html');
            var b = doc.querySelector('#table-body');
            var p = doc.querySelector('#pagination-container');
            if (b) document.querySelector('#table-body').innerHTML = b.innerHTML;
            if (p) document.querySelector('#pagination-container').innerHTML = p.innerHTML;
        } catch (e) { console.error(e); }
    }

    function populateRoleSelect(selectId, selectedRoleId) {
        var s = document.getElementById(selectId);
        s.innerHTML = '<option value="">Select a role</option>';
        roles.forEach(function(role) {
            s.innerHTML += '<option value="' + role.id + '" ' + (role.id == selectedRoleId ? 'selected' : '') + '>' + role.name.charAt(0).toUpperCase() + role.name.slice(1) + '</option>';
        });
    }

    // ===== CREATE =====
    function openCreateModal() {
        document.getElementById('create-form').reset();
        clearErrors('create-errors');
        showModal('create-modal');
    }
    function closeCreateModal() { hideModal('create-modal'); }
    async function submitCreateForm(e) {
        e.preventDefault(); clearErrors('create-errors');
        var fd = new FormData(document.getElementById('create-form')); fd.append('_token', csrfToken);
        try {
            var res = await fetch('{{ route("admin.users.store") }}', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }, body: fd });
            if (res.ok) { closeCreateModal(); showToast('User created successfully.', 'success'); await refreshTable(); }
            else { var d = await res.json(); showErrors('create-errors', d.errors || { general: [d.message || 'Error'] }); }
        } catch (e) { showErrors('create-errors', { general: ['Network error.'] }); }
    }

    // ===== EDIT =====
    async function openEditModal(userId) {
        editingUserId = userId; clearErrors('edit-errors');
        populateRoleSelect('edit-role_id', null);
        try {
            var res = await fetch('/admin/users/' + userId + '/edit', { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });
            if (res.ok) {
                var data = await res.json();
                document.getElementById('edit-name').value = data.user.name;
                document.getElementById('edit-email').value = data.user.email;
                populateRoleSelect('edit-role_id', data.user.role_id);
            } else {
                var html = await res.text();
                var doc = new DOMParser().parseFromString(html, 'text/html');
                var n = doc.querySelector('input[name="name"]');
                var e = doc.querySelector('input[name="email"]');
                var r = doc.querySelector('select[name="role_id"]');
                if (n) document.getElementById('edit-name').value = n.value;
                if (e) document.getElementById('edit-email').value = e.value;
                if (r) populateRoleSelect('edit-role_id', r.value);
            }
        } catch (err) { showErrors('edit-errors', { general: ['Failed to load user data.'] }); return; }
        showModal('edit-modal');
    }
    function closeEditModal() { hideModal('edit-modal'); editingUserId = null; }
    async function submitEditForm(e) {
        e.preventDefault(); if (!editingUserId) return; clearErrors('edit-errors');
        var fd = new FormData(document.getElementById('edit-form'));
        try {
            var res = await fetch('/admin/users/' + editingUserId, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }, body: fd });
            if (res.ok) { closeEditModal(); showToast('User updated successfully.', 'success'); await refreshTable(); }
            else { var d = await res.json(); showErrors('edit-errors', d.errors || { general: [d.message || 'Error'] }); }
        } catch (e) { showErrors('edit-errors', { general: ['Network error.'] }); }
    }

    // ===== DELETE =====
    function deleteUser(id) { showDeleteModal('User', id, '/admin/users/' + id); }


    // ===== BLOCK / UNBLOCK =====
    async function toggleBlock(id) {
        try {
            var res = await fetch('/admin/users/' + id + '/toggle-block', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken } });
            var d = await res.json();
            if (d.success) { showToast(d.message, 'success'); await refreshTable(); }
            else { showToast(d.message || 'Action failed.', 'error'); }
        } catch (e) { showToast('Network error.', 'error'); }
    }
</script>
@endpush
