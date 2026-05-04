<div class="mb-3">
	<label class="form-label">Nama</label>
	<input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" class="form-control" required>
</div>
<div class="mb-3">
	<label class="form-label">Kategori</label>
	<select name="category_id" class="form-control" required>
		@foreach($categories as $c)
		<option value="{{ $c->id }}" {{ (old('category_id', $product->category_id ?? '') == $c->id) ? 'selected' : '' }}>{{ $c->name }}</option>
		@endforeach
	</select>
</div>
<div class="mb-3">
	<label class="form-label">Deskripsi</label>
	<textarea name="description" class="form-control" rows="4">{{ old('description', $product->description ?? '') }}</textarea>
</div>
<div class="row">
	<div class="col-md-4 mb-3">
		<label class="form-label">Harga</label>
		<input type="number" name="price" value="{{ old('price', $product->price ?? '') }}" class="form-control" required>
	</div>
	<div class="col-md-4 mb-3">
		<label class="form-label">Stok</label>
		<input type="number" name="stock" value="{{ old('stock', $product->stock ?? '') }}" class="form-control" required>
	</div>
	<div class="col-md-4 mb-3">
		<label class="form-label">Aktif</label>
		<div class="form-check">
			<input type="checkbox" name="is_active" class="form-check-input" {{ old('is_active', $product->is_active ?? false) ? 'checked' : '' }}>
		</div>
	</div>
</div>
<div class="mb-3">
	<label class="form-label">Gambar Utama</label>
	<input type="file" name="image" class="form-control">
	@if(!empty($product->image))
	<img src="{{ asset('storage/'.$product->image) }}" class="img-fluid mt-2" style="max-width:150px;">
	@endif
</div>
<div class="mb-3">
	<label class="form-label">Gambar Tambahan</label>
	<input type="file" name="images[]" multiple class="form-control">
	@if(!empty($product->productImages))
	<div class="mt-2">
		@foreach($product->productImages as $img)
		<img src="{{ asset('storage/'.$img->path) }}" class="img-thumbnail me-2 mb-2" style="width:80px;">
		@endforeach
	</div>
	@endif
</div>