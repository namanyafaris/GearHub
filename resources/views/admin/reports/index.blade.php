@extends('admin.layouts.app')

@section('title','Laporan Penjualan')

@section('content')
<h4>Laporan Penjualan</h4>

<form method="GET" class="row g-2 mb-3">
	<div class="col-auto">
		<select name="month" class="form-control">
			@for($m=1;$m<=12;$m++)
				<option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ $m }}</option>
				@endfor
		</select>
	</div>
	<div class="col-auto">
		<input type="number" name="year" value="{{ $year }}" class="form-control">
	</div>
	<div class="col-auto"><button class="btn btn-secondary">Filter</button></div>
</form>

<div class="mb-3">
	<form action="{{ route('admin.reports.export') }}" method="POST">@csrf
		<input type="hidden" name="month" value="{{ $month }}">
		<input type="hidden" name="year" value="{{ $year }}">
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