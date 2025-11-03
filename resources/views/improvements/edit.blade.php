<form action="{{ route('improvements.update', $improvement->id) }}" method="POST" enctype="multipart/form-data"
    id="improvementsForm">
    @csrf
    @method('PUT')

    <div id="card-image-uploader-modal">
        <label class="form-label fw-bold d-block">Improvement Image</label>

        <div class="ciu-box mx-auto position-relative"
            style="width:100%; max-width:420px; height:auto; cursor:pointer;
                display:flex; align-items:center; justify-content:center;
                background:#f9f9f9; border:2px dashed #ccc; border-radius:12px; overflow:hidden;">

            <img id="imagePreview" src="{{ $improvement->image ? asset('storage/' . $improvement->image) : '' }}"
                alt="Preview"
                style="width:100%; height:100%; object-fit:cover; display:{{ $improvement->image ? 'block' : 'none' }};">

            <span class="ciu-placeholder"
                style="font-size:2rem; color:#888; {{ $improvement->image ? 'display:none;' : '' }}">+ Image</span>
        </div>

        <input type="file" name="image" id="imageInput" class="d-none" accept="image/*">
    </div>

    <div class="mb-3 mt-3">
        <label for="name" class="form-label">Improvement Name</label>
        <input type="text" name="name" class="form-control" value="{{ $improvement->name }}" required>
    </div>

    <div class="mb-3">
        <label for="price" class="form-label">Price</label>
        <input type="number" step="0.01" name="price" class="form-control" value="{{ $improvement->price }}"
            required>
    </div>

    <button type="submit" class="btn btn-success">Update Improvement</button>
</form>
