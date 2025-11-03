@php
    $years = isset($retroCar->years) ? explode('-', $retroCar->years) : ['', ''];
    $startYear = trim($years[0] ?? '');
    $endYear = trim($years[1] ?? '');
@endphp

<form id="retroCarForm" action="{{ route('retro_cars.update', $retroCar->id) }}" method="POST"
    enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div id="card-image-uploader-modal">
        <label class="form-label fw-bold d-block">Retro Cars Image</label>

        <div class="ciu-box mx-auto position-relative"
            style="width:100%; max-width:420px; height:auto; cursor:pointer;
                display:flex; align-items:center; justify-content:center;
                background:#f9f9f9; border:2px dashed #ccc; border-radius:12px; overflow:hidden;">

            <img id="imagePreview" src="{{ $retroCar->image ? asset('storage/' . $retroCar->image) : '' }}" alt="Preview"
                style="width:100%; height:100%; object-fit:cover; display:{{ $retroCar->image ? 'block' : 'none' }};">

            <span class="ciu-placeholder" style="font-size:2rem; color:#888; {{ $retroCar->image ? 'display:none;' : '' }}">+
                Image</span>
        </div>

        <input type="file" name="image" id="imageInput" class="d-none" accept="image/*">
    </div>
    <div class="mb-3">
        <label for="name" class="form-label">Car Name</label>
        <input type="text" name="name" id="name" class="form-control"
            value="{{ old('name', $retroCar->name ?? '') }}" required>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="start_year" class="form-label">Start Year</label>
            <input type="number" name="start_year" id="start_year" class="form-control"
                value="{{ old('start_year', $startYear) }}" min="1900" max="{{ date('Y') }}" required>
        </div>

        <div class="col-md-6">
            <label for="end_year" class="form-label">End Year</label>
            <input type="number" name="end_year" id="end_year" class="form-control"
                value="{{ old('end_year', $endYear) }}" min="1900" max="{{ date('Y') }}" required>
        </div>
    </div>

    <div class="mb-3">
        <label for="price" class="form-label">Price</label>
        <input type="number" step="0.01" name="price" id="price" class="form-control"
            value="{{ old('price', $retroCar->price ?? '') }}" required>
    </div>

    <button type="submit" class="btn btn-primary">
        {{ isset($retroCar) ? 'Update Car' : 'Save Car' }}
    </button>
</form>
