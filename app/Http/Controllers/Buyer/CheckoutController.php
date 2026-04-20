<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\UserInteractionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CheckoutController extends Controller
{
	public function __construct(private readonly UserInteractionService $interactionService) {}

	/**
	 * Display checkout page.
	 */
	public function index(Request $request): View|RedirectResponse
	{
		$cartItems = Cart::query()
			->where('buyer_id', $request->user()->id)
			->with('product')
			->get();

		if ($cartItems->isEmpty()) {
			return redirect()->route('cart.index')->with('error', 'Keranjang kamu masih kosong.');
		}

		$total = $cartItems->sum(fn(Cart $item): int => (int) $item->product->price * $item->quantity);

		return view('buyer.checkout.index', [
			'cartItems' => $cartItems,
			'total' => $total,
		]);
	}

	/**
	 * Create order from current cart.
	 *
	 * @throws ValidationException
	 */
	public function store(Request $request): RedirectResponse
	{
		$validated = $request->validate([
			'shipping_name' => ['required', 'string', 'max:255'],
			'shipping_phone' => ['required', 'string', 'max:30'],
			'shipping_address' => ['required', 'string'],
			'payment_method' => ['required', 'in:transfer,cod'],
		]);

		$buyer = $request->user();

		$resultOrder = DB::transaction(function () use ($buyer, $validated) {
			$cartItems = Cart::query()
				->where('buyer_id', $buyer->id)
				->with('product')
				->lockForUpdate()
				->get();

			if ($cartItems->isEmpty()) {
				throw ValidationException::withMessages([
					'cart' => 'Keranjang kosong. Tambahkan produk terlebih dahulu.',
				]);
			}

			$productIds = $cartItems->pluck('product_id')->all();
			$products = Product::query()->whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');

			$totalPrice = 0;

			foreach ($cartItems as $item) {
				$product = $products->get($item->product_id);

				if (! $product || $product->stock < $item->quantity) {
					throw ValidationException::withMessages([
						'stock' => 'Stok produk "' . $item->product->name . '" tidak mencukupi.',
					]);
				}

				$totalPrice += (int) $product->price * $item->quantity;
			}

			$order = Order::create([
				'buyer_id' => $buyer->id,
				'total_price' => $totalPrice,
				'status' => 'pending',
				'shipping_name' => $validated['shipping_name'],
				'shipping_phone' => $validated['shipping_phone'],
				'shipping_address' => $validated['shipping_address'],
				'payment_method' => $validated['payment_method'],
			]);

			foreach ($cartItems as $item) {
				$product = $products->get($item->product_id);

				$product->decrement('stock', $item->quantity);

				OrderItem::create([
					'order_id' => $order->id,
					'product_id' => $product->id,
					'quantity' => $item->quantity,
					'price' => (int) $product->price,
				]);

				$this->interactionService->log($buyer, $product->id, 'purchase');
			}

			Cart::query()->where('buyer_id', $buyer->id)->delete();

			return $order;
		});

		return redirect()
			->route('orders.show', $resultOrder)
			->with('success', 'Pesanan berhasil dibuat.');
	}
}
