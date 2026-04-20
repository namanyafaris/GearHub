<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Services\UserInteractionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(private readonly UserInteractionService $interactionService) {}

    /**
     * Display product catalog with filter, sort, and search.
     */
    public function index(Request $request): View
    {
        $categories = Category::query()->orderBy('name')->get();

        $query = Product::query()
            ->activeProducts()
            ->with(['category', 'reviews']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->string('search') . '%');
        }

        if ($request->filled('category')) {
            $query->whereHas('category', function ($categoryQuery) use ($request): void {
                $categoryQuery->where('slug', $request->string('category'));
            });
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', (int) $request->input('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', (int) $request->input('max_price'));
        }

        $sort = $request->string('sort')->toString();

        switch ($sort) {
            case 'price_low':
                $query->orderBy('price');
                break;
            case 'price_high':
                $query->orderByDesc('price');
                break;
            case 'rating':
                $query->withAvg('reviews as avg_rating', 'rating')
                    ->orderByDesc('avg_rating')
                    ->orderByDesc('id');
                break;
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12)->withQueryString();

        return view('buyer.products.index', [
            'categories' => $categories,
            'products' => $products,
            'selectedCategory' => $request->string('category')->toString(),
            'selectedSort' => $sort,
            'search' => $request->string('search')->toString(),
            'minPrice' => $request->input('min_price'),
            'maxPrice' => $request->input('max_price'),
        ]);
    }

    /**
     * Display a product detail page.
     */
    public function show(Request $request, Product $product): View
    {
        $product->load([
            'category',
            'productImages',
            'reviews' => function ($query): void {
                $query->latest()->with('user');
            },
        ]);

        $user = $request->user();

        if ($user instanceof User && $user->isBuyer()) {
            $this->interactionService->log($user, $product->id, 'view');
        }

        $similarProducts = Product::query()
            ->activeProducts()
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->id)
            ->take(4)
            ->get();

        return view('buyer.products.show', [
            'product' => $product,
            'similarProducts' => $similarProducts,
        ]);
    }
}
