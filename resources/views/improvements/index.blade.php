@extends('layouts.app')
@section('title', 'Shares')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-sm-flex align-items-center mb-4">
                            <h4 class="card-title mb-sm-0">Improvements Inventory</h4>

                            <div class="ms-auto d-flex align-items-center">
                                <a href="{{ route('improvements.create') }}"
                                    class="btn btn-primary btn-sm me-3 open-card-modal" data-title="Create New Improvement">
                                    <i class="icon-plus"></i> Create New Improvement
                                </a>

                                <a class="btn btn-success btn-sm open-import-modal text-end"
                                    data-title="Improvements Bulk Import" data-form-url="{{ route('bulk.import.form') }}"
                                    data-form-submit="{{ route('improvements.bulk.import') }}"
                                    data-form-demo-download="{{ route('improvements.demo.download') }}">
                                    <i class="bi bi-cloud-arrow-up me-1"></i> Open Bulk Import
                                </a>
                            </div>
                        </div>

                        <div class="table-responsive border rounded p-1">
                            <table class="table align-middle">
                                <thead>
                                    <tr>0
                                        <th>Image</th>
                                        <th>No</th>
                                        <th>Name</th>
                                        <th>Price(%)</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($improvements as $improvement)
                                        <tr id="card-row-{{ $improvement->id }}">
                                            <td>
                                                @php
                                                    $imagePath = $improvement->image
                                                        ? getImageUrl($improvement->image)
                                                        : asset('default.jpeg');

                                                    if (
                                                        $improvement->image &&
                                                        !Storage::disk('public')->exists($improvement->image)
                                                    ) {
                                                        $imagePath = asset('default.jpeg');
                                                    }
                                                @endphp

                                                <img src="{{ $imagePath }}" alt="{{ $improvement->name }}"
                                                    class="rounded"
                                                    style="width: 50px; height: 50px; object-fit: contain; border: 1px solid #ddd; background-color: #fff; padding: 2px;">
                                            </td>
                                            <td>{{ $improvement->no ?? 'N/A' }}</td>
                                            <td>{{ $improvement->name }}</td>
                                            <td>{{ number_format($improvement->price) }}%</td>
                                            <td class="text-center">
                                                <a href="{{ route('improvements.edit', $improvement->id) }}"
                                                    class="btn btn-sm btn-outline-primary open-card-modal"
                                                    data-title="Edit Improvement">Edit</a>
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-share"
                                                    data-url="{{ route('improvements.destroy', $improvement->id) }}"
                                                    data-id="{{ $improvement->id }}">
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">No improvements found.
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
    <form id="deleteshareForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection
@push('scripts')
    <script>
        function loadImprovementsTable() {
            $.get("{{ route('improvements.index') }}", function(res) {
                if (res.success) {
                    let rows = '';

                    if (res.improvements.length > 0) {
                        res.improvements.forEach(function(improvement) {
                            rows += `
                        <tr id="card-row-${improvement.id}">
                            <td>
                                ${improvement.image_url ? `<img src="${improvement.image_url}" alt="image"
                                      class="rounded" style="width: 50px; height: 50px; object-fit: contain; border: 1px solid #ddd; background-color: #fff; padding: 2px;">`
                                    : '<span class="text-muted">No image</span>'}
                            </td>
                            <td>${improvement.no ?? 'N/A'}</td>
                            <td>${improvement.name}</td>
                            <td>${Number(improvement.price)}%</td>
                            <td class="text-center">
                                <a href="/improvements/${improvement.id}/edit"
                                    class="btn btn-sm btn-outline-primary open-card-modal"
                                    data-title="Edit Improvement">Edit</a>
                                <button type="button"
                                    class="btn btn-sm btn-outline-danger delete-share"
                                    data-url="/improvements/${improvement.id}"
                                    data-id="${improvement.id}">
                                    Delete
                                </button>
                            </td>
                        </tr>`;
                        });
                    } else {
                        rows =
                            `<tr><td colspan="5" class="text-center text-muted py-4">No improvements found.</td></tr>`;
                    }

                    $('table.table tbody').html(rows);
                } else {
                    toastr.error('Failed to load improvements.');
                }
            }).fail(function() {
                toastr.error('Error loading improvements.');
            });
        }

        $(document).on('submit', '#improvementsForm', function(e) {
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
                        toastr.success(res.message || 'Improvements saved successfully');
                        loadImprovementsTable();
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

        $(document).ready(function() {
            $(document).on('click', '.delete-share', function() {
                deleteCardId = $(this).data('id');
                $('#confirmDeleteMessage').html('<p>Are you sure you want to delete this share ?</p>');
                $('#confirmDeleteModal').modal('show');
            });

            $('#confirmDeleteBtn').on('click', function() {
                if (deleteCardId) {
                    $.ajax({
                        url: '/improvements/' + deleteCardId,
                        type: 'POST',
                        data: {
                            _method: 'DELETE',
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(res) {
                            if (res.ok) {
                                $('#confirmDeleteModal').modal('hide');

                                // Optionally remove the card from DOM
                                $('#card-row-' + deleteCardId).remove();

                                toastr.success(res.message || 'Card deleted successfully.');
                            } else {
                                toastr.error(res.message || 'Something went wrong.');
                            }
                        },
                        error: function(xhr) {
                            $('#confirmDeleteModal').modal('hide');
                            toastr.error('Failed to delete card.');
                        }
                    });
                }
            });
        });
    </script>
@endpush
