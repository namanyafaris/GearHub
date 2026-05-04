@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card p-3">
            <h6>Total Produk Aktif</h6>
            <h3>{{ $totalProducts }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <h6>Pesanan Hari Ini</h6>
            <h3>{{ $ordersToday }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <h6>Pesanan Pending</h6>
            <h3>{{ $pendingOrders }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <h6>Total Pendapatan Bulan Ini</h6>
            <h3>Rp {{ number_format($revenueMonth,0,',','.') }}</h3>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card p-3 mb-4">
            <h6>Penjualan 7 Hari Terakhir</h6>
            <div id="sales-data" data-labels='@json($labels)' data-values='@json($data)'></div>
            <canvas id="salesChart" height="120"></canvas>
        </div>

        <div class="card p-3">
            <h6>5 Pesanan Terbaru</h6>
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Buyer</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($latestOrders as $o)
                    <tr>
                        <td>{{ $o->id }}</td>
                        <td>{{ $o->user->name ?? '—' }}</td>
                        <td>Rp {{ number_format($o->total_price,0,',','.') }}</td>
                        <td>{{ $o->status }}</td>
                        <td>{{ $o->created_at->format('Y-m-d') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-3">
            <h6>Shortcut</h6>
            <ul class="list-unstyled">
                <li><a href="{{ route('admin.products.index') }}">Kelola Produk</a></li>
                <li><a href="{{ route('admin.categories.index') }}">Kelola Kategori</a></li>
                <li><a href="{{ route('admin.orders.index') }}">Kelola Pesanan</a></li>
            </ul>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const holder = document.getElementById('sales-data');
    const labels = JSON.parse(holder?.dataset.labels || '[]');
    const data = JSON.parse(holder?.dataset.values || '[]');
    const ctx = document.getElementById('salesChart');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Pendapatan',
                data: data,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)'
            }]
        },
        options: {
            responsive: true
        }
    });
</script>
@endpush