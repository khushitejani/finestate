<form id="taxiForm" action="{{ route('business-taxis.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <!-- Image uploader -->
    <div id="card-image-uploader-modal" data-output-width="400" data-output-height="400" data-aspect-ratio="1"
        class="mb-3">
        <label class="form-label fw-bold d-block">Taxi Image</label>
        <div class="ciu-box mx-auto position-relative"
            style="width:100%; max-width:500px; height:auto; cursor:pointer;
                   display:flex; align-items:center; justify-content:center;
                   background:#f9f9f9; border:2px dashed #ccc; border-radius:12px; overflow:hidden; min-height:200px;">
            <img class="ciu-preview" id="imagePreview" style="display:none; max-width:100%; max-height:500px;">
            <span class="ciu-placeholder" style="font-size:2rem; color:#888;">+ Image</span>
        </div>
        <input type="file" class="ciu-input d-none" name="image" id="imageInput" accept="image/*">
    </div>

    <!-- Taxi Name -->
    <div class="mb-3 mt-3">
        <label class="form-label">Taxi Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
    </div>
    <div class="mb-3">
        <label class="form-label">No</label>
        <div class="input-group">
            <input type="number" id="noInput" name="no" class="form-control" placeholder="Enter or Generate"
                required>
            <button type="button" class="btn btn-info btn-sm generate-number-btn" data-table="BusinessTaxi">
                Generate Number
            </button>
        </div>
    </div>
    <!-- Resource -->
    <div class="mb-3">
        <label class="form-label">Resource</label>
        <textarea name="resource" class="form-control">{{ old('resource') }}</textarea>
    </div>
    <!-- Class Dropdown -->
    <div class="mb-3">
        <label class="form-label">Class</label>
        <select name="class" class="form-control" required>
            @php
                $classes = ['Smart', 'Comfort', 'Luxe', 'Spacio', 'Business', 'Premier', 'Elite', 'Royal'];
            @endphp
            <option value="">Select Class</option>
            @foreach ($classes as $class)
                <option value="{{ $class }}" {{ old('class') == $class ? 'selected' : '' }}>{{ $class }}
                </option>
            @endforeach
        </select>
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
        <button type="submit" class="btn btn-success text-end">Create Taxi</button>
    </div>

</form>
