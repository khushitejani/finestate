<form id="paintingForm" method="POST" action="{{ route('paintings.update', $painting->id) }}"
    enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div id="card-image-uploader-modal" data-output-width="400" data-output-height="400" data-aspect-ratio="1"
        class="mb-3">
        <label class="form-label fw-bold d-block">Share Image</label>

        <div class="ciu-box mx-auto position-relative"
            style="width:100%; max-width:500px; height:auto; cursor:pointer;
            display:flex; align-items:center; justify-content:center;
            background:#f9f9f9; border:2px dashed #ccc; border-radius:12px; overflow:hidden; min-height:200px;">
            @php
                $imageUrl =
                    isset($painting) && $painting->image && file_exists(public_path('storage/' . $painting->image))
                        ? asset('storage/' . $painting->image)
                        : asset('default.jpeg');
            @endphp

            <img class="ciu-preview" id="imagePreview" alt="Preview"
                style="width:auto; height:auto; max-width:100%; max-height:500px; 
                   {{ isset($painting) && $imageUrl ? '' : 'display:none;' }}"
                src="{{ $imageUrl }}">
            <span class="ciu-placeholder"
                style="font-size:2rem; color:#888; {{ isset($painting) && $imageUrl ? 'display:none;' : '' }}">
                + Image
            </span>
        </div>

        <input type="file" class="ciu-input d-none" name="image" id="imageInput" accept="image/*">
    </div>

    <div class="mb-3">
        <label for="paintingName" class="form-label">Name</label>
        <input type="text" class="form-control" id="paintingName" name="name" placeholder="Enter painting name"
            required value="{{ old('name', $painting->name) }}">
    </div>

    <div class="mb-3">
        <label class="form-label">No</label>
        <div class="input-group">
            <input type="number" id="noInput" name="no" class="form-control" value="{{ $painting->no }}"
                required>

            <button type="button" class="btn btn-info btn-sm generate-number-btn" data-table="Painting">
                Generate Number
            </button>
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label fw-bold">Years</label>
        <input type="text" name="years" class="form-control" value="{{ old('years', $painting->years) }}"
            placeholder="e.g. 1965–1999" required>
        @error('years')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="mb-3">
        <label for="paintingPrice" class="form-label">Price</label>
        <input type="number" step="0.01" class="form-control" id="paintingPrice" name="price"
            placeholder="Enter price" required value="{{ old('price', $painting->price) }}">
    </div>

    <button type="submit" class="btn btn-primary">Save Changes</button>
</form>
