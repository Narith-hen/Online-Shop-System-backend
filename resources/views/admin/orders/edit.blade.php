@extends('MainLayout')

@section('title', 'Edit Order #' . $order->id)
@section('page_title', 'Edit Order #' . $order->id)
@section('page_subtitle', 'Update order status')

@section('content')

<div class="space-y-6">

    <!-- Back Link -->
    <a href="{{ route('admin.orders.show', $order->id) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
        <i class="fas fa-arrow-left mr-1"></i> Back to Order
    </a>

    <div class="bg-white rounded-lg shadow p-6 max-w-3xl">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Update Order Status</h3>

        <div id="edit-errors"></div>
        <form id="edit-form" onsubmit="submitEditForm(event)" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Order Info -->
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-500 text-sm">Order ID</span>
                        <span class="font-semibold text-gray-900">#{{ $order->id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 text-sm">Customer</span>
                        <span class="font-semibold text-gray-900">{{ $order->customer_name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 text-sm">Total</span>
                        <span class="font-bold text-blue-600">${{ number_format($order->total, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 text-sm">Placed on</span>
                        <span class="font-semibold text-gray-900">{{ $order->created_at->format('M d, Y') }}</span>
                    </div>
                </div>

                <!-- Status Select -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="edit-status" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="pending" @selected(old('status', $order->status) === 'pending')>Pending</option>
                        <option value="processing" @selected(old('status', $order->status) === 'processing')>Processing</option>
                        <option value="shipped" @selected(old('status', $order->status) === 'shipped')>Shipped</option>
                        <option value="completed" @selected(old('status', $order->status) === 'completed')>Completed</option>
                        <option value="cancelled" @selected(old('status', $order->status) === 'cancelled')>Cancelled</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-lg">
                    <i class="fas fa-save mr-1"></i> Update Status
                </button>
                <a href="{{ route('admin.orders.show', $order->id) }}" class="text-gray-600 hover:text-gray-900">Cancel</a>
            </div>
        </form>
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

    function showErrors(containerId, errors) {
        const container = document.getElementById(containerId);
        let html = '<div class="p-4 bg-red-100 text-red-700 border border-red-300 rounded-lg mb-4"><ul class="list-disc list-inside text-sm">';
        for (const key in errors) {
            (Array.isArray(errors[key]) ? errors[key] : [errors[key]]).forEach(msg => { html += '<li>' + msg + '</li>'; });
        }
        html += '</ul></div>';
        container.innerHTML = html;
    }

    async function submitEditForm(e) {
        e.preventDefault();
        document.getElementById('edit-errors').innerHTML = '';

        const formData = new FormData(document.getElementById('edit-form'));
        formData.append('_method', 'PUT');

        try {
            const res = await fetch('{{ route("admin.orders.update", $order->id) }}', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: formData
            });

            if (res.ok) {
                showToast('Order status updated successfully.', 'success');
                setTimeout(() => { window.location.href = '{{ route("admin.orders.index") }}'; }, 1000);
            } else {
                const data = await res.json();
                showErrors('edit-errors', data.errors || { general: [data.message || 'Something went wrong.'] });
            }
        } catch (err) {
            showErrors('edit-errors', { general: ['Network error. Please try again.'] });
        }
    }
</script>
@endpush
