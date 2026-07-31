@extends('buyer.layouts.app', ['title' => $product->name])

@section('content')
@php
$images = collect([$product->image])->merge($product->productImages->pluck('image_path'))->filter()->values();
$mainImage = $product->image_url;
$avgRating = $product->averageRating();
$avgRounded = (int) round($avgRating);
@endphp

<div class="row g-4 mb-5">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <img
                    id="mainProductImage"
                    src="{{ $mainImage }}"
                    alt="{{ $product->name }}"
                    class="img-fluid rounded-3 w-100"
                    style="height: 420px; object-fit: cover;">

                @if ($images->count() > 1)
                <div class="d-flex flex-wrap gap-2 mt-3">
                    @foreach ($images as $image)
                    <button type="button" class="btn p-0 border rounded-3 thumb-trigger" data-image="{{ Storage::url($image) }}">
                        <img src="{{ Storage::url($image) }}" alt="thumb" width="72" height="72" style="object-fit: cover; border-radius: 0.5rem;" onerror="this.closest('button').style.display='none';">
                    </button>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <span class="badge text-bg-light mb-2">{{ $product->category?->name }}</span>
        <h1 class="h3 fw-bold mb-2">{{ $product->name }}</h1>
        <div class="d-flex align-items-center gap-2 mb-2">
            <div class="rating-stars">
                @for ($i = 1; $i <= 5; $i++)
                    <i class="bi {{ $i <= $avgRounded ? 'bi-star-fill' : 'bi-star' }}"></i>
                    @endfor
            </div>
            <small class="text-secondary">{{ number_format($avgRating, 1) }} / 5 ({{ $product->reviews->count() }} review)</small>
        </div>

        <div class="h4 fw-bold mb-3">{{ $product->format_price }}</div>
        <p class="mb-3">
            <span class="fw-semibold">Stok:</span>
            @if ($product->stock < 1)
                <span class="badge text-bg-danger">Habis</span>
            @elseif ($product->stock <= 10)
                <span class="badge text-bg-warning">{{ $product->stock }}</span>
            @else
                <span class="badge text-bg-success">{{ $product->stock }}</span>
            @endif
        </p>
        <p class="text-secondary">{{ \Illuminate\Support\Str::limit($product->description, 180) }}</p>

        @auth
        @if (auth()->user()->isBuyer())
        <form action="{{ route('cart.store', $product) }}" method="POST" class="row g-2 align-items-end mt-3" id="addToCartForm">
            @csrf
            <div class="col-auto">
                <label for="quantity" class="form-label">Qty</label>
                <input type="number" min="1" max="{{ $product->stock }}" value="1" name="quantity" id="quantity" class="form-control" style="max-width: 100px;" {{ $product->stock < 1 ? 'disabled' : '' }}>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-brand" id="addToCartBtn" {{ $product->stock < 1 ? 'disabled' : '' }}>
                    Tambah ke Keranjang
                </button>
            </div>
        </form>
        @if ($product->stock < 1)
            <small class="text-danger d-block mt-2">Stok habis. Produk tidak bisa ditambahkan ke keranjang.</small>
            @endif
            @endif
            @else
            <a href="{{ route('login') }}" class="btn btn-brand mt-2">Login untuk Belanja</a>
            @endauth
    </div>
</div>

<ul class="nav nav-tabs" id="productTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="desc-tab" data-bs-toggle="tab" data-bs-target="#desc-pane" type="button" role="tab">Deskripsi</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="review-tab" data-bs-toggle="tab" data-bs-target="#review-pane" type="button" role="tab">Review & Rating ({{ $product->reviews->count() }})</button>
    </li>
</ul>

<div class="tab-content border border-top-0 rounded-bottom p-4 mb-5 bg-white">
    <div class="tab-pane fade show active" id="desc-pane" role="tabpanel">
        <p class="mb-0">{{ $product->description }}</p>
    </div>
    <div class="tab-pane fade" id="review-pane" role="tabpanel">

        {{-- Review Form dipindahkan ke halaman Detail Pesanan setelah Checkout --}}

        {{-- Review List --}}
        @forelse ($product->reviews as $review)
        <div class="border rounded-3 p-3 mb-2">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <strong>{{ $review->user?->name ?? 'Buyer' }}</strong>
                <small class="text-secondary">{{ $review->created_at?->format('d M Y') }}</small>
            </div>
            <div class="rating-stars mb-2">
                @for ($i = 1; $i <= 5; $i++)
                    <i class="bi {{ $i <= $review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                    @endfor
            </div>
            <p class="mb-0">{{ $review->comment ?: '-' }}</p>
        </div>
        @empty
        <div class="alert alert-light mb-0">Belum ada review untuk produk ini.</div>
        @endforelse
    </div>
</div>

<section>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 fw-bold mb-0">Produk Serupa</h2>
        <a href="{{ route('products.index', ['category' => $product->category?->slug]) }}" class="text-decoration-none">Lihat Semua</a>
    </div>
    <div class="row g-3">
        @forelse ($similarProducts as $item)
        <div class="col-12 col-sm-6 col-md-3">
            @include('buyer.partials.product-card', ['product' => $item])
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-light mb-0">Belum ada produk serupa pada kategori ini.</div>
        </div>
        @endforelse
    </div>
</section>

@if ($cfRecommendations->isNotEmpty())
<section class="mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="h4 fw-bold mb-1">Rekomendasi Untukmu</h2>
            <small class="text-success"><i class="bi bi-bullseye"></i> Berdasarkan preferensi kamu (Collaborative Filtering)</small>
        </div>
        <span class="badge bg-success">
            <i class="bi bi-bullseye me-1"></i>CF
        </span>
    </div>
    <div class="row g-3">
        @foreach ($cfRecommendations as $item)
        <div class="col-12 col-sm-6 col-md-3">
            @include('buyer.partials.product-card', ['product' => $item])
        </div>
        @endforeach
    </div>
</section>
@endif
@endsection

@push('scripts')
<script>
    // Thumbnail gallery switcher
    document.querySelectorAll('.thumb-trigger').forEach(function(button) {
        button.addEventListener('click', function() {
            var image = button.getAttribute('data-image');
            var mainImage = document.getElementById('mainProductImage');
            var placeholder = document.getElementById('mainPlaceholder');

            if (mainImage) {
                mainImage.style.display = 'block';
                mainImage.src = image;
            }

            if (placeholder) {
                placeholder.style.display = 'none';
            }
        });
    });

    // Interactive star picker
    (function() {
        var picker = document.getElementById('starPicker');
        if (!picker) return;

        var labels = picker.querySelectorAll('.star-label');
        var activeColor = '#f59e0b';
        var inactiveColor = '#d1d5db';

        function updateStars(activeIndex) {
            labels.forEach(function(label, index) {
                label.querySelector('i').style.color = index <= activeIndex ? activeColor : inactiveColor;
            });
        }

        // Initialize from old input
        var checked = picker.querySelector('input:checked');
        if (checked) {
            updateStars(parseInt(checked.value) - 1);
        }

        labels.forEach(function(label, index) {
            label.addEventListener('click', function() {
                label.querySelector('input').checked = true;
                updateStars(index);
            });

            label.addEventListener('mouseenter', function() {
                updateStars(index);
            });
        });

        picker.addEventListener('mouseleave', function() {
            var checked = picker.querySelector('input:checked');
            if (checked) {
                updateStars(parseInt(checked.value) - 1);
            } else {
                updateStars(-1);
            }
        });
    })();

    // Anti-double submit for review form
    (function() {
        var form = document.getElementById('reviewForm');
        if (!form) return;

        form.addEventListener('submit', function() {
            var btn = document.getElementById('submitReviewBtn');
            var spinner = document.getElementById('reviewSpinner');

            if (btn) {
                btn.disabled = true;
            }

            if (spinner) {
                spinner.classList.remove('d-none');
            }
        });
    })();
</script>
@endpush