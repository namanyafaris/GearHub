<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
	/**
	 * Display buyer order history.
	 */
	public function index(Request $request): View
	{
		$orders = Order::query()
			->where('buyer_id', $request->user()->id)
			->withSum('orderItems as total_items', 'quantity')
			->latest()
			->paginate(10);

		return view('buyer.orders.index', [
			'orders' => $orders,
		]);
	}

	/**
	 * Display a specific order detail.
	 */
	public function show(Request $request, Order $order): View
	{
		abort_unless($order->buyer_id === $request->user()->id, 403);

		$order->load(['orderItems.product']);

		return view('buyer.orders.show', [
			'order' => $order,
		]);
	}
}
