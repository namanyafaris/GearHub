@extends('admin.layouts.app')

@section('title','Detail Pesanan')

@section('content')
<h4>Pesanan #{{ $order->id }}</h4>
<div class="card mb-3 p-3">
	<h6>Buyer</h6>
	<p>{{ $order->user->name ?? '-' }}<br>{{ $order->user->email ?? '' }}</p>

	<h6>Alamat</h6>
	<p>{!! nl2br(e($order->address)) !!}</p>

	<h6>Items</h6>
	<table class="table table-sm">
		<thead>
			<tr>
				<th>Produk</th>
				<th>Harga</th>
				<th>Qty</th>
				<th>Subtotal</th>
			</tr>
		</thead>
		<tbody>
			@foreach($order->orderItems as $it)
			<tr>
				<td>{{ $it->product->name ?? '—' }}</td>
				<td>Rp {{ number_format($it->price,0,',','.') }}</td>
				<td>{{ $it->quantity }}</td>
				<td>Rp {{ number_format($it->price * $it->quantity,0,',','.') }}</td>
			</tr>
			@endforeach
		</tbody>
	</table>

	<h6>Total: Rp {{ number_format($order->total_price,0,',','.') }}</h6>

	<div class="mt-3">
		<form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="d-inline">@csrf
			<select name="status" class="form-select d-inline w-auto">
				<option value="pending" {{ $order->status=='pending'?'selected':'' }}>pending</option>
				<option value="processing" {{ $order->status=='processing'?'selected':'' }}>processing</option>
				<option value="shipped" {{ $order->status=='shipped'?'selected':'' }}>shipped</option>
				<option value="delivered" {{ $order->status=='delivered'?'selected':'' }}>delivered</option>
			</select>
			<button class="btn btn-primary btn-sm">Update Status</button>
		</form>

		@if($order->status === 'pending')
		<form action="{{ route('admin.orders.cancel', $order) }}" method="POST" class="d-inline ms-2">@csrf
			<button class="btn btn-danger btn-sm">Batalkan Pesanan</button>
		</form>
		@endif
	</div>
</div>
@endsection