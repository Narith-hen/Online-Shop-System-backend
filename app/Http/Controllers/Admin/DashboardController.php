<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = Cache::remember('dashboard_stats', 300, function () {
            $data = [];
            if (Schema::hasTable('products')) {
                $data['totalProducts'] = Product::count();
                $data['activeProducts'] = Product::where('is_active', true)->count();
                $data['topProducts'] = Product::with('category')->latest()->take(5)->get();
            } else {
                $data['totalProducts'] = 0;
                $data['activeProducts'] = 0;
                $data['topProducts'] = collect();
            }

            if (Schema::hasTable('categories')) {
                $data['totalCategories'] = Category::count();
                $data['activeCategories'] = Category::where('is_active', true)->count();
            } else {
                $data['totalCategories'] = 0;
                $data['activeCategories'] = 0;
            }

            if (Schema::hasTable('users')) {
                $data['totalUsers'] = User::count();
            } else {
                $data['totalUsers'] = 0;
            }

            if (Schema::hasTable('orders')) {
                $data['totalOrders'] = Order::count();
                $data['totalRevenue'] = Order::sum('total') ?? 0;
                $data['pendingOrders'] = Order::where('status', 'pending')->count();
                $data['recentOrders'] = Order::with('user')->latest()->take(8)->get();

                $monthlySales = Order::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(total) as total')
                    ->where('created_at', '>=', Carbon::now()->subMonths(6)->startOfMonth())
                    ->groupBy('year', 'month')
                    ->orderBy('year')
                    ->orderBy('month')
                    ->get()
                    ->keyBy(fn ($item) => $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT));

                $data['monthLabels'] = [];
                $data['monthlySales'] = [];
                $now = Carbon::now();
                for ($i = 6; $i >= 0; $i--) {
                    $date = $now->copy()->subMonths($i);
                    $data['monthLabels'][] = $date->format('M');
                    $key = $date->format('Y-m');
                    $data['monthlySales'][] = (float) ($monthlySales[$key]->total ?? 0);
                }
            } else {
                $data['totalOrders'] = 0;
                $data['totalRevenue'] = 0;
                $data['pendingOrders'] = 0;
                $data['recentOrders'] = collect();
                $data['monthlySales'] = [];
                $data['monthLabels'] = [];
            }

            return $data;
        });

        return view('admin.dashboard', $stats);
    }
}
