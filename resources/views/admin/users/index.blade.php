@extends('MainLayout')

@section('title', 'Users')
@section('page_title', 'Users')
@section('page_subtitle', 'Manage registered users')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">All Users</h2>
        </div>
        <button onclick="openCreateModal()"
           class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-lg font-medium transition">
            <i class="fas fa-plus"></i>
            Add New User
        </button>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm p-4">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search by name or email..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <select name="role" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                            {{ ucfirst($role->name) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-5 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition">
                <i class="fas fa-search mr-1"></i> Filter
            </button>
            @if(request()->hasAny(['search', 'role']))
                <a href="{{ route('admin.users.index') }}" class="px-5 py-2 text-gray-600 hover:text-gray-900 transition">
                    <i class="fas fa-times mr-1"></i> Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Main Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">ID</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">User</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Email</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Role</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Created At</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody id="table-body" class="divide-y divide-gray-200">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-gray-700 font-medium">{{ $user->code }}</td>
                    
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            @if($user->avatar_url)
                                <img src="{{ $user->avatar_url }}" 
                                     alt="{{ $user->name }}" 
                                     class="w-10 h-10 object-cover rounded-full">
                            @elseif($user->provider_avatar)
                                <img src="{{ $user->provider_avatar }}" 
                                     alt="{{ $user->name }}" 
                                     class="w-10 h-10 object-cover rounded-full">
                            @else
                                <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center">
                                    <span class="text-white font-bold text-sm">{{ substr($user->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <span class="font-medium text-gray-800">{{ $user->name }}</span>
                        </div>
                    </td>

                    <td class="px-6 py-4 text-gray-600 text-sm">
                        {{ $user->email }}
                        @if($user->provider)
                            <span class="ml-2 px-2 py-0.5 text-xs bg-gray-100 text-gray-600 rounded-full">
                                {{ ucfirst($user->provider) }}
                            </span>
                        @endif
                    </td>

                    <td class="px-6 py-4">
                        @if($user->isAdmin())
                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-700">Admin</span>
                        @else
                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-700">Customer</span>
                        @endif
                    </td>

                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $user->created_at->format('M d, Y') }}
                    </td>

                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <a href="{{ route('admin.users.show', $user->id) }}" class="text-green-600 hover:text-green-800 transition">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button onclick="openEditModal({{ $user->id }})"
                               class="text-blue-600 hover:text-blue-800 transition">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteUser({{ $user->id }})" class="text-red-600 hover:text-red-800 transition">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center">
                        <i class="fas fa-users text-5xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">No users found</p>
                        <button onclick="openCreateModal()"
                           class="text-blue-600 hover:underline mt-3 inline-block">
                            Create your first user
                        </button>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div id="pagination-container">
        @if($users->hasPages())
            <div class="flex justify-end mt-4">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>

<!-- ==================== CREATE MODAL ==================== -->
<div id="create-modal" class="fixed inset-0 z-50 hidden items-center justify-center" style="display: none;">
    <div class="absolute inset-0 bg-black opacity-50" onclick="closeCreateModal()"></div>
    <div class="relative bg-white rounded-lg shadow-xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-5 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Create User</h3>
            <button onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-5">
            <div id="create-errors"></div>
            <form id="create-form" onsubmit="submitCreateForm(event)" class="space-y-5">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="create-name"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" id="create-email"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
                        <input type="password" name="password" id="create-password"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Role <span class="text-red-500">*</span></label>
                        <select name="role_id" id="create-role_id"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Select a role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                            @endforeach
                        </select>
                    </div>
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
            <h3 class="text-lg font-semibold text-gray-800">Edit User</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-5">
            <div id="edit-errors"></div>
            <form id="edit-form" onsubmit="submitEditForm(event)" class="space-y-5">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="edit-name"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" id="edit-email"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Password <span class="text-gray-400 font-normal">(leave empty to keep current)</span></label>
                        <input type="password" name="password" id="edit-password"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Role <span class="text-red-500">*</span></label>
                        <select name="role_id" id="edit-role_id"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Select a role</option>
                        </select>
                    </div>
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
    const roles = @json($roles);
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    let editingUserId = null;

    function showModal(id) {
        const modal = document.getElementById(id);
        modal.style.display = 'flex';
    }

    function hideModal(id) {
        const modal = document.getElementById(id);
        modal.style.display = 'none';
    }

    function showErrors(containerId, errors) {
        const container = document.getElementById(containerId);
        let html = '<div class="p-4 bg-red-100 text-red-700 border border-red-300 rounded-lg mb-4"><ul class="list-disc list-inside text-sm">';
        for (const key in errors) {
            if (Array.isArray(errors[key])) {
                errors[key].forEach(msg => { html += '<li>' + msg + '</li>'; });
            } else {
                html += '<li>' + errors[key] + '</li>';
            }
        }
        html += '</ul></div>';
        container.innerHTML = html;
    }

    function clearErrors(containerId) {
        document.getElementById(containerId).innerHTML = '';
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
            const newPag = doc.querySelector('#pagination-container');
            if (newBody) document.querySelector('#table-body').innerHTML = newBody.innerHTML;
            if (newPag) document.querySelector('#pagination-container').innerHTML = newPag.innerHTML;
        } catch (e) { window.location.reload(); }
    }

    // ============ CREATE MODAL ============
    function openCreateModal() {
        document.getElementById('create-form').reset();
        clearErrors('create-errors');
        showModal('create-modal');
    }

    function closeCreateModal() {
        hideModal('create-modal');
    }

    async function submitCreateForm(e) {
        e.preventDefault();
        clearErrors('create-errors');

        const form = document.getElementById('create-form');
        const formData = new FormData(form);
        formData.append('_token', csrfToken);

        try {
            const res = await fetch('{{ route("admin.users.store") }}', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: formData
            });

            if (res.ok) {
                closeCreateModal();
                showToast('User created successfully.', 'success');
                await refreshTable();
            } else {
                const data = await res.json();
                showErrors('create-errors', data.errors || { general: [data.message || 'Something went wrong.'] });
            }
        } catch (err) {
            showErrors('create-errors', { general: ['Network error. Please try again.'] });
        }
    }

    // ============ EDIT MODAL ============
    function populateRoleSelect(selectId, selectedRoleId) {
        const select = document.getElementById(selectId);
        select.innerHTML = '<option value="">Select a role</option>';
        roles.forEach(role => {
            const selected = role.id == selectedRoleId ? 'selected' : '';
            select.innerHTML += `<option value="${role.id}" ${selected}>${role.name.charAt(0).toUpperCase() + role.name.slice(1)}</option>`;
        });
    }

    async function openEditModal(userId) {
        editingUserId = userId;
        clearErrors('edit-errors');
        populateRoleSelect('edit-role_id', null);

        document.getElementById('edit-name').value = '';
        document.getElementById('edit-email').value = '';
        document.getElementById('edit-password').value = '';
        document.getElementById('edit-role_id').value = '';

        try {
            const res = await fetch(`/admin/users/${userId}/edit`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });

            if (res.ok) {
                const data = await res.json();
                document.getElementById('edit-name').value = data.user.name;
                document.getElementById('edit-email').value = data.user.email;
                populateRoleSelect('edit-role_id', data.user.role_id);
            } else {
                const html = await res.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const nameInput = doc.querySelector('input[name="name"]');
                const emailInput = doc.querySelector('input[name="email"]');
                const roleSelect = doc.querySelector('select[name="role_id"]');

                if (nameInput) document.getElementById('edit-name').value = nameInput.value;
                if (emailInput) document.getElementById('edit-email').value = emailInput.value;
                if (roleSelect) populateRoleSelect('edit-role_id', roleSelect.value);
            }
        } catch (err) {
            showErrors('edit-errors', { general: ['Failed to load user data.'] });
            return;
        }

        showModal('edit-modal');
    }

    function closeEditModal() {
        hideModal('edit-modal');
        editingUserId = null;
    }

    async function submitEditForm(e) {
        e.preventDefault();
        if (!editingUserId) return;
        clearErrors('edit-errors');

        const form = document.getElementById('edit-form');
        const formData = new FormData(form);
        formData.append('_method', 'PUT');

        try {
            const res = await fetch(`/admin/users/${editingUserId}`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: formData
            });

            if (res.ok) {
                closeEditModal();
                showToast('User updated successfully.', 'success');
                await refreshTable();
            } else {
                const data = await res.json();
                showErrors('edit-errors', data.errors || { general: [data.message || 'Something went wrong.'] });
            }
        } catch (err) {
            showErrors('edit-errors', { general: ['Network error. Please try again.'] });
        }
    }

    // ============ DELETE ============
    async function deleteUser(id) {
        if (!confirm('Are you sure you want to delete this user?')) return;
        try {
            const res = await fetch(`/admin/users/${id}`, {
                method: 'DELETE',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
            });
            if (res.ok) { showToast('User deleted successfully.', 'success'); await refreshTable(); }
            else { const d = await res.json(); showToast(d.message || 'Delete failed.', 'error'); }
        } catch (e) { showToast('Network error.', 'error'); }
    }
</script>
@endpush
