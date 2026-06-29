@extends('MainLayout')

@section('title', 'Notifications')
@section('page_title', 'Notifications')
@section('page_subtitle', 'Manage system notifications')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col md:flex-row gap-4 items-start md:items-center justify-between">
        <form method="GET" action="{{ route('admin.notifications.index') }}" class="flex flex-wrap items-center gap-3 flex-1">
            <div class="flex-1 min-w-[200px]">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search notifications..." class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
            </div>
            <select name="type" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                <option value="">All Types</option>
                <option value="news" {{ request('type') === 'news' ? 'selected' : '' }}>News</option>
                <option value="alert" {{ request('type') === 'alert' ? 'selected' : '' }}>Alert</option>
                <option value="update" {{ request('type') === 'update' ? 'selected' : '' }}>Update</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition text-sm font-medium">
                <i class="fas fa-search mr-1"></i> Filter
            </button>
            @if(request()->hasAny(['search', 'type']))
                <a href="{{ route('admin.notifications.index') }}" class="px-4 py-2 text-gray-500 hover:text-gray-700 transition text-sm font-medium">
                    <i class="fas fa-times mr-1"></i> Clear
                </a>
            @endif
        </form>
        <button onclick="openCreateModal()" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium transition shadow-sm hover:shadow-md whitespace-nowrap">
            <i class="fas fa-plus"></i>
            Create Notification
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card p-4">
            <div class="flex items-center justify-between mb-1">
                <p class="text-gray-500 text-sm font-medium">Total</p>
                <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-bell text-blue-500 text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $totalNotifications ?? 0 }}</p>
        </div>
        <div class="card p-4">
            <div class="flex items-center justify-between mb-1">
                <p class="text-gray-500 text-sm font-medium">Reads</p>
                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                    <i class="fas fa-check-double text-emerald-500 text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-emerald-600">{{ $totalReads ?? 0 }}</p>
        </div>
        <div class="card p-4">
            <div class="flex items-center justify-between mb-1">
                <p class="text-gray-500 text-sm font-medium">Unread</p>
                <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center">
                    <i class="fas fa-envelope text-amber-500 text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-amber-600">{{ $totalUnread ?? 0 }}</p>
        </div>
        <div class="card p-4">
            <div class="flex items-center justify-between mb-1">
                <p class="text-gray-500 text-sm font-medium">Recipients</p>
                <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center">
                    <i class="fas fa-users text-purple-500 text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-purple-600">{{ $uniqueRecipients ?? 0 }}</p>
        </div>
    </div>

    <!-- Notifications Table -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full" id="notifications-table">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left py-3 px-4 w-10"><input type="checkbox" class="bulk-select-all rounded border-gray-300"></th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-600 text-sm">ID</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-600 text-sm">Title</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-600 text-sm hide-tablet">Message</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-600 text-sm">Type</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-600 text-sm">Date</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-600 text-sm">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($notifications as $notification)
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="py-3 px-4"><input type="checkbox" class="bulk-checkbox rounded border-gray-300" data-id="{{ $notification->id }}"></td>
                            <td class="py-3 px-4 text-sm text-gray-500 font-medium">#{{ $notification->id }}</td>
                            <td class="py-3 px-4">
                                <p class="text-sm font-medium text-gray-900">{{ $notification->title }}</p>
                            </td>
                            <td class="py-3 px-4 hide-tablet">
                                <p class="text-sm text-gray-500 truncate max-w-xs">{{ $notification->message ?? '—' }}</p>
                            </td>
                            <td class="py-3 px-4">
                                @php
                                    $typeStyles = ['news' => 'bg-blue-50 text-blue-700 border-blue-200', 'alert' => 'bg-red-50 text-red-600 border-red-200', 'update' => 'bg-emerald-50 text-emerald-700 border-emerald-200'];
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium border {{ $typeStyles[$notification->type] ?? 'bg-gray-100 text-gray-600 border-gray-200' }}">
                                    {{ ucfirst($notification->type) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-500">{{ $notification->created_at->format('M d, Y H:i') }}</td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.notifications.show', $notification->id) }}" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button onclick="showDeleteModal('Notification', {{ $notification->id }}, '{{ route('admin.notifications.destroy', $notification->id) }}', { redirectUrl: '{{ route('admin.notifications.index') }}' })" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center">
                                <div class="text-gray-300 mb-3"><i class="fas fa-bell text-5xl"></i></div>
                                <p class="text-gray-500 font-medium">No notifications yet</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($notifications, 'links') && $notifications->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">{{ $notifications->links() }}</div>
        @endif
    </div>
</div>

<!-- CREATE MODAL -->
<div id="create-modal" class="modal-overlay">
    <div class="modal-backdrop" onclick="closeCreateModal()"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3>Create Notification</h3>
            <button onclick="closeCreateModal()" class="modal-close"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div id="create-errors"></div>
            <form id="create-form" onsubmit="submitCreateForm(event)" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="create-title" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Type <span class="text-red-500">*</span></label>
                    <select name="type" id="create-type" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" required>
                        <option value="news">News</option>
                        <option value="alert">Alert</option>
                        <option value="update">Update</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Message</label>
                    <textarea name="message" id="create-message" rows="3" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Link (optional)</label>
                    <input type="url" name="link" id="create-link" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="notify_all" value="1" checked class="mt-0.5 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <div>
                        <span class="text-sm font-medium text-gray-700">Notify all users (with socket broadcast)</span>
                        <p class="text-xs text-gray-500 mt-0.5">Delivered to all users and pushed in real-time.</p>
                    </div>
                </label>
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg text-sm transition">Send Notification</button>
                    <button type="button" onclick="closeCreateModal()" class="px-5 py-2.5 text-gray-600 hover:text-gray-800 font-medium text-sm">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>initBulk('notifications-table', '{{ route("admin.notifications.bulk-destroy") }}');</script>
@push('scripts')
<script>
var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');


function showModal(id) { document.getElementById(id).classList.add('active'); }
function hideModal(id) { document.getElementById(id).classList.remove('active'); }

function showErrors(containerId, errors) {
    var c = document.getElementById(containerId);
    var h = '<div class="p-3 bg-red-50 border border-red-200 rounded-lg mb-4"><ul class="list-disc list-inside text-sm text-red-700">';
    for (var k in errors) { (Array.isArray(errors[k]) ? errors[k] : [errors[k]]).forEach(function(m) { h += '<li>' + m + '</li>'; }); }
    c.innerHTML = h + '</ul></div>';
}
function clearErrors(id) { document.getElementById(id).innerHTML = ''; }

// ===== CREATE =====
function openCreateModal() {
    document.getElementById('create-form').reset();
    clearErrors('create-errors');
    showModal('create-modal');
}
function closeCreateModal() { hideModal('create-modal'); }

async function submitCreateForm(e) {
    e.preventDefault();
    clearErrors('create-errors');
    var fd = new FormData(document.getElementById('create-form'));
    fd.append('_token', csrfToken);
    try {
        var res = await fetch('{{ route("admin.notifications.store") }}', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: fd
        });
        if (res.ok) {
            closeCreateModal();
            showToast('Notification sent successfully.', 'success');
            if (typeof refreshTable === 'function') await refreshTable();
            else window.location.reload();
        } else {
            var d = await res.json();
            showErrors('create-errors', d.errors || { general: [d.message || 'Error'] });
        }
    } catch (e) {
        showErrors('create-errors', { general: ['Network error.'] });
    }
}
</script>
@endpush
@endsection
