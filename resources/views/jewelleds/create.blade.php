<form id="jewelledForm" action="{{ route('jewelleds.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="mb-3">
        <label class="form-label fw-bold d-block">Jewel Image</label>

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
    {{-- Name --}}
    <div class="mb-3">
        <label for="name" class="form-label">Jewelled Name</label>
        <input type="text" name="name" id="name" class="form-control" placeholder="Enter jewelled name"
            required>
    </div>

    {{-- Price --}}
    <div class="mb-3">
        <label for="price" class="form-label">Price</label>
        <input type="number" step="0.01" name="price" id="price" class="form-control"
            placeholder="Enter price" required>
    </div>

    {{-- Submit --}}
    <div class="text-end">
        <button type="submit" class="btn btn-primary">Save Jewelled</button>
    </div>
</form>
