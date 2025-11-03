<form id="stampForm" action="{{ route('stamps.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="mb-3">
        <label class="form-label fw-bold d-block">Stamp Image</label>

        <div class="ciu-box mx-auto position-relative" id="imageBox"
            style="width:100%; max-width:500px; min-height:200px; cursor:pointer;
                   display:flex; align-items:center; justify-content:center;
                   background:#f9f9f9; border:2px dashed #ccc; border-radius:12px; overflow:hidden;">
            <img id="imagePreview" class="ciu-preview" alt="Preview"
                style="display:none; width:auto; height:auto; max-width:100%; max-height:500px;">
            <span class="ciu-placeholder" style="font-size:2rem; color:#888;">+ Image</span>
        </div>

        <input type="file" name="image" id="imageInput" class="d-none" accept="image/*">
        @error('image')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" class="form-control" required>
    </div>
    <!-- Years -->
    <div class="mb-3">
        <label>Years</label>
        <input type="text" name="years" class="form-control" value="{{ old('years') }}"
            placeholder="e.g. 1965–1999" required>
        @error('years')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="mb-3">
        <label>Price</label>
        <input type="number" step="0.01" name="price" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Save Stamp</button>
</form>
