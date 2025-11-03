<form action="{{ route('aircraftshops.store') }}" method="POST" enctype="multipart/form-data" id="aircraftShopForm">
    @csrf

    <div class="mb-3">
        <label class="form-label fw-bold">Shop Images</label>
        <div class="d-flex flex-column align-items-center">
            {{-- Main Preview --}}
            <div id="mainPreviewWrapper"
                style="width:400px; height:200px; border:2px dashed #ccc; display:flex; align-items:center; justify-content:center; margin-bottom:10px; overflow:hidden; border-radius:12px;">
                <span id="mainPlaceholder" style="font-size:2rem; color:#888;">+ Image</span>
            </div>
            <input type="file" name="images[]" id="imageInput" class="d-none" accept="image/*" multiple>

            <div id="imagePreview" class="d-flex gap-2 flex-wrap" style="max-width:400px;"></div>
            <button type="button" class="btn btn-outline-primary mt-2" id="addImagesBtn">Add Images</button>
        </div>
    </div>
    {{-- Name --}}
    <div class="mb-3">
        <label class="form-label fw-bold">Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
    </div>

    {{-- Price --}}
    <div class="mb-3">
        <label class="form-label fw-bold">Price</label>
        <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price') }}" required>
    </div>

    {{-- Description --}}
    <div class="mb-3">
        <label class="form-label fw-bold">Description</label>
        <textarea name="description" rows="4" class="form-control" required>{{ old('description') }}</textarea>
    </div>

    <button type="submit" class="btn btn-success">Save Aircraft Shop</button>
</form>
<script>
    $(document).ready(function() {
        let filesArray = [];
        const input = $('#imageInput');
        const addBtn = $('#addImagesBtn');
        const preview = $('#imagePreview');
        const mainWrapper = $('#mainPreviewWrapper');
        const form = $('#aircraftShopForm');

        // Open file picker
        addBtn.on('click', function(e) {
            e.preventDefault();
            input.trigger('click');
        });

        // Handle file selection
        input.on('change', function() {
            const newFiles = Array.from(this.files);

            newFiles.forEach(file => {
                if (!filesArray.some(f => f.name === file.name && f.size === file.size)) {
                    filesArray.push(file);
                }
            });

            input.val(''); // reset input
            renderPreviews();
        });

        function renderPreviews() {
            preview.html('');

            if (filesArray.length === 0) {
                mainWrapper.html(
                    '<span id="mainPlaceholder" style="font-size:2rem; color:#888;">+ Image</span>');
                return;
            }

            filesArray.forEach(file => {
                if (!file.src) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        file.src = e.target.result;
                        addImageElement(file);
                    };
                    reader.readAsDataURL(file);
                } else {
                    addImageElement(file);
                }
            });
        }

        function addImageElement(file) {
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

            // First image sets main preview
            if ($('#mainPreview').length === 0 || mainWrapper.find('span').length) {
                mainWrapper.html(
                    `<img id="mainPreview" src="${file.src}" style="width:100%; height:100%; object-fit:cover;">`
                );
            }

            img.on('click', function() {
                mainWrapper.html(
                    `<img id="mainPreview" src="${file.src}" style="width:100%; height:100%; object-fit:cover;">`
                );
            });

            removeBtn.on('click', function() {
                filesArray = filesArray.filter(f => f !== file);
                renderPreviews();
            });
        }

        // Before submit
        form.on('submit', function() {
            const dt = new DataTransfer();
            filesArray.forEach(f => dt.items.add(f));
            input[0].files = dt.files;
        });
    });
</script>
