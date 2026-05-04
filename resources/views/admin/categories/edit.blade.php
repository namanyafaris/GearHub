@extends('admin.layouts.app')

@section('title','Edit Kategori')

@section('content')
<h4>Edit Kategori</h4>
<form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
	@csrf
	@method('PUT')
	@include('admin.categories.form')
	<button class="btn btn-primary">Perbarui</button>
</form>
@endsection