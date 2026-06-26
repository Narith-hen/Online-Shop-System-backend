@extends('MainLayout')

@section('title', $user->name)
@section('page_title', $user->name)
@section('page_subtitle', 'User details and order history')

@section('content')

<div class="space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
            <i class="fas fa-arrow-left mr-1"></i> Back to Users
        </a>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.edit', $user->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                <i class="fas fa-edit mr-1"></i> Edit User
            </a>
            <button onclick="deleteUser({{ $user->id }})" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                <i class="fas fa-trash mr-1"></i> Delete
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">User Summary</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">User ID</span>
                    <span class="font-semibold text-gray-900">{{ $user->code }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">Name</span>
                    <span class="font-semibold text-gray-900">{{ $user->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">Email</span>
                    <span class="font-semibold text-gray-900">{{ $user->email }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">Role</span>
                    @if($user->isAdmin())
                        <span class="px-3 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-700">Admin</span>
                    @else
                        <span class="px-3 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-700">Customer</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Account Info</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">Provider</span>
                    <span class="font-semibold text-gray-900">{{ $user->provider ? ucfirst($user->provider) : 'Email' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">Registered</span>
                    <span class="font-semibold text-gray-900">{{ $user->created_at->format('M d, Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">Total Orders</span>
                    <span class="font-bold text-lg text-blue-600">{{ $user->orders->count() }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Avatar</h3>
            <div class="flex flex-col items-center justify-center h-40">
                @if($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-24 h-24 object-cover rounded-full border-2 border-gray-200">
                @elseif($user->provider_avatar)
                    <img src="{{ $user->provider_avatar }}" alt="{{ $user->name }}" class="w-24 h-24 object-cover rounded-full border-2 border-gray-200">
                @else
                    <div class="w-24 h-24 rounded-full bg-blue-600 flex items-center justify-center">
                        <span class="text-white text-3xl font-bold">{{ substr($user->name, 0, 1) }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-800">Recent Orders</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="text-left py-3 px-6 font-semibold text-gray-700 text-sm">Order ID</th>
                        <th class="text-left py-3 px-6 font-semibold text-gray-700 text-sm">Total</th>
                        <th class="text-left py-3 px-6 font-semibold text-gray-700 text-sm">Status</th>
                        <th class="text-left py-3 px-6 font-semibold text-gray-700 text-sm">Date</th>
                        <th class="text-right py-3 px-6 font-semibold text-gray-700 text-sm">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($user->orders as $order)
                        <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                            <td class="py-4 px-6">
                                <span class="font-medium text-gray-900">#{{ $order->id }}</span>
                            </td>
                            <td class="py-4 px-6 text-sm font-semibold text-blue-600">${{ number_format($order->total, 2) }}</td>
                            <td class="py-4 px-6">
                                @if($order->status === 'completed')
                                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Completed</span>
                                @elseif($order->status === 'pending')
                                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700">Pending</span>
                                @elseif($order->status === 'cancelled')
                                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">Cancelled</span>
                                @else
                                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-700">{{ ucfirst($order->status) }}</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-sm text-gray-500">{{ $order->created_at->format('M d, Y') }}</td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    <i class="fas fa-eye mr-1"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 px-6 text-center text-gray-500">
                                <i class="fas fa-receipt text-4xl mb-3"></i>
                                <p class="text-sm">No orders yet</p>
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

    async function deleteUser(id) {
        if (!confirm('Are you sure you want to delete this user?')) return;
        try {
            const res = await fetch(`/admin/users/${id}`, {
                method: 'DELETE',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
            });
            if (res.ok) {
                showToast('User deleted successfully.', 'success');
                setTimeout(() => { window.location.href = '{{ route("admin.users.index") }}'; }, 1000);
            } else {
                const d = await res.json();
                showToast(d.message || 'Delete failed.', 'error');
            }
        } catch (e) { showToast('Network error.', 'error'); }
    }
</script>
@endpush
