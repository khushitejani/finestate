@extends('layouts.app')
@section('title', 'Jewels')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-sm-flex align-items-center mb-4">
                            <h4 class="card-title mb-sm-0">Jewels Inventory</h4>
                            <div class="ms-auto d-flex align-items-center">
                                <a href="{{ route('jewelleds.create') }}" class="btn btn-primary btn-sm me-3 open-card-modal"
                                    data-title="Create New Jewel">
                                    <i class="icon-plus"></i> Create New Jewel
                                </a>

                                <a class="btn btn-success btn-sm open-import-modal text-end" data-title="Jewels Bulk Import"
                                    data-form-url="{{ route('bulk.import.form') }}"
                                    data-form-submit="{{ route('jewelleds.bulk.import') }}"
                                     data-form-demo-download="{{ route('jewelleds.demo.download') }}">
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
                                        <th>Price</th>
                                        <th class="text-end" style="width:220px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="jewelleds-table-rows">
                                    @forelse($jewelleds as $jewel)
                                        <tr>
                                            <td>
                                                @php
                                                    $defaultImage = asset('default.jpeg');
                                                    $imagePath = $jewel->image;

                                                    $imageUrl =
                                                        $imagePath &&
                                                        \Illuminate\Support\Facades\Storage::disk('public')->exists(
                                                            $imagePath,
                                                        )
                                                            ? asset('storage/' . $imagePath)
                                                            : $defaultImage;
                                                @endphp

                                                <img src="{{ $imageUrl }}" alt="{{ $jewel->name }}" class="rounded"
                                                    style="width:50px; height:50px; object-fit:cover; border:1px solid #ddd; background-color:#fff; padding:2px;">
                                            </td>
                                            <td>{{ $jewel->name }}</td>
                                            <td>{{ number_format($jewel->price, 2) }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('jewelleds.edit', $jewel->id) }}"
                                                    class="btn btn-sm btn-outline-primary open-card-modal"
                                                    data-title="Edit Jewel">Edit</a>
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-jewelled"
                                                    data-id="{{ $jewel->id }}"
                                                    data-url="{{ route('jewelleds.destroy', $jewel->id) }}">
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">No jewelleds found.</td>
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

    <form id="deleteJewelForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('scripts')
    <script>
        function loadJewelledsTable() {
            $.ajax({
                url: '{{ route('jewelleds.index') }}',
                type: 'GET',
                dataType: 'html',
                success: function(response) {
                    const newRows = $(response).find('#jewelleds-table-rows').html();
                    $('#jewelleds-table-rows').html(newRows);
                },
                error: function() {
                    toastr.error('Failed to reload table data.');
                }
            });
        }

        // Save (Create/Update)
        $(document).on('submit', '#jewelledForm', function(e) {
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
                        toastr.success(res.message || 'Jewelled saved successfully');
                        loadJewelledsTable();
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
        let deleteJewelledId = null;

        $(document).on('click', '.delete-jewelled', function() {
            deleteJewelledId = $(this).data('id');
            $('#confirmDeleteMessage').html('<p>Are you sure you want to delete this jewelled?</p>');
            $('#confirmDeleteModal').modal('show');
        });

        $('#confirmDeleteBtn').on('click', function() {
            if (deleteJewelledId) {
                $.ajax({
                    url: '/jewelleds/' + deleteJewelledId,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        if (res.success) {
                            $('#confirmDeleteModal').modal('hide');
                            loadJewelledsTable();
                            toastr.success(res.message || 'Jewelled deleted successfully.');
                        } else {
                            toastr.error(res.message || 'Something went wrong.');
                        }
                    },
                    error: function() {
                        $('#confirmDeleteModal').modal('hide');
                        toastr.error('Failed to delete jewelled.');
                    }
                });
            }
        });
    </script>
@endpush
