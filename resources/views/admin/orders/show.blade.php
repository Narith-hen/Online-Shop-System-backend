@extends('MainLayout')

@section('title', 'Order #' . $order->id)
@section('page_title', 'Order #' . $order->id)
@section('page_subtitle', 'Order details and items')

@section('content')

<div class="space-y-6">

    <!-- Back Link & Actions -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.orders.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
            <i class="fas fa-arrow-left mr-1"></i> Back to Orders
        </a>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.orders.edit', $order->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                <i class="fas fa-edit mr-1"></i> Edit Status
            </a>
            <button onclick="deleteOrder({{ $order->id }})" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                    <i class="fas fa-trash mr-1"></i> Delete
                </button>
        </div>
    </div>

    <!-- Order Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Order Summary -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Order Summary</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">Order ID</span>
                    <span class="font-semibold text-gray-900">#{{ $order->id }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">Date</span>
                    <span class="font-semibold text-gray-900">{{ $order->created_at->format('M d, Y h:i A') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">Status</span>
                    @if($order->status === 'completed')
                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-medium">Completed</span>
                    @elseif($order->status === 'pending')
                        <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-medium">Pending</span>
                    @elseif($order->status === 'cancelled')
                        <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-medium">Cancelled</span>
                    @else
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-medium">{{ ucfirst($order->status) }}</span>
                    @endif
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">Total</span>
                    <span class="font-bold text-lg text-blue-600">${{ number_format($order->total, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Customer</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">Name</span>
                    <span class="font-semibold text-gray-900">{{ $order->customer_name ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">Email</span>
                    <span class="font-semibold text-gray-900">{{ $order->customer_email ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">User ID</span>
                    <span class="font-semibold text-gray-900">#{{ $order->user_id }}</span>
                </div>
            </div>
        </div>

        <!-- Status Timeline -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Timeline</h3>
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <div class="w-3 h-3 rounded-full bg-green-500"></div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">Order Placed</p>
                        <p class="text-xs text-gray-500">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
                @if($order->status === 'cancelled')
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-red-500"></div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Cancelled</p>
                            <p class="text-xs text-gray-500">{{ $order->updated_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full {{ $order->status !== 'pending' ? 'bg-green-500' : 'bg-gray-300' }}"></div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Processing</p>
                            @if($order->status !== 'pending')
                                <p class="text-xs text-gray-500">Started</p>
                            @else
                                <p class="text-xs text-gray-500">Waiting</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full {{ in_array($order->status, ['shipped', 'completed']) ? 'bg-green-500' : 'bg-gray-300' }}"></div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Shipped</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full {{ $order->status === 'completed' ? 'bg-green-500' : 'bg-gray-300' }}"></div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Delivered</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Order Items -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-800">Order Items</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="text-left py-3 px-6 font-semibold text-gray-700 text-sm">Product</th>
                        <th class="text-left py-3 px-6 font-semibold text-gray-700 text-sm">Price</th>
                        <th class="text-left py-3 px-6 font-semibold text-gray-700 text-sm">Quantity</th>
                        <th class="text-right py-3 px-6 font-semibold text-gray-700 text-sm">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($order->items as $item)
                        <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                            <td class="py-4 px-6">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $item->product->name ?? 'Product #' . $item->product_id }}</p>
                                    <p class="text-xs text-gray-500">ID: {{ $item->product_id }}</p>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-sm text-gray-600">${{ number_format($item->price, 2) }}</td>
                            <td class="py-4 px-6 text-sm text-gray-600">{{ $item->quantity }}</td>
                            <td class="py-4 px-6 text-sm font-semibold text-gray-900 text-right">${{ number_format($item->price * $item->quantity, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 px-6 text-center text-gray-500">
                                <i class="fas fa-box-open text-3xl mb-2"></i>
                                <p class="text-sm mt-2">No items in this order</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50">
                        <td colspan="3" class="py-4 px-6 text-right font-semibold text-gray-700">Total:</td>
                        <td class="py-4 px-6 text-right font-bold text-lg text-blue-600">${{ number_format($order->total, 2) }}</td>
                    </tr>
                </tfoot>
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

    async function deleteOrder(id) {
        if (!confirm('Are you sure you want to delete this order?')) return;
        try {
            const res = await fetch(`/admin/orders/${id}`, {
                method: 'DELETE',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
            });
            if (res.ok) {
                showToast('Order deleted successfully.', 'success');
                setTimeout(() => { window.location.href = '{{ route("admin.orders.index") }}'; }, 1000);
            } else {
                const d = await res.json();
                showToast(d.message || 'Delete failed.', 'error');
            }
        } catch (e) { showToast('Network error.', 'error'); }
    }
</script>
@endpush
