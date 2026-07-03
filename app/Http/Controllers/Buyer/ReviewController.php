<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;

class ReviewController extends Controller
{
    /**
     * Store a new review for a product.
     *
     * Business rules:
     * 1. Buyer harus pernah membeli produk ini (order status = delivered)
     * 2. Buyer hanya boleh 1 review per produk (unique buyer_id + product_id)
     */
    public function store(StoreReviewRequest $request, Product $product): RedirectResponse
    {
        $buyer = $request->user();

        // Validasi 1: Cek apakah buyer memiliki order delivered yang mengandung produk ini
        $hasDeliveredOrder = Order::query()
            ->where('buyer_id', $buyer->id)
            ->where('status', 'delivered')
            ->whereHas('orderItems', function ($query) use ($product): void {
                $query->where('product_id', $product->id);
            })
            ->exists();

        if (!$hasDeliveredOrder) {
            return back()->with('error', 'Kamu hanya bisa memberikan review untuk produk yang sudah diterima (delivered).');
        }

        // Validasi 2: Cek apakah buyer sudah pernah review produk ini
        $alreadyReviewed = Review::query()
            ->where('buyer_id', $buyer->id)
            ->where('product_id', $product->id)
            ->exists();

        if ($alreadyReviewed) {
            return back()->with('error', 'Kamu sudah memberikan review untuk produk ini.');
        }

        // Simpan review
        Review::create([
            'buyer_id'   => $buyer->id,
            'product_id' => $product->id,
            'rating'     => $request->validated('rating'),
            'comment'    => $request->validated('comment'),
        ]);

        return back()->with('success', 'Review berhasil dikirim. Terima kasih atas ulasan kamu!');
    }
}
