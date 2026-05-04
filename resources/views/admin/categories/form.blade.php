<div class="mb-3">
	<label class="form-label">Nama</label>
	<input type="text" name="name" value="{{ old('name', $category->name ?? '') }}" class="form-control" required>
</div>
<div class="mb-3">
	<label class="form-label">Gambar</label>
	<input type="file" name="image" class="form-control">
	@if(!empty($category->image))
	<img src="{{ asset('storage/'.$category->image) }}" class="img-fluid mt-2" style="max-width:150px;">
	@endif
</div>