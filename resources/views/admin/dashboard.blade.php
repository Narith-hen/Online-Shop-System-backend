@extends('MainLayout')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Welcome back! Here\'s what\'s happening in your store')

@section('content')

<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">

    <!-- Total Products -->
    <a href="{{ route('admin.products.index') }}" class="group bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 p-5 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs uppercase tracking-wider font-semibold">Products</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $totalProducts }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $activeProducts }} active</p>
            </div>
            <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                <i class="fas fa-box text-xl text-white"></i>
            </div>
        </div>
    </a>

    <!-- Categories -->
    <a href="{{ route('admin.categories.index') }}" class="group bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 p-5 border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs uppercase tracking-wider font-semibold">Categories</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $totalCategories }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $activeCategories }} active</p>
            </div>
            <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                <i class="fas fa-tag text-xl text-white"></i>
            </div>
        </div>
    </a>

    <!-- Total Orders -->
    <a href="{{ route('admin.orders.index') }}" class="group bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 p-5 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs uppercase tracking-wider font-semibold">Orders</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $totalOrders }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $pendingOrders }} pending</p>
            </div>
            <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                <i class="fas fa-shopping-cart text-xl text-white"></i>
            </div>
        </div>
    </a>

    <!-- Total Revenue -->
    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 p-5 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs uppercase tracking-wider font-semibold">Revenue</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">${{ number_format($totalRevenue, 2) }}</p>
                <p class="text-xs text-gray-400 mt-1">all time</p>
            </div>
            <div class="w-12 h-12 bg-yellow-500 rounded-xl flex items-center justify-center">
                <i class="fas fa-dollar-sign text-xl text-white"></i>
            </div>
        </div>
    </div>

    <!-- Users -->
    <a href="{{ route('admin.users.index') }}" class="group bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 p-5 border-l-4 border-indigo-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs uppercase tracking-wider font-semibold">Users</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $totalUsers }}</p>
                <p class="text-xs text-gray-400 mt-1">registered</p>
            </div>
            <div class="w-12 h-12 bg-indigo-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                <i class="fas fa-users text-xl text-white"></i>
            </div>
        </div>
    </a>

    <!-- Average Order -->
    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 p-5 border-l-4 border-pink-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs uppercase tracking-wider font-semibold">Avg Order</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">
                    ${{ $totalOrders > 0 ? number_format($totalRevenue / $totalOrders, 2) : '0.00' }}
                </p>
                <p class="text-xs text-gray-400 mt-1">per order</p>
            </div>
            <div class="w-12 h-12 bg-pink-500 rounded-xl flex items-center justify-center">
                <i class="fas fa-chart-bar text-xl text-white"></i>
            </div>
        </div>
    </div>

</div>

<!-- Charts & Top Products Row -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-8">

    <!-- Sales Chart -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Revenue Overview</h3>
                <p class="text-xs text-gray-400 mt-0.5">Monthly revenue for the last 7 months</p>
            </div>
        </div>
        <div class="h-72">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    <!-- Top Products -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Latest Products</h3>
            <a href="{{ route('admin.products.index') }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">View all</a>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($topProducts as $product)
                <div class="flex items-center gap-3 py-3">
                    <img src="{{ $product->image_url ?? 'https://via.placeholder.com/36?text=P' }}"
                         alt="{{ $product->name }}"
                         class="w-9 h-9 rounded-lg object-cover flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $product->name }}</p>
                        <p class="text-xs text-gray-400">{{ $product->category->name ?? 'Uncategorized' }}</p>
                    </div>
                    <span class="text-sm font-semibold text-gray-900">${{ number_format($product->price, 2) }}</span>
                </div>
            @empty
                <div class="py-8 text-center">
                    <i class="fas fa-box-open text-3xl text-gray-300 mb-2"></i>
                    <p class="text-sm text-gray-500">No products yet</p>
                    <a href="{{ route('admin.products.create') }}" class="text-xs text-blue-600 hover:underline mt-2 inline-block">Add your first product</a>
                </div>
            @endforelse
        </div>
    </div>

</div>

<!-- Quick Actions & Recent Orders -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 gap-3">
            <a href="{{ route('admin.products.create') }}"
               class="flex flex-col items-center gap-2 p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl hover:from-blue-100 hover:to-blue-200 transition-all duration-300 group">
                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fas fa-plus text-white text-sm"></i>
                </div>
                <span class="text-xs font-medium text-gray-700">Add Product</span>
            </a>
            <a href="{{ route('admin.categories.create') }}"
               class="flex flex-col items-center gap-2 p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl hover:from-purple-100 hover:to-purple-200 transition-all duration-300 group">
                <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fas fa-folder-plus text-white text-sm"></i>
                </div>
                <span class="text-xs font-medium text-gray-700">Add Category</span>
            </a>
            <a href="{{ route('admin.users.create') }}"
               class="flex flex-col items-center gap-2 p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-xl hover:from-green-100 hover:to-green-200 transition-all duration-300 group">
                <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fas fa-user-plus text-white text-sm"></i>
                </div>
                <span class="text-xs font-medium text-gray-700">Add User</span>
            </a>
            <a href="{{ route('admin.orders.index') }}"
               class="flex flex-col items-center gap-2 p-4 bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl hover:from-amber-100 hover:to-amber-200 transition-all duration-300 group">
                <div class="w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fas fa-clipboard-list text-white text-sm"></i>
                </div>
                <span class="text-xs font-medium text-gray-700">View Orders</span>
            </a>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Recent Orders</h3>
                <p class="text-xs text-gray-400 mt-0.5">Latest {{ $recentOrders->count() }} orders</p>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                View All <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left py-3 px-3 font-semibold text-gray-500 text-xs uppercase tracking-wider">Order</th>
                        <th class="text-left py-3 px-3 font-semibold text-gray-500 text-xs uppercase tracking-wider">Customer</th>
                        <th class="text-left py-3 px-3 font-semibold text-gray-500 text-xs uppercase tracking-wider">Amount</th>
                        <th class="text-left py-3 px-3 font-semibold text-gray-500 text-xs uppercase tracking-wider">Status</th>
                        <th class="text-left py-3 px-3 font-semibold text-gray-500 text-xs uppercase tracking-wider">Date</th>
                        <th class="text-right py-3 px-3 font-semibold text-gray-500 text-xs uppercase tracking-wider"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recentOrders as $order)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-3 px-3">
                                <span class="font-semibold text-gray-900">#{{ $order->id }}</span>
                            </td>
                            <td class="py-3 px-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0">
                                        <span class="text-xs font-bold text-gray-500">{{ substr($order->customer_name ?? 'U', 0, 1) }}</span>
                                    </div>
                                    <span class="text-gray-700">{{ $order->customer_name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-3 font-semibold text-gray-900">${{ number_format($order->total, 2) }}</td>
                            <td class="py-3 px-3">
                                @if($order->status === 'completed')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Completed
                                    </span>
                                @elseif($order->status === 'pending')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span> Pending
                                    </span>
                                @elseif($order->status === 'cancelled')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Cancelled
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> {{ ucfirst($order->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-gray-500 text-xs">{{ $order->created_at->format('M d, Y') }}</td>
                            <td class="py-3 px-3 text-right">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="text-blue-600 hover:text-blue-800 font-medium text-xs">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-gray-400">
                                <i class="fas fa-inbox text-3xl mb-2 block"></i>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('salesChart').getContext('2d');

    const gradient = ctx.createLinearGradient(0, 0, 0, 280);
    gradient.addColorStop(0, 'rgba(59, 130, 246, 0.35)');
    gradient.addColorStop(0.5, 'rgba(59, 130, 246, 0.1)');
    gradient.addColorStop(1, 'rgba(59, 130, 246, 0)');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($monthLabels) !!},
            datasets: [{
                label: 'Revenue',
                data: {!! json_encode($monthlySales) !!},
                backgroundColor: gradient,
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1.5,
                borderRadius: 8,
                borderSkipped: false,
                hoverBackgroundColor: 'rgba(59, 130, 246, 0.6)',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#1f2937',
                    titleColor: '#f9fafb',
                    bodyColor: '#f9fafb',
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return ' $' + parseFloat(context.raw).toFixed(2);
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#9ca3af',
                        font: { size: 11 }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(229, 231, 235, 0.5)'
                    },
                    ticks: {
                        color: '#9ca3af',
                        font: { size: 11 },
                        callback: function(value) {
                            return '$' + value;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
