<form action="{{ route('shares.update', $share->id) }}" method="POST" enctype="multipart/form-data" id="shareForm">
    @csrf
    @method('PUT')
    <div id="card-image-uploader-modal" data-output-width="400" data-output-height="400" data-aspect-ratio="1"
        class="mb-3">
        <label class="form-label fw-bold d-block">Share Image</label>

        <div class="ciu-box mx-auto position-relative"
            style="width:100%; max-width:500px; height:auto; cursor:pointer;
            display:flex; align-items:center; justify-content:center;
            background:#f9f9f9; border:2px dashed #ccc; border-radius:12px; overflow:hidden; min-height:200px;">
            <img class="ciu-preview" id="imagePreview" alt="Preview"
                style="width:auto; height:auto; max-width:100%; max-height:500px; 
                   {{ isset($share) && $share->image ? '' : 'display:none;' }}"
                src="{{ isset($share) && $share->image ? asset('storage/' . $share->image) : 'default.jpeg' }}">
            <span class="ciu-placeholder"
                style="font-size:2rem; color:#888; {{ isset($share) && $share->image ? 'display:none;' : '' }}">
                + Image
            </span>
        </div>

        <input type="file" class="ciu-input d-none" name="image" id="imageInput" accept="image/*">
    </div>
    <div class="mb-3 mt-3">
        <label class="form-label">Share Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $share->name) }}" required>
    </div>

    <div class="mb-3">
        <label class="form-label">No</label>
        <div class="input-group">
            <input type="number" id="noInput" name="no" class="form-control" value="{{ $share->no }}"
                required>

            <button type="button" class="btn btn-info btn-sm generate-number-btn" data-table="shares">
                Generate Number
            </button>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Share Price</label>
        <input type="number" name="share_price" step="0.01" class="form-control"
            value="{{ old('share_price', $share->share_price) }}" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Dividend</label>
        <input type="number" name="dividend" step="0.01" class="form-control"
            value="{{ old('dividend', $share->dividend) }}" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Time Period</label>
        <input type="text" name="time_period" class="form-control"
            value="{{ old('time_period', $share->time_period) }}" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Company Capitalization</label>
        <input type="text" name="capitalization" class="form-control"
            value="{{ old('capitalization', $share->capitalization) }}" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Available Shares</label>
        <input type="number" name="available_shares" class="form-control"
            value="{{ old('available_shares', $share->available_shares) }}" required>
    </div>
    <div class="mb-3">
        <label class="form-label" for="day_prices_input">Day Prices (comma-separated)</label>
        <input type="text" name="day_prices_input" id="day_prices_input" class="form-control"
            placeholder="e.g. 100, 200, 300"
            value="{{ old('day_prices_input', isset($share->day_prices['base']) ? implode(',', $share->day_prices['base']) : '') }}">
        <small class="text-muted">These prices will be stored for this share.</small>
    </div>
    <button type="submit" class="btn btn-primary">Update Share</button>
</form>

<style>
    .cropper-crop-box,
    .cropper-view-box {
        border: 2px solid #00aaff !important;
        box-shadow: 0 0 0 1px rgba(0, 170, 255, 0.3) inset !important;
    }

    .cropper-face {
        background-color: rgba(255, 255, 255, 0.1) !important;
        border: 1px dashed #00aaff !important;
    }
</style>
