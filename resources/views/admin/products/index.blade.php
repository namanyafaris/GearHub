@extends('admin.layouts.app')

@section('title','Produk')

@section('content')
<div class="d-flex justify-content-between mb-3">
	<h4>Produk</h4>
	<a href="{{ route('admin.products.create') }}" class="btn btn-primary">Tambah Produk</a>
</div>

<form class="row g-2 mb-3">
	<div class="col-auto"><input name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari..."></div>
	<div class="col-auto">
		<select name="category" class="form-control">
			<option value="">Semua kategori</option>
			@foreach($categories as $c)
			<option value="{{ $c->id }}" {{ request('category') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
			@endforeach
		</select>
	</div>
	<div class="col-auto"><button class="btn btn-secondary">Filter</button></div>
</form>

<table class="table table-sm">
	<thead>
		<tr>
			<th>Gambar</th>
			<th>Nama</th>
			<th>Kategori</th>
			<th>Harga</th>
			<th>Stok</th>
			<th>Status</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		@foreach($products as $p)
		<tr>
			<td style="width:80px;"><img src="{{ $p->image_url }}" alt="" class="img-fluid"></td>
			<td>{{ $p->name }}</td>
			<td>{{ $p->category->name ?? '-' }}</td>
			<td>Rp {{ number_format($p->price,0,',','.') }}</td>
			<td>{{ $p->stock }}</td>
			<td>{{ $p->is_active ? 'Aktif' : 'Nonaktif' }}</td>
			<td>
				<a href="{{ route('admin.products.edit', $p) }}" class="btn btn-sm btn-outline-primary">Edit</a>
				<form action="{{ route('admin.products.destroy', $p) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Hapus</button></form>
			</td>
		</tr>
		@endforeach
	</tbody>
</table>

{{ $products->links() }}
@endsection