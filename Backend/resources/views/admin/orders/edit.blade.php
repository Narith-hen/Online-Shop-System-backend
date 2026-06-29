@extends('MainLayout')

@section('title', 'Edit Order #' . $order->id)
@section('page_title', 'Edit Order #' . $order->id)
@section('page_subtitle', 'Update order status')

@section('content')
<div class="space-y-6">

    <a href="{{ route('admin.orders.show', $order->id) }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-blue-600 transition">
        <i class="fas fa-arrow-left"></i> Back to Order
    </a>

    <div class="card p-6 max-w-2xl">
        <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
            <i class="fas fa-edit text-blue-500"></i> Update Order Status
        </h3>

        <div id="edit-errors"></div>
        <form id="edit-form" onsubmit="submitEditForm(event)" class="space-y-5">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-3 p-4 bg-gray-50 rounded-lg">
                    <div class="flex justify-between">
                        <span class="text-gray-500 text-sm">Order ID</span>
                        <span class="font-semibold text-gray-900">#{{ $order->id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 text-sm">Customer</span>
                        <span class="font-semibold text-gray-900 text-sm">{{ $order->customer_name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 text-sm">Total</span>
                        <span class="font-bold text-blue-600">${{ number_format($order->total, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 text-sm">Placed on</span>
                        <span class="font-semibold text-gray-900 text-sm">{{ $order->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 text-sm">Current</span>
                        @php
                            $sc = ['pending' => 'bg-amber-50 text-amber-700 border-amber-200', 'processing' => 'bg-blue-50 text-blue-700 border-blue-200', 'shipped' => 'bg-indigo-50 text-indigo-700 border-indigo-200', 'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'cancelled' => 'bg-red-50 text-red-600 border-red-200'];
                        @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium border {{ $sc[$order->status] ?? 'bg-gray-100 text-gray-600 border-gray-200' }}">{{ ucfirst($order->status) }}</span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">New Status <span class="text-red-500">*</span></label>
                    <select name="status" id="edit-status" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" required>
                        <option value="pending" @selected(old('status', $order->status) === 'pending')>Pending</option>
                        <option value="processing" @selected(old('status', $order->status) === 'processing')>Processing</option>
                        <option value="shipped" @selected(old('status', $order->status) === 'shipped')>Shipped</option>
                        <option value="completed" @selected(old('status', $order->status) === 'completed')>Completed</option>
                        <option value="cancelled" @selected(old('status', $order->status) === 'cancelled')>Cancelled</option>
                    </select>
                    @if($order->payment_status === 'pending_verification')
                    <div class="mt-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Payment Status</label>
                        <select name="payment_status" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="">Keep current</option>
                            <option value="verified">Verified</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
                <button type="submit" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2.5 rounded-lg text-sm transition shadow-sm">
                    <i class="fas fa-save"></i> Update Status
                </button>
                <a href="{{ route('admin.orders.show', $order->id) }}" class="px-5 py-2.5 text-gray-600 hover:text-gray-800 font-medium text-sm">Cancel</a>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function showErrors(containerId, errors) {
        var c = document.getElementById(containerId);
        var h = '<div class="p-3 bg-red-50 border border-red-200 rounded-lg mb-4"><ul class="list-disc list-inside text-sm text-red-700">';
        for (var k in errors) { (Array.isArray(errors[k]) ? errors[k] : [errors[k]]).forEach(function(m) { h += '<li>' + m + '</li>'; }); }
        c.innerHTML = h + '</ul></div>';
    }

    async function submitEditForm(e) {
        e.preventDefault();
        document.getElementById('edit-errors').innerHTML = '';
        var fd = new FormData(document.getElementById('edit-form'));
        fd.append('_method', 'PUT');
        try {
            var res = await fetch('{{ route("admin.orders.update", $order->id) }}', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }, body: fd });
            if (res.ok) {
                showToast('Order status updated successfully.', 'success');
                setTimeout(function() {
                    if (typeof adminNavigate === 'function') adminNavigate('{{ route("admin.orders.index") }}');
                    else window.location.href = '{{ route("admin.orders.index") }}';
                }, 1000);
            } else {
                var d = await res.json();
                showErrors('edit-errors', d.errors || { general: [d.message || 'Something went wrong.'] });
            }
        } catch (err) { showErrors('edit-errors', { general: ['Network error.'] }); }
    }
</script>
@endpush
