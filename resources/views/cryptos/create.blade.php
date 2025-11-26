<form action="{{ route('cryptos.store') }}" id="cryptoForm" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="mb-3">
        <label class="form-label fw-bold d-block">Crypto Image</label>

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
        <label class="form-label" for="name">Name</label>
        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
    </div>

    <div class="mb-3">
        <label class="form-label">No</label>
        <div class="input-group">
            <input type="number" id="noInput" name="no" class="form-control" placeholder="Enter or Generate"
                required>
            <button type="button" class="btn btn-info btn-sm generate-number-btn" data-table="Cryptocurrency">
                Generate Number
            </button>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label" for="price">Price (ETH)</label>
        <input type="number" name="price" id="price" step="0.0001" class="form-control"
            value="{{ old('price') }}" required>
    </div>

    <div class="mb-3">
        <label class="form-label" for="cryptocurrencies_cap">Market Capitalization</label>
        <input type="number" name="cryptocurrencies_cap" id="cryptocurrencies_cap" step="0.01" class="form-control"
            value="{{ old('cryptocurrencies_cap', $cryptocurrency->cryptocurrencies_cap ?? '') }}">
    </div>
    <div class="mb-3">
        <img id="imagePreview" src="#" alt="Image Preview"
            style="display: none; max-width: 200px; max-height: 200px;" />
    </div>

    <div class="mb-3">
        <label class="form-label" for="available_for_purchase">Amount Available for Purchase</label>
        <input type="number" name="available_for_purchase" id="available_for_purchase" step="0.01"
            class="form-control"
            value="{{ old('available_for_purchase', $cryptocurrency->available_for_purchase ?? '') }}">
    </div>
    <div class="mb-3">
        <label class="form-label" for="day_prices_input">Intraday Prices (comma-separated)</label>
        <input type="text" name="day_prices_input" id="day_prices_input" class="form-control"
            placeholder="e.g. 100, 200, 300">
        <small class="text-muted">These prices will be recorded for today at creation time.</small>
    </div>


    <button type="submit" class="btn btn-primary">Create Cryptocurrency</button>
</form>
