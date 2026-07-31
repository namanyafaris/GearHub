<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
	public function index(Request $request)
	{
		$query = Product::query()->with('category');

		if ($request->filled('q')) {
			$query->where('name', 'like', '%' . $request->q . '%');
		}

		if ($request->filled('category')) {
			$query->where('category_id', $request->category);
		}

		$products = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
		$categories = Category::all();

		return view('admin.products.index', compact('products', 'categories'));
	}

	public function create()
	{
		$categories = Category::all();
		return view('admin.products.create', compact('categories'));
	}

	public function store(Request $request)
	{
		$data = $request->validate([
			'name' => 'required|string|max:255',
			'category_id' => 'required|integer|exists:categories,id',
			'description' => 'nullable|string',
			'price' => 'required|numeric|min:0',
			'stock' => 'required|integer|min:0',
			'image' => 'nullable|image|max:2048',
			'images.*' => 'nullable|image|max:2048',
			'is_active' => 'sometimes|boolean'
		]);

		$data['slug'] = Str::slug($data['name']) . '-' . Str::random(5);
		$data['is_active'] = $request->has('is_active');

		if ($request->hasFile('image')) {
			$data['image'] = $request->file('image')->store('products', 'public');
		}

		$product = Product::create($data);

		if ($request->hasFile('images')) {
			foreach ($request->file('images') as $img) {
				$path = $img->store('products', 'public');
				ProductImage::create(['product_id' => $product->id, 'image_path' => $path]);
			}
		}

		return redirect()->route('admin.products.index')->with('success', 'Produk dibuat.');
	}

	public function edit(Product $product)
	{
		$categories = Category::all();
		$product->load('productImages');
		return view('admin.products.edit', compact('product', 'categories'));
	}

	public function update(Request $request, Product $product)
	{
		$data = $request->validate([
			'name' => 'required|string|max:255',
			'category_id' => 'required|integer|exists:categories,id',
			'description' => 'nullable|string',
			'price' => 'required|numeric|min:0',
			'stock' => 'required|integer|min:0',
			'image' => 'nullable|image|max:2048',
			'images.*' => 'nullable|image|max:2048',
			'is_active' => 'sometimes|boolean'
		]);

		$data['slug'] = $product->name !== $data['name'] ? Str::slug($data['name']) . '-' . Str::random(5) : $product->slug;
		$data['is_active'] = $request->has('is_active');

		if ($request->hasFile('image')) {
			$data['image'] = $request->file('image')->store('products', 'public');
		}

		$product->update($data);

		if ($request->hasFile('images')) {
			foreach ($request->file('images') as $img) {
				$path = $img->store('products', 'public');
				ProductImage::create(['product_id' => $product->id, 'image_path' => $path]);
			}
		}

		return redirect()->route('admin.products.index')->with('success', 'Produk diperbarui.');
	}

	public function destroy(Product $product)
	{
		$product->delete();
		return back()->with('success', 'Produk dihapus.');
	}
}
