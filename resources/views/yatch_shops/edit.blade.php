<form id="yatchForm" method="POST" action="{{ route('yatchshop.update', $yatchshop->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    @php
        $yatchImages = json_decode($yatchshop->image, true); // decode JSON array
        $firstImage = isset($yatchImages[0]) ? $yatchImages[0] : null;
    @endphp

    <div id="card-image-uploader-modal">
        <label class="form-label fw-bold d-block">Yatch Image</label>

        <div class="ciu-box mx-auto position-relative"
            style="width:100%; max-width:420px; height:auto; cursor:pointer;
            display:flex; align-items:center; justify-content:center;
            background:#f9f9f9; border:2px dashed #ccc; border-radius:12px; overflow:hidden;">

            <img id="imagePreview" src="{{ $firstImage ? asset('storage/' . $firstImage) : '' }}" alt="Preview"
                style="width:100%; height:100%; object-fit:cover; display:{{ $firstImage ? 'block' : 'none' }};">

            <span class="ciu-placeholder" style="font-size:2rem; color:#888; {{ $firstImage ? 'display:none;' : '' }}">+
                Image</span>
        </div>

        <input type="file" name="image" id="imageInput" class="d-none" accept="image/*">
    </div>
    <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" name="name" value="{{ $yatchshop->name }}" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">No</label>
        <div class="input-group">
            <input type="number" id="noInput" name="no" class="form-control" value="{{ $yatchshop->no }}"
                required>

            <button type="button" class="btn btn-info btn-sm generate-number-btn" data-table="YatchShop">
                Generate Number
            </button>
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="3">{{ $yatchshop->description }}</textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Price</label>
        <input type="number" step="0.01" name="price" value="{{ $yatchshop->price }}" class="form-control"
            required>
    </div>

    <button type="submit" class="btn btn-primary">Update Yacht</button>
</form>
