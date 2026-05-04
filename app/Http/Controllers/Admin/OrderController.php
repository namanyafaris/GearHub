<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\DB;

class OrderController extends Controller
{
	public function index(Request $request)
	{
		$query = Order::with('user')->latest();

		if ($request->filled('status')) {
			$query->where('status', $request->status);
		}

		$orders = $query->paginate(20)->withQueryString();

		return view('admin.orders.index', compact('orders'));
	}

	public function show(Order $order)
	{
		$order->load('orderItems.product', 'user');
		return view('admin.orders.show', compact('order'));
	}

	public function updateStatus(Request $request, Order $order)
	{
		$allowed = ['pending', 'processing', 'shipped', 'delivered'];
		$status = $request->validate(['status' => 'required|string']);
		if (! in_array($status['status'], $allowed, true)) {
			return back()->with('error', 'Status tidak valid.');
		}

		$order->status = $status['status'];
		$order->save();

		return back()->with('success', 'Status pesanan diperbarui.');
	}

	public function cancel(Request $request, Order $order)
	{
		if ($order->status !== 'pending') {
			return back()->with('error', 'Hanya pesanan pending yang dapat dibatalkan.');
		}

		DB::transaction(function () use ($order) {
			// Restore stock
			foreach ($order->orderItems as $item) {
				$product = $item->product;
				$product->increment('stock', $item->quantity);
			}

			$order->status = 'cancelled';
			$order->save();
		});

		return back()->with('success', 'Pesanan dibatalkan dan stok dikembalikan.');
	}
}
