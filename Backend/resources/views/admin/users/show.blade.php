@extends('MainLayout')

@section('title', $user->name)
@section('page_title', $user->name)
@section('page_subtitle', 'User details and order history')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-blue-600 transition">
            <i class="fas fa-arrow-left"></i> Back to Users
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.users.edit', $user->id) }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                <i class="fas fa-edit"></i> Edit User
            </a>
            @if(!$user->isAdmin())
            <button onclick="toggleBlock({{ $user->id }})" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm {{ $user->is_blocked ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-orange-500 hover:bg-orange-600 text-white' }}">
                <i class="fas {{ $user->is_blocked ? 'fa-unlock' : 'fa-ban' }}"></i> {{ $user->is_blocked ? 'Unblock' : 'Block' }}
            </button>
            @endif
            @if(auth()->id() !== $user->id && !$user->isAdmin())
            <button onclick="deleteUser({{ $user->id }})" class="inline-flex items-center gap-2 bg-white border border-red-200 text-red-600 hover:bg-red-50 px-4 py-2 rounded-lg text-sm font-medium transition">
                <i class="fas fa-trash"></i> Delete
            </button>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="card p-5">
            <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-user text-blue-500"></i> User Summary
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500 text-sm">User Code</span>
                    <span class="font-semibold text-gray-900 text-sm">{{ $user->code }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500 text-sm">Name</span>
                    <span class="font-semibold text-gray-900 text-sm">{{ $user->name }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500 text-sm">Email</span>
                    <span class="font-semibold text-gray-900 text-sm">{{ $user->email }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-500 text-sm">Role</span>
                    @if($user->isAdmin())
                        <span class="px-2.5 py-1 bg-purple-50 text-purple-700 rounded-full text-xs font-medium border border-purple-200">Admin</span>
                    @else
                        <span class="px-2.5 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-medium border border-blue-200">Customer</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="card p-5">
            <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-info-circle text-emerald-500"></i> Account Info
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500 text-sm">Provider</span>
                    <span class="font-semibold text-gray-900 text-sm">{{ $user->provider ? ucfirst($user->provider) : 'Email' }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500 text-sm">Status</span>
                    @if($user->is_blocked)
                        <span class="px-2.5 py-1 bg-red-50 text-red-600 rounded-full text-xs font-medium border border-red-200">Blocked</span>
                    @else
                        <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-full text-xs font-medium border border-emerald-200">Active</span>
                    @endif
                </div>
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500 text-sm">Registered</span>
                    <span class="font-semibold text-gray-900 text-sm">{{ $user->created_at->format('M d, Y') }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-500 text-sm">Total Orders</span>
                    <span class="font-bold text-blue-600">{{ $user->orders->count() }}</span>
                </div>
            </div>
        </div>

        <div class="card p-5">
            <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-image text-purple-500"></i> Avatar
            </h3>
            <div class="flex flex-col items-center justify-center py-4">
                @if($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-24 h-24 rounded-full object-cover border-2 border-gray-200">
                @elseif($user->provider_avatar)
                    <img src="{{ $user->provider_avatar }}" alt="{{ $user->name }}" class="w-24 h-24 rounded-full object-cover border-2 border-gray-200">
                @else
                    <div class="w-24 h-24 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-sm">
                        <span class="text-white text-3xl font-bold">{{ substr($user->name, 0, 1) }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-receipt text-indigo-500"></i> Recent Orders
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left py-3 px-5 font-semibold text-gray-600 text-sm">Order ID</th>
                        <th class="text-left py-3 px-5 font-semibold text-gray-600 text-sm">Total</th>
                        <th class="text-left py-3 px-5 font-semibold text-gray-600 text-sm">Status</th>
                        <th class="text-left py-3 px-5 font-semibold text-gray-600 text-sm">Date</th>
                        <th class="text-right py-3 px-5 font-semibold text-gray-600 text-sm">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($user->orders as $order)
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="py-3 px-5"><span class="font-medium text-gray-900">#{{ $order->id }}</span></td>
                            <td class="py-3 px-5 text-sm font-semibold text-blue-600">${{ number_format($order->total, 2) }}</td>
                            <td class="py-3 px-5">
                                @php
                                    $sc = ['completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'pending' => 'bg-amber-50 text-amber-700 border-amber-200', 'cancelled' => 'bg-red-50 text-red-600 border-red-200'];
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium border {{ $sc[$order->status] ?? 'bg-blue-50 text-blue-700 border-blue-200' }}">{{ ucfirst($order->status) }}</span>
                            </td>
                            <td class="py-3 px-5 text-sm text-gray-500">{{ $order->created_at->format('M d, Y') }}</td>
                            <td class="py-3 px-5 text-right">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center">
                                <div class="text-gray-300 mb-2"><i class="fas fa-receipt text-4xl"></i></div>
                                <p class="text-gray-500 text-sm">No orders yet</p>
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
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function deleteUser(id) {
        showDeleteModal('User', id, '/admin/users/' + id, { redirectUrl: '{{ route("admin.users.index") }}' });
    }

    async function toggleBlock(id) {
        try {
            var res = await fetch('/admin/users/' + id + '/toggle-block', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken } });
            var d = await res.json();
            if (d.success) {
                showToast(d.message, 'success');
                setTimeout(function() { if (typeof adminNavigate === 'function') adminNavigate(window.location.href, true); else window.location.reload(); }, 1000);
            } else { showToast(d.message || 'Action failed.', 'error'); }
        } catch (e) { showToast('Network error.', 'error'); }
    }
</script>
@endpush
