@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-sm-flex align-items-center mb-4">
                            <h4 class="card-title mb-sm-0">Paintings Inventory</h4>
                            <div class="ms-auto d-flex align-items-center">
                                <a href="{{ route('paintings.create') }}" class="btn btn-primary btn-sm me-3 open-card-modal"
                                    data-title="Create New Painting">
                                    <i class="icon-plus"></i> Create New Painting
                                </a>
                                <a class="btn btn-success btn-sm open-import-modal text-end"
                                    data-title="Paintings Bulk Import" data-form-url="{{ route('bulk.import.form') }}"
                                    data-form-submit="{{ route('paintings.bulk.import') }}"
                                    data-form-demo-download="{{ route('paintings.demo.download') }}">
                                    <i class="bi bi-cloud-arrow-up me-1"></i> Open Bulk Import
                                </a>
                            </div>
                        </div>

                        <div class="table-responsive border rounded p-1">
                            <table class="table align-middle" id="paintings-table-rows">
                                <thead>
                                    <tr>
                                        <th class="font-weight-bold" style="width:90px;">Image</th>
                                        <th class="font-weight-bold">Name</th>
                                        <th class="font-weight-bold">Years</th>
                                        <th class="font-weight-bold">Price</th>
                                        <th class="font-weight-bold text-end" style="width:220px;">Actions</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($paintings as $painting)
                                        <tr>
                                            <td>
                                                @php
                                                    $imagePath = $painting->image
                                                        ? 'storage/' . $painting->image
                                                        : null;

                                                    $defaultImage = asset('default.jpeg');

                                                    $imageUrl =
                                                        $imagePath && file_exists(public_path($imagePath))
                                                            ? asset($imagePath)
                                                            : $defaultImage;
                                                @endphp

                                                <img src="{{ $imageUrl }}" alt="{{ $painting->name }}" class="rounded"
                                                    style="width:50px; height:50px; object-fit:contain; border:1px solid #ddd; background-color:#fff; padding:2px;">
                                            </td>
                                            <td>{{ $painting->name }}</td>
                                            <td>{{ $painting->years ?? '—' }}</td>
                                            <td>{{ number_format($painting->price, 2) }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('paintings.edit', $painting->id) }}"
                                                    class="btn btn-sm btn-outline-primary open-card-modal"
                                                    data-title="Edit Painting">Edit</a>
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-painting"
                                                    data-id="{{ $painting->id }}"
                                                    data-url="{{ route('paintings.destroy', $painting->id) }}">
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">No Paintings found.</td>
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

    <form id="deleteCardForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('styles')
    <style>
        .ciu-box.force-upload .cropper-container {
            pointer-events: none !important;
        }

        .cropper-view-box,
        .cropper-face {
            border-radius: 12px;
            overflow: hidden;
        }

        .cropper-view-box {
            outline: 2px dashed rgba(255, 255, 255, 0.8);
            outline-offset: -2px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Function to reload paintings table via AJAX
        function loadPaintingsTable() {
            $.ajax({
                url: '{{ route('paintings.index') }}',
                type: 'GET',
                dataType: 'html',
                success: function(response) {
                    const newRows = $(response).find('#paintings-table-rows').html();
                    $('#paintings-table-rows').html(newRows);
                },
                error: function() {
                    toastr.error('Failed to reload table data.');
                }
            });
        }

        // Handle AJAX form submission for creating/editing paintings
        $(document).on('submit', '#paintingForm', function(e) {
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
                        toastr.success(res.message || 'Painting saved successfully');
                        loadPaintingsTable();
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

        let deletePaintingId = null;

        $(document).on('click', '.delete-painting', function() {
            deletePaintingId = $(this).data('id');
            $('#confirmDeleteMessage').html('<p>Are you sure you want to delete this painting?</p>');
            $('#confirmDeleteModal').modal('show');
        });

        $('#confirmDeleteBtn').on('click', function() {
            if (deletePaintingId) {
                $.ajax({
                    url: '/paintings/' + deletePaintingId,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        if (res.success) {
                            $('#confirmDeleteModal').modal('hide');
                            loadPaintingsTable();
                            toastr.success(res.message || 'Painting deleted successfully.');
                        } else {
                            toastr.error(res.message || 'Something went wrong.');
                        }
                    },
                    error: function() {
                        $('#confirmDeleteModal').modal('hide');
                        toastr.error('Failed to delete painting.');
                    }
                });
            }
        });
    </script>
@endpush
