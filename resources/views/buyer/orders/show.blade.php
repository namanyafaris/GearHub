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
	<div>
		<a href="{{ route('orders.invoice', $order) }}" class="btn btn-sm btn-outline-danger me-2">Download Invoice PDF</a>
		<span class="badge text-bg-{{ $statusClass[$order->status] ?? 'secondary' }}">{{ ucfirst($order->status) }}</span>
	</div>
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

		@if($order->payment_method === 'transfer')
		<div class="card border-0 shadow-sm mt-3 border-primary">
			<div class="card-body">
				<h2 class="h6 fw-bold mb-3 text-primary">Bukti Pembayaran</h2>
				
				@if($order->payment_proof)
					<div class="alert alert-success mb-2">Bukti pembayaran telah diunggah.</div>
					<a href="{{ asset('storage/' . $order->payment_proof) }}" target="_blank" class="btn btn-outline-primary btn-sm w-100">Lihat Bukti Struk</a>
				@elseif($order->status !== 'pending')
					<div class="alert alert-info mb-0">Pesanan telah diproses. Bukti transfer tidak diperlukan lagi.</div>
				@else
					<div class="alert alert-warning mb-3">
						Silakan transfer sebesar <strong>Rp {{ number_format($order->total_price, 0, ',', '.') }}</strong> ke rekening berikut agar pesanan dapat diproses:
						<div class="mt-2 p-2 bg-white text-dark rounded border border-warning" style="font-size: 14px;">
							<strong>Bank BCA</strong><br>
							No. Rekening: <strong>1234567890</strong><br>
							Atas Nama: <strong>Syawal Alfarisi</strong>
						</div>
					</div>
					
					<form action="{{ route('orders.payment_proof', $order) }}" method="POST" enctype="multipart/form-data" onsubmit="this.querySelector('button').disabled=true; this.querySelector('.spinner-border').classList.remove('d-none');">
						@csrf
						<div class="mb-3">
							<input class="form-control form-control-sm" type="file" name="payment_proof" accept="image/jpeg,image/png" required>
							<div class="form-text text-muted" style="font-size: 11px;">Format: JPG, PNG. Maksimal 2MB. Jika gambar terlalu besar, proses upload akan gagal.</div>
						</div>
						<button type="submit" class="btn btn-primary btn-sm w-100">
							<span class="spinner-border spinner-border-sm d-none me-1" role="status" aria-hidden="true"></span>
							Kirim Bukti Pembayaran
						</button>
					</form>
				@endif
			</div>
		</div>
		@endif
	</div>
</div>

<a href="{{ route('orders.index') }}" class="btn btn-outline-dark">Kembali ke Riwayat Pesanan</a>
@endsection