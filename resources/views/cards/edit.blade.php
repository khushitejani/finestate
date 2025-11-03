ed  <form action="{{ route('cards.update', $card->id) }}" method="POST" enctype="multipart/form-data" id="cardForm">
      @csrf
      @method('PUT')

      <div id="card-image-uploader-modal">
          <label class="form-label fw-bold d-block">Card Image</label>

          <div class="ciu-box mx-auto position-relative"
              style="width:100%; max-width:420px; height:220px; cursor:pointer;
                   display:flex; align-items:center; justify-content:center;
                   background:#f9f9f9; border:2px dashed #ccc; border-radius:12px; overflow:hidden;">

              <img id="imagePreview" src="{{ $card->image ? getImageUrl($card->image) : '' }}" alt="Preview"
                  style="width:100%; height:100%; object-fit:cover; display:{{ $card->image ? 'block' : 'none' }};">

              <span class="ciu-placeholder"
                  style="font-size:2rem; color:#888; {{ $card->image ? 'display:none;' : '' }}">+ Image</span>
          </div>

          <input type="file" name="image" id="imageInput" class="d-none" accept="image/*">
      </div>
      <div class="mb-3 mt-3">
          <label class="form-label">Card Name</label>
          <input type="text" name="name" class="form-control" value="{{ old('name', $card->name) }}" required>
      </div>

      <div class="mb-3">
          <label class="form-label">Price</label>
          <input type="number" name="price" step="0.01" class="form-control"
              value="{{ old('price', $card->price) }}" required>
      </div>

      <div class="mb-3">
          <label class="form-label">Price Sign</label>
          <input type="text" name="sign_price" class="form-control"
              value="{{ old('price_sign', $card->sign_price ?? '$') }}" required>
      </div>

      <button type="submit" class="btn btn-success">Update Card</button>
  </form>
