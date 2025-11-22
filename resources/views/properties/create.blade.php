<form id="propertyForm" action="{{ route('properties.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <!-- Property Images -->
    <div class="mb-3">
        <label class="form-label fw-bold">Property Images</label>
        <div class="d-flex flex-column align-items-center">
            <!-- Main Preview -->
            <div id="mainPreviewWrapper"
                style="width:400px; height:300px; border:2px dashed #ccc; display:flex; align-items:center; justify-content:center; margin-bottom:10px; overflow:hidden; border-radius:12px;">
                <span id="mainPlaceholder" style="font-size:2rem; color:#888;">+ Image</span>
            </div>

            <!-- Hidden File Input -->
            {{-- <input type="file" id="propertyImages" name="property_images[]" multiple class="d-none" accept="image/*"> --}}
            <input type="file" id="imageInput" name="property_images[]" multiple class="d-none" accept="image/*">

            <!-- Thumbnails -->
            <div id="imagePreview" class="d-flex gap-2 flex-wrap" style="max-width:400px;"></div>

            <button type="button" class="btn btn-outline-primary mt-2" id="addImagesBtn">Add Images</button>
        </div>
    </div>

    <!-- Income Per Hour -->
    <div class="mb-3 mt-3">
        <label class="form-label fw-bold">Income Per Hour</label>
        <input type="number" name="income_per_hour" class="form-control" step="0.01" min="0"
            value="{{ old('income_per_hour') }}" required>
    </div>

    <!-- Address -->
    <div class="mb-3">
        <label class="form-label fw-bold">Address</label>
        <textarea name="address" class="form-control" rows="2" required>{{ old('address') }}</textarea>
    </div>

    <!-- Price -->
    <div class="mb-3">
        <label class="form-label fw-bold">Price</label>
        <input type="number" name="price" step="0.01" class="form-control" value="{{ old('price') }}" required>
    </div>

    <button type="submit" class="btn btn-primary">Create Property</button>
</form>
<script>
    // function initPropertyImagePreview(container) {
    //     const form = container.querySelector('#propertyForm');
    //     // if (!form || form.dataset.previewInit === "true") return;
    //     if (window.previewInit) return;
    //     window.previewInit = true;
    //     form.dataset.previewInit = "true";

    //     // const input = form.querySelector('#propertyImages');

    //     const input = form.querySelector('#imageInput');
    //     const preview = form.querySelector('#imagePreview');
    //     const mainWrapper = form.querySelector('#mainPreviewWrapper');
    //     const addBtn = form.querySelector('#addImagesBtn');
    //     let filesArray = [];

    //     function updatePreview() {
    //         preview.innerHTML = '';
    //         filesArray.forEach((file, index) => {
    //             const reader = new FileReader();
    //             reader.onload = function(e) {
    //                 const wrapper = document.createElement('div');
    //                 wrapper.classList.add('position-relative', 'me-2', 'mb-2');
    //                 wrapper.style.width = '100px';
    //                 wrapper.style.height = '80px';
    //                 wrapper.dataset.index = index;
    //                 wrapper.innerHTML = `
    //             <img src="${e.target.result}" class="rounded border thumbnail" style="width:100%; height:100%; object-fit:cover; cursor:pointer;">
    //             <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 remove-image">×</button>
    //         `;
    //                 preview.appendChild(wrapper);
    //                 if (index === 0) {
    //                     mainWrapper.innerHTML =
    //                         `<img id="mainPreview" src="${e.target.result}" style="width:100%; height:100%; object-fit:cover;">`;
    //                 }
    //                 wrapper.querySelector('.thumbnail').addEventListener('click', function() {
    //                     mainWrapper.innerHTML =
    //                         `<img id="mainPreview" src="${this.src}" style="width:100%; height:100%; object-fit:cover;">`;
    //                 });
    //                 wrapper.querySelector('.remove-image').addEventListener('click', function() {
    //                     filesArray.splice(index, 1);
    //                     updatePreview();
    //                 });
    //             };
    //             reader.readAsDataURL(file);
    //         });
    //     }
    //     addBtn.addEventListener('click', () => input.click());
    //     input.addEventListener('change', function() {
    //         const newFiles = Array.from(this.files);
    //         newFiles.forEach(file => {
    //             if (!filesArray.some(f => f.name === file.name && f.size === file.size)) {
    //                 filesArray.push(file);
    //             }
    //         });
    //         updatePreview();
    //         this.value = '';
    //     });
    //     preview.addEventListener('click', function(e) {
    //         if (e.target.classList.contains('remove-image')) {
    //             const wrapper = e.target.closest('div.position-relative');
    //             const index = parseInt(wrapper.dataset.index);
    //             if (!isNaN(index)) {
    //                 filesArray.splice(index, 1);
    //                 updatePreview();
    //             }
    //         }
    //     });
    //     form.addEventListener('submit', function(e) {
    //         const dt = new DataTransfer();
    //         filesArray.forEach(f => dt.items.add(f));
    //         input.files = dt.files;
    //     });
    // }
    // document.addEventListener('DOMContentLoaded', function() {
    //     initPropertyImagePreview(document);
    // });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const form = document.getElementById('propertyForm');
        const input = document.getElementById('imageInput');
        const preview = document.getElementById('imagePreview');
        const mainWrapper = document.getElementById('mainPreviewWrapper');
        const addBtn = document.getElementById('addImagesBtn');
        const placeholder = document.getElementById('mainPlaceholder');

        let filesArray = [];

        function updatePreview() {
            preview.innerHTML = '';

            // hide "+ Image" text if first image exists
            if (filesArray.length > 0) {
                placeholder.style.display = 'none';
            } else {
                placeholder.style.display = 'flex';
                mainWrapper.innerHTML =
                    '<span id="mainPlaceholder" style="font-size:2rem; color:#888;">+ Image</span>';
            }

            filesArray.forEach((file, index) => {
                const reader = new FileReader();

                reader.onload = function(e) {
                    const wrapper = document.createElement('div');
                    wrapper.classList.add('position-relative', 'me-2', 'mb-2');
                    wrapper.style.width = '100px';
                    wrapper.style.height = '80px';
                    wrapper.dataset.index = index;

                    wrapper.innerHTML = `
                    <img src="${e.target.result}" class="rounded border thumbnail"
                        style="width:100%; height:100%; object-fit:cover; cursor:pointer;">
                    <button type="button"
                        class="btn btn-sm btn-danger position-absolute top-0 end-0 remove-image">×</button>
                `;

                    preview.appendChild(wrapper);

                    // Set first image as main preview
                    if (index === 0) {
                        mainWrapper.innerHTML =
                            `<img src="${e.target.result}" style="width:100%; height:100%; object-fit:cover;">`;
                    }

                    wrapper.querySelector('.thumbnail').addEventListener('click', function() {
                        mainWrapper.innerHTML =
                            `<img src="${this.src}" style="width:100%; height:100%; object-fit:cover;">`;
                    });

                    wrapper.querySelector('.remove-image').addEventListener('click', function() {
                        filesArray.splice(index, 1);
                        updatePreview();
                    });
                };

                reader.readAsDataURL(file);
            });
        }

        addBtn.addEventListener('click', () => input.click());

        input.addEventListener('change', function() {
            filesArray.push(...Array.from(this.files));
            updatePreview();
            this.value = ''; // reset
        });

        form.addEventListener('submit', function(e) {
            const dt = new DataTransfer();
            filesArray.forEach(f => dt.items.add(f));
            input.files = dt.files;
        });

    });
</script>
