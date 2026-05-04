<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
	public function index(Request $request)
	{
		$totalProducts = Product::where('is_active', true)->count();

		$today = Carbon::today();
		$ordersToday = Order::whereDate('created_at', $today)->count();

		$pendingOrders = Order::where('status', 'pending')->count();

		$startMonth = $today->copy()->startOfMonth();
		$endMonth = $today->copy()->endOfMonth();

		$revenueMonth = Order::whereBetween('created_at', [$startMonth, $endMonth])
			->whereNotIn('status', ['pending', 'cancelled'])
			->sum('total_price');

		// Sales for last 7 days
		$sales7 = Order::whereBetween('created_at', [Carbon::today()->subDays(6), Carbon::today()])
			->whereNotIn('status', ['pending', 'cancelled'])
			->selectRaw("DATE(created_at) as date, SUM(total_price) as total")
			->groupBy('date')
			->orderBy('date')
			->get()
			->keyBy('date')
			->map(fn($r) => (int) $r->total);

		// Ensure keys for last 7 days
		$labels = [];
		$data = [];
		for ($i = 6; $i >= 0; $i--) {
			$d = Carbon::today()->subDays($i)->toDateString();
			$labels[] = $d;
			$data[] = $sales7->get($d, 0);
		}

		$latestOrders = Order::with('user')->latest()->limit(5)->get();

		return view('admin.dashboard', compact(
			'totalProducts',
			'ordersToday',
			'pendingOrders',
			'revenueMonth',
			'labels',
			'data',
			'latestOrders'
		));
	}
}
