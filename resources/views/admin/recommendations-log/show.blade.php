@extends('admin.layouts.app', ['title' => 'Detail Log Rekomendasi'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-0">Detail Proses Rekomendasi</h1>
        <div class="text-secondary mt-1">User Target: <strong>{{ $user->name }}</strong> (ID: {{ $user->id }})</div>
    </div>
    <div>
        <a href="{{ route('admin.recommendations-log.index') }}" class="btn btn-outline-secondary me-2">Kembali</a>
        <a href="{{ route('admin.recommendations-log.show', ['user' => $user->id, 'export' => 'pdf']) }}" class="btn btn-danger" target="_blank">
            <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white fw-bold py-3">
        1. User-Item Vector (Target User)
    </div>
    <div class="card-body">
        @if(empty($targetVector))
            <div class="alert alert-warning mb-0">User ini belum memiliki interaksi (Cold Start Problem). Rekomendasi fallback akan digunakan.</div>
        @else
            <table class="table table-sm table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID Produk</th>
                        <th>Akumulasi Bobot (Weight)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($targetVector as $pId => $weight)
                    <tr>
                        <td>{{ $pId }}</td>
                        <td>{{ $weight }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white fw-bold py-3">
        2. Top Similar Users (Cosine Similarity)
    </div>
    <div class="card-body">
        @if(empty($similarUsersDetails))
            <div class="alert alert-secondary mb-0">Tidak ada user lain yang memiliki preferensi mirip.</div>
        @else
            <table class="table table-sm table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Rank</th>
                        <th>ID User</th>
                        <th>Nama User</th>
                        <th>Cosine Similarity Score</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($similarUsersDetails as $idx => $sim)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td>{{ $sim['id'] }}</td>
                        <td>{{ $sim['name'] }}</td>
                        <td class="fw-bold text-primary">{{ number_format($sim['score'], 4) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white fw-bold py-3">
        3. Kalkulasi Weighted Score Produk Rekomendasi
    </div>
    <div class="card-body">
        @if(empty($productScoresDetails))
            <div class="alert alert-secondary mb-0">Tidak ada produk yang bisa dikalkulasi dari similar users.</div>
        @else
            <table class="table table-sm table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Rank</th>
                        <th>ID Produk</th>
                        <th>Nama Produk</th>
                        <th>Total Weighted Score</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($productScoresDetails as $idx => $prod)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td>{{ $prod['id'] }}</td>
                        <td>{{ $prod['name'] }}</td>
                        <td class="fw-bold text-success">{{ number_format($prod['score'], 4) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

<div class="card border-0 shadow-sm mb-4 border-success border-2">
    <div class="card-header bg-success text-white fw-bold py-3">
        4. Final Output (Top 8 Rekomendasi)
    </div>
    <div class="card-body">
        <table class="table table-sm mb-0">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>ID Produk</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recommendations as $idx => $rec)
                <tr>
                    <td class="fw-bold">{{ $idx + 1 }}</td>
                    <td>{{ $rec->id }}</td>
                    <td>{{ $rec->name }}</td>
                    <td>{{ $rec->category->name ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">Tidak ada rekomendasi yang dihasilkan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
