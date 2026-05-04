@extends('admin.layouts.app')

@section('title','Tambah Produk')

@section('content')
<h4>Tambah Produk</h4>
<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
	@csrf
	@include('admin.products.form')
	<button class="btn btn-primary">Simpan</button>
</form>
@endsection