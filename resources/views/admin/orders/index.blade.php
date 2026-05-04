@extends('admin.layouts.app')

@section('title','Pesanan')

@section('content')
<div class="d-flex justify-content-between mb-3">
	<h4>Pesanan</h4>
</div>

<form class="row g-2 mb-3">
	<div class="col-auto">
		<select name="status" class="form-control" onchange="this.form.submit()">
			<option value="">Semua status</option>
			<option value="pending" {{ request('status')=='pending'?'selected':'' }}>pending</option>
			<option value="processing" {{ request('status')=='processing'?'selected':'' }}>processing</option>
			<option value="shipped" {{ request('status')=='shipped'?'selected':'' }}>shipped</option>
			<option value="delivered" {{ request('status')=='delivered'?'selected':'' }}>delivered</option>
		</select>
	</div>
</form>

<table class="table table-sm">
	<thead>
		<tr>
			<th>ID</th>
			<th>Buyer</th>
			<th>Total</th>
			<th>Status</th>
			<th>Tanggal</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		@foreach($orders as $o)
		<tr>
			<td>{{ $o->id }}</td>
			<td>{{ $o->user->name ?? '—' }}</td>
			<td>Rp {{ number_format($o->total_price,0,',','.') }}</td>
			<td>{{ $o->status }}</td>
			<td>{{ $o->created_at->format('Y-m-d') }}</td>
			<td><a href="{{ route('admin.orders.show', $o) }}" class="btn btn-sm btn-outline-primary">Detail</a></td>
		</tr>
		@endforeach
	</tbody>
</table>

{{ $orders->links() }}
@endsection