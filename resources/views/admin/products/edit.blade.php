@extends('admin.layouts.app')

@section('title','Edit Produk')

@section('content')
<h4>Edit Produk</h4>
<form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
	@csrf
	@method('PUT')
	@include('admin.products.form')
	<button class="btn btn-primary">Perbarui</button>
</form>
@endsection