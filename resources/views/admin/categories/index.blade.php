@extends('admin.layouts.app')

@section('title','Kategori')

@section('content')
<div class="d-flex justify-content-between mb-3">
	<h4>Kategori</h4>
	<a href="{{ route('admin.categories.create') }}" class="btn btn-primary">Tambah Kategori</a>
</div>

<table class="table table-sm">
	<thead>
		<tr>
			<th>Nama</th>
			<th>Slug</th>
			<th>Jumlah Produk</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		@foreach($categories as $c)
		<tr>
			<td>{{ $c->name }}</td>
			<td>{{ $c->slug }}</td>
			<td>{{ $c->products_count }}</td>
			<td>
				<a href="{{ route('admin.categories.edit', $c) }}" class="btn btn-sm btn-outline-primary">Edit</a>
				<form action="{{ route('admin.categories.destroy', $c) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Hapus</button></form>
			</td>
		</tr>
		@endforeach
	</tbody>
</table>

{{ $categories->links() }}
@endsection