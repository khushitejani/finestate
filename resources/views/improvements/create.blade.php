<form action="{{ route('improvements.store') }}" method="POST" enctype="multipart/form-data" id="improvementsForm">
    @csrf
    {{-- <div id="card-image-uploader-modal" data-output-width="100" data-output-height="100" data-aspect-ratio="1">
        <label class="form-label fw-bold d-block">Improvements Image</label>

        <div class="ciu-box mx-auto position-relative"
            style="width:100%; max-width:200px; height:auto; cursor:pointer;
               display:flex; align-items:center; justify-content:center;
               background:#f9f9f9; border:2px dashed #ccc; border-radius:12px; overflow:hidden;">
            <img class="ciu-preview" id="imagePreview" alt="Preview" style="display:none;">
            <span class="ciu-placeholder" style="font-size:2rem; color:#888;">+ Image</span>
        </div>

        <input type="file" class="ciu-input d-none" id="imageInput" name="image" accept="image/*">
    </div> --}}
    <div id="card-image-uploader-modal" data-output-width="400" data-output-height="400" data-aspect-ratio="1"
        class="mb-3">
        <label class="form-label fw-bold d-block">Improvements Image</label>

        <div class="ciu-box mx-auto position-relative"
            style="width:100%; max-width:500px; height:auto; cursor:pointer;
               display:flex; align-items:center; justify-content:center;
               background:#f9f9f9; border:2px dashed #ccc; border-radius:12px; overflow:hidden; min-height:200px;">
            <img class="ciu-preview" id="imagePreview" alt="Preview"
                style="display:none; width:auto; height:auto; max-width:100%; max-height:500px;">
            <span class="ciu-placeholder" style="font-size:2rem; color:#888;">+ Image</span>
        </div>

        <input type="file" class="ciu-input d-none" id="imageInput" name="image" accept="image/*">
    </div>


    <div class="mb-3">
        <label for="name" class="form-label">Improvement Name</label>
        <input type="text" name="name" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="price" class="form-label">Price</label>
        <input type="number" step="0.01" name="price" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">Add Improvement</button>
</form>
