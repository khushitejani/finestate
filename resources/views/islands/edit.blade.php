<form id="islandForm" action="{{ route('islands.update', $island->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div id="card-image-uploader-modal">
        <label class="form-label fw-bold d-block">Retro Cars Image</label>

        <div class="ciu-box mx-auto position-relative"
            style="width:100%; max-width:420px; height:auto; cursor:pointer;
                display:flex; align-items:center; justify-content:center;
                background:#f9f9f9; border:2px dashed #ccc; border-radius:12px; overflow:hidden;">

            <img id="imagePreview" src="{{ $island->image ? asset('storage/' . $island->image) : '' }}" alt="Preview"
                style="width:100%; height:100%; object-fit:cover; display:{{ $island->image ? 'block' : 'none' }};">

            <span class="ciu-placeholder"
                style="font-size:2rem; color:#888; {{ $island->image ? 'display:none;' : '' }}">+
                Image</span>
        </div>

        <input type="file" name="image" id="imageInput" class="d-none" accept="image/*">
    </div>
    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $island->name) }}" required>
    </div>

    <div class="mb-3">
        <label class="form-label">No</label>
        <div class="input-group">
            <input type="number" id="noInput" name="no" class="form-control" value="{{ $island->no }}"
                required>

            <button type="button" class="btn btn-info btn-sm generate-number-btn" data-table="Island">
                Generate Number
            </button>
        </div>
    </div>

    <div class="mb-3">
        <label>Price (ETH)</label>
        <input type="number" step="0.01" name="price" class="form-control"
            value="{{ old('price', $island->price) }}" required>
    </div>

    <div class="mb-3">
        <label>Description</label>
        <textarea name="description" class="form-control" rows="3">{{ old('description', $island->description) }}</textarea>
    </div>

    <button type="submit" class="btn btn-primary">Update Island</button>
</form>
