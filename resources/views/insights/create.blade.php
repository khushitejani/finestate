<form id="insightForm" action="{{ route('insights.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="mb-3">
        <label class="form-label fw-bold d-block">Insights Image</label>

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
        <label for="name" class="form-label">Name</label>
        <input type="text" name="name" id="name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">No</label>
        <div class="input-group">
            <input type="number" id="noInput" name="no" class="form-control" placeholder="Enter or Generate"
                required>
            <button type="button" class="btn btn-info btn-sm generate-number-btn" data-table="Insight">
                Generate Number
            </button>
        </div>
    </div>
    <div class="mb-3">
        <label for="years" class="form-label">Years</label>
        <input type="text" name="years" id="years" class="form-control" placeholder="e.g. 1990–2020"
            value="{{ old('years') }}" required>
        @error('years')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="mb-3">
        <label for="conditions" class="form-label">Conditions</label>
        <textarea name="conditions" id="conditions" class="form-control" rows="3"></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Save</button>
</form>
