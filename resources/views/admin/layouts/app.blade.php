<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Admin - @yield('title', 'Dashboard')</title>
	<link href="/build/assets/app.css" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<style>
		body {
			padding-top: 56px;
		}

		.sidebar {
			min-height: 100vh;
		}
	</style>
</head>

<body>
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
		<div class="container-fluid">
			<a class="navbar-brand" href="{{ route('admin.dashboard') }}">Admin</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarNav">
				<ul class="navbar-nav ms-auto">
					<li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Lihat toko</a></li>
					<li class="nav-item"><a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a></li>
				</ul>
			</div>
		</div>
	</nav>

	<div class="container-fluid">
		<div class="row">
			<aside class="col-md-2 bg-light sidebar p-3">
				<ul class="nav flex-column">
					<li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
					<li class="nav-item"><a class="nav-link" href="{{ route('admin.products.index') }}">Produk</a></li>
					<li class="nav-item"><a class="nav-link" href="{{ route('admin.categories.index') }}">Kategori</a></li>
					<li class="nav-item"><a class="nav-link" href="{{ route('admin.orders.index') }}">Pesanan</a></li>
					<li class="nav-item"><a class="nav-link" href="{{ route('admin.reports.index') }}">Laporan</a></li>
				</ul>
			</aside>

			<main class="col-md-10 py-4">
				<div class="container-fluid">
					@if(session('success'))
					<div class="alert alert-success">{{ session('success') }}</div>
					@endif
					@if(session('error'))
					<div class="alert alert-danger">{{ session('error') }}</div>
					@endif
					@yield('content')
				</div>
			</main>
		</div>
	</div>

	<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	@stack('scripts')
</body>

</html>