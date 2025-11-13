<form id="taxiForm" action="{{ route('business-taxis.update', $taxi->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <!-- Image uploader -->
    <div id="card-image-uploader-modal" data-output-width="400" data-output-height="400" data-aspect-ratio="1"
        class="mb-3">
        <label class="form-label fw-bold d-block">Taxi Image</label>
        <div class="ciu-box mx-auto position-relative"
            style="width:100%; max-width:500px; height:auto; cursor:pointer;
                   display:flex; align-items:center; justify-content:center;
                   background:#f9f9f9; border:2px dashed #ccc; border-radius:12px; overflow:hidden; min-height:200px;">
            <img class="ciu-preview" id="imagePreview" src="{{ $taxi->image ? asset('storage/' . $taxi->image) : '' }}"
                style="{{ $taxi->image ? '' : 'display:none;' }} max-width:100%; max-height:500px;">
            <span class="ciu-placeholder"
                style="font-size:2rem; color:#888; {{ $taxi->image ? 'display:none;' : '' }}">+ Image</span>
        </div>
        <input type="file" class="ciu-input d-none" name="image" id="imageInput" accept="image/*">
    </div>

    <!-- Taxi Name -->
    <div class="mb-3 mt-3">
        <label class="form-label">Taxi Name</label>
        <input type="text" name="name" class="form-control" value="{{ $taxi->name }}" required>
    </div>

    <!-- Resource -->
    <div class="mb-3">
        <label class="form-label">Resource</label>
        <textarea name="resource" class="form-control">{{ $taxi->resource }}</textarea>
    </div>


    <!-- Class Dropdown -->
    <div class="mb-3">
        <label class="form-label">Class</label>
        @php
            $classes = ['Smart', 'Comfort', 'Luxe', 'Spacio', 'Business', 'Premier', 'Elite', 'Royal'];
        @endphp
        <select name="class" class="form-control" required>
            <option value="">Select Class</option>
            @foreach ($classes as $class)
                <option value="{{ $class }}" {{ $taxi->class == $class ? 'selected' : '' }}>
                    {{ $class }}</option>
            @endforeach
        </select>
    </div>

    <!-- Income / Hour -->
    <div class="mb-3">
        <label class="form-label">Income / Hour</label>
        <input type="number" step="0.01" name="income_per_hour" class="form-control"
            value="{{ $taxi->income_per_hour }}">
    </div>


    <div class="mb-3">
        <label class="form-label">Price</label>
        <input type="number" step="0.01" name="price" class="form-control" value="{{ $taxi->price }}">
    </div>

    <div class="d-flex justify-content-end">
        <button type="submit" class="btn btn-success text-end">Update Taxi</button>
    </div>
</form>
