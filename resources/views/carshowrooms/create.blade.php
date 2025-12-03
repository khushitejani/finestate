<form id="carShowroomForm" action="{{ route('carshowrooms.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
        <label class="form-label fw-bold">Car Images</label>
        <div class="d-flex flex-column align-items-center">
            <!-- Main Preview Box -->
            <div id="mainPreviewWrapper"
                style="width:400px; height:300px; border:2px dashed #ccc; display:flex; align-items:center; justify-content:center; margin-bottom:10px; overflow:hidden; border-radius:12px;">
                <span id="mainPlaceholder" style="font-size:2rem; color:#888;">+ Image</span>
            </div>

            <!-- Hidden File Input -->
            <input type="file" id="carImages" name="images[]" multiple accept="image/*" style="display:none;">

            <!-- Thumbnails -->
            <div id="imagePreview" class="d-flex gap-2 flex-wrap" style="max-width:400px;"></div>

            <!-- Add Button -->
            <button type="button" class="btn btn-outline-primary mt-2" id="addImagesBtn">Add Images</button>
        </div>
    </div>

    <!-- Name -->
    <div class="mb-3">
        <label class="form-label fw-bold">Name</label>
        <input type="text" name="name" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">No</label>
        <div class="input-group">
            <input type="number" id="noInput" name="no" class="form-control" placeholder="Enter or Generate"
                required>
            <button type="button" class="btn btn-info btn-sm generate-number-btn" data-table="Carshowroom">
                Generate Number
            </button>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Level Name</label>
        <select name="level_name" class="form-control" required>
            <option value="" disabled {{ old('level_name') ? '' : 'selected' }}>Select Level</option>
            @php
                $levels = ['Budget', 'Standard', 'Premium', 'Luxury', 'Ultra-Luxury', 'Hypercar', 'Legendary'];
            @endphp
            @foreach ($levels as $level)
                <option value="{{ $level }}" {{ old('level_name') == $level ? 'selected' : '' }}>
                    {{ $level }}
                </option>
            @endforeach
        </select>
        @error('level_name')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <!-- Price -->
    <div class="mb-3">
        <label class="form-label fw-bold">Price</label>
        <input type="number" step="0.01" name="price" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-success">Save Showroom</button>
</form>

<script>
    $(document).ready(function() {
        const filesArray = [];
        const input = $('#carImages');
        const addBtn = $('#addImagesBtn');
        const preview = $('#imagePreview');
        const mainWrapper = $('#mainPreviewWrapper');
        const form = $('#carShowroomForm');

        // Open file picker
        $(document).on('click', '#addImagesBtn', function(e) {
            e.preventDefault();
            input.trigger('click');
        });

        // Handle file selection
        $(document).on('change', '#carImages', function() {
            const newFiles = Array.from(this.files);
            newFiles.forEach(file => {
                if (!filesArray.some(f => f.name === file.name && f.size === file.size)) {
                    filesArray.push(file);
                }
            });
            updatePreview();
            input.val('');
        });

        // Update preview thumbnails and main image
        function updatePreview() {
            preview.html('');

            if (filesArray.length === 0) {
                mainWrapper.html(
                    '<span id="mainPlaceholder" style="font-size:2rem; color:#888;">+ Image</span>');
                return;
            }

            filesArray.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const wrapper = $('<div>').addClass('position-relative me-2 mb-2').css({
                        width: '100px',
                        height: '80px'
                    }).attr('data-index', index);
                    const img = $('<img>').attr('src', e.target.result).addClass(
                        'rounded border thumbnail').css({
                        width: '100%',
                        height: '100%',
                        objectFit: 'cover',
                        cursor: 'pointer'
                    });
                    const removeBtn = $('<button>').attr('type', 'button').addClass(
                            'btn btn-sm btn-danger position-absolute top-0 end-0 remove-image')
                        .text('×');

                    wrapper.append(img).append(removeBtn);
                    preview.append(wrapper);
                    if (index === 0) {
                        mainWrapper.html(
                            `<img id="mainPreview" src="${e.target.result}" style="width:100%; height:100%; object-fit:cover;">`
                        );
                    }
                    img.on('click', function() {
                        mainWrapper.html(
                            `<img id="mainPreview" src="${this.src}" style="width:100%; height:100%; object-fit:cover;">`
                        );
                    });
                    removeBtn.on('click', function() {
                        filesArray.splice(index, 1);
                        updatePreview();
                    });
                };
                reader.readAsDataURL(file);
            });
        }

        form.on('submit', function(e) {
            const dt = new DataTransfer();
            filesArray.forEach(f => dt.items.add(f));
            input[0].files = dt.files;
        });
    });
</script>
