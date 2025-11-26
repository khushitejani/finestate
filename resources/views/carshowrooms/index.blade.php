@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-sm-flex align-items-center mb-4">
                            <h4 class="card-title mb-sm-0">Products Inventory</h4>
                            <div class="ms-auto d-flex align-items-center gap-2">
                                <a href="{{ route('carshowrooms.create') }}"
                                    class="btn btn-primary btn-sm ms-3 open-card-modal" data-title="Create New Card">
                                    <i class="icon-plus"></i> Create New Cars
                                </a>
                                <a class="btn btn-success btn-sm open-import-modal text-end"
                                    data-title="Car Showroom Bulk Import" data-form-url="{{ route('bulk.import.form') }}"
                                    data-form-submit="{{ route('carshowrooms.bulk.import') }}"
                                    data-form-demo-download="{{ route('carshowrooms.demo.download') }}">
                                    <i class="bi bi-cloud-arrow-up me-1"></i> Bulk Import
                                </a>

                            </div>
                        </div>
                        <div class="table-responsive border rounded p-1">
                            <table class="table align-middle" id="car-showroom-table-rows">
                                <thead>
                                    <tr>
                                        <th class="font-weight-bold" style="width:90px;">Image</th>
                                        <th class="font-weight-bold">No</th>
                                        <th class="font-weight-bold">Name</th>
                                        <th class="font-weight-bold">Price</th>
                                        <th class="font-weight-bold text-end" style="width:220px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($carshowrooms as $showroom)
                                        <tr>
                                            <td>
                                                @php
                                                    $images = is_string($showroom->images)
                                                        ? json_decode($showroom->images, true)
                                                        : $showroom->images ?? [];
                                                    $images = is_array($images) ? $images : [];

                                                    $firstImage = count($images) ? $images[0] : null;

                                                    $imageUrl = $firstImage
                                                        ? asset('storage/' . $firstImage)
                                                        : asset('default.jpeg');
                                                    if ($firstImage && !Storage::disk('public')->exists($firstImage)) {
                                                        $imageUrl = asset('default.jpeg');
                                                    }
                                                @endphp

                                                <img src="{{ $imageUrl }}" alt="{{ $showroom->name }}" class="rounded"
                                                    style="width: 50px; height: 50px; object-fit: contain; border: 1px solid #ddd; background-color: #fff; padding: 2px;">
                                            </td>
                                            <td>{{ $showroom->no ?? 'N/A' }}</td>
                                            <td>{{ $showroom->name }}</td>
                                            <td>{{ number_format($showroom->price, 2) }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('carshowrooms.edit', $showroom->id) }}"
                                                    class="btn btn-sm btn-outline-primary open-card-modal"
                                                    data-title="Edit Car Showroom">Edit</a>
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-showroom"
                                                    data-id="{{ $showroom->id }}"
                                                    data-url="{{ route('carshowrooms.destroy', $showroom->id) }}">
                                                    Delete
                                                </button>

                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">No Cars found.</td>
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
        function loadCarShowroomTable() {
            $.ajax({
                url: '{{ route('carshowrooms.index') }}',
                type: 'GET',
                dataType: 'html',
                success: function(response) {
                    const newRows = $(response).find('#car-showroom-table-rows').html();
                    $('#car-showroom-table-rows').html(newRows);
                },
                error: function() {
                    toastr.error('Failed to reload table data.');
                }
            });
        }


        $(document).on('submit', '#carShowroomForm', function(e) {
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
                        toastr.success(res.message || 'Car Showroom saved successfully');
                        loadCarShowroomTable();
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
        let deleteCardId = null;

        $(document).on('click', '.delete-showroom', function() {
            deleteCardId = $(this).data('id');
            $('#confirmDeleteMessage').html('<p>Are you sure you want to delete this car?</p>');
            $('#confirmDeleteModal').modal('show');
        });

        $('#confirmDeleteBtn').on('click', function() {
            if (deleteCardId) {
                $.ajax({
                    url: '/carshowrooms/' + deleteCardId,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        if (res.success) {
                            $('#confirmDeleteModal').modal('hide');
                            loadCarShowroomTable();
                            toastr.success(res.message || 'Car deleted successfully.');
                        } else {
                            toastr.error(res.message || 'Something went wrong.');
                        }
                    },
                    error: function() {
                        $('#confirmDeleteModal').modal('hide');
                        toastr.error('Failed to delete car.');
                    }
                });
            }
        });
    </script>
@endpush
