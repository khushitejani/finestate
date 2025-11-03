<form id="paintingForm" method="POST" action="{{ route('paintings.store') }}" enctype="multipart/form-data">
    @csrf

    <!-- your form inputs -->
    <div class="mb-3">
        <label for="name" class="form-label">Painting Name</label>
        <input type="text" class="form-control" id="name" name="name" required value="{{ old('name') }}">
    </div>
    <div class="mb-3">
        <label class="form-label fw-bold">Years</label>
        <input type="text" name="years" class="form-control" value="{{ old('years') }}"
            placeholder="e.g. 1965–1999" required>
        @error('years')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="mb-3">
        <label for="price" class="form-label">Price</label>
        <input type="number" step="0.01" class="form-control" id="price" name="price" required
            value="{{ old('price') }}">
    </div>

    <div class="mb-3">
        <label for="image" class="form-label">Painting Image</label>
        <input type="file" class="form-control" id="image" name="image" required>
    </div>

    <button type="submit" class="btn btn-success mt-3">Save Painting</button>
</form>
