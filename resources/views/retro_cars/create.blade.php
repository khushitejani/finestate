<form id="retroCarForm" action="{{ route('retro_cars.store') }}" method="POST" enctype="multipart/form-data">

    @csrf

    <div class="mb-3">
        <label class="form-label fw-bold d-block">Retro Cars Image</label>

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
    </div>
    <div class="mb-3">
        <label for="name" class="form-label">Car Name</label>
        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
    </div>
    <div class="mb-3">
        <label class="form-label">No</label>
        <div class="input-group">
            <input type="number" id="noInput" name="no" class="form-control" placeholder="Enter or Generate"
                required>
            <button type="button" class="btn btn-info btn-sm generate-number-btn" data-table="retro_cars">
                Generate Number
            </button>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="start_year" class="form-label">Start Year</label>
            <input type="number" name="start_year" id="start_year" class="form-control" placeholder="1963"
                min="1900" max="{{ date('Y') }}" value="{{ old('start_year') }}" required>
        </div>

        <div class="col-md-6">
            <label for="end_year" class="form-label">End Year</label>
            <input type="number" name="end_year" id="end_year" class="form-control" placeholder="1970" min="1900"
                max="{{ date('Y') }}" value="{{ old('end_year') }}" required>
        </div>
    </div>
    <div class="mb-3">
        <label for="price" class="form-label">Price</label>
        <input type="number" step="0.01" name="price" id="price" class="form-control"
            value="{{ old('price') }}" required>
    </div>

    <button type="submit" class="btn btn-primary">Save Car</button>
</form>
