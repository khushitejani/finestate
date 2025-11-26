@extends('layouts.app')
@section('title', 'Yachts')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-sm-flex align-items-center mb-4">
                            <h4 class="card-title mb-sm-0">Yachts Inventory</h4>
                            <div class="ms-auto d-flex align-items-center">
                                <a href="{{ route('yatchshop.create') }}" class="btn btn-primary btn-sm me-3 open-card-modal"
                                    data-title="Create New Yacht">
                                    <i class="icon-plus"></i> Create New Yacht
                                </a>
                                <a class="btn btn-success btn-sm open-import-modal text-end" data-title="Yachts Bulk Import"
                                    data-form-url="{{ route('bulk.import.form') }}"
                                    data-form-submit="{{ route('yatch.bulk.import') }}"
                                    data-form-demo-download="{{ route('yatch_shops.demo.download') }}">
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
                                        <th>Price</th>
                                        <th>Description</th>
                                        <th class="text-end" style="width:220px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="yatch-table-rows">
                                    @forelse($yatch_shops as $yatch)
                                        <tr>
                                            <td>
                                                @php
                                                    $images = is_string($yatch->image)
                                                        ? json_decode($yatch->image, true)
                                                        : $yatch->image;
                                                    $images = is_array($images) ? $images : [];
                                                    $firstImage = count($images) > 0 ? $images[0] : null;
                                                    $defaultImage = asset('default.jpeg');
                                                    $imageUrl =
                                                        $firstImage &&
                                                        \Illuminate\Support\Facades\Storage::disk('public')->exists(
                                                            $firstImage,
                                                        )
                                                            ? asset('storage/' . $firstImage)
                                                            : $defaultImage;
                                                @endphp

                                                <img src="{{ $imageUrl }}" alt="{{ $yatch->name }}" class="rounded"
                                                    style="width:50px; height:50px; object-fit:cover; border:1px solid #ddd; background-color:#fff; padding:2px;">
                                            </td>
                                            <td>{{ $yatch->no ?? 'N/A'}}</td>
                                            <td>{{ $yatch->name }}</td>
                                            <td>{{ number_format($yatch->price, 2) }}</td>
                                            <td>{{ \Illuminate\Support\Str::limit($yatch->description, 50) }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('yatchshop.edit', $yatch->id) }}"
                                                    class="btn btn-sm btn-outline-primary open-card-modal"
                                                    data-title="Edit Yacht">
                                                    Edit
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-yatch"
                                                    data-id="{{ $yatch->id }}">Delete</button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">No yachts found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <form id="deleteYatchForm" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).on('submit', '#yatchForm', function(e) {
            e.preventDefault();
            let form = $(this);
            let url = form.attr('action');
            let method = form.attr('method') || 'POST';
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
                        toastr.success(res.message || 'Yacht saved successfully');
                        loadYatchTable();
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

        function loadYatchTable() {
            $.ajax({
                url: '{{ route('yatchshop.index') }}',
                type: 'GET',
                dataType: 'html',
                success: function(response) {
                    const rows = $(response).find('#yatch-table-rows').html();
                    $('#yatch-table-rows').html(rows);
                }
            });
        }
        let deleteYatchId = null;
        $(document).on('click', '.delete-yatch', function() {
            deleteYatchId = $(this).data('id');
            $('#confirmDeleteMessage').html('<p>Are you sure you want to delete this yacht?</p>');
            $('#confirmDeleteModal').modal('show');
        });

        $('#confirmDeleteBtn').on('click', function() {
            if (deleteYatchId) {
                $.ajax({
                    url: '/yatchshop/' + deleteYatchId,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        if (res.success) {
                            $('#confirmDeleteModal').modal('hide');
                            loadYatchTable();
                            toastr.success(res.message || 'Yacht deleted successfully.');
                        } else {
                            toastr.error(res.message || 'Something went wrong.');
                        }
                    },
                    error: function() {
                        $('#confirmDeleteModal').modal('hide');
                        toastr.error('Failed to delete yacht.');
                    }
                });
            }
        });
    </script>
@endpush
