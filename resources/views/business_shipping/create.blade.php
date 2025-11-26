<form id="shippingForm" action="{{ route('business-shippings.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <!-- Image uploader -->
    <div id="card-image-uploader-modal" data-output-width="400" data-output-height="400" data-aspect-ratio="1"
        class="mb-3">
        <label class="form-label fw-bold d-block">Shipping Image</label>
        <div class="ciu-box mx-auto position-relative"
            style="width:100%; max-width:500px; height:auto; cursor:pointer;
                   display:flex; align-items:center; justify-content:center;
                   background:#f9f9f9; border:2px dashed #ccc; border-radius:12px; overflow:hidden; min-height:200px;">
            <img class="ciu-preview" id="imagePreview" style="display:none; max-width:100%; max-height:500px;">
            <span class="ciu-placeholder" style="font-size:2rem; color:#888;">+ Image</span>
        </div>
        <input type="file" class="ciu-input d-none" name="image" id="imageInput" accept="image/*">
    </div>

    <!-- Shipping Name -->
    <div class="mb-3 mt-3">
        <label class="form-label">Shipping Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
    </div>

    <div class="mb-3">
        <label class="form-label">No</label>
        <div class="input-group">
            <input type="number" id="noInput" name="no" class="form-control" placeholder="Enter or Generate"
                required>
            <button type="button" class="btn btn-info btn-sm generate-number-btn" data-table="BusinessShipping">
                Generate Number
            </button>
        </div>
    </div>

    <!-- Category -->
    <div class="mb-3">
        <label class="form-label">Category</label>
        <select name="category" class="form-control" required>
            <option value="">Select Category</option>
            <option value="City" {{ old('category') == 'City' ? 'selected' : '' }}>City</option>
            <option value="State" {{ old('category') == 'State' ? 'selected' : '' }}>State</option>
            <option value="Long-distance" {{ old('category') == 'Long-distance' ? 'selected' : '' }}>Long-distance
            </option>
        </select>
    </div>

    <!-- Resource -->
    <div class="mb-3">
        <label class="form-label">Resource</label>
        <textarea name="resource" class="form-control">{{ old('resource') }}</textarea>
    </div>

    <!-- Income / Hour -->
    <div class="mb-3">
        <label class="form-label">Income / Hour</label>
        <input type="number" step="0.01" name="income_per_hour" class="form-control"
            value="{{ old('income_per_hour') }}">
    </div>

    <!-- Price -->
    <div class="mb-3">
        <label class="form-label">Price</label>
        <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price') }}">
    </div>

    <div class="d-flex justify-content-end">
        <button type="submit" class="btn btn-success text-end">Create Shipping</button>
    </div>
</form>
