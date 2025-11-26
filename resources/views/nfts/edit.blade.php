<form id="nftForm" action="{{ route('nfts.update', $nft->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT') <!-- Important for update -->
    <div id="card-image-uploader-modal">
        <label class="form-label fw-bold d-block">Crar Image</label>

        <div class="ciu-box mx-auto position-relative"
            style="width:100%; max-width:420px; height:auto; cursor:pointer;
                display:flex; align-items:center; justify-content:center;
                background:#f9f9f9; border:2px dashed #ccc; border-radius:12px; overflow:hidden;">

            <img id="imagePreview" src="{{ $nft->image ? asset('storage/' . $nft->image) : '' }}" alt="Preview"
                style="width:100%; height:100%; object-fit:cover; display:{{ $nft->image ? 'block' : 'none' }};">

            <span class="ciu-placeholder" style="font-size:2rem; color:#888; {{ $nft->image ? 'display:none;' : '' }}">+
                Image</span>
        </div>

        <input type="file" name="image" id="imageInput" class="d-none" accept="image/*">
    </div>
    <div class="mb-3">
        <label for="name" class="form-label">NFT Name</label>
        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $nft->name) }}"
            required>
    </div>

    <div class="mb-3">
        <label class="form-label">No</label>
        <div class="input-group">
            <input type="number" id="noInput" name="no" class="form-control" value="{{ $nft->no }}"
                required>

            <button type="button" class="btn btn-info btn-sm generate-number-btn" data-table="NFT">
                Generate Number
            </button>
        </div>
    </div>

    <div class="mb-3">
        <label for="price" class="form-label">Price (ETH)</label>
        <input type="number" step="0.01" name="price" id="price" class="form-control"
            value="{{ old('price', $nft->price) }}" required>
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea name="description" id="description" class="form-control" rows="3" required>{{ old('description', $nft->description) }}</textarea>
    </div>
    <button type="submit" class="btn btn-primary">Update NFT</button>
</form>
