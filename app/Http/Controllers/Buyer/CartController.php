<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Services\UserInteractionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(private readonly UserInteractionService $interactionService) {}

    /**
     * Display current buyer cart.
     */
    public function index(Request $request): View
    {
        $cartItems = Cart::query()
            ->where('buyer_id', $request->user()->id)
            ->with('product.category')
            ->latest()
            ->get();

        $total = $cartItems->sum(fn(Cart $item): int => (int) $item->product->price * $item->quantity);

        return view('buyer.cart.index', [
            'cartItems' => $cartItems,
            'total' => $total,
        ]);
    }

    /**
     * Add product to cart.
     *
     * @throws ValidationException
     */
    public function store(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        if ($product->stock < 1) {
            throw ValidationException::withMessages([
                'quantity' => 'Produk sedang habis.',
            ]);
        }

        $quantity = (int) ($validated['quantity'] ?? 1);

        $cartItem = Cart::query()->firstOrNew([
            'buyer_id' => $request->user()->id,
            'product_id' => $product->id,
        ]);

        $newQuantity = ($cartItem->exists ? $cartItem->quantity : 0) + $quantity;

        if ($newQuantity > $product->stock) {
            throw ValidationException::withMessages([
                'quantity' => 'Jumlah melebihi stok tersedia.',
            ]);
        }

        $cartItem->quantity = $newQuantity;
        $cartItem->save();

        $this->interactionService->log($request->user(), $product->id, 'cart');

        return back()->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    /**
     * Update quantity for a cart item.
     *
     * @throws ValidationException
     */
    public function update(Request $request, Cart $cart): RedirectResponse
    {
        $this->ensureOwnership($request, $cart);

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product = $cart->product;
        $newQuantity = (int) $validated['quantity'];

        if ($newQuantity > $product->stock) {
            throw ValidationException::withMessages([
                'quantity' => 'Jumlah melebihi stok tersedia.',
            ]);
        }

        $cart->update([
            'quantity' => $newQuantity,
        ]);

        return back()->with('success', 'Jumlah keranjang berhasil diperbarui.');
    }

    /**
     * Remove item from cart.
     */
    public function destroy(Request $request, Cart $cart): RedirectResponse
    {
        $this->ensureOwnership($request, $cart);

        $cart->delete();

        return back()->with('success', 'Item berhasil dihapus dari keranjang.');
    }

    /**
     * Ensure current user owns requested cart item.
     */
    private function ensureOwnership(Request $request, Cart $cart): void
    {
        abort_unless($cart->buyer_id === $request->user()->id, 403);
    }
}
