@extends('admin.layouts.app')

@section('title','Tambah Kategori')

@section('content')
<h4>Tambah Kategori</h4>
<form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
	@csrf
	@include('admin.categories.form')
	<button class="btn btn-primary">Simpan</button>
</form>
@endsection