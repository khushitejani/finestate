@extends('layouts.app')
@section('title', 'Constructions')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-sm-flex align-items-center mb-4">
                            <h4 class="card-title mb-sm-0">Construction Projects</h4>

                            <div class="ms-auto d-flex align-items-center">
                                <a href="{{ route('constructions.create') }}"
                                    class="btn btn-primary btn-sm me-3 open-card-modal" data-title="Create New Construction">
                                    <i class="icon-plus"></i> Create New Construction
                                </a>
                                <a class="btn btn-success btn-sm open-import-modal text-end"
                                    data-title="Constructions Bulk Import" data-form-url="{{ route('bulk.import.form') }}"
                                    data-form-submit="{{ route('constructions.bulk.import') }}"
                                    data-form-demo-download="{{ route('constructions.demo.download') }}">
                                    <i class="bi bi-cloud-arrow-up me-1"></i> Open Bulk Import
                                </a>
                            </div>
                        </div>
                        <style>
                            .table td,
                            .table th {
                                white-space: normal !important;
                                word-break: break-word;
                                max-width: 200px;
                            }

                            .table th {
                                text-align: left;
                            }
                        </style>
                        <div class="table-responsive border rounded p-1">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>No</th>
                                        <th>Name</th>
                                        <th>Metal</th>
                                        <th>Builder/Men</th>
                                        <th>Wood</th>
                                        <th>Concrete</th>
                                        <th>Total cost of construction</th>
                                        <th>Total Profit in ($)</th>
                                        <th>Profit Percentage(%)</th>
                                        <th>Time</th>
                                        <th>Total return after complete construction</th>
                                        <th class="text-end" style="width:180px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="constructions-table-rows">
                                    @forelse($constructions as $construction)
                                        <tr id="construction-row-{{ $construction->id }}">
                                            <td>
                                                @php
                                                    $imagePath = $construction->image
                                                        ? getImageUrl($construction->image)
                                                        : asset('default.jpeg');

                                                    if (
                                                        $construction->image &&
                                                        !Storage::disk('public')->exists($construction->image)
                                                    ) {
                                                        $imagePath = asset('default.jpeg');
                                                    }
                                                @endphp

                                                <img src="{{ $imagePath }}" alt="{{ $construction->name }}"
                                                    class="rounded"
                                                    style="width: 50px; height: 50px; object-fit: contain; border: 1px solid #ddd; background-color: #fff; padding: 2px;">
                                            </td>
                                            <td>{{ $construction->no }}</td>
                                            <td>{{ $construction->name }}</td>
                                            <td>{{ $construction->metal }}</td>
                                            <td>{{ $construction->builder_men }}</td>
                                            <td>{{ $construction->wood }}</td>
                                            <td>{{ $construction->concrete }}</td>
                                            <td>{{ number_format($construction->total_cost_of_construction, 2) }}</td>
                                            <td>{{ number_format($construction->total_profit, 2) }}</td>
                                            <td>{{ number_format($construction->total_percentage, 2) }}%</td>
                                            <td>{{ $construction->time }}</td>
                                            <td>{{ number_format($construction->total_return_after_completion, 2) }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('constructions.edit', $construction->id) }}"
                                                    class="btn btn-sm btn-outline-primary open-card-modal">Edit</a>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-danger delete-construction"
                                                    data-id="{{ $construction->id }}">Delete</button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="14" class="text-center text-muted py-4">No constructions found.
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

    <form id="deleteConstructionForm" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('scripts')
    <script>
        function loadConstructionsTable() {
            $.ajax({
                url: '{{ route('constructions.index') }}',
                type: 'GET',
                dataType: 'html',
                success: function(response) {
                    const newRows = $(response).find('#constructions-table-rows').html();
                    $('#constructions-table-rows').html(newRows);
                },
                error: function() {
                    toastr.error('Failed to reload constructions table.');
                }
            });
        }

        $(document).on('submit', '#constructionForm', function(e) {
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
                        toastr.success(res.message || 'Construction saved successfully');
                        loadConstructionsTable();
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

        let deleteConstructionId = null;
        $(document).on('click', '.delete-construction', function() {
            deleteConstructionId = $(this).data('id');
            $('#confirmDeleteMessage').html('<p>Are you sure you want to delete this construction?</p>');
            $('#confirmDeleteModal').modal('show');
        });

        $('#confirmDeleteBtn').on('click', function() {
            if (deleteConstructionId) {
                $.ajax({
                    url: '/constructions/' + deleteConstructionId,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        if (res.success) {
                            $('#confirmDeleteModal').modal('hide');
                            loadConstructionsTable();
                            toastr.success(res.message || 'Construction deleted successfully.');
                        } else {
                            toastr.error(res.message || 'Something went wrong.');
                        }
                    },
                    error: function() {
                        $('#confirmDeleteModal').modal('hide');
                        toastr.error('Failed to delete construction.');
                    }
                });
            }
        });
    </script>
@endpush
