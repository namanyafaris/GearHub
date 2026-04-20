<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
	/**
	 * Display storefront homepage.
	 */
	public function index(): View
	{
		$categories = Category::query()
			->withCount('products')
			->orderBy('name')
			->get();

		$categoryIcons = [
			'mouse-gaming' => 'bi-mouse3-fill',
			'keyboard-gaming' => 'bi-keyboard-fill',
			'headset-gaming' => 'bi-headset',
			'mousepad-gaming' => 'bi-grid-3x3-gap-fill',
			'controller-gaming' => 'bi-controller',
			'webcam-gaming' => 'bi-camera-video-fill',
		];

		$latestProducts = Product::query()
			->activeProducts()
			->with('category')
			->latest()
			->take(8)
			->get();

		// Placeholder recommendation section: top-selling products.
		$recommendedProducts = Product::query()
			->activeProducts()
			->with('category')
			->withSum('orderItems as sold_count', 'quantity')
			->orderByDesc('sold_count')
			->orderByDesc('id')
			->take(8)
			->get();

		return view('buyer.home', [
			'categories' => $categories,
			'categoryIcons' => $categoryIcons,
			'latestProducts' => $latestProducts,
			'recommendedProducts' => $recommendedProducts,
		]);
	}
}
