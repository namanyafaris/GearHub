@extends('buyer.layouts.app', ['title' => 'Katalog Produk'])

@section('content')
<div class="row g-4">
	<aside class="col-lg-3">
		<div class="card border-0 shadow-sm">
			<div class="card-body">
				<h5 class="fw-bold mb-3">Filter Produk</h5>

				<form method="GET" action="{{ route('products.index') }}" class="d-grid gap-3">
					<div>
						<label for="search" class="form-label">Cari Nama Produk</label>
						<input type="text" name="search" id="search" class="form-control" value="{{ $search }}" placeholder="Contoh: Logitech G102">
					</div>

					<div>
						<label class="form-label">Kategori</label>
						<select name="category" class="form-select">
							<option value="">Semua Kategori</option>
							@foreach ($categories as $category)
							<option value="{{ $category->slug }}" @selected($selectedCategory===$category->slug)>
								{{ $category->name }}
							</option>
							@endforeach
						</select>
					</div>

					<div>
						<label class="form-label">Range Harga</label>
						<div class="row g-2">
							<div class="col-6">
								<input type="number" min="0" name="min_price" class="form-control" placeholder="Min" value="{{ $minPrice }}">
							</div>
							<div class="col-6">
								<input type="number" min="0" name="max_price" class="form-control" placeholder="Max" value="{{ $maxPrice }}">
							</div>
						</div>
					</div>

					<div>
						<label for="sort" class="form-label">Urutkan</label>
						<select name="sort" id="sort" class="form-select">
							<option value="newest" @selected($selectedSort==='newest' || $selectedSort==='' )>Terbaru</option>
							<option value="price_low" @selected($selectedSort==='price_low' )>Harga Terendah</option>
							<option value="price_high" @selected($selectedSort==='price_high' )>Harga Tertinggi</option>
							<option value="rating" @selected($selectedSort==='rating' )>Rating Tertinggi</option>
						</select>
					</div>

					<button type="submit" class="btn btn-brand">Terapkan Filter</button>
					<a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Reset</a>
				</form>
			</div>
		</div>
	</aside>

	<section class="col-lg-9">
		<div class="d-flex justify-content-between align-items-center mb-3">
			<h1 class="h4 fw-bold mb-0">Katalog Produk</h1>
			<small class="text-secondary">{{ $products->total() }} produk ditemukan</small>
		</div>

		<div class="row g-3">
			@forelse ($products as $product)
			<div class="col-12 col-sm-6 col-md-3">
				@include('buyer.partials.product-card', ['product' => $product])
			</div>
			@empty
			<div class="col-12">
				<div class="alert alert-warning mb-0">Produk tidak ditemukan. Coba ubah filter pencarian.</div>
			</div>
			@endforelse
		</div>

		<div class="mt-4">
			{{ $products->links() }}
		</div>
	</section>
</div>
@endsection