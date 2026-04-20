@extends('buyer.layouts.app', ['title' => 'Keranjang Belanja'])

@section('content')
<h1 class="h4 fw-bold mb-4">Keranjang Belanja</h1>

@if ($cartItems->isEmpty())
<div class="alert alert-info">
	Keranjang masih kosong. <a href="{{ route('products.index') }}" class="alert-link">Mulai belanja sekarang</a>.
</div>
@else
<div class="table-responsive mb-4">
	<table class="table align-middle">
		<thead>
			<tr>
				<th>Produk</th>
				<th>Harga</th>
				<th style="width: 180px;">Quantity</th>
				<th>Subtotal</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			@foreach ($cartItems as $item)
			<tr>
				<td>
					<div class="d-flex align-items-center gap-3">
						@if (! empty($item->product->image))
						<img src="{{ asset($item->product->image) }}" alt="{{ $item->product->name }}" width="64" height="64" class="rounded" style="object-fit: cover;" onerror="this.style.display='none';">
						@endif
						<div>
							<div class="fw-semibold">{{ $item->product->name }}</div>
							<small class="text-secondary">{{ $item->product->category?->name }}</small>
						</div>
					</div>
				</td>
				<td>{{ $item->product->format_price }}</td>
				<td>
					<form action="{{ route('cart.update', $item) }}" method="POST" class="d-flex gap-2">
						@csrf
						@method('PATCH')
						<input type="number" name="quantity" min="1" max="{{ $item->product->stock }}" value="{{ $item->quantity }}" class="form-control form-control-sm">
						<button type="submit" class="btn btn-outline-dark btn-sm">Update</button>
					</form>
				</td>
				<td class="fw-semibold">Rp {{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}</td>
				<td>
					<form action="{{ route('cart.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus item ini dari keranjang?')">
						@csrf
						@method('DELETE')
						<button type="submit" class="btn btn-outline-danger btn-sm">Hapus</button>
					</form>
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>
</div>

<div class="d-flex justify-content-end">
	<div class="card border-0 shadow-sm" style="min-width: 320px;">
		<div class="card-body">
			<div class="d-flex justify-content-between mb-3">
				<span class="fw-semibold">Total</span>
				<span class="h5 mb-0">Rp {{ number_format($total, 0, ',', '.') }}</span>
			</div>
			<a href="{{ route('checkout.index') }}" class="btn btn-brand w-100">Lanjut ke Checkout</a>
		</div>
	</div>
</div>
@endif
@endsection