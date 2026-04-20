@extends('buyer.layouts.app', ['title' => 'Detail Pesanan'])

@section('content')
@php
$statusClass = [
'pending' => 'warning',
'processing' => 'info',
'shipped' => 'primary',
'delivered' => 'success',
'cancelled' => 'danger',
];
@endphp

<div class="d-flex justify-content-between align-items-center mb-4">
	<h1 class="h4 fw-bold mb-0">Detail Pesanan #{{ $order->id }}</h1>
	<span class="badge text-bg-{{ $statusClass[$order->status] ?? 'secondary' }}">{{ ucfirst($order->status) }}</span>
</div>

<div class="row g-4 mb-4">
	<div class="col-lg-7">
		<div class="card border-0 shadow-sm">
			<div class="card-body">
				<h2 class="h6 fw-bold mb-3">Daftar Produk</h2>
				<div class="table-responsive">
					<table class="table align-middle mb-0">
						<thead>
							<tr>
								<th>Produk</th>
								<th>Harga</th>
								<th>Qty</th>
								<th>Subtotal</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($order->orderItems as $item)
							<tr>
								<td>{{ $item->product?->name ?? 'Produk tidak tersedia' }}</td>
								<td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
								<td>{{ $item->quantity }}</td>
								<td class="fw-semibold">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-5">
		<div class="card border-0 shadow-sm mb-3">
			<div class="card-body">
				<h2 class="h6 fw-bold mb-3">Info Pengiriman</h2>
				<p class="mb-2"><span class="fw-semibold">Penerima:</span> {{ $order->shipping_name }}</p>
				<p class="mb-2"><span class="fw-semibold">Telepon:</span> {{ $order->shipping_phone }}</p>
				<p class="mb-0"><span class="fw-semibold">Alamat:</span> {{ $order->shipping_address }}</p>
			</div>
		</div>

		<div class="card border-0 shadow-sm">
			<div class="card-body">
				<h2 class="h6 fw-bold mb-3">Ringkasan</h2>
				<div class="d-flex justify-content-between mb-2">
					<span>Metode Bayar</span>
					<span class="text-capitalize">{{ $order->payment_method }}</span>
				</div>
				<div class="d-flex justify-content-between fw-semibold">
					<span>Total</span>
					<span>Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
				</div>
			</div>
		</div>
	</div>
</div>

<a href="{{ route('orders.index') }}" class="btn btn-outline-dark">Kembali ke Riwayat Pesanan</a>
@endsection