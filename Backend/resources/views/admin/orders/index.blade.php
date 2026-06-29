@extends('MainLayout')

@section('title', 'Orders')
@section('page_title', 'Orders')
@section('page_subtitle', 'Manage customer orders')

@section('content')
<div class="space-y-6">

    <!-- Search & Filters -->
    <div class="card p-4">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-[200px]">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search orders..." class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
            </div>
            <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Shipped</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition text-sm font-medium">
                <i class="fas fa-search mr-1"></i> Filter
            </button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 text-gray-500 hover:text-gray-700 transition text-sm font-medium">
                    <i class="fas fa-times mr-1"></i> Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card p-4">
            <div class="flex items-center justify-between mb-1">
                <p class="text-gray-500 text-sm font-medium">Total Orders</p>
                <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-blue-500 text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $totalOrders ?? 0 }}</p>
        </div>
        <div class="card p-4">
            <div class="flex items-center justify-between mb-1">
                <p class="text-gray-500 text-sm font-medium">Pending</p>
                <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center">
                    <i class="fas fa-clock text-amber-500 text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-amber-600">{{ $pendingOrders ?? 0 }}</p>
        </div>
        <div class="card p-4">
            <div class="flex items-center justify-between mb-1">
                <p class="text-gray-500 text-sm font-medium">Completed</p>
                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                    <i class="fas fa-check-circle text-emerald-500 text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-emerald-600">{{ $completedOrders ?? 0 }}</p>
        </div>
        <div class="card p-4">
            <div class="flex items-center justify-between mb-1">
                <p class="text-gray-500 text-sm font-medium">Total Revenue</p>
                <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-purple-500 text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-purple-600">${{ number_format($totalRevenue ?? 0, 2) }}</p>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full" id="orders-table">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left py-3 px-4 w-10"><input type="checkbox" class="bulk-select-all rounded border-gray-300"></th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-600 text-sm">#</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-600 text-sm hide-tablet">Customer</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-600 text-sm">Total</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-600 text-sm">Status</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-600 text-sm hide-tablet">Payment</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-600 text-sm">Date</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-600 text-sm">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders ?? [] as $order)
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="py-3 px-4"><input type="checkbox" class="bulk-checkbox rounded border-gray-300" data-id="{{ $order->id }}"></td>
                            <td class="py-3 px-4 text-sm text-gray-500 font-medium">#{{ $orders->firstItem() + $loop->index }}</td>
                            <td class="py-3 px-4 hide-tablet">
                                <p class="text-sm font-medium text-gray-900">{{ $order->customer_name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500">{{ $order->customer_email ?? '' }}</p>
                            </td>
                            <td class="py-3 px-4 text-sm font-semibold text-gray-900">${{ number_format($order->total ?? 0, 2) }}</td>
                            <td class="py-3 px-4">
                                @php
                                    $statusColors = ['pending' => 'bg-amber-50 text-amber-700 border-amber-200', 'processing' => 'bg-blue-50 text-blue-700 border-blue-200', 'shipped' => 'bg-indigo-50 text-indigo-700 border-indigo-200', 'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'cancelled' => 'bg-red-50 text-red-600 border-red-200'];
                                    $statusIcons = ['pending' => 'fa-clock', 'processing' => 'fa-spinner', 'shipped' => 'fa-truck', 'completed' => 'fa-check-circle', 'cancelled' => 'fa-times-circle'];
                                @endphp
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium border {{ $statusColors[$order->status] ?? 'bg-gray-50 text-gray-600 border-gray-200' }}">
                                    <i class="fas {{ $statusIcons[$order->status] ?? 'fa-circle' }} text-[10px]"></i>
                                    {{ ucfirst($order->status ?? 'Unknown') }}
                                </span>
                            </td>
                            <td class="py-3 px-4 hide-tablet">
                                @php $ps = $order->payment_status ?? 'unpaid'; @endphp
                                @if($ps === 'verified')
                                    <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-full text-xs font-medium border border-emerald-200">Verified</span>
                                @elseif($ps === 'pending_verification')
                                    <span class="px-2.5 py-1 bg-amber-50 text-amber-700 rounded-full text-xs font-medium border border-amber-200">Pending</span>
                                @elseif($ps === 'failed')
                                    <span class="px-2.5 py-1 bg-red-50 text-red-600 rounded-full text-xs font-medium border border-red-200">Failed</span>
                                @else
                                    <span class="px-2.5 py-1 bg-gray-100 text-gray-500 rounded-full text-xs font-medium border border-gray-200">Unpaid</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-500">{{ $order->created_at->format('M d, Y') }}</td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.orders.edit', $order->id) }}" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit Status">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-16 text-center">
                                <div class="text-gray-300 mb-3"><i class="fas fa-shopping-cart text-5xl"></i></div>
                                <p class="text-gray-500 font-medium">No orders yet</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(isset($orders) && method_exists($orders, 'links') && $orders->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">{{ $orders->links() }}</div>
        @endif
    </div>
</div>

@push('scripts')
<script>
</script>
@endpush

<script>initBulk('orders-table', '{{ route("admin.orders.bulk-destroy") }}');</script>
@endsection
