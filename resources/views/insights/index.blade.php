@extends('layouts.app')
@section('title', 'Insights')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-sm-flex align-items-center mb-4">
                            <h4 class="card-title mb-sm-0">Insights Inventory</h4>
                            <div class="ms-auto d-flex align-items-center">
                                <a href="{{ route('insights.create') }}" class="btn btn-primary btn-sm me-3 open-card-modal"
                                    data-title="Create New Insight">
                                    <i class="icon-plus"></i> Create New Insight
                                </a>
                                <a class="btn btn-success btn-sm open-import-modal text-end"
                                    data-title="Insights Bulk Import" data-form-url="{{ route('bulk.import.form') }}"
                                    data-form-submit="{{ route('insights.bulk.import') }}"
                                    data-form-demo-download="{{ route('insights.demo.download') }}">
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
                                        <th class="font-weight-bold">Years</th>
                                        <th>Conditions</th>
                                        <th class="text-end" style="width:220px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="insight-table-rows">
                                    @forelse($insights as $insight)
                                        <tr>
                                            <td>
                                                @php
                                                    $imageUrl =
                                                        $insight->image &&
                                                        \Illuminate\Support\Facades\Storage::disk('public')->exists(
                                                            $insight->image,
                                                        )
                                                            ? asset('storage/' . $insight->image)
                                                            : asset('default.jpeg');
                                                @endphp

                                                <img src="{{ $imageUrl }}" class="rounded"
                                                    style="height:50px; width:50px; object-fit:cover; border:1px solid #ddd; background-color:#fff; padding:2px;">
                                            </td>
                                            <td>{{ $insight->name }}</td>
                                            <td>{{ $insight->years ?? '—' }}</td>
                                            <td>
                                                {{ \Illuminate\Support\Str::limit(strip_tags($insight->conditions), 100, '...') }}
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('insights.edit', $insight->id) }}"
                                                    class="btn btn-sm btn-outline-primary open-card-modal">Edit</a>
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-insight"
                                                    data-id="{{ $insight->id }}">Delete</button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">No Insights Found.</td>
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


    <form id="deleteInsightForm" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('scripts')
    <script>
        let deleteInsightId = null;

        function loadInsightTable() {
            $.ajax({
                url: '{{ route('insights.index') }}',
                type: 'GET',
                dataType: 'html',
                success: function(response) {
                    const rows = $(response).find('#insight-table-rows').html();
                    $('#insight-table-rows').html(rows);
                }
            });
        }

        $(document).on('submit', '#insightForm', function(e) {
            e.preventDefault();
            let form = $(this);
            let url = form.attr('action');
            let method = form.find('input[name="_method"]').val() || 'POST';
            let formData = new FormData(this);
            if (method === 'PUT') formData.append('_method', 'PUT');

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.success) {
                        $('#commonmodal').modal('hide');
                        toastr.success(res.message || 'Saved successfully');
                        loadInsightTable();
                    } else {
                        toastr.error(res.message || 'Something went wrong');
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        Object.keys(errors).forEach(key => toastr.error(errors[key][0]));
                    } else {
                        toastr.error('Unexpected error occurred.');
                    }
                }
            });
        });

        $(document).on('click', '.delete-insight', function() {
            deleteInsightId = $(this).data('id');
            $('#confirmDeleteMessage').html('<p>Are you sure you want to delete this Insight?</p>');
            $('#confirmDeleteModal').modal('show');
        });

        $('#confirmDeleteBtn').on('click', function() {
            if (deleteInsightId) {
                $.ajax({
                    url: '/insights/' + deleteInsightId,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        if (res.success) {
                            $('#confirmDeleteModal').modal('hide');
                            $('button.delete-insight[data-id="' + deleteInsightId + '"]').closest('tr')
                                .remove();
                            toastr.success(res.message || 'Deleted successfully');
                        } else {
                            toastr.error(res.message || 'Something went wrong');
                        }
                    },
                    error: function() {
                        $('#confirmDeleteModal').modal('hide');
                        toastr.error('Failed to delete.');
                    }
                });
            }
        });
    </script>
@endpush
