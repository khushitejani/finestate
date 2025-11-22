@extends('layouts.app')
@section('title', 'Business Shippings')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-sm-flex align-items-center mb-4">
                            <h4 class="card-title mb-sm-0">Business Shippings</h4>
                            <div class="ms-auto d-flex align-items-center">
                                <a href="{{ route('business-shippings.create') }}"
                                    class="btn btn-primary btn-sm me-3 open-card-modal" data-title="Create New Shipping">
                                    <i class="icon-plus"></i> Add New Shipping
                                </a>
                                <a class="btn btn-success btn-sm open-import-modal text-end"
                                    data-title="Business Shippings Bulk Import"
                                    data-form-url="{{ route('bulk.import.form') }}"
                                    data-form-submit="{{ route('business-shippings.bulk.import') }}"
                                    data-form-demo-download="{{ route('business-shippings.demo.download') }}">
                                    <i class="bi bi-cloud-arrow-up me-1"></i> Open Bulk Import
                                </a>
                            </div>
                        </div>

                        <div class="table-responsive border rounded p-1">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th style="width:80px;">Image</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Resource</th>
                                        <th>Income / Hour</th>
                                        <th>Price</th>
                                        <th class="text-end" style="width:180px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="business-shippings-table">
                                    @forelse($shippings as $ship)
                                        <tr id="shipping-row-{{ $ship->id }}">
                                            <td>
                                                @php
                                                    $imageUrl = $ship->image
                                                        ? asset('storage/' . $ship->image)
                                                        : asset('default.jpeg');
                                                @endphp
                                                <img src="{{ $imageUrl }}" alt="{{ $ship->name }}" class="rounded"
                                                    style="width:50px; height:50px; object-fit:contain; border:1px solid #ddd;">
                                            </td>
                                            <td>{{ $ship->name }}</td>
                                            <td>{{ $ship->category }}</td>
                                            <td>{{ $ship->resource }}</td>
                                            <td>{{ number_format($ship->income_per_hour, 2) }}</td>
                                            <td>{{ number_format($ship->price, 2) }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('business-shippings.edit', $ship->id) }}"
                                                    class="btn btn-sm btn-outline-primary open-card-modal"
                                                    data-title="Edit Shipping">
                                                    Edit
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-shipping"
                                                    data-id="{{ $ship->id }}">Delete</button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">No business shippings found.
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

    <form id="deleteShippingForm" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            // Open modal for create/edit
            $(document).on('click', '.open-card-modal', function(e) {
                e.preventDefault();
                let url = $(this).attr('href');
                let title = $(this).data('title') || 'Form';

                $('#commonmodalTitle').text(title);
                $('#commonmodalBody').html('<div class="text-center py-4">Loading...</div>');
                $('#commonmodal').modal('show');

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(res) {
                        $('#commonmodalBody').html(res);
                    },
                    error: function() {
                        $('#commonmodalBody').html(
                            '<div class="text-danger text-center py-4">Failed to load form.</div>'
                        );
                    }
                });
            });

            $(document).on('submit', '#shippingForm', function(e) {
                e.preventDefault();
                let form = $(this);
                let url = form.attr('action');
                let method = form.attr('method');
                let formData = new FormData(this);

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.success) {
                            $('#commonmodal').modal('hide');
                            toastr.success(res.message || 'Shipping saved successfully');
                            reloadShippingsTable();
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

            // Reload shippings table
            function reloadShippingsTable() {
                $.ajax({
                    url: '{{ route('business-shippings.index') }}',
                    type: 'GET',
                    dataType: 'html',
                    success: function(res) {
                        let newRows = $(res).find('#business-shippings-table').html();
                        $('#business-shippings-table').html(newRows);
                    },
                    error: function() {
                        toastr.error('Failed to reload shippings table.');
                    }
                });
            }

            // Delete shipping via AJAX
            let deleteShippingId = null;
            $(document).on('click', '.delete-shipping', function() {
                deleteShippingId = $(this).data('id');
                $('#confirmDeleteMessage').html('<p>Are you sure you want to delete this shipping?</p>');
                $('#confirmDeleteModal').modal('show');
            });

            $('#confirmDeleteBtn').on('click', function() {
                if (!deleteShippingId) return;

                $.ajax({
                    url: '/business-shippings/' + deleteShippingId,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        if (res.success) {
                            $('#confirmDeleteModal').modal('hide');
                            toastr.success(res.message || 'Shipping deleted successfully.');
                            reloadShippingsTable();
                        } else {
                            toastr.error(res.message || 'Failed to delete shipping.');
                        }
                    },
                    error: function() {
                        $('#confirmDeleteModal').modal('hide');
                        toastr.error('Unexpected error occurred');
                    }
                });
            });

        });
    </script>
@endpush
