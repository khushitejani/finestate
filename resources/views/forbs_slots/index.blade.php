@extends('layouts.app')
@section('title', 'Forbs Slots')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    
                    <div class="d-sm-flex align-items-center mb-4">
                        <h4 class="card-title mb-sm-0">Forbs Slots</h4>

                        <div class="ms-auto d-flex align-items-center">

                            <!-- Create New Button -->
                            <a href="{{ route('forbs_slots.create') }}"
                               class="btn btn-primary btn-sm me-3 open-card-modal"
                               data-title="Create New Forbs Slot">
                               <i class="icon-plus"></i> Create New Slot
                            </a>

                            <!-- Bulk Import Button (same style like Improvements) -->
                            <a class="btn btn-success btn-sm open-import-modal text-end"
                                data-title="Forbs Slot Bulk Import"
                                data-form-url="{{ route('bulk.import.form') }}"
                                data-form-submit="{{ route('forbs_slots.bulk.import') }}"
                                data-form-demo-download="{{ route('forbs_slots.demo.download') }}">
                                <i class="bi bi-cloud-arrow-up me-1"></i> Open Bulk Import
                            </a>

                        </div>
                    </div>

                    <div class="table-responsive border rounded p-1">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th style="width:90px;">Image</th>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Business</th>
                                    <th>Price</th>
                                    <th class="text-end" style="width:180px;">Actions</th>
                                </tr>
                            </thead>

                            <tbody id="forbs-slots-table-rows">
                                @forelse($forbs_slots as $slot)
                                    <tr id="forbs-slot-row-{{ $slot->id }}">
                                        
                                        <td>
                                            @php
                                                $img = $slot->image && Storage::disk('public')->exists($slot->image)
                                                    ? asset('storage/' . $slot->image)
                                                    : asset('default.jpeg');
                                            @endphp

                                            <img src="{{ $img }}" alt="Image"
                                                 class="rounded"
                                                 style="width:50px;height:50px;object-fit:cover;border:1px solid #ddd;background:#fff;padding:2px;">
                                        </td>

                                        <td>{{ $slot->no ?? 'N/A' }}</td>
                                        <td>{{ $slot->name }}</td>
                                        <td>{{ $slot->business }}</td>
                                        <td>{{ number_format($slot->price, 2) }}</td>

                                        <td class="text-end">
                                            <a href="{{ route('forbs_slots.edit', $slot->id) }}"
                                               class="btn btn-sm btn-outline-primary open-card-modal"
                                               data-title="Edit Forbs Slot">Edit</a>

                                            <button type="button"
                                                    class="btn btn-sm btn-outline-danger delete-forbs-slot"
                                                    data-id="{{ $slot->id }}">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>

                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            No Forbs Slots found.
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

<form id="deleteForbsSlotForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>
@endsection


@push('scripts')
<script>
function loadForbsSlotsTable() {
    $.ajax({
        url: '{{ route("forbs_slots.index") }}',
        type: 'GET',
        dataType: 'html',
        success: function(response) {
            const newRows = $(response).find('#forbs-slots-table-rows').html();
            $('#forbs-slots-table-rows').html(newRows);
        },
        error: function() {
            toastr.error('Failed to reload table data.');
        }
    });
}


// Save (Create/Update)
$(document).on('submit', '#forbsSlotForm', function(e) {
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
                loadForbsSlotsTable();
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

$(document).on('click', '.delete-forbs-slot', function() {
    deleteSlotId = $(this).data('id');
    $('#confirmDeleteMessage').html('<p>Are you sure you want to delete this slot?</p>');
    $('#confirmDeleteModal').modal('show');
});

$('#confirmDeleteBtn').on('click', function() {
    if (deleteSlotId) {
        $.ajax({
            url: '/forbs_slots/' + deleteSlotId,
            type: 'POST',
            data: {
                _method: 'DELETE',
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                if (res.success) {
                    $('#confirmDeleteModal').modal('hide');
                    loadForbsSlotsTable();
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
