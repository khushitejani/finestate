@extends('layouts.app')
@section('title', 'Business Slots')

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">

                        <div class="d-sm-flex align-items-center mb-4">
                            <h4 class="card-title mb-sm-0">Business Slots</h4>
                            <div class="ms-auto d-flex align-items-center">

                                <!-- Create New Button -->
                                <a href="{{ route('business_slots.create') }}"
                                    class="btn btn-primary btn-sm me-3 open-card-modal"
                                    data-title="Create New Business Slot">
                                    <i class="icon-plus"></i> Create New Slot
                                </a>

                                <!-- Bulk Import Button -->
                                <a class="btn btn-success btn-sm open-import-modal text-end"
                                    data-title="Business Slot Bulk Import" data-form-url="{{ route('bulk.import.form') }}"
                                    data-form-submit="{{ route('business_slots.bulk.import') }}"
                                    data-form-demo-download="{{ route('business_slots.demo.download') }}">
                                    <i class="bi bi-cloud-arrow-up me-1"></i> Open Bulk Import
                                </a>

                            </div>
                        </div>


                        <div class="table-responsive border rounded p-1">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Slot No</th>
                                        <th>Expansion Time</th>
                                        <th>Price</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="business-slots-table-rows">
                                    @forelse($business_slots as $slot)
                                        <tr id="business-slot-row-{{ $slot->id }}">
                                            <td>{{ $slot->no ?? 'N/A' }}</td>
                                            <td>{{ $slot->expansion_time ?? 'N/A' }}</td>
                                            <td>{{ number_format($slot->price, 2) }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('business_slots.edit', $slot->id) }}"
                                                    class="btn btn-sm btn-outline-primary open-card-modal"
                                                    data-title="Edit Business Slot">Edit</a>

                                                <button type="button"
                                                    class="btn btn-sm btn-outline-danger delete-business-slot"
                                                    data-id="{{ $slot->id }}">
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">
                                                No Business Slots found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Confirm Modal --}}
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body" id="confirmDeleteMessage"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <form id="deleteBusinessSlotForm" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('scripts')
    <script>
        function loadBusinessSlotsTable() {
            $.ajax({
                url: '{{ route('business_slots.index') }}',
                type: 'GET',
                dataType: 'html',
                success: function(response) {
                    const newRows = $(response).find('#business-slots-table-rows').html();
                    $('#business-slots-table-rows').html(newRows);
                },
                error: function() {
                    toastr.error('Failed to reload table data.');
                }
            });
        }

        // Save (Create/Update) Business Slot
        $(document).on('submit', '#businessSlotForm', function(e) {
            e.preventDefault();

            let form = $(this);
            let url = form.attr('action');
            let formData = new FormData(this);

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.success) {
                        $('#commonmodal').modal('hide');
                        toastr.success(res.message || 'Saved successfully');
                        loadBusinessSlotsTable();
                    } else {
                        toastr.error(res.message || 'Something went wrong');
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        Object.keys(errors).forEach(function(key) {
                            toastr.error(errors[key][0]);
                        });
                    } else {
                        toastr.error('Unexpected error occurred.');
                    }
                }
            });
        });

        // Delete
        let deleteSlotId = null;

        $(document).on('click', '.delete-business-slot', function() {
            deleteSlotId = $(this).data('id');
            $('#confirmDeleteMessage').html('<p>Are you sure you want to delete this slot?</p>');
            $('#confirmDeleteModal').modal('show');
        });

        $('#confirmDeleteBtn').on('click', function() {
            if (deleteSlotId) {
                $.ajax({
                    url: '/business_slots/' + deleteSlotId,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        if (res.success) {
                            $('#confirmDeleteModal').modal('hide');
                            loadBusinessSlotsTable();
                            toastr.success(res.message || 'Slot deleted successfully.');
                        } else {
                            toastr.error(res.message || 'Something went wrong.');
                        }
                    },
                    error: function() {
                        $('#confirmDeleteModal').modal('hide');
                        toastr.error('Failed to delete slot.');
                    }
                });
            }
        });
    </script>
@endpush
