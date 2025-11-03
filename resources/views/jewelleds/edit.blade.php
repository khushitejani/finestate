<form id="jewelledForm" action="{{ route('jewelleds.update', $jewelled->id) }}" method="POST"
    enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div id="card-image-uploader-modal">
        <label class="form-label fw-bold d-block">Jewel Image</label>

        <div class="ciu-box mx-auto position-relative"
            style="width:100%; max-width:420px; height:auto; cursor:pointer;
                display:flex; align-items:center; justify-content:center;
                background:#f9f9f9; border:2px dashed #ccc; border-radius:12px; overflow:hidden;">

            <img id="imagePreview" src="{{ $jewelled->image ? asset('storage/' . $jewelled->image) : '' }}" alt="Preview"
                style="width:100%; height:100%; object-fit:cover; display:{{ $jewelled->image ? 'block' : 'none' }};">

            <span class="ciu-placeholder"
                style="font-size:2rem; color:#888; {{ $jewelled->image ? 'display:none;' : '' }}">+
                Image</span>
        </div>

        <input type="file" name="image" id="imageInput" class="d-none" accept="image/*">
    </div>
    <div class="mb-3">
        <label for="name" class="form-label">Jewelled Name</label>
        <input type="text" name="name" id="name" class="form-control"
            value="{{ old('name', $jewelled->name) }}" required>
    </div>

    <div class="mb-3">
        <label for="price" class="form-label">Price</label>
        <input type="number" step="0.01" name="price" id="price" class="form-control"
            value="{{ old('price', $jewelled->price) }}" required>
    </div>
    <div class="text-end">
        <button type="submit" class="btn btn-primary">Update Jewelled</button>
    </div>
</form>
