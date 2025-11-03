<form id="insightForm" action="{{ route('insights.update', $insight->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div id="card-image-uploader-modal">
        <label class="form-label fw-bold d-block">Insight Image</label>

        <div class="ciu-box mx-auto position-relative"
            style="width:100%; max-width:420px; height:auto; cursor:pointer;
                display:flex; align-items:center; justify-content:center;
                background:#f9f9f9; border:2px dashed #ccc; border-radius:12px; overflow:hidden;">

            <img id="imagePreview" src="{{ $insight->image ? asset('storage/' . $insight->image) : '' }}" alt="Preview"
                style="width:100%; height:100%; object-fit:cover; display:{{ $insight->image ? 'block' : 'none' }};">

            <span class="ciu-placeholder"
                style="font-size:2rem; color:#888; {{ $insight->image ? 'display:none;' : '' }}">+
                Image</span>
        </div>

        <input type="file" name="image" id="imageInput" class="d-none" accept="image/*">
    </div>
    <div class="mb-3">
        <label for="name" class="form-label">Name</label>
        <input type="text" name="name" id="name" class="form-control"
            value="{{ old('name', $insight->name) }}" required>
    </div>

    <!-- Years -->
    <div class="mb-3">
        <label for="years" class="form-label">Years</label>
        <input type="text" name="years" id="years" class="form-control"
            value="{{ old('years', $insight->years) }}" placeholder="e.g. 1990–2020" required>
        @error('years')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>


    <div class="mb-3">
        <label for="conditions" class="form-label">Conditions</label>
        <textarea name="conditions" id="conditions" class="form-control" rows="3">{{ old('conditions', $insight->conditions) }}</textarea>
    </div>
    <button type="submit" class="btn btn-primary">Update</button>
</form>
