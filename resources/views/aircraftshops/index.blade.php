@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-sm-flex align-items-center mb-4">
                            <h4 class="card-title mb-sm-0">Aircraft Shops Inventory</h4>
                            <div class="ms-auto d-flex align-items-center">
                                <a href="{{ route('aircraftshops.create') }}"
                                    class="btn btn-primary btn-sm ms-3 open-card-modal"
                                    data-title="Create New Aircraft Shop">
                                    <i class="icon-plus"></i> Create New Aircraft Shop
                                </a>
                                <a class="btn btn-success btn-sm open-import-modal ms-3"
                                    data-title="Aircraft Shops Bulk Import" data-form-url="{{ route('bulk.import.form') }}"
                                    data-form-submit="{{ route('aircraftshops.bulkImport') }}"
                                    data-form-demo-download="{{ route('aircraftshops.demo.download') }}">
                                    <i class="bi bi-cloud-arrow-up me-1"></i> Bulk Import
                                </a>

                            </div>
                        </div>
                        <div class="table-responsive border rounded p-1">
                            <table class="table align-middle" id="aircraft-shop-table-rows">
                                <thead>
                                    <tr>
                                        <th class="font-weight-bold" style="width:90px;">Image</th>
                                        <th class="font-weight-bold">No</th>
                                        <th class="font-weight-bold">Name</th>
                                        <th class="font-weight-bold">Price</th>
                                        <th class="font-weight-bold">Description</th>
                                        <th class="font-weight-bold text-end" style="width:220px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($aircrafts as $aircraft)
                                        <tr>
                                            <td>
                                                @php
                                                    $images = is_array($aircraft->images)
                                                        ? $aircraft->images
                                                        : json_decode($aircraft->images, true);

                                                    $images = is_array($images) ? $images : [];
                                                    $firstImage = count($images) ? $images[0] : null;
                                                    $imageUrl = $firstImage
                                                        ? asset('storage/' . $firstImage)
                                                        : asset('default.jpeg');
                                                    if ($firstImage && !Storage::disk('public')->exists($firstImage)) {
                                                        $imageUrl = asset('default.jpeg');
                                                    }
                                                @endphp

                                                <img src="{{ $imageUrl }}" alt="{{ $aircraft->name }}" class="rounded"
                                                    style="width:50px; height:50px; object-fit:contain; border:1px solid #ddd; background-color:#fff; padding:2px;">
                                            </td>
                                            <td>{{ $aircraft->no ?? 'N/A' }}</td>
                                            <td>{{ $aircraft->name }}</td>
                                            <td>{{ number_format($aircraft->price, 2) }}</td>
                                            <td>{{ \Illuminate\Support\Str::limit($aircraft->description, 50) }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('aircraftshops.edit', $aircraft->id) }}"
                                                    class="btn btn-sm btn-outline-primary open-card-modal"
                                                    data-title="Edit Aircraft Shop">Edit</a>
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-shop"
                                                    data-id="{{ $aircraft->id }}"
                                                    data-url="{{ route('aircraftshops.destroy', $aircraft->id) }}">
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">No Aircraft Shops found.
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
        function loadAircraftShopTable() {
            $.ajax({
                url: '{{ route('aircraftshops.index') }}',
                type: 'GET',
                dataType: 'html',
                success: function(response) {
                    const newRows = $(response).find('#aircraft-shop-table-rows').html();
                    $('#aircraft-shop-table-rows').html(newRows);
                },
                error: function() {
                    toastr.error('Failed to reload table data.');
                }
            });
        }

        $(document).on('submit', '#aircraftShopForm', function(e) {
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
                        toastr.success(res.message || 'Aircraft Shop saved successfully');
                        loadAircraftShopTable();
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

        let deleteShopId = null;

        $(document).on('click', '.delete-shop', function() {
            deleteShopId = $(this).data('id');
            $('#confirmDeleteMessage').html('<p>Are you sure you want to delete this aircraft shop?</p>');
            $('#confirmDeleteModal').modal('show');
        });

        $('#confirmDeleteBtn').on('click', function() {
            if (deleteShopId) {
                $.ajax({
                    url: '/aircraftshops/' + deleteShopId,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        if (res.success) {
                            $('#confirmDeleteModal').modal('hide');
                            loadAircraftShopTable();
                            toastr.success(res.message || 'Aircraft Shop deleted successfully.');
                        } else {
                            toastr.error(res.message || 'Something went wrong.');
                        }
                    },
                    error: function() {
                        $('#confirmDeleteModal').modal('hide');
                        toastr.error('Failed to delete aircraft shop.');
                    }
                });
            }
        });
    </script>
@endpush
