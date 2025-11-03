<form action="{{ route('properties.update', $property->id) }}" method="POST" enctype="multipart/form-data"
    id="propertyForm">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label class="form-label fw-bold">Property Images</label>

        <div class="d-flex flex-column align-items-center">
            <!-- Main Preview -->
            <div id="mainPreviewWrapper"
                style="width:400px; height:300px; border:2px dashed #ccc; display:flex; align-items:center; justify-content:center; margin-bottom:10px; overflow:hidden; border-radius:12px;">
                @php
                    $images = $property->image ? json_decode($property->image, true) : [];
                    $firstImage = $images[0] ?? null;
                @endphp
                @if ($firstImage)
                    <img id="mainPreview" src="{{ asset('storage/' . $firstImage) }}"
                        style="width:100%; height:100%; object-fit:cover;">
                @else
                    <span id="mainPlaceholder" style="font-size:2rem; color:#888;">+ Image</span>
                @endif
            </div>

            <!-- Hidden file input -->
            <input type="file" id="imageInput" name="property_images[]" multiple class="d-none" accept="image/*">

            <!-- Thumbnails -->
            <div id="thumbnails" class="d-flex gap-2 flex-wrap" style="max-width:400px;"></div>

            <button type="button" class="btn btn-outline-primary mt-2" id="addImagesBtn">Add Images</button>

            <!-- Hidden input for old images -->
            <input type="hidden" name="property_images_json" id="propertyImagesJson" value="{{ $property->image }}">
        </div>
    </div>

    <div class="mb-3 mt-3">
        <label class="form-label fw-bold">Income Per Hour</label>
        <input type="number" name="income_per_hour" class="form-control" step="0.01" min="0"
            value="{{ old('income_per_hour', $property->income_per_hour) }}" required>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Address</label>
        <textarea name="address" class="form-control" rows="2" required>{{ old('address', $property->address) }}</textarea>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Price</label>
        <input type="number" name="price" step="0.01" class="form-control"
            value="{{ old('price', $property->price) }}" required>
    </div>

    <button type="submit" class="btn btn-primary">Update Property</button>
</form>

<script>
    const mainWrapper = document.getElementById('mainPreviewWrapper');
    const thumbnails = document.getElementById('thumbnails');
    const imageInput = document.getElementById('imageInput');
    const addImagesBtn = document.getElementById('addImagesBtn');
    const propertyImagesJson = document.getElementById('propertyImagesJson');

    // Arrays for old images and new files
    let oldImages = propertyImagesJson.value ? JSON.parse(propertyImagesJson.value) : [];
    let newFiles = [];

    function renderThumbnails() {
        thumbnails.innerHTML = '';

        // Old images
        oldImages.forEach((path, idx) => {
            const wrapper = document.createElement('div');
            wrapper.classList.add('thumbnail-wrapper', 'position-relative');
            wrapper.dataset.index = idx;
            wrapper.dataset.type = 'old';
            wrapper.innerHTML = `
            <img src="/storage/${path}" class="thumbnail rounded border" style="width:80px; height:60px; object-fit:cover; cursor:pointer;">
            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 remove-thumb">×</button>
        `;
            thumbnails.appendChild(wrapper);
        });

        // New images
        newFiles.forEach((file, idx) => {
            const wrapper = document.createElement('div');
            wrapper.classList.add('thumbnail-wrapper', 'position-relative');
            wrapper.dataset.index = idx;
            wrapper.dataset.type = 'new';

            const reader = new FileReader();
            reader.onload = function(e) {
                wrapper.innerHTML = `
                <img src="${e.target.result}" class="thumbnail rounded border" style="width:80px; height:60px; object-fit:cover; cursor:pointer;">
                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 remove-thumb">×</button>
            `;
            }
            reader.readAsDataURL(file);
            thumbnails.appendChild(wrapper);
        });

        // Add click listener to each thumbnail to update main preview
        thumbnails.querySelectorAll('.thumbnail').forEach(img => {
            img.addEventListener('click', function() {
                mainWrapper.innerHTML =
                    `<img id="mainPreview" src="${this.src}" style="width:100%; height:100%; object-fit:cover;">`;
            });
        });

        updateMainPreview();
    }

    // Add new images
    addImagesBtn.addEventListener('click', () => imageInput.click());
    imageInput.addEventListener('change', function() {
        const files = Array.from(this.files);
        files.forEach(f => newFiles.push(f));
        renderThumbnails();
        this.value = '';
    });

    // Remove image
    thumbnails.addEventListener('click', function(e) {
        if (!e.target.classList.contains('remove-thumb')) return;
        const wrapper = e.target.closest('.thumbnail-wrapper');
        const type = wrapper.dataset.type;
        const idx = parseInt(wrapper.dataset.index);

        if (type === 'old') oldImages.splice(idx, 1);
        if (type === 'new') newFiles.splice(idx, 1);

        renderThumbnails();
    });

    document.getElementById('propertyForm').addEventListener('submit', function() {
        propertyImagesJson.value = JSON.stringify(oldImages);

        const dt = new DataTransfer();
        newFiles.forEach(f => dt.items.add(f));
        imageInput.files = dt.files;

        console.log(imageInput.files);
    });
    renderThumbnails();
</script>
