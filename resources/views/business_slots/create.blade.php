<form action="{{ route('business_slots.store') }}" method="POST" id="businessSlotForm" enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
        <label class="form-label">No</label>
        <div class="input-group">
            <input type="number" id="noInput" name="no" class="form-control" required>

            <button type="button" class="btn btn-info btn-sm generate-number-btn" data-table="BusinessSlot">
                Generate Number
            </button>
        </div>
    </div>

    <div class="form-group mt-3">
        <label>Expansion Time <span class="text-danger">*</span></label>
        <input type="text" name="expansion_time" class="form-control" placeholder="Enter expansion time" required>
    </div>

    <div class="form-group mt-3">
        <label>Price <span class="text-danger">*</span></label>
        <input type="number" step="0.01" name="price" class="form-control" placeholder="Enter price" required>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
