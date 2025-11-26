<form action="{{ route('carshowrooms.update', $carshowroom->id) }}" method="POST" enctype="multipart/form-data"
    id="carShowroomForm">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label class="form-label fw-bold">Car Images</label>
        <div class="d-flex flex-column align-items-center">
            <div id="mainPreviewWrapper"
                style="width:400px; height:300px; border:2px dashed #ccc; display:flex; align-items:center; justify-content:center; margin-bottom:10px; overflow:hidden; border-radius:12px;">
                <span id="mainPlaceholder" style="font-size:2rem; color:#888;">+ Image</span>
            </div>
            <input type="hidden" name="existing_images" id="existingImagesInput">
            <input type="file" id="carImages" name="images[]" multiple accept="image/*" style="display:none;">
            <div id="imagePreview" class="d-flex gap-2 flex-wrap justify-content-center" style="max-width:400px;">
                @php
                    $oldImages = is_array($carshowroom->images)
                        ? $carshowroom->images
                        : json_decode($carshowroom->images, true);
                    $oldImages = is_array($oldImages) ? $oldImages : [];
                @endphp
                @foreach ($oldImages as $img)
                    <div class="position-relative me-2 mb-2" style="width:100px; height:80px;">
                        <img src="{{ asset('storage/' . $img) }}" class="rounded border thumbnail existing-img"
                            style="width:100%; height:100%; object-fit:cover; cursor:pointer;">
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
        <input type="text" name="name" class="form-control" value="{{ old('name', $carshowroom->name) }}"
            required>
    </div>
    <div class="mb-3">
        <label class="form-label">No</label>
        <div class="input-group">
            <input type="number" id="noInput" name="no" class="form-control" value="{{ $carshowroom->no }}"
                required>

            <button type="button" class="btn btn-info btn-sm generate-number-btn" data-table="CarShowroom">
                Generate Number
            </button>
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label fw-bold">Price</label>
        <input type="number" step="0.01" name="price" class="form-control"
            value="{{ old('price', $carshowroom->price) }}" required>
    </div>

    <button type="submit" class="btn btn-primary">Update Car Showroom</button>
</form>
<script>
    $(document).off('click', '#addImagesBtn').on('click', '#addImagesBtn', function(e) {
        e.preventDefault();
        $('#carImages').trigger('click');
    });

    $(document).ready(function() {
        let filesArray = [];
        const input = $('#carImages');
        const preview = $('#imagePreview');
        const mainWrapper = $('#mainPreviewWrapper');
        const form = $('#carShowroomForm');

        // Prevent duplicate event bindings
        input.off('change');

        // Load existing images
        $('#imagePreview .existing-img').each(function() {
            const imgSrc = $(this).attr('src');
            filesArray.push({
                existing: true,
                src: imgSrc
            });
        });

        function updateMainPreview() {
            if (filesArray.length > 0) {
                mainWrapper.html(
                    `<img id="mainPreview" src="${filesArray[0].src}" style="width:100%; height:100%; object-fit:cover;">`
                );
            } else {
                mainWrapper.html(
                    '<span id="mainPlaceholder" style="font-size:2rem; color:#888;">+ Image</span>');
            }
        }

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

        function addImageElement(fileObj, src, existing) {
            const wrapper = $('<div>')
                .addClass('position-relative me-2 mb-2')
                .css({
                    width: '100px',
                    height: '80px'
                });

            const img = $('<img>')
                .attr('src', src)
                .addClass('rounded border thumbnail')
                .css({
                    width: '100%',
                    height: '100%',
                    objectFit: 'cover',
                    cursor: 'pointer'
                });

            const removeBtn = $('<button>')
                .attr('type', 'button')
                .addClass('btn btn-sm btn-danger position-absolute top-0 end-0 remove-image')
                .text('×');

            wrapper.append(img).append(removeBtn);
            preview.append(wrapper);

            img.on('click', () => {
                mainWrapper.html(
                    `<img id="mainPreview" src="${src}" style="width:100%; height:100%; object-fit:cover;">`
                );
            });

            removeBtn.on('click', function() {
                filesArray = filesArray.filter(f => f !== fileObj);
                renderPreviews();
            });
        }

        // Handle file input change (once)
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

        // Form submit logic
        form.on('submit', function() {
            const keptExisting = filesArray
                .filter(f => f.existing)
                .map(f => f.src.replace('{{ asset('storage/') }}/', ''));

            $('#existingImagesInput').val(JSON.stringify(keptExisting));

            const dt = new DataTransfer();
            filesArray.forEach(f => {
                if (!f.existing) dt.items.add(f);
            });
            input[0].files = dt.files;
        });

        renderPreviews();
    });
</script>
