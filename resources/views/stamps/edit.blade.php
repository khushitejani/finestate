<form id="stampForm" action="{{ route('stamps.update', $stamp->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div id="card-image-uploader-modal">
        <label class="form-label fw-bold d-block">Jewel Image</label>

        <div class="ciu-box mx-auto position-relative"
            style="width:100%; max-width:420px; height:auto; cursor:pointer;
                display:flex; align-items:center; justify-content:center;
                background:#f9f9f9; border:2px dashed #ccc; border-radius:12px; overflow:hidden;">

            <img id="imagePreview" src="{{ $stamp->image ? asset('storage/' . $stamp->image) : '' }}" alt="Preview"
                style="width:100%; height:100%; object-fit:cover; display:{{ $stamp->image ? 'block' : 'none' }};">

            <span class="ciu-placeholder"
                style="font-size:2rem; color:#888; {{ $stamp->image ? 'display:none;' : '' }}">+
                Image</span>
        </div>

        <input type="file" name="image" id="imageInput" class="d-none" accept="image/*">
    </div>
    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" class="form-control" value="{{ $stamp->name }}" required>
    </div>
    <!-- Years -->
    <div class="mb-3">
        <label>Years</label>
        <input type="text" name="years" class="form-control" value="{{$stamp->years}}"
            placeholder="e.g. 1965–1999" required>
        @error('years')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    <div class="mb-3">
        <label>Price</label>
        <input type="number" step="0.01" name="price" class="form-control" value="{{ $stamp->price }}" required>
    </div>
    <button type="submit" class="btn btn-primary">Update Stamp</button>
</form>
