@extends('buyer.layouts.app', ['title' => 'GearHub - Home'])

@section('content')
<div class="hero-wrap p-4 p-lg-5 mb-5">
	<div class="row align-items-center g-4">
		<div class="col-lg-7">
			<span class="badge bg-light text-dark mb-3">Gaming Gear Store - Single Seller</span>
			<h1 class="display-6 fw-bold mb-3">Upgrade Setup Gaming-mu dengan Gear Terbaik</h1>
			<p class="mb-4 text-white-50">Mouse, keyboard, headset, webcam, dan aksesoris pilihan. Belanja cepat, aman, dan siap dipersonalisasi dengan sistem rekomendasi.</p>
			<div class="d-flex gap-2">
				<a href="{{ route('products.index') }}" class="btn btn-brand btn-lg">Mulai Belanja</a>
				<a href="#rekomendasi" class="btn btn-outline-light btn-lg">Lihat Rekomendasi</a>
			</div>
		</div>
		<div class="col-lg-5">
			<div class="p-4 rounded-4 bg-white text-dark">
				<h5 class="fw-bold mb-3">Kenapa GearHub?</h5>
				<ul class="mb-0">
					<li class="mb-2">Produk gaming gear curated</li>
					<li class="mb-2">Checkout cepat & ringkas</li>
					<li class="mb-2">Riwayat order transparan</li>
					<li>Siap integrasi rekomendasi CF</li>
				</ul>
			</div>
		</div>
	</div>
</div>

<section class="mb-5">
	<div class="d-flex justify-content-between align-items-center mb-3">
		<h2 class="section-title h4 mb-0">Kategori Produk</h2>
	</div>
	<div class="row g-3">
		@foreach ($categories as $category)
		<div class="col-6 col-md-4 col-lg-2">
			<a href="{{ route('products.index', ['category' => $category->slug]) }}" class="text-decoration-none">
				<div class="category-card h-100 p-3 bg-white text-center">
					<div class="fs-2 mb-2 text-dark">
						<i class="bi {{ $categoryIcons[$category->slug] ?? 'bi-grid' }}"></i>
					</div>
					<div class="fw-semibold small text-dark">{{ $category->name }}</div>
					<small class="text-secondary">{{ $category->products_count }} produk</small>
				</div>
			</a>
		</div>
		@endforeach
	</div>
</section>

<section class="mb-5">
	<div class="d-flex justify-content-between align-items-center mb-3">
		<h2 class="section-title h4 mb-0">Produk Terbaru</h2>
		<a href="{{ route('products.index', ['sort' => 'newest']) }}" class="text-decoration-none">Lihat Semua</a>
	</div>
	<div class="row g-3">
		@foreach ($latestProducts as $product)
		<div class="col-12 col-sm-6 col-md-3">
			@include('buyer.partials.product-card', ['product' => $product])
		</div>
		@endforeach
	</div>
</section>

<section id="rekomendasi" class="mb-4">
	<div class="d-flex justify-content-between align-items-center mb-3">
		<h2 class="section-title h4 mb-0">Rekomendasi untuk Kamu</h2>
		<small class="text-secondary">Placeholder: produk terlaris</small>
	</div>
	<div class="row g-3">
		@foreach ($recommendedProducts as $product)
		<div class="col-12 col-sm-6 col-md-3">
			@include('buyer.partials.product-card', ['product' => $product])
		</div>
		@endforeach
	</div>
</section>
@endsection