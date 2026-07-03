<!doctype html>
<html>

<head>
	<meta charset="utf-8">
	<title>Laporan Penjualan</title>
	<style>
		table {
			width: 100%;
			border-collapse: collapse
		}

		th,
		td {
			border: 1px solid #ccc;
			padding: 6px
		}
	</style>
</head>

<body>
	<h3>Laporan Penjualan ({{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }})</h3>
	<table>
		<thead>
			<tr>
				<th>Tanggal</th>
				<th>ID</th>
				<th>Buyer</th>
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
				<td>{{ number_format($t->total_price,0,',','.') }}</td>
				<td>{{ $t->status }}</td>
			</tr>
			@endforeach
		</tbody>
	</table>
	<h4>Total: Rp {{ number_format($total,0,',','.') }}</h4>
</body>

</html>