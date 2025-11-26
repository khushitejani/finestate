<form action="{{ route('unique_items.update', $uniqueItem->id) }}" method="POST" enctype="multipart/form-data"
    id="uniqueItemForm">
    @csrf
    @method('PATCH')

    <div class="mb-3">
        <label class="form-label fw-bold d-block">Unique Item Image</label>

        <div class="ciu-box mx-auto position-relative" id="imageBox"
            style="width:100%; max-width:500px; min-height:200px; cursor:pointer;
               display:flex; align-items:center; justify-content:center;
               background:#f9f9f9; border:2px dashed #ccc; border-radius:12px; overflow:hidden;">

            @if ($uniqueItem->image && Storage::disk('public')->exists($uniqueItem->image))
                <img id="imagePreview" class="ciu-preview" src="{{ asset('storage/' . $uniqueItem->image) }}"
                    alt="Preview" style="display:block; width:auto; height:auto; max-width:100%; max-height:500px;">
            @else
                <img id="imagePreview" class="ciu-preview" alt="Preview"
                    style="display:none; width:auto; height:auto; max-width:100%; max-height:500px;">
                <span class="ciu-placeholder" style="font-size:2rem; color:#888;">+ Image</span>
            @endif
        </div>

        <input type="file" name="image" id="imageInput" class="d-none" accept="image/*">
        @error('image')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    {{-- Name --}}
    <div class="mb-3 mt-3">
        <label class="form-label fw-bold">Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $uniqueItem->name) }}" required>
    </div>

    <div class="mb-3">
        <label class="form-label">No</label>
        <div class="input-group">
            <input type="number" id="noInput" name="no" class="form-control" value="{{ $uniqueItem->no }}"
                required>

            <button type="button" class="btn btn-info btn-sm generate-number-btn" data-table="unique_items">
                Generate Number
            </button>
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label fw-bold">Years</label>
        <input type="text" name="years" class="form-control" value="{{ old('years', $uniqueItem->years) }}"
            placeholder="e.g. 1965–1999" required>
        @error('years')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    {{-- Price --}}
    <div class="mb-3">
        <label class="form-label fw-bold">Price</label>
        <input type="number" step="0.01" name="price" class="form-control"
            value="{{ old('price', $uniqueItem->price) }}" required>
    </div>

    <div class="d-flex justify-content-end">
        <button type="submit" class="btn btn-primary">Update Unique Item</button>
    </div>
</form>
