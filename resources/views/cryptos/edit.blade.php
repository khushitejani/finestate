<form action="{{ route('cryptos.update', $cryptocurrency->id) }}" id="cryptoForm" method="POST"
    enctype="multipart/form-data">
    @csrf
    @method('PUT')
     <div id="card-image-uploader-modal">
        <label class="form-label fw-bold d-block">Crypto Image</label>

        <div class="ciu-box mx-auto position-relative"
            style="width:100%; max-width:420px; height:auto; cursor:pointer;
                display:flex; align-items:center; justify-content:center;
                background:#f9f9f9; border:2px dashed #ccc; border-radius:12px; overflow:hidden;">

            <img id="imagePreview" src="{{ $cryptocurrency->image ? asset('storage/' . $cryptocurrency->image) : '' }}" alt="Preview"
                style="width:100%; height:100%; object-fit:cover; display:{{ $cryptocurrency->image ? 'block' : 'none' }};">

            <span class="ciu-placeholder"
                style="font-size:2rem; color:#888; {{ $cryptocurrency->image ? 'display:none;' : '' }}">+
                Image</span>
        </div>

        <input type="file" name="image" id="imageInput" class="d-none" accept="image/*">
    </div>
    <div class="mb-3">
        <label for="name" class="form-label">Name</label>
        <input type="text" name="name" id="name" class="form-control"
            value="{{ old('name', $cryptocurrency->name) }}" required>
    </div>

    <div class="mb-3">
        <label for="price" class="form-label">Price (ETH)</label>
        <input type="number" name="price" id="price" step="0.0001" class="form-control"
            value="{{ old('price', $cryptocurrency->price) }}" required>
    </div>

    <div class="mb-3">
        <label for="cryptocurrencies_cap" class="form-label">Market Capitalization</label>
        <input type="number" name="cryptocurrencies_cap" id="cryptocurrencies_cap" step="0.01" class="form-control"
            value="{{ old('cryptocurrencies_cap', $cryptocurrency->cryptocurrencies_cap) }}">
    </div>

    <div class="mb-3">
        <label for="available_for_purchase" class="form-label">Amount Available for Purchase</label>
        <input type="number" name="available_for_purchase" id="available_for_purchase" step="0.01"
            class="form-control" value="{{ old('available_for_purchase', $cryptocurrency->available_for_purchase) }}">
    </div>
    <div class="mb-3">
        <label class="form-label" for="day_prices_input" >Add Intraday Prices (comma-separated)</label>
        <input type="text" name="day_prices_input"class="form-control"
            value="{{ old('day_prices_input', isset($cryptocurrency->day_prices['base']) ? implode(',', $cryptocurrency->day_prices['base']) : '') }}">

        <small class="text-muted">These prices will be recorded for today at this update.</small>
    </div>

    <button type="submit" class="btn btn-primary">Update Cryptocurrency</button>
</form>
