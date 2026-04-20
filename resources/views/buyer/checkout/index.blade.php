@extends('buyer.layouts.app', ['title' => 'Checkout'])

@section('content')
<h1 class="h4 fw-bold mb-4">Checkout</h1>

<div class="row g-4">
	<div class="col-lg-7">
		<div class="card border-0 shadow-sm">
			<div class="card-body">
				<form action="{{ route('checkout.store') }}" method="POST" class="row g-3">
					@csrf
					<div class="col-12">
						<label for="shipping_name" class="form-label">Nama Penerima</label>
						<input type="text" id="shipping_name" name="shipping_name" class="form-control" value="{{ old('shipping_name', auth()->user()->name) }}" required>
					</div>

					<div class="col-12">
						<label for="shipping_phone" class="form-label">Nomor Telepon</label>
						<input type="text" id="shipping_phone" name="shipping_phone" class="form-control" value="{{ old('shipping_phone') }}" required>
					</div>

					<div class="col-12">
						<label for="shipping_address" class="form-label">Alamat Lengkap</label>
						<textarea id="shipping_address" name="shipping_address" class="form-control" rows="4" required>{{ old('shipping_address') }}</textarea>
					</div>

					<div class="col-12">
						<label class="form-label d-block">Metode Pembayaran</label>
						<div class="form-check mb-2">
							<input class="form-check-input" type="radio" name="payment_method" id="transfer" value="transfer" {{ old('payment_method', 'transfer') === 'transfer' ? 'checked' : '' }}>
							<label class="form-check-label" for="transfer">Transfer Bank</label>
						</div>
						<div class="form-check">
							<input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" {{ old('payment_method') === 'cod' ? 'checked' : '' }}>
							<label class="form-check-label" for="cod">COD</label>
						</div>
					</div>

					<div class="col-12">
						<button type="submit" class="btn btn-brand">Buat Pesanan</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div class="col-lg-5">
		<div class="card border-0 shadow-sm">
			<div class="card-body">
				<h2 class="h6 fw-bold mb-3">Ringkasan Pesanan</h2>
				@foreach ($cartItems as $item)
				<div class="d-flex justify-content-between small mb-2">
					<span>{{ $item->product->name }} x {{ $item->quantity }}</span>
					<span>Rp {{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}</span>
				</div>
				@endforeach
				<hr>
				<div class="d-flex justify-content-between fw-semibold">
					<span>Total</span>
					<span>Rp {{ number_format($total, 0, ',', '.') }}</span>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection