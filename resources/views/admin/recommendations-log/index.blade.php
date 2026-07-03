@extends('admin.layouts.app', ['title' => 'Log Rekomendasi'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold mb-0">Log Rekomendasi (White-Box Testing)</h1>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <p class="text-secondary">Pilih Buyer (User) untuk melihat detail proses kalkulasi Collaborative Filtering dan rekomendasi produk yang dihasilkan.</p>
        
        <div class="table-responsive mt-3">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID User</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($buyers as $buyer)
                    <tr>
                        <td>{{ $buyer->id }}</td>
                        <td class="fw-semibold">{{ $buyer->name }}</td>
                        <td>{{ $buyer->email }}</td>
                        <td>
                            <a href="{{ route('admin.recommendations-log.show', $buyer) }}" class="btn btn-sm btn-primary">
                                Lihat Proses CF
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4">Belum ada data buyer.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
