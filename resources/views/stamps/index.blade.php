@extends('layouts.app')
@section('title', 'Stamps Inventory')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-sm-flex align-items-center mb-4">
                            <h4 class="card-title mb-sm-0">Stamps Inventory</h4>
                            <div class="ms-auto d-flex align-items-center">
                                <a href="{{ route('stamps.create') }}" class="btn btn-primary btn-sm me-3 open-card-modal"
                                    data-title="Create New Stamp">
                                    <i class="icon-plus"></i> Create New Stamp
                                </a>

                                <a class="btn btn-success btn-sm open-import-modal text-end" data-title="Stamps Bulk Import"
                                    data-form-url="{{ route('bulk.import.form') }}"
                                    data-form-submit="{{ route('stamps.bulk.import') }}"
                                    data-form-demo-download="{{ route('stamps.demo.download') }}">
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
                                        <th class="font-weight-bold">Years</th>
                                        <th>Price</th>
                                        <th class="text-end" style="width:220px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="stamps-table-rows">
                                    @forelse($stamps as $stamp)
                                        <tr>
                                            <td>
                                                @php
                                                    $defaultImage = asset('default.jpeg');
                                                    $imagePath = $stamp->image;

                                                    $imageUrl =
                                                        $imagePath &&
                                                        \Illuminate\Support\Facades\Storage::disk('public')->exists(
                                                            $imagePath,
                                                        )
                                                            ? asset('storage/' . $imagePath)
                                                            : $defaultImage;
                                                @endphp

                                                <img src="{{ $imageUrl }}" alt="{{ $stamp->name }}" class="rounded"
                                                    style="width:70px; height:40px; object-fit:cover; border:1px solid #ddd; background-color:#fff; padding:2px;">
                                            </td>
                                            <td>{{ $stamp->no ?? 'N/A' }}</td>
                                            <td>{{ $stamp->name }}</td>
                                            <td>{{ $stamp->years ?? '—' }}</td>
                                            <td>{{ number_format($stamp->price, 2) }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('stamps.edit', $stamp->id) }}"
                                                    class="btn btn-sm btn-outline-primary open-card-modal"
                                                    data-title="Edit Stamp">Edit</a>
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-stamp"
                                                    data-id="{{ $stamp->id }}"
                                                    data-url="{{ route('stamps.destroy', $stamp->id) }}">
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">No stamps found.</td>
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

    <form id="deleteStampForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('scripts')
    <script>
        function loadStampsTable() {
            $.ajax({
                url: '{{ route('stamps.index') }}',
                type: 'GET',
                dataType: 'html',
                success: function(response) {
                    const newRows = $(response).find('#stamps-table-rows').html();
                    $('#stamps-table-rows').html(newRows);
                },
                error: function() {
                    toastr.error('Failed to reload table data.');
                }
            });
        }

        // Save (Create/Update)
        $(document).on('submit', '#stampForm', function(e) {
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
                        toastr.success(res.message || 'Stamp saved successfully');
                        loadStampsTable();
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
        let deleteStampId = null;

        $(document).on('click', '.delete-stamp', function() {
            deleteStampId = $(this).data('id');
            $('#confirmDeleteMessage').html('<p>Are you sure you want to delete this stamp?</p>');
            $('#confirmDeleteModal').modal('show');
        });

        $('#confirmDeleteBtn').on('click', function() {
            if (deleteStampId) {
                $.ajax({
                    url: '/stamps/' + deleteStampId,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        if (res.success) {
                            $('#confirmDeleteModal').modal('hide');
                            loadStampsTable();
                            toastr.success(res.message || 'Stamp deleted successfully.');
                        } else {
                            toastr.error(res.message || 'Something went wrong.');
                        }
                    },
                    error: function() {
                        $('#confirmDeleteModal').modal('hide');
                        toastr.error('Failed to delete stamp.');
                    }
                });
            }
        });
    </script>
@endpush
