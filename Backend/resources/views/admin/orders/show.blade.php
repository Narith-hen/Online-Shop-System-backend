@extends('MainLayout')

@section('title', 'Order #' . $order->id)
@section('page_title', 'Order #' . $order->id)
@section('page_subtitle', 'Order details and items')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-blue-600 transition">
            <i class="fas fa-arrow-left"></i> Back to Orders
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.orders.edit', $order->id) }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                <i class="fas fa-edit"></i> Edit Status
            </a>
            <button onclick="deleteOrder({{ $order->id }})" class="inline-flex items-center gap-2 bg-white border border-red-200 text-red-600 hover:bg-red-50 px-4 py-2 rounded-lg text-sm font-medium transition">
                <i class="fas fa-trash"></i> Delete
            </button>
        </div>
    </div>

    <!-- Info Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Summary -->
        <div class="card p-5">
            <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-receipt text-blue-500"></i> Order Summary
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500 text-sm">Order ID</span>
                    <span class="font-semibold text-gray-900">#{{ $order->id }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500 text-sm">Date</span>
                    <span class="font-semibold text-gray-900 text-sm">{{ $order->created_at->format('M d, Y h:i A') }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500 text-sm">Status</span>
                    @php
                        $sc = ['pending' => 'bg-amber-50 text-amber-700 border-amber-200', 'processing' => 'bg-blue-50 text-blue-700 border-blue-200', 'shipped' => 'bg-indigo-50 text-indigo-700 border-indigo-200', 'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'cancelled' => 'bg-red-50 text-red-600 border-red-200'];
                        $si = ['pending' => 'fa-clock', 'processing' => 'fa-spinner', 'shipped' => 'fa-truck', 'completed' => 'fa-check-circle', 'cancelled' => 'fa-times-circle'];
                    @endphp
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border {{ $sc[$order->status] ?? 'bg-gray-100 text-gray-600 border-gray-200' }}">
                        <i class="fas {{ $si[$order->status] ?? 'fa-circle' }}"></i>
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-500 text-sm">Total</span>
                    <span class="font-bold text-lg text-blue-600">${{ number_format($order->total, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="card p-5">
            <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-user text-purple-500"></i> Customer
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500 text-sm">Name</span>
                    <span class="font-semibold text-gray-900 text-sm">{{ $order->customer_name ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500 text-sm">Email</span>
                    <span class="font-semibold text-gray-900 text-sm">{{ $order->customer_email ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500 text-sm">User ID</span>
                    <span class="font-semibold text-gray-900 text-sm">#{{ $order->user_id }}</span>
                </div>
                @if($order->phone)
                <div class="flex justify-between py-2">
                    <span class="text-gray-500 text-sm">Phone</span>
                    <span class="font-semibold text-gray-900 text-sm">{{ $order->phone }}</span>
                </div>
                @endif
                @if($order->shipping_address)
                <div class="border-t border-gray-100 pt-3 mt-2">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Shipping Address</p>
                    <p class="text-sm text-gray-900">{{ $order->shipping_name ?? 'N/A' }}</p>
                    <p class="text-sm text-gray-600">{{ $order->shipping_address }}</p>
                    @if($order->shipping_city)<p class="text-sm text-gray-600">{{ $order->shipping_city }}{{ $order->shipping_zip ? ', '.$order->shipping_zip : '' }}</p>@endif
                    @if($order->shipping_phone)<p class="text-sm text-gray-600">Phone: {{ $order->shipping_phone }}</p>@endif
                </div>
                @endif
            </div>
        </div>

        <!-- Timeline -->
        <div class="card p-5">
            <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-clock text-amber-500"></i> Timeline
            </h3>
            <div class="space-y-0 relative">
                <div class="absolute left-[11px] top-3 bottom-3 w-0.5 bg-gray-200"></div>

                <div class="flex items-start gap-3 pb-4 relative">
                    <div class="w-6 h-6 rounded-full bg-emerald-500 flex items-center justify-center shrink-0 mt-0.5 ring-2 ring-white z-10">
                        <i class="fas fa-check text-white text-[10px]"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">Order Placed</p>
                        <p class="text-xs text-gray-500">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>

                @if($order->status === 'cancelled')
                    <div class="flex items-start gap-3 pb-4 relative">
                        <div class="w-6 h-6 rounded-full bg-red-500 flex items-center justify-center shrink-0 mt-0.5 ring-2 ring-white z-10">
                            <i class="fas fa-times text-white text-[10px]"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Cancelled</p>
                            <p class="text-xs text-gray-500">{{ $order->updated_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                @else
                    @foreach(['processing', 'shipped', 'completed'] as $step)
                        <div class="flex items-start gap-3 pb-4 relative">
                            <div class="w-6 h-6 rounded-full flex items-center justify-center shrink-0 mt-0.5 ring-2 ring-white z-10
                                {{ in_array($order->status, ['processing', 'shipped', 'completed']) && in_array($step, ['processing', 'shipped', 'completed']) && array_search($step, ['processing', 'shipped', 'completed']) <= array_search($order->status, ['processing', 'shipped', 'completed']) ? 'bg-emerald-500' : 'bg-gray-300' }}">
                                @if(in_array($order->status, ['processing', 'shipped', 'completed']) && in_array($step, ['processing', 'shipped', 'completed']) && array_search($step, ['processing', 'shipped', 'completed']) <= array_search($order->status, ['processing', 'shipped', 'completed']))
                                    <i class="fas fa-check text-white text-[10px]"></i>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-semibold {{ in_array($order->status, ['processing', 'shipped', 'completed']) && in_array($step, ['processing', 'shipped', 'completed']) && array_search($step, ['processing', 'shipped', 'completed']) <= array_search($order->status, ['processing', 'shipped', 'completed']) ? 'text-gray-900' : 'text-gray-400' }}">
                                    {{ ucfirst($step) }}
                                </p>
                                @if($step === $order->status)
                                    <p class="text-xs text-emerald-600 font-medium">Current</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <!-- Payment Proof -->
    @if($order->payment_proof)
    <div class="card overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-credit-card text-blue-500"></i> Payment Proof
            </h3>
        </div>
        <div class="p-5">
            <div class="flex flex-col md:flex-row gap-6 items-start">
                <div class="bg-gray-50 rounded-xl p-3 border border-gray-200">
                    <img src="{{ $order->payment_proof_url }}" alt="Payment Proof" class="w-72 h-auto object-contain rounded-lg cursor-pointer transition-transform hover:scale-[1.02]" onclick="openImagePreview('{{ $order->payment_proof_url }}')">
                </div>
                <div class="flex-1 space-y-3">
                    <div class="flex justify-between py-2 border-b border-gray-50">
                        <span class="text-gray-500 text-sm">Payment Method</span>
                        <span class="font-semibold text-gray-900 text-sm">ABA KHQR</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-50">
                        <span class="text-gray-500 text-sm">Payment Status</span>
                        @php
                            $psc = ['verified' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'pending_verification' => 'bg-blue-50 text-blue-700 border-blue-200', 'failed' => 'bg-red-50 text-red-600 border-red-200', 'unpaid' => 'bg-gray-100 text-gray-500 border-gray-200'];
                        @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium border {{ $psc[$order->payment_status] ?? 'bg-gray-100 text-gray-600 border-gray-200' }}">
                            {{ ucfirst(str_replace('_', ' ', $order->payment_status ?? 'unpaid')) }}
                        </span>
                    </div>
                    @if($order->payment_status === 'pending_verification')
                    <div class="flex gap-3 pt-3">
                        <form method="POST" action="{{ route('admin.orders.verify-payment', $order->id) }}" onsubmit="return confirm('Verify this payment?')">
                            @csrf
                            <input type="hidden" name="payment_status" value="verified">
                            <button type="submit" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                                <i class="fas fa-check"></i> Verify Payment
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.orders.verify-payment', $order->id) }}" onsubmit="return confirm('Reject this payment?')">
                            @csrf
                            <input type="hidden" name="payment_status" value="failed">
                            <button type="submit" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                                <i class="fas fa-times"></i> Reject Payment
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Image Preview Modal -->
    <div id="image-preview-modal" class="modal-overlay">
        <div class="modal-backdrop" onclick="closeImagePreview()"></div>
        <div class="relative max-w-3xl mx-auto m-4" style="max-height: 90vh;">
            <button onclick="closeImagePreview()" class="absolute top-3 right-3 w-10 h-10 bg-white/90 rounded-full flex items-center justify-center shadow-lg z-10 hover:bg-white transition">
                <i class="fas fa-times text-gray-700"></i>
            </button>
            <img id="preview-image" src="" alt="Payment Proof" class="w-full h-auto rounded-xl shadow-2xl">
        </div>
    </div>

    <!-- Order Items -->
    <div class="card overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-boxes text-indigo-500"></i> Order Items
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left py-3 px-5 font-semibold text-gray-600 text-sm">Product</th>
                        <th class="text-left py-3 px-5 font-semibold text-gray-600 text-sm">Price</th>
                        <th class="text-left py-3 px-5 font-semibold text-gray-600 text-sm">Qty</th>
                        <th class="text-right py-3 px-5 font-semibold text-gray-600 text-sm">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($order->items as $item)
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="py-3 px-5">
                                <p class="text-sm font-medium text-gray-900">{{ $item->product->name ?? 'Product #'.$item->product_id }}</p>
                                <p class="text-xs text-gray-500">ID: {{ $item->product_id }}</p>
                            </td>
                            <td class="py-3 px-5 text-sm text-gray-600">${{ number_format($item->price, 2) }}</td>
                            <td class="py-3 px-5 text-sm text-gray-600">{{ $item->quantity }}</td>
                            <td class="py-3 px-5 text-sm font-semibold text-gray-900 text-right">${{ number_format($item->price * $item->quantity, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-10 text-center">
                                <div class="text-gray-300 mb-2"><i class="fas fa-box-open text-4xl"></i></div>
                                <p class="text-gray-500 text-sm">No items in this order</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50">
                        <td colspan="3" class="py-3 px-5 text-right font-semibold text-gray-700">Total:</td>
                        <td class="py-3 px-5 text-right font-bold text-lg text-blue-600">${{ number_format($order->total, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
function openImagePreview(url) {
    document.getElementById('preview-image').src = url;
    document.getElementById('image-preview-modal').classList.add('active');
}
function closeImagePreview() {
    document.getElementById('image-preview-modal').classList.remove('active');
}
</script>

@endsection

@push('scripts')
<script>
    function deleteOrder(id) {
        showDeleteModal('Order', id, '/admin/orders/' + id, { redirectUrl: '{{ route("admin.orders.index") }}' });
    }
</script>
@endpush
