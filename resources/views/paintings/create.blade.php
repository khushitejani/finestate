<form id="paintingForm" method="POST" action="{{ route('paintings.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
        <label class="form-label fw-bold d-block">Painting Image</label>

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
    <!-- your form inputs -->
    <div class="mb-3">
        <label for="name" class="form-label">Painting Name</label>
        <input type="text" class="form-control" id="name" name="name" required value="{{ old('name') }}">
    </div>

    <div class="mb-3">
        <label class="form-label">No</label>
        <div class="input-group">
            <input type="number" id="noInput" name="no" class="form-control" placeholder="Enter or Generate"
                required>
            <button type="button" class="btn btn-info btn-sm generate-number-btn" data-table="Painting">
                Generate Number
            </button>
        </div>
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
    <button type="submit" class="btn btn-success mt-3">Save Painting</button>
</form>
