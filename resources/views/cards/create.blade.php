<form action="{{ route('cards.store') }}" method="POST" enctype="multipart/form-data" id="cardForm">
    @csrf

       <div id="card-image-uploader-modal">
        <label class="form-label fw-bold d-block">Card Image</label>

        <div class="ciu-box mx-auto position-relative"
            style="width:100%; max-width:400px; height:136px; cursor:pointer;
                display:flex; align-items:center; justify-content:center;
                background:#f9f9f9; border:2px dashed #ccc; border-radius:12px; overflow:hidden;">
            <img id="imagePreview" alt="Preview" style="width:100%; height:100%; object-fit:cover; display:none;">
            <span class="ciu-placeholder" style="font-size:2rem; color:#888;">+ Image</span>
        </div>

        <input type="file" name="image" id="imageInput" class="d-none" accept="image/*">
    </div>

    <div class="mb-3 mt-3">
        <label class="form-label">Card Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Price</label>
        <input type="number" name="price" step="0.01" class="form-control" value="{{ old('price') }}" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Price Sign</label>
        <input type="text" name="sign_price" class="form-control" value="{{ old('price_sign', '$') }}" required>
    </div>

    <button type="submit" class="btn btn-success">Save Card</button>
</form>
