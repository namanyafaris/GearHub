@extends('admin.layouts.app')

@section('title','Laporan Penjualan')

@section('content')
<h4>Laporan Penjualan</h4>

<form method="GET" class="row g-2 mb-3 align-items-center">
	<div class="col-auto">
		<label for="start_date" class="col-form-label">Dari Tanggal:</label>
	</div>
	<div class="col-auto">
		<input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="form-control">
	</div>
	<div class="col-auto">
		<label for="end_date" class="col-form-label">Sampai Tanggal:</label>
	</div>
	<div class="col-auto">
		<input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="form-control">
	</div>
	<div class="col-auto">
		<button class="btn btn-secondary">Filter</button>
	</div>
</form>

<div class="mb-3">
	<form action="{{ route('admin.reports.export') }}" method="POST">@csrf
		<input type="hidden" name="start_date" value="{{ $startDate }}">
		<input type="hidden" name="end_date" value="{{ $endDate }}">
		<button class="btn btn-outline-primary">Export PDF</button>
	</form>
</div>

<table class="table table-sm">
	<thead>
		<tr>
			<th>Tanggal</th>
			<th>ID Pesanan</th>
			<th>Nama Buyer</th>
			<th>Total</th>
			<th>Status</th>
		</tr>
	</thead>
	<tbody>
		@foreach($transactions as $t)
		<tr>
			<td>{{ $t->created_at->format('Y-m-d') }}</td>
			<td>{{ $t->id }}</td>
			<td>{{ $t->user->name ?? '-' }}</td>
			<td>Rp {{ number_format($t->total_price,0,',','.') }}</td>
			<td>{{ $t->status }}</td>
		</tr>
		@endforeach
	</tbody>
</table>

<h5>Total Pendapatan: Rp {{ number_format($total,0,',','.') }}</h5>

@endsection