@extends('MainLayout')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Welcome back! Here\'s what\'s happening in your store')

@section('content')

<!-- Top Metric Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <a href="{{ route('admin.products.index') }}" class="group card p-5 transition-all hover:shadow-md">
        <div class="flex items-center justify-between mb-2">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Products</p>
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-sm">
                <i class="fas fa-box text-white text-sm"></i>
            </div>
        </div>
        <p class="text-2xl font-extrabold text-gray-900">{{ $totalProducts }}</p>
        <p class="text-xs text-gray-400 mt-0.5">{{ $activeProducts }} active</p>
    </a>

    <a href="{{ route('admin.orders.index') }}" class="group card p-5 transition-all hover:shadow-md">
        <div class="flex items-center justify-between mb-2">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Orders</p>
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-sm">
                <i class="fas fa-shopping-cart text-white text-sm"></i>
            </div>
        </div>
        <p class="text-2xl font-extrabold text-gray-900">{{ $totalOrders }}</p>
        <p class="text-xs text-gray-400 mt-0.5">{{ $pendingOrders }} pending</p>
    </a>

    <div class="card p-5 transition-all hover:shadow-md">
        <div class="flex items-center justify-between mb-2">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Revenue</p>
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center shadow-sm">
                <i class="fas fa-dollar-sign text-white text-sm"></i>
            </div>
        </div>
        <p class="text-2xl font-extrabold text-gray-900">${{ number_format($totalRevenue, 2) }}</p>
        <p class="text-xs text-gray-400 mt-0.5">all time</p>
    </div>

    <div class="card p-5 transition-all hover:shadow-md">
        <div class="flex items-center justify-between mb-2">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Avg Order</p>
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-rose-500 to-rose-600 flex items-center justify-center shadow-sm">
                <i class="fas fa-chart-bar text-white text-sm"></i>
            </div>
        </div>
        <p class="text-2xl font-extrabold text-gray-900">
            ${{ $totalOrders > 0 ? number_format($totalRevenue / $totalOrders, 2) : '0.00' }}
        </p>
        <p class="text-xs text-gray-400 mt-0.5">per order</p>
    </div>
</div>

<!-- Charts + Recent Orders -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <!-- Revenue Chart -->
        <div class="card p-5 lg:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
                <div>
                    <h3 class="text-base lg:text-lg font-bold text-gray-900">Revenue</h3>
                    <p class="text-xs text-gray-400 mt-0.5" id="revenueSubtitle">Daily revenue for the last 14 days</p>
                </div>
                <div class="relative" id="revenueDropdown">
                    <button id="revDropdownBtn" class="px-4 py-1.5 text-sm font-semibold rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 flex items-center gap-2 transition-colors">
                        <span id="revDropdownLabel">Daily</span>
                        <i class="fas fa-chevron-down text-xs text-gray-500"></i>
                    </button>
                    <div id="revDropdownMenu" class="absolute right-0 mt-1.5 w-36 bg-white rounded-xl shadow-lg border border-gray-100 py-1 hidden z-10">
                        <button data-view="monthly" class="revDropdownItem w-full text-left px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-50">Monthly</button>
                        <button data-view="weekly" class="revDropdownItem w-full text-left px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-50">Weekly</button>
                        <button data-view="daily" class="revDropdownItem w-full text-left px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-50">Daily</button>
                    </div>
                </div>
            </div>
            <div class="h-[240px] lg:h-[300px] relative">
                <canvas id="revenueChart"></canvas>
                <div id="revenueEmpty" class="absolute inset-0 flex items-center justify-center text-gray-400 text-sm hidden">No revenue data available for this period.</div>
            </div>
        </div>

        <!-- Products by Category -->
        <div class="card p-5 lg:p-6">
            <div class="mb-3">
                <h3 class="text-base lg:text-lg font-bold text-gray-900">Products by Category</h3>
                <p class="text-xs text-gray-400 mt-0.5">Distribution across categories</p>
            </div>
            <div class="h-[200px] lg:h-[240px] flex items-center justify-center">
                <canvas id="productsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="lg:col-span-1">
        <div class="card h-full flex flex-col overflow-hidden">
            <div class="flex items-center justify-between px-5 lg:px-6 py-4 border-b border-gray-100 shrink-0">
                <div>
                    <h3 class="text-base lg:text-lg font-bold text-gray-900">Recent Orders</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Latest {{ $recentOrders->count() }} orders</p>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 transition-colors flex items-center gap-1.5 shrink-0">
                    View All <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>

            <div class="overflow-y-auto flex-1">
                <table class="w-full text-sm">
                    <thead class="sticky top-0 bg-white">
                        <tr class="border-b border-gray-50">
                            <th class="text-left py-3 px-4 font-bold text-gray-500 text-xs uppercase tracking-wider">Order</th>
                            <th class="text-left py-3 px-4 font-bold text-gray-500 text-xs uppercase tracking-wider">Customer</th>
                            <th class="text-left py-3 px-4 font-bold text-gray-500 text-xs uppercase tracking-wider">Amount</th>
                            <th class="text-left py-3 px-4 font-bold text-gray-500 text-xs uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($recentOrders as $order)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="py-2.5 px-4"><span class="font-semibold text-gray-900">#{{ $order->id }}</span></td>
                                <td class="py-2.5 px-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center flex-shrink-0">
                                            <span class="text-[10px] font-bold text-gray-500">{{ substr($order->customer_name ?? 'U', 0, 1) }}</span>
                                        </div>
                                        <span class="text-gray-700 text-xs truncate max-w-[100px]">{{ $order->customer_name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="py-2.5 px-4 font-semibold text-gray-900 text-sm">${{ number_format($order->total, 2) }}</td>
                                <td class="py-2.5 px-4">
                                    @php
                                        $sc = ['completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'pending' => 'bg-amber-50 text-amber-700 border-amber-200', 'cancelled' => 'bg-red-50 text-red-600 border-red-200'];
                                        $sd = ['completed' => 'Done', 'pending' => 'Pending', 'cancelled' => 'Cancelled'];
                                    @endphp
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $sc[$order->status] ?? 'bg-blue-50 text-blue-700 border-blue-200' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $order->status === 'completed' ? 'bg-emerald-500' : ($order->status === 'pending' ? 'bg-amber-500' : ($order->status === 'cancelled' ? 'bg-red-500' : 'bg-blue-500')) }}"></span>
                                        {{ $sd[$order->status] ?? ucfirst($order->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-16 text-center text-gray-400">
                                    <i class="fas fa-inbox text-3xl mb-2 block text-gray-300"></i>
                                    <p class="text-sm font-medium">No orders yet</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<style>
    canvas { display: block; width: 100% !important; height: 100% !important; }
</style>
<script>
var _revLabels = {
    monthly: {!! json_encode($monthLabels) !!},
    weekly: {!! json_encode($weekLabels) !!},
    daily: {!! json_encode($dayLabels) !!}
};
var _revData = {
    monthly: {!! json_encode($monthlySales) !!},
    weekly: {!! json_encode($weeklySales) !!},
    daily: {!! json_encode($dailySales) !!}
};
var _revSubtitles = {
    monthly: 'Monthly revenue for the last 7 months',
    weekly: 'Weekly revenue for the last 8 weeks',
    daily: 'Daily revenue for the last 14 days'
};

var _revenueChart = null;

function fmt$(v) { return '$' + Number(v).toFixed(2); }

function renderRevenue(view) {
    var subEl = document.getElementById('revenueSubtitle');
    if (subEl) subEl.textContent = _revSubtitles[view];
    var labelMap = { monthly: 'Monthly', weekly: 'Weekly', daily: 'Daily' };
    var labelEl = document.getElementById('revDropdownLabel');
    if (labelEl) labelEl.textContent = labelMap[view] || 'Daily';
    var canvas = document.getElementById('revenueChart');
    if (!canvas) return;
    var labels = _revLabels[view] || [];
    var data = _revData[view] || [];
    var isDaily = view === 'daily';
    var emptyEl = document.getElementById('revenueEmpty');
    var hasData = data.some(function(v) { return v > 0; });
    if (!hasData) {
        if (_revenueChart) { _revenueChart.destroy(); _revenueChart = null; }
        canvas.style.display = 'none';
        if (emptyEl) emptyEl.classList.remove('hidden');
        return;
    }
    canvas.style.display = 'block';
    if (emptyEl) emptyEl.classList.add('hidden');
    if (_revenueChart) _revenueChart.destroy();
    var ctx = canvas.getContext('2d');
    var maxVal = Math.max.apply(null, data);
    if (maxVal === -Infinity) maxVal = 1;
    var bgColor = isDaily ? data.map(function(v) {
        if (v === 0) return 'rgba(229, 231, 235, 0.6)';
        var r = v / maxVal;
        if (r >= 0.7) return 'rgba(16, 185, 129, 0.85)';
        return 'rgba(59, 130, 246, 0.8)';
    }) : undefined;
    var gradient = null;
    if (!isDaily) {
        gradient = ctx.createLinearGradient(0, 0, 0, 340);
        gradient.addColorStop(0, 'rgba(59, 130, 246, 0.20)');
        gradient.addColorStop(1, 'rgba(59, 130, 246, 0.01)');
    }
    _revenueChart = new Chart(ctx, {
        type: isDaily ? 'bar' : 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Revenue',
                data: data,
                borderColor: '#3b82f6',
                backgroundColor: isDaily ? bgColor : gradient,
                fill: !isDaily,
                tension: isDaily ? 0 : 0.35,
                pointBackgroundColor: '#3b82f6',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: isDaily ? 0 : 4,
                pointHoverRadius: 7,
                borderWidth: isDaily ? 0 : 2.5,
                borderRadius: isDaily ? 6 : 0,
                barPercentage: isDaily ? 0.65 : undefined,
                categoryPercentage: isDaily ? 0.8 : undefined,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 400 },
            interaction: { intersect: false, mode: 'index' },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1f2937',
                    padding: 14,
                    cornerRadius: 10,
                    displayColors: false,
                    titleFont: { size: 13, weight: '600' },
                    bodyFont: { size: 14, weight: '700' },
                    callbacks: {
                        title: function(items) { return items[0].label; },
                        label: function(c) {
                            var v = parseFloat(c.raw);
                            if (isDaily) {
                                var level = v === 0 ? 'No sales' : (v / maxVal >= 0.7 ? 'High sales' : (v / maxVal >= 0.4 ? 'Medium sales' : 'Low sales'));
                                return '  ' + level + ': ' + fmt$(v);
                            }
                            return '  ' + fmt$(v);
                        }
                    }
                }
            },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#9ca3af', font: { size: 11, weight: '500' }, maxRotation: isDaily ? 45 : 0 } },
                y: { beginAtZero: true, grid: { color: 'rgba(229, 231, 235, 0.4)', drawBorder: false }, border: { display: false }, ticks: { color: '#9ca3af', font: { size: 11 }, padding: 8, callback: function(v) { return '$' + v; } } }
            }
        }
    });
}

function initDashboard() {
    if (typeof Chart === 'undefined') {
        var rc = document.getElementById('revenueChart');
        if (rc) rc.parentElement.innerHTML = '<p class="text-gray-400 text-sm text-center w-full py-16">Chart library failed to load.</p>';
        return;
    }
    var ddBtn = document.getElementById('revDropdownBtn');
    var ddMenu = document.getElementById('revDropdownMenu');
    if (ddBtn && ddMenu) {
        ddBtn.onclick = function(e) { e.stopPropagation(); ddMenu.classList.toggle('hidden'); };
        ddMenu.onclick = function(e) {
            var item = e.target.closest('.revDropdownItem');
            if (item) { renderRevenue(item.getAttribute('data-view')); ddMenu.classList.add('hidden'); }
        };
        document.addEventListener('click', function() { ddMenu.classList.add('hidden'); });
    }
    renderRevenue('daily');

    var catData = {!! json_encode($productCategories) !!};
    var pcCanvas = document.getElementById('productsChart');
    if (catData && catData.length > 0 && pcCanvas) {
        var ep = Chart.getChart(pcCanvas); if (ep) ep.destroy();
        var colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#06b6d4', '#84cc16', '#f97316', '#6366f1'];
        new Chart(pcCanvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: catData.map(function(p) { return p.category; }),
                datasets: [{ data: catData.map(function(p) { return p.count; }), backgroundColor: colors.slice(0, catData.length), borderWidth: 3, borderColor: '#fff', hoverOffset: 8 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '72%', animation: { animateRotate: true, duration: 600 },
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 12, usePointStyle: true, pointStyle: 'circle', font: { size: 11, weight: '500' }, color: '#6b7280' } },
                    tooltip: { backgroundColor: '#1f2937', padding: 12, cornerRadius: 8, callbacks: { label: function(c) { var t = c.dataset.data.reduce(function(a,b){return a+b},0); return ' ' + c.label + ': ' + c.raw + ' (' + ((c.raw/t)*100).toFixed(1) + '%)'; } } }
                }
            }
        });
    } else if (pcCanvas) {
        pcCanvas.parentElement.innerHTML = '<p class="text-gray-400 text-sm text-center w-full">No product data</p>';
    }
}

if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initDashboard);
else initDashboard();
</script>
@endpush
