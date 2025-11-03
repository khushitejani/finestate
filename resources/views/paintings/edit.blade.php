<form id="paintingForm" method="POST" action="{{ route('paintings.update', $painting->id) }}"
    enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label for="paintingName" class="form-label">Name</label>
        <input type="text" class="form-control" id="paintingName" name="name" placeholder="Enter painting name"
            required value="{{ old('name', $painting->name) }}">
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Years</label>
        <input type="text" name="years" class="form-control" value="{{ old('years', $painting->years) }}"
            placeholder="e.g. 1965–1999" required>
        @error('years')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="mb-3">
        <label for="paintingPrice" class="form-label">Price</label>
        <input type="number" step="0.01" class="form-control" id="paintingPrice" name="price"
            placeholder="Enter price" required value="{{ old('price', $painting->price) }}">
    </div>

    <div class="mb-3">
        <label for="paintingImage" class="form-label">Upload Image</label>
        <input type="file" class="form-control" id="paintingImage" name="image" accept="image/*" value="">

        @if ($painting->image)
            <p>Current Image:</p>
            <img src="{{ asset('storage/' . $painting->image) }}" alt="Current Painting Image"
                style="max-width: 200px; margin-top: 10px;" id="imageDisplay">
        @endif
    </div>

    <button type="submit" class="btn btn-primary">Save Changes</button>
</form>

<script>
    const paintingImageInput = document.getElementById('paintingImage');
    const imageDisplay = document.getElementById('imageDisplay');

    paintingImageInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            // Replace old image src with new image preview
            imageDisplay.src = URL.createObjectURL(file);
        } else {
            // If no file selected, reset src to old image URL
            imageDisplay.src = "{{ asset('storage/' . $painting->image) }}";
        }
    });

    // Clear file input on page load to avoid old filename pre-filled
    window.addEventListener('load', () => {
        paintingImageInput.value = '';
    });
</script>
