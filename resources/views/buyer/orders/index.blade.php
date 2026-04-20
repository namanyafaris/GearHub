@extends('buyer.layouts.app', ['title' => 'Riwayat Pesanan'])

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

<h1 class="h4 fw-bold mb-4">Riwayat Pesanan</h1>

<div class="card border-0 shadow-sm">
	<div class="table-responsive">
		<table class="table align-middle mb-0">
			<thead>
				<tr>
					<th>ID Pesanan</th>
					<th>Tanggal</th>
					<th>Item</th>
					<th>Total</th>
					<th>Status</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				@forelse ($orders as $order)
				<tr>
					<td>#{{ $order->id }}</td>
					<td>{{ $order->created_at->format('d M Y H:i') }}</td>
					<td>{{ (int) ($order->total_items ?? 0) }} produk</td>
					<td class="fw-semibold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
					<td>
						<span class="badge text-bg-{{ $statusClass[$order->status] ?? 'secondary' }}">{{ ucfirst($order->status) }}</span>
					</td>
					<td>
						<a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-dark">Lihat Detail</a>
					</td>
				</tr>
				@empty
				<tr>
					<td colspan="6" class="text-center py-4">Belum ada pesanan.</td>
				</tr>
				@endforelse
			</tbody>
		</table>
	</div>
</div>

<div class="mt-4">
	{{ $orders->links() }}
</div>
@endsection