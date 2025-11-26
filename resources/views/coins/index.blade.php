@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-sm-flex align-items-center mb-4">
                            <h4 class="card-title mb-sm-0">Coins Inventory</h4>
                            <div class="ms-auto d-flex align-items-center gap-2">
                                <a href="{{ route('coins.create') }}" class="btn btn-primary btn-sm open-card-modal"
                                    data-title="Create New Coin">
                                    <i class="icon-plus"></i> Create New Coin
                                </a>
                                <a class="btn btn-success btn-sm open-import-modal" data-title="Coins Bulk Import"
                                    data-form-url="{{ route('bulk.import.form') }}"
                                    data-form-submit="{{ route('coins.bulk.import') }}"
                                    data-form-demo-download="{{ route('coins.demo.download') }}">
                                    <i class="bi bi-cloud-arrow-up me-1"></i> Bulk Import
                                </a>
                            </div>
                        </div>

                        <div class="table-responsive border rounded p-1">
                            <table class="table align-middle" id="coins-table-rows">
                                <thead>
                                    <tr>
                                        <th class="font-weight-bold" style="width:90px;">Image</th>
                                        <th class="font-weight-bold">No</th>
                                        <th class="font-weight-bold">Name</th>
                                        <th class="font-weight-bold">Years</th>
                                        <th class="font-weight-bold">Price</th>
                                        <th class="font-weight-bold text-end" style="width:220px;">Actions</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($coins as $coin)
                                        <tr>
                                            <td>
                                                @php
                                                    $imagePath = $coin->image ? 'storage/' . $coin->image : null;
                                                    $defaultImage = asset('default.jpeg');
                                                    $imageUrl =
                                                        $imagePath && file_exists(public_path($imagePath))
                                                            ? asset($imagePath)
                                                            : $defaultImage;
                                                @endphp

                                                <img src="{{ $imageUrl }}" alt="{{ $coin->name }}" class="rounded"
                                                    style="width:50px; height:50px; object-fit:contain; border:1px solid #ddd; background-color:#fff; padding:2px;">
                                            </td>
                                            <td>{{ $coin->no ?? 'N/A' }}</td>
                                            <td>{{ $coin->name }}</td>
                                            <td>{{ $coin->years ?? '—' }}</td>
                                            <td>{{ number_format($coin->price, 2) }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('coins.edit', $coin->id) }}"
                                                    class="btn btn-sm btn-outline-primary open-card-modal"
                                                    data-title="Edit Coin">Edit</a>
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-coin"
                                                    data-id="{{ $coin->id }}"
                                                    data-url="{{ route('coins.destroy', $coin->id) }}">
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">No Coins found.</td>
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
        function loadCoinsTable() {
            $.ajax({
                url: '{{ route('coins.index') }}',
                type: 'GET',
                dataType: 'html',
                success: function(response) {
                    const newRows = $(response).find('#coins-table-rows').html();
                    $('#coins-table-rows').html(newRows);
                },
                error: function() {
                    toastr.error('Failed to reload table data.');
                }
            });
        }

        $(document).on('submit', '#coinForm', function(e) {
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
                        toastr.success(res.message || 'Coin saved successfully');
                        loadCoinsTable();
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

        let deleteCoinId = null;

        $(document).on('click', '.delete-coin', function() {
            deleteCoinId = $(this).data('id');
            $('#confirmDeleteMessage').html('<p>Are you sure you want to delete this coin?</p>');
            $('#confirmDeleteModal').modal('show');
        });

        $('#confirmDeleteBtn').on('click', function() {
            if (deleteCoinId) {
                $.ajax({
                    url: '/coins/' + deleteCoinId,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        if (res.success) {
                            $('#confirmDeleteModal').modal('hide');
                            loadCoinsTable();
                            toastr.success(res.message || 'Coin deleted successfully.');
                        } else {
                            toastr.error(res.message || 'Something went wrong.');
                        }
                    },
                    error: function() {
                        $('#confirmDeleteModal').modal('hide');
                        toastr.error('Failed to delete coin.');
                    }
                });
            }
        });
    </script>
@endpush
