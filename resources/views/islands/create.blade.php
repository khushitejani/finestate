<form id="islandForm" action="{{ route('islands.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    {{-- <div class="mb-3">
        <label class="form-label fw-bold d-block">Island Image</label>

        <div class="ciu-box mx-auto position-relative" id="imageBox"
            style="width:100%; max-width:500px; min-height:200px; cursor:pointer;
                   display:flex; align-items:center; justify-content:center;
                   background:#f9f9f9; border:2px dashed #ccc; border-radius:12px; overflow:hidden;">
            <img id="imagePreview" class="ciu-preview" alt="Preview"
                style="display:none; width:auto; height:auto; max-width:100%; max-height:500px;">
            <span class="ciu-placeholder" style="font-size:2rem; color:#888;">+ Image</span>
        </div>

        <input type="file" name="image" id="imageInput" class="d-none" accept="image/*">
        @error('image')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div> --}}
    <div class="mb-3">
        <label class="form-label fw-bold">Island Images</label>
        <div class="d-flex flex-column align-items-center">
            {{-- Main Preview --}}
            <div id="mainPreviewWrapper"
                style="width:400px; height:200px; border:2px dashed #ccc; display:flex; align-items:center; justify-content:center; margin-bottom:10px; overflow:hidden; border-radius:12px;">
                <span id="mainPlaceholder" style="font-size:2rem; color:#888;">+ Image</span>
            </div>

            <input type="file" name="images[]" id="imageInput" class="d-none" accept="image/*" multiple>
            <div id="imagePreview" class="d-flex gap-2 flex-wrap" style="max-width:400px;"></div>
            <button type="button" class="btn btn-outline-primary mt-2" id="addImagesBtn">Add Images</button>
            @error('images')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
    </div>

    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
    </div>

    <div class="mb-3">
        <label class="form-label">No</label>
        <div class="input-group">
            <input type="number" id="noInput" name="no" class="form-control" placeholder="Enter or Generate"
                required>
            <button type="button" class="btn btn-info btn-sm generate-number-btn" data-table="Island">
                Generate Number
            </button>
        </div>
    </div>
    <div class="mb-3">
        <label>Price (ETH)</label>
        <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price') }}" required>
    </div>

    <div class="mb-3">
        <label>Description</label>
        <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
    </div>
    <button type="submit" class="btn btn-primary">Create Island</button>
</form>
<script>
    $(document).ready(function() {
        let filesArray = [];
        const input = $('#imageInput');
        const addBtn = $('#addImagesBtn');
        const preview = $('#imagePreview');
        const mainWrapper = $('#mainPreviewWrapper');
        const form = $('#islandForm');

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

            input.val('');
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
            const wrapper = $('<div>').addClass('position-relative me-2 mb-2').css({
                width: '100px',
                height: '80px'
            });
            const img = $('<img>').attr('src', file.src).addClass('rounded border thumbnail')
                .css({
                    width: '100%',
                    height: '100%',
                    objectFit: 'cover',
                    cursor: 'pointer'
                });
            const removeBtn = $('<button>').attr('type', 'button').addClass(
                'btn btn-sm btn-danger position-absolute top-0 end-0 remove-image').text('×');

            wrapper.append(img).append(removeBtn);
            preview.append(wrapper);

            // Set first image as main preview
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

        // Attach files to input before submit
        form.on('submit', function() {
            const dt = new DataTransfer();
            filesArray.forEach(f => dt.items.add(f));
            input[0].files = dt.files;
        });
    });
</script>
