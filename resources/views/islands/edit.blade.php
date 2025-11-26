<form id="islandForm" action="{{ route('islands.update', $island->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    {{-- Images --}}
    <div class="mb-3">
        <label class="form-label fw-bold">Island Images</label>
        <div class="d-flex flex-column align-items-center">
            {{-- Main Preview --}}
            <div id="mainPreviewWrapper"
                style="width:400px; height:200px; border:2px dashed #ccc; display:flex; align-items:center; justify-content:center; margin-bottom:10px; overflow:hidden; border-radius:12px;">
                <span id="mainPlaceholder" style="font-size:2rem; color:#888;">+ Image</span>
            </div>

            <input type="file" name="images[]" id="imageInput" class="d-none" accept="image/*" multiple>
            <div id="imagePreview" class="d-flex gap-2 flex-wrap" style="max-width:400px;">
                @php
                    $oldImages = is_array($island->images) ? $island->images : json_decode($island->images, true);
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

<script>
    $(document).ready(function() {
        let filesArray = [];
        const input = $('#imageInput');
        const addBtn = $('#addImagesBtn');
        const preview = $('#imagePreview');
        const mainWrapper = $('#mainPreviewWrapper');
        const form = $('#islandForm');

        // Load existing images
        @foreach ($oldImages as $img)
            filesArray.push({
                existing: true,
                path: "{{ $img }}",
                src: "{{ asset('storage/' . $img) }}"
            });
        @endforeach

        // Update Main Preview
        function updateMainPreview() {
            if (filesArray.length > 0) {
                mainWrapper.html(
                    `<img id="mainPreview" src="${filesArray[0].src}" style="width:100%; height:100%; object-fit:cover;">`
                );
            } else {
                mainWrapper.html(
                    '<span id="mainPlaceholder" style="font-size:2rem; color:#888;">+ Image</span>'
                );
            }
        }

        // Render Image Previews
        function renderPreviews() {
            preview.html('');
            filesArray.forEach(file => {
                const wrapper = $('<div>')
                    .addClass('position-relative me-2 mb-2')
                    .css({
                        width: '100px',
                        height: '80px'
                    });

                const img = $('<img>')
                    .attr('src', file.src)
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
                        `<img id="mainPreview" src="${file.src}" style="width:100%; height:100%; object-fit:cover;">`
                    );
                });

                removeBtn.on('click', () => {
                    filesArray = filesArray.filter(f => f !== file);
                    renderPreviews();
                });
            });

            updateMainPreview();
        }

        // Add New Images
        addBtn.on('click', (e) => {
            e.preventDefault();
            input.trigger('click');
        });

        input.on('change', function() {
            const newFiles = Array.from(this.files);

            newFiles.forEach(file => {
                const url = URL.createObjectURL(file);
                filesArray.push({
                    existing: false,
                    file: file,
                    src: url
                });
            });

            input.val('');
            renderPreviews();
        });

        // On form submit
        form.on('submit', function() {
            $('input[name="existing_images[]"]').remove();

            // Save existing images (correct path)
            filesArray.filter(f => f.existing).forEach(f => {
                $('<input>').attr({
                    type: 'hidden',
                    name: 'existing_images[]',
                    value: f.path
                }).appendTo(form);
            });

            // Add new images to input field
            const dt = new DataTransfer();
            filesArray.forEach(f => {
                if (!f.existing) dt.items.add(f.file);
            });

            input[0].files = dt.files;
        });

        renderPreviews();
    });
</script>
