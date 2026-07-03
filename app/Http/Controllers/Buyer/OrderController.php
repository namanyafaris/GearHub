<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
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

	/**
	 * Upload payment proof for transfer orders.
	 */
	public function uploadPaymentProof(Request $request, Order $order)
	{
		abort_unless($order->buyer_id === $request->user()->id, 403);
		abort_unless($order->payment_method === 'transfer', 400, 'Metode pembayaran bukan transfer.');
		abort_if($order->payment_proof, 400, 'Bukti pembayaran sudah diunggah.');

		$request->validate([
			'payment_proof' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
		], [
			'payment_proof.required' => 'Bukti pembayaran wajib diunggah.',
			'payment_proof.image' => 'File harus berupa gambar.',
			'payment_proof.mimes' => 'Format gambar harus JPG/PNG.',
			'payment_proof.max' => 'Ukuran maksimal gambar adalah 2MB.',
		]);

		$path = $request->file('payment_proof')->store('payment_proofs', 'public');

		$order->update([
			'payment_proof' => $path,
		]);

		return back()->with('success', 'Bukti pembayaran berhasil diunggah. Menunggu konfirmasi Admin.');
	}

	/**
	 * Generate and download PDF invoice.
	 */
	public function invoice(Request $request, Order $order)
	{
		abort_unless($order->buyer_id === $request->user()->id, 403);

		$order->load(['orderItems.product']);

		$pdf = Pdf::loadView('buyer.orders.invoice', compact('order'));
		
		return $pdf->download('invoice_GearHub_' . $order->id . '.pdf');
	}
}
