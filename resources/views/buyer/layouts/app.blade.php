<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Gaming Gear Store' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --brand: #ff6b00;
            --brand-dark: #e25700;
            --ink: #121212;
            --muted: #6b7280;
            --bg-soft: #fff6ee;
            --card-border: #f1e0cf;
        }

        body {
            font-family: 'Sora', sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at 85% 0%, #ffe8d4 0%, transparent 30%),
                radial-gradient(circle at 0% 100%, #fff0df 0%, transparent 35%),
                #fff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1 0 auto;
        }

        .navbar-brand {
            font-weight: 800;
            letter-spacing: 0.02em;
        }

        .brand-dot {
            color: var(--brand);
        }

        .btn-brand {
            background-color: var(--brand);
            border-color: var(--brand);
            color: #fff;
        }

        .btn-brand:hover,
        .btn-brand:focus {
            background-color: var(--brand-dark);
            border-color: var(--brand-dark);
            color: #fff;
        }

        .section-title {
            font-weight: 700;
            letter-spacing: 0.01em;
        }

        .product-card,
        .category-card {
            border: 1px solid var(--card-border);
            border-radius: 1rem;
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }

        .product-card:hover,
        .category-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 28px rgba(255, 107, 0, 0.12);
        }

        .hero-wrap {
            border-radius: 1.5rem;
            background: linear-gradient(125deg, #111 0%, #222 46%, #ff7b1a 140%);
            color: #fff;
            overflow: hidden;
            position: relative;
        }

        .hero-wrap::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 85% 20%, rgba(255, 255, 255, 0.3), transparent 40%);
            pointer-events: none;
        }

        .hero-wrap>* {
            position: relative;
            z-index: 1;
        }

        .placeholder-image {
            background: linear-gradient(135deg, #1f2937, #374151);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            min-height: 180px;
        }

        .rating-stars {
            color: #f59e0b;
            letter-spacing: 0.05em;
        }

        footer {
            border-top: 1px solid #eee;
            background: #fff;
        }

        /* Menghilangkan icon mata bawaan browser Edge */
        input::-ms-reveal,
        input::-ms-clear {
            display: none;
        }
    </style>

    @stack('styles')
</head>

<body>
    @php
    $cartItemCount = 0;

    if (auth()->check() && auth()->user()->isBuyer()) {
    $cartItemCount = \App\Models\Cart::query()
    ->where('buyer_id', auth()->id())
    ->sum('quantity');
    }
    @endphp

    <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
        <div class="container py-2">
            <a class="navbar-brand" href="{{ route('home') }}">
                GEAR<span class="brand-dot">HUB</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav ms-auto me-lg-3 mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active fw-semibold' : '' }}" href="{{ route('home') }}">Home</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('products.*') ? 'active fw-semibold' : '' }}" href="{{ route('products.index') }}">Katalog</a></li>
                    @auth
                    @if (auth()->user()->isBuyer())
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('orders.*') ? 'active fw-semibold' : '' }}" href="{{ route('orders.index') }}">Pesanan Saya</a></li>
                    @endif
                    @endauth
                </ul>

                <div class="d-flex align-items-center gap-2">
                    @auth
                    @if (auth()->user()->isBuyer())
                    <a href="{{ route('cart.index') }}" class="btn btn-outline-dark position-relative">
                        <i class="bi bi-cart3"></i>
                        @if ($cartItemCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill text-bg-danger">{{ $cartItemCount }}</span>
                        @endif
                    </a>
                    @endif

                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-brand">Logout</button>
                    </form>
                    @else
                    <a href="{{ route('login') }}" class="btn btn-outline-dark">Login</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="py-4">
        <div class="container">
            @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @yield('content')
        </div>
    </main>

    <footer class="py-4 mt-auto">
        <div class="container d-flex flex-column flex-md-row justify-content-between gap-2">
            <small class="text-secondary">&copy; {{ date('Y') }} GearHub - Single Seller Gaming Gear Store</small>
            <small class="text-secondary">Skripsi S1 - Rekomendasi Produk User-based CF</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>