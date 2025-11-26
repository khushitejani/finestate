@extends('layouts.app')
@section('title', 'Yachts')

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-sm-flex align-items-center mb-4">
                            <h4 class="card-title mb-sm-0">Yacht Inventory</h4>
                            <div class="ms-auto d-flex align-items-center">
                                <a href="{{ route('unique_items.create') }}"
                                    class="btn btn-primary btn-sm me-3 open-card-modal" data-title="Create New Unique Item">
                                    <i class="icon-plus"></i> Create New Unique Item
                                </a>

                                <a class="btn btn-success btn-sm open-import-modal text-end"
                                    data-title="Unique Items Bulk Import" data-form-url="{{ route('bulk.import.form') }}"
                                    data-form-submit="{{ route('unique_items.bulk.import') }}"
                                    data-form-demo-download="{{ route('unique_items.demo.download') }}">
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
                                <tbody id="unique-item-table-rows">
                                    @forelse($unique_items as $unique_item)
                                        <tr>
                                            <td>
                                                @php
                                                    $imagePath = $unique_item->image;
                                                    $defaultImage = asset('default.jpeg');

                                                    $imageUrl =
                                                        $imagePath &&
                                                        \Illuminate\Support\Facades\Storage::disk('public')->exists(
                                                            $imagePath,
                                                        )
                                                            ? asset('storage/' . $imagePath)
                                                            : $defaultImage;
                                                @endphp

                                                <img src="{{ $imageUrl }}"
                                                    alt="{{ $unique_item->name ?? 'Unique Item' }}" class="rounded"
                                                    style="width:50px; height:50px; object-fit:contain; border:1px solid #ddd; background:#fff; padding:2px;">
                                            </td>
                                            <td>{{ $unique_item->no ?? 'N/A'}}</td>
                                            <td>{{ $unique_item->name }}</td>
                                            <td>{{ $unique_item->years ?? '—' }}</td>
                                            <td>{{ number_format($unique_item->price, 2) }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('unique_items.edit', $unique_item->id) }}"
                                                    class="btn btn-sm btn-outline-primary open-card-modal"
                                                    data-title="Edit Unique Item">Edit</a>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-danger delete-unique-item"
                                                    data-id="{{ $unique_item->id }}">Delete</button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">No yachts found.</td>
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

    {{-- Delete form --}}
    <form id="deleteYatchForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('scripts')
    <script>
        // Submit Yacht form via AJAX
        $(document).on('submit', '#uniqueItemForm', function(e) {
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
                        $('#commonmodal').modal('hide'); // close modal
                        toastr.success(res.message || 'Unique Item saved successfully');
                        loadUniqueItemTable(); // reload table
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

        // Reload Yatch table
        function loadUniqueItemTable() {
            $.ajax({
                url: '{{ route('unique_items.index') }}',
                type: 'GET',
                dataType: 'html',
                success: function(response) {
                    const rows = $(response).find('#unique-item-table-rows').html();
                    $('#unique-item-table-rows').html(rows);
                },
                error: function() {
                    toastr.error('Failed to reload table data.');
                }
            });
        }

        let deleteUniqueItemId = null;

        $(document).on('click', '.delete-unique-item', function() {
            deleteUniqueItemId = $(this).data('id'); // assign to correct variable
            $('#confirmDeleteMessage').html('<p>Are you sure you want to delete this Unique Item?</p>');
            $('#confirmDeleteModal').modal('show');
        });

        $('#confirmDeleteBtn').on('click', function() {
            if (!deleteUniqueItemId) return;

            $.ajax({
                url: '/unique_items/' + deleteUniqueItemId,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    $('#confirmDeleteModal').modal('hide');
                    if (res.success) {
                        toastr.success(res.message);
                        loadUniqueItemTable();
                        deleteUniqueItemId = null; 
                    } else {
                        toastr.error(res.message);
                    }
                },
                error: function() {
                    $('#confirmDeleteModal').modal('hide');
                    toastr.error('Failed to delete Unique Item.');
                }
            });
        });
    </script>
@endpush
