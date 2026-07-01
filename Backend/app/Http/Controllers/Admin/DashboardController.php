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
        $stats = (function () {
            $data = [];
            if (Schema::hasTable('products')) {
                $data['totalProducts'] = Product::count();
                $data['activeProducts'] = Product::where('is_active', true)->count();
                $data['topProducts'] = Product::with('category')->latest()->take(5)->get();
                $data['productCategories'] = Product::join('categories', 'products.category_id', '=', 'categories.id')
                    ->selectRaw('categories.name as category, COUNT(*) as count')
                    ->groupBy('categories.name')
                    ->get();
            } else {
                $data['totalProducts'] = 0;
                $data['activeProducts'] = 0;
                $data['topProducts'] = collect();
                $data['productCategories'] = collect();
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

                $now = Carbon::now();

                // Monthly sales for last 7 months
                $monthlySales = Order::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(total) as total')
                    ->where('created_at', '>=', $now->copy()->subMonths(6)->startOfMonth())
                    ->groupBy('year', 'month')
                    ->orderBy('year')
                    ->orderBy('month')
                    ->get()
                    ->keyBy(fn ($item) => $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT));

                $data['monthLabels'] = [];
                $data['monthlySales'] = [];
                for ($i = 6; $i >= 0; $i--) {
                    $date = $now->copy()->subMonths($i);
                    $data['monthLabels'][] = $date->format('M');
                    $key = $date->format('Y-m');
                    $data['monthlySales'][] = (float) ($monthlySales[$key]->total ?? 0);
                }

                // Daily sales for last 14 days
                $dailySales = Order::selectRaw('DATE(created_at) as date, SUM(total) as total')
                    ->where('created_at', '>=', $now->copy()->subDays(13)->startOfDay())
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get()
                    ->keyBy('date');

                $data['dayLabels'] = [];
                $data['dailySales'] = [];
                for ($i = 13; $i >= 0; $i--) {
                    $date = $now->copy()->subDays($i);
                    $data['dayLabels'][] = $date->format('D, M d');
                    $key = $date->format('Y-m-d');
                    $data['dailySales'][] = (float) ($dailySales[$key]->total ?? 0);
                }

                // Weekly sales for last 8 weeks
                $weeklySales = Order::selectRaw('YEARWEEK(created_at, 1) as week, SUM(total) as total')
                    ->where('created_at', '>=', $now->copy()->subWeeks(7)->startOfWeek())
                    ->groupBy('week')
                    ->orderBy('week')
                    ->get()
                    ->keyBy('week');

                $data['weekLabels'] = [];
                $data['weeklySales'] = [];
                for ($i = 7; $i >= 0; $i--) {
                    $date = $now->copy()->subWeeks($i)->startOfWeek();
                    $data['weekLabels'][] = 'Week ' . $date->format('M d');
                    $key = $date->isoWeekYear() . str_pad($date->isoWeek(), 2, '0', STR_PAD_LEFT);
                    $data['weeklySales'][] = (float) ($weeklySales[$key]->total ?? 0);
                }
            } else {
                $data['totalOrders'] = 0;
                $data['totalRevenue'] = 0;
                $data['pendingOrders'] = 0;
                $data['recentOrders'] = collect();
                $data['monthlySales'] = [];
                $data['monthLabels'] = [];
                $data['dailySales'] = [];
                $data['dayLabels'] = [];
                $data['weeklySales'] = [];
                $data['weekLabels'] = [];
            }

            return $data;
        })();

        return view('admin.dashboard', $stats);
    }
}
