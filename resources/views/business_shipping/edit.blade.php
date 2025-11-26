<form id="shippingForm" action="{{ route('business-shippings.update', $shipping->id) }}" method="POST"
    enctype="multipart/form-data">
    @csrf
    @method('PUT') <!-- Important for update -->
    <div id="card-image-uploader-modal" data-output-width="400" data-output-height="400" data-aspect-ratio="1"
        class="mb-3">
        <label class="form-label fw-bold d-block">Shipping Image</label>
        <div class="ciu-box mx-auto position-relative"
            style="width:100%; max-width:500px; height:auto; cursor:pointer;
                   display:flex; align-items:center; justify-content:center;
                   background:#f9f9f9; border:2px dashed #ccc; border-radius:12px; overflow:hidden; min-height:200px;">
            @php
                $imageUrl = old('image', $shipping->image ? asset('storage/' . $shipping->image) : '');
            @endphp
            <img class="ciu-preview" id="imagePreview" src="{{ $imageUrl }}"
                style="{{ $imageUrl ? 'display:block;' : 'display:none;' }} max-width:100%; max-height:500px;">
            <span class="ciu-placeholder" style="{{ $imageUrl ? 'display:none;' : 'font-size:2rem; color:#888;' }}">+
                Image</span>
        </div>
        <input type="file" class="ciu-input d-none" name="image" id="imageInput" accept="image/*">
    </div>
    <div class="mb-3 mt-3">
        <label class="form-label">Shipping Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $shipping->name) }}" required>
    </div>
    <div class="mb-3">
        <label class="form-label">No</label>
        <div class="input-group">
            <input type="number" id="noInput" name="no" class="form-control" value="{{ $shipping->no }}"
                required>

            <button type="button" class="btn btn-info btn-sm generate-number-btn" data-table="BusinessShipping">
                Generate Number
            </button>
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label">Category</label>
        <select name="category" class="form-control" required>
            @php
                $categories = ['City', 'State', 'Long-distance'];
            @endphp
            <option value="">Select Category</option>
            @foreach ($categories as $category)
                <option value="{{ $category }}"
                    {{ old('category', $shipping->category) == $category ? 'selected' : '' }}>
                    {{ $category }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Resource</label>
        <textarea name="resource" class="form-control">{{ old('resource', $shipping->resource) }}</textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Income / Hour</label>
        <input type="number" step="0.01" name="income_per_hour" class="form-control"
            value="{{ old('income_per_hour', $shipping->income_per_hour) }}">
    </div>
    <div class="mb-3">
        <label class="form-label">Price</label>
        <input type="number" step="0.01" name="price" class="form-control"
            value="{{ old('price', $shipping->price) }}">
    </div>
    <div class="d-flex justify-content-end">
        <button type="submit" class="btn btn-success text-end">Update Shipping</button>
    </div>
</form>
