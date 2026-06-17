<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\CollaborativeFilteringService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
	public function __construct(
		private readonly CollaborativeFilteringService $cfService
	) {}

	/**
	 * Display storefront homepage.
	 */
	public function index(Request $request): View
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

		// ─────────────────────────────────────────────────
		// REKOMENDASI: Collaborative Filtering Integration
		// ─────────────────────────────────────────────────
		$user = $request->user();

		if ($user && $user->isBuyer()) {
			// User login sebagai buyer → gunakan CF algorithm
			$recommendedProducts = $this->cfService->getRecommendations($user->id);

			// Tentukan tipe rekomendasi untuk label di view
			if ($recommendedProducts->isNotEmpty() && !$this->cfService->isNewUser($user->id)) {
				$recommendationType = 'cf';
			} else {
				$recommendationType = 'bestseller';
			}
		} else {
			// Guest atau admin → tampilkan best-seller sebagai fallback
			$recommendedProducts = Product::query()
				->activeProducts()
				->with('category')
				->withSum('orderItems as sold_count', 'quantity')
				->orderByDesc('sold_count')
				->orderByDesc('id')
				->take(8)
				->get();

			$recommendationType = 'guest';
		}

		return view('buyer.home', [
			'categories' => $categories,
			'categoryIcons' => $categoryIcons,
			'latestProducts' => $latestProducts,
			'recommendedProducts' => $recommendedProducts,
			'recommendationType' => $recommendationType,
		]);
	}
}
