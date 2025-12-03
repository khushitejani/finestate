<form action="{{ route('aircraftshops.update', $aircraftShop->id) }}" method="POST" enctype="multipart/form-data"
    id="aircraftShopForm">
    @csrf
    @method('PATCH')

    <div class="mb-3">
        <label class="form-label fw-bold">Shop Images</label>
        <div class="d-flex flex-column align-items-center">

            <div id="mainPreviewWrapper"
                style="width:400px; height:200px; border:2px dashed #ccc; display:flex; align-items:center; justify-content:center; margin-bottom:10px; overflow:hidden; border-radius:12px; background:#f9f9f9;">
                <span id="mainPlaceholder" style="font-size:2rem; color:#888;">+ Image</span>
            </div>

            <input type="hidden" name="existing_images[]" id="existingImagesInput">

            <input type="file" name="images[]" id="imageInput" class="d-none" accept="image/*" multiple>

            <div id="imagePreview" class="d-flex gap-2 flex-wrap" style="max-width:400px;">
                @php
                    $oldImages = is_array($aircraftShop->images)
                        ? $aircraftShop->images
                        : json_decode($aircraftShop->images, true);
                    $oldImages = is_array($oldImages) ? $oldImages : [];
                @endphp

                @foreach ($oldImages as $img)
                    @php $img = trim($img); @endphp
                    <div class="position-relative me-2 mb-2"
                        style="width:100px; height:80px; display:flex; align-items:center; justify-content:center; overflow:hidden; background:#f9f9f9; border:1px solid #ddd; border-radius:6px;">
                        <img src="{{ asset('storage/' . $img) }}" class="rounded border thumbnail existing-img"
                            style="max-width:100%; max-height:100%; object-fit:contain; cursor:pointer;">
                        <button type="button"
                            class="btn btn-sm btn-danger position-absolute top-0 end-0 remove-image">×</button>
                    </div>
                @endforeach
            </div>

            <button type="button" class="btn btn-outline-primary mt-2" id="addImagesBtn">Add Images</button>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $aircraftShop->name) }}"
            required>
    </div>

    <div class="mb-3">
        <label class="form-label">No</label>
        <div class="input-group">
            <input type="number" id="noInput" name="no" class="form-control" value="{{ $aircraftShop->no }}"
                required>
            <button type="button" class="btn btn-info btn-sm generate-number-btn" data-table="AircraftShop">
                Generate Number
            </button>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Price</label>
        <input type="number" step="0.01" name="price" class="form-control"
            value="{{ old('price', $aircraftShop->price) }}" required>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Description</label>
        <textarea name="description" rows="4" class="form-control">{{ old('description', $aircraftShop->description) }}</textarea>
    </div>

    <button type="submit" class="btn btn-primary">Update Aircraft Shop</button>
</form>

<script>
    $(document).ready(function() {
        let filesArray = [];
        const input = $('#imageInput');
        const addBtn = $('#addImagesBtn');
        const preview = $('#imagePreview');
        const mainWrapper = $('#mainPreviewWrapper');
        const form = $('#aircraftShopForm');

        // Load existing images
        $('#imagePreview .existing-img').each(function() {
            const imgSrc = $(this).attr('src');
            filesArray.push({
                existing: true,
                src: imgSrc
            });
        });

        // Update main preview
        function updateMainPreview(src) {
            if (!src && filesArray.length === 0) {
                mainWrapper.html(
                    '<span id="mainPlaceholder" style="font-size:2rem; color:#888;">+ Image</span>');
                return;
            }
            const imgSrc = src || filesArray[0].src;
            mainWrapper.html(`
            <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; overflow:hidden; background:#f9f9f9;">
                <img src="${imgSrc}" style="max-width:100%; max-height:100%; object-fit:contain; display:block;">
            </div>
        `);
        }

        // Render thumbnails
        function renderPreviews() {
            preview.html('');
            filesArray.forEach(file => {
                if (file.existing) {
                    addImageElement(file, file.src, true);
                } else {
                    const reader = new FileReader();
                    reader.onload = e => addImageElement(file, e.target.result, false);
                    reader.readAsDataURL(file);
                }
            });
            updateMainPreview();
        }

        // Add individual thumbnail
        function addImageElement(fileObj, src, existing) {
            const wrapper = $('<div>').addClass('position-relative me-2 mb-2').css({
                width: '100px',
                height: '80px',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
                overflow: 'hidden',
                background: '#f9f9f9',
                border: '1px solid #ddd',
                borderRadius: '6px'
            });

            const img = $('<img>').attr('src', src).addClass('rounded thumbnail').css({
                maxWidth: '100%',
                maxHeight: '100%',
                objectFit: 'contain',
                cursor: 'pointer'
            });

            const removeBtn = $('<button>').attr('type', 'button').addClass(
                'btn btn-sm btn-danger position-absolute top-0 end-0 remove-image').text('×');

            wrapper.append(img).append(removeBtn);
            preview.append(wrapper);

            img.on('click', () => updateMainPreview(src));

            removeBtn.on('click', function() {
                filesArray = filesArray.filter(f => f !== fileObj);
                renderPreviews();
            });
        }

        addBtn.on('click', e => {
            e.preventDefault();
            input.trigger('click');
        });

        input.on('change', function() {
            const newFiles = Array.from(this.files);
            newFiles.forEach(file => {
                if (!filesArray.some(f => !f.existing && f.name === file.name && f.size === file
                        .size)) {
                    filesArray.push(file);
                }
            });
            input.val('');
            renderPreviews();
        });

        form.on('submit', function() {
            $('#existingImagesInput').remove();
            filesArray.filter(f => f.existing).forEach(f => {
                $('<input>').attr({
                    type: 'hidden',
                    name: 'existing_images[]',
                    value: f.src.replace('{{ asset('storage/') }}/', '')
                }).appendTo(form);
            });

            const dt = new DataTransfer();
            filesArray.forEach(f => {
                if (!f.existing) dt.items.add(f);
            });
            input[0].files = dt.files;
        });

        renderPreviews();
    });
</script>
