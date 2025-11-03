@extends('layouts.app')
@section('title', 'Islands')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-sm-flex align-items-center mb-4">
                            <h4 class="card-title mb-sm-0">Islands Inventory</h4>
                            <div class="ms-auto d-flex align-items-center">
                                <a href="{{ route('islands.create') }}" class="btn btn-primary btn-sm me-3 open-card-modal"
                                    data-title="Create New Island">
                                    <i class="icon-plus"></i> Create New Island
                                </a>

                                <a class="btn btn-success btn-sm open-import-modal text-end"
                                    data-title="Islands Bulk Import" data-form-url="{{ route('bulk.import.form') }}"
                                    data-form-submit="{{ route('islands.bulk.import') }}"
                                     data-form-demo-download="{{ route('islands.demo.download') }}">
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
                                        <th>Description</th>
                                        <th class="text-end" style="width:220px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="islands-table-rows">
                                    @forelse($islands as $island)
                                        <tr>
                                            <td>
                                                @php
                                                    $defaultImage = asset('default.jpeg');
                                                    $imagePath = $island->image;

                                                    $imageUrl =
                                                        $imagePath &&
                                                        \Illuminate\Support\Facades\Storage::disk('public')->exists(
                                                            $imagePath,
                                                        )
                                                            ? asset('storage/' . $imagePath)
                                                            : $defaultImage;
                                                @endphp

                                                <img src="{{ $imageUrl }}" alt="{{ $island->name }}" class="rounded"
                                                    style="width:50px; height:50px; object-fit:cover; border:1px solid #ddd; background-color:#fff; padding:2px;">
                                            </td>
                                            <td>{{ $island->name }}</td>
                                            <td>{{ number_format($island->price, 2) }} ETH</td>
                                            <td>{{ $island->description }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('islands.edit', $island->id) }}"
                                                    class="btn btn-sm btn-outline-primary open-card-modal"
                                                    data-title="Edit Island">Edit</a>

                                                <button type="button" class="btn btn-sm btn-outline-danger delete-island"
                                                    data-id="{{ $island->id }}"
                                                    data-url="{{ route('islands.destroy', $island->id) }}">
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">No islands found.</td>
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

    <form id="deleteIslandForm" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('scripts')
    <script>
        function loadIslandsTable() {
            $.ajax({
                url: '{{ route('islands.index') }}',
                type: 'GET',
                dataType: 'html',
                success: function(response) {
                    const newRows = $(response).find('#islands-table-rows').html();
                    $('#islands-table-rows').html(newRows);
                },
                error: function() {
                    toastr.error('Failed to reload table data.');
                }
            });
        }

        // Save (Create/Update)
        $(document).on('submit', '#islandForm', function(e) {
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
                        toastr.success(res.message || 'Island saved successfully');
                        loadIslandsTable();
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

        let deleteIslandId = null;

        $(document).on('click', '.delete-island', function() {
            deleteIslandId = $(this).data('id');
            $('#confirmDeleteMessage').html('<p>Are you sure you want to delete this island?</p>');
            $('#confirmDeleteModal').modal('show');
        });

        $('#confirmDeleteBtn').on('click', function() {
            if (deleteIslandId) {
                $.ajax({
                    url: '/islands/' + deleteIslandId,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        if (res.success) {
                            $('#confirmDeleteModal').modal('hide');
                            loadIslandsTable();
                            toastr.success(res.message || 'Island deleted successfully.');
                        } else {
                            toastr.error(res.message || 'Something went wrong.');
                        }
                    },
                    error: function() {
                        $('#confirmDeleteModal').modal('hide');
                        toastr.error('Failed to delete island.');
                    }
                });
            }
        });
    </script>
@endpush
