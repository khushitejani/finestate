<form method="POST" action="{{ route('constructions.update', $construction->id) }}" enctype="multipart/form-data"
    id="constructionForm">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label class="form-label fw-bold d-block">Construction Image</label>
        <div class="ciu-box mx-auto position-relative" id="imageBox"
            style="width:100%; max-width:500px; min-height:200px; cursor:pointer;
                   display:flex; align-items:center; justify-content:center;
                   background:#f9f9f9; border:2px dashed #ccc; border-radius:12px; overflow:hidden;">
            <img id="imagePreview" class="ciu-preview" alt="Preview"
                style="width:auto; height:auto; max-width:100%; max-height:500px; {{ $construction->image ? '' : 'display:none;' }}"
                src="{{ $construction->image ? asset('storage/' . $construction->image) : '' }}">
            <span class="ciu-placeholder"
                style="font-size:2rem; color:#888; {{ $construction->image ? 'display:none;' : '' }}">+ Image</span>
        </div>
        <input type="file" name="image" id="imageInput" class="d-none" accept="image/*">
        @error('image')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    <div class="mb-3">
        <label class="form-label">No</label>
        <div class="input-group">
            <input type="number" id="noInput" name="no"
                class="form-control"value="{{ old('name', $construction->no) }}" placeholder="Enter or Generate"
                required>
            <button type="button" class="btn btn-info btn-sm generate-number-btn" data-table="Construction">
                Generate Number
            </button>
        </div>
    </div>

    <div class="mb-3">
        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $construction->name) }}"
            required>
    </div>

    <div class="mb-3">
        <label for="metal" class="form-label">Metal</label>
        <input type="text" name="metal" class="form-control" value="{{ old('metal', $construction->metal) }}">
    </div>

    <div class="mb-3">
        <label for="builder_men" class="form-label">Builder Men</label>
        <input type="text" name="builder_men" class="form-control"
            value="{{ old('builder_men', $construction->builder_men) }}">
    </div>

    <div class="mb-3">
        <label for="wood" class="form-label">Wood</label>
        <input type="text" name="wood" class="form-control" value="{{ old('wood', $construction->wood) }}">
    </div>

    <div class="mb-3">
        <label for="concrete" class="form-label">Concrete</label>
        <input type="text" name="concrete" class="form-control"
            value="{{ old('concrete', $construction->concrete) }}">
    </div>

    <div class="mb-3">
        <label for="total_cost_of_construction" class="form-label">Total Cost of Construction</label>
        <input type="number" step="0.01" name="total_cost_of_construction" class="form-control"
            value="{{ old('total_cost_of_construction', $construction->total_cost_of_construction) }}">
    </div>

    <div class="mb-3">
        <label for="total_profit" class="form-label">Total Profit</label>
        <input type="number" step="0.01" name="total_profit" class="form-control"
            value="{{ old('total_profit', $construction->total_profit) }}">
    </div>

    <div class="mb-3">
        <label for="total_percentage" class="form-label">Total Percentage</label>
        <input type="number" step="0.01" name="total_percentage" class="form-control"
            value="{{ old('total_percentage', $construction->total_percentage) }}">
    </div>

    <div class="mb-3">
        <label for="time" class="form-label">Time</label>
        <input type="text" name="time" class="form-control" value="{{ old('time', $construction->time) }}">
    </div>

    <div class="mb-3">
        <label for="total_return_after_completion" class="form-label">Total Return After Completion</label>
        <input type="number" step="0.01" name="total_return_after_completion" class="form-control"
            value="{{ old('total_return_after_completion', $construction->total_return_after_completion) }}">
    </div>
    <button type="submit" class="btn btn-primary">Update Construction</button>
</form>
