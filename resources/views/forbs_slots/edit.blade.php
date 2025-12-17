<form action="{{ route('forbs_slots.update', $forbsSlot->id) }}" method="POST" enctype="multipart/form-data"
    id="forbsSlotForm">

    @csrf
    @method('PUT')

    <!-- Image Upload Box -->
    <div id="card-image-uploader-modal">
        <label class="form-label fw-bold d-block">Slot Image</label>

        <div class="ciu-box mx-auto position-relative"
            style="width:100%; max-width:500px; height:auto; cursor:pointer;
               display:flex; align-items:center; justify-content:center;
               background:#f9f9f9; border:2px dashed #ccc; border-radius:12px; 
               overflow:hidden; min-height:200px;">

            <!-- If existing image -->
            @if ($forbsSlot->image)
                <img id="imagePreview" src="{{ $forbsSlot->image ? asset('storage/' . $forbsSlot->image) : '' }}"
                    alt="Preview"
                    style="width:100%; height:100%; object-fit:cover; display:{{ $forbsSlot->image ? 'block' : 'none' }};">
            @else
                <img id="imagePreview" src="" alt="Preview"
                    style="width:100%; height:100%; object-fit:cover; display:none;">
                <span class="ciu-placeholder" style="font-size:2rem; color:#888;">+ Image</span>
            @endif
        </div>

        <input type="file" name="image" id="imageInput" class="d-none" accept="image/*">
    </div>


    <!-- No -->
    <div class="mb-3 mt-3">
        <label class="form-label">No</label>
        <div class="input-group">
            <input type="number" name="no" class="form-control" value="{{ $forbsSlot->no }}"
                placeholder="Enter slot number">

            <button type="button" class="btn btn-info btn-sm generate-number-btn" data-table="ForbsSlot">
                Generate Number
            </button>
        </div>
    </div>

    <!-- Name -->
    <div class="mb-3">
        <label class="form-label">Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" value="{{ $forbsSlot->name }}" required>
    </div>

    <!-- Business -->
    <div class="mb-3">
        <label class="form-label">Business</label>
        <input type="text" name="business" class="form-control" value="{{ $forbsSlot->business }}">
    </div>

    <!-- Price -->
    <div class="mb-3">
        <label class="form-label">Price</label>
        <input type="number" step="0.01" name="price" class="form-control" value="{{ $forbsSlot->price }}">
    </div>

    <button type="submit" class="btn btn-primary mt-3">Update Slot</button>
</form>
