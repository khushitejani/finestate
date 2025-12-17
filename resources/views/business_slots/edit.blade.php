<form action="{{ route('business_slots.update', $businessSlot->id) }}" id="businessSlotForm" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label class="form-label">No</label>
        <div class="input-group">
            <input type="number" id="noInput" name="no" value="{{ $businessSlot->no }}" class="form-control" required>

            <button type="button" class="btn btn-info btn-sm generate-number-btn" data-table="BusinessSlot">
                Generate Number
            </button>
        </div>
    </div>

    <div class="form-group mt-3">
        <label>Expansion Time <span class="text-danger">*</span></label>
        <input type="text" name="expansion_time" class="form-control" value="{{ $businessSlot->expansion_time }}" required>
    </div>

    <div class="form-group mt-3">
        <label>Price <span class="text-danger">*</span></label>
        <input type="number" step="0.01" name="price" class="form-control" value="{{ $businessSlot->price }}" required>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-success">Update</button>
    </div>
</form>
