@extends('buyer.layouts.app', ['title' => 'Halaman Tidak Ditemukan — GearHub'])

@section('content')
<div class="text-center py-5">
    <div class="mb-4">
        <i class="bi bi-exclamation-triangle" style="font-size: 5rem; color: var(--brand);"></i>
    </div>
    <h1 class="display-1 fw-bold" style="color: var(--brand);">404</h1>
    <h2 class="h4 fw-bold mb-3">Halaman Tidak Ditemukan</h2>
    <p class="text-secondary mb-4" style="max-width: 480px; margin: 0 auto;">
        Maaf, halaman yang kamu cari tidak tersedia atau mungkin sudah dipindahkan.
        Silakan kembali ke beranda untuk melanjutkan belanja.
    </p>
    <div class="d-flex justify-content-center gap-2">
        <a href="{{ route('home') }}" class="btn btn-brand btn-lg">
            <i class="bi bi-house-door me-1"></i>Kembali ke Beranda
        </a>
        <a href="{{ route('products.index') }}" class="btn btn-outline-dark btn-lg">
            <i class="bi bi-grid me-1"></i>Lihat Katalog
        </a>
    </div>
</div>
@endsection
