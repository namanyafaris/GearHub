@php
$avgRating = number_format($product->averageRating(), 1);
$ratingInt = (int) round($product->averageRating());
@endphp

<div class="card product-card h-100">
    <a href="{{ route('products.show', $product->slug) }}" class="text-decoration-none text-dark">
        <img 
            src="{{ $product->image_url }}" 
            alt="{{ $product->name }}" 
            class="card-img-top"
            style="height: 190px; object-fit: cover;"
        >
    </a>

    <div class="card-body d-flex flex-column">
        <small class="text-secondary mb-1">{{ $product->category?->name ?? 'Uncategorized' }}</small>
        <a href="{{ route('products.show', $product) }}" class="text-decoration-none text-dark fw-semibold mb-2">
            {{ $product->name }}
        </a>

        <div class="rating-stars small mb-2">
            @for ($i = 1; $i <= 5; $i++)
                <i class="bi {{ $i <= $ratingInt ? 'bi-star-fill' : 'bi-star' }}"></i>
                @endfor
                <span class="text-secondary ms-1">({{ $avgRating }})</span>
        </div>

        <div class="mt-auto d-flex justify-content-between align-items-end">
            <div class="fw-bold">{{ $product->format_price }}</div>
            @if ($product->stock < 1)
                <span class="badge text-bg-danger">Habis</span>
            @elseif ($product->stock <= 10)
                <span class="badge text-bg-warning">Stok: {{ $product->stock }}</span>
            @else
                <span class="badge text-bg-success">Stok: {{ $product->stock }}</span>
            @endif
        </div>
    </div>
</div>