@extends('layouts.app')
@section('title', 'Shares')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-sm-flex align-items-center mb-4">
                            <h4 class="card-title mb-sm-0">Shares Inventory</h4>
                            <div class="ms-auto d-flex align-items-center">
                                <a href="{{ route('shares.create') }}" class="btn btn-primary btn-sm me-3 open-card-modal"
                                    data-title="Create New Share">
                                    <i class="icon-plus"></i> Create New Share
                                </a>
                                <a class="btn btn-success btn-sm open-import-modal text-end" data-title="Shares Bulk Import"
                                    data-form-url="{{ route('bulk.import.form') }}"
                                    data-form-submit="{{ route('share.bulk.import') }}"
                                    data-form-demo-download="{{ route('shares.demo.download') }}">
                                    <i class="bi bi-cloud-arrow-up me-1"></i> Open Bulk Import
                                </a>
                            </div>
                        </div>

                        <div class="table-responsive border rounded p-1">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th style="width:90px;">Image</th>
                                        <th>Name</th>
                                        <th>Share Price</th>
                                        <th>Dividend</th>
                                        <th>Time Period</th>
                                        <th>Capitalization</th>
                                        <th>Available Shares</th>
                                        <th class="text-end" style="width:220px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="shares-table-rows">
                                    @forelse($shares as $share)
                                        <tr id="share-row-{{ $share->id }}">
                                            <td>
                                                @php
                                                    $imageUrl = $share->image
                                                        ? getImageUrl($share->image)
                                                        : asset('default.jpeg');
                                                @endphp
                                                <img src="{{ $imageUrl }}" alt="{{ $share->name }}" class="rounded"
                                                    style="width: 50px; height: 50px; object-fit: contain; border: 1px solid #ddd; background-color: #fff; padding: 2px;">
                                            </td>
                                            <td>{{ $share->name }}</td>
                                            <td>{{ number_format($share->share_price, 2) }}</td>
                                            <td>{{ $share->dividend }}</td>
                                            <td>{{ $share->time_period }}</td>
                                            <td>{{ $share->capitalization }}</td>
                                            <td>{{ $share->available_shares }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('shares.edit', $share->id) }}"
                                                    class="btn btn-sm btn-outline-primary open-card-modal">Edit</a>
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-share"
                                                    data-id="{{ $share->id }}"
                                                    data-url="{{ route('shares.destroy', $share->id) }}">Delete</button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">No shares found.</td>
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

    <form id="deleteShareForm" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('scripts')
    <script>
        function loadSharesTable() {
            $.ajax({
                url: '{{ route('shares.index') }}',
                type: 'GET',
                dataType: 'html',
                success: function(response) {
                    const newRows = $(response).find('#shares-table-rows').html();
                    $('#shares-table-rows').html(newRows);
                },
                error: function() {
                    toastr.error('Failed to reload shares table.');
                }
            });
        }
        $(document).on('submit', '#shareForm', function(e) {
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
                        toastr.success(res.message || 'Share saved successfully');
                        loadSharesTable();
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


        let deleteShareId = null;
        $(document).on('click', '.delete-share', function() {
            deleteShareId = $(this).data('id');
            $('#confirmDeleteMessage').html('<p>Are you sure you want to delete this share?</p>');
            $('#confirmDeleteModal').modal('show');
        });

        $('#confirmDeleteBtn').on('click', function() {
            if (deleteShareId) {
                $.ajax({
                    url: '/shares/' + deleteShareId,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        if (res.success) {
                            $('#confirmDeleteModal').modal('hide');
                            loadSharesTable();
                            toastr.success(res.message || 'Share deleted successfully.');
                        } else {
                            toastr.error(res.message || 'Something went wrong.');
                        }
                    },
                    error: function() {
                        $('#confirmDeleteModal').modal('hide');
                        toastr.error('Failed to delete share.');
                    }
                });
            }
        });
    </script>
@endpush
