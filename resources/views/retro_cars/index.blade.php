@extends('layouts.app')
@section('title', 'Retro Cars')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-sm-flex align-items-center mb-4">
                            <h4 class="card-title mb-sm-0">Products Inventory</h4>
                            <div class="ms-auto d-flex align-items-center gap-2">
                                <a href="{{ route('retro_cars.create') }}" class="btn btn-primary btn-sm ms-3 open-card-modal"
                                    data-title="Create New Retro Car">
                                    <i class="icon-plus"></i> Create New Retro Car
                                </a>
                                <a class="btn btn-success btn-sm open-import-modal text-end"
                                    data-title="Retro Cars Bulk Import" data-form-url="{{ route('bulk.import.form') }}"
                                    data-form-submit="{{ route('retro_cars.bulk.import') }}"
                                    data-form-demo-download="{{ route('retro_cars.demo.download') }}">
                                    <i class="bi bi-cloud-arrow-up me-1"></i> Bulk Import
                                </a>
                            </div>
                        </div>
                        <div class="table-responsive border rounded p-1">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th class="font-weight-bold" style="width:90px;">Image</th>
                                        <th class="font-weight-bold">Name</th>
                                        <th class="font-weight-bold">Years</th>
                                        <th class="font-weight-bold">Price</th>
                                        <th class="font-weight-bold text-end" style="width:220px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="retro-cars-table-rows">
                                    @forelse($retro_cars as $car)
                                        <tr id="car-row-{{ $car->id }}">
                                            <td>
                                                @php
                                                    $imagePath = $car->image;
                                                    $defaultImage = asset('default.jpeg');
                                                    $imageUrl =
                                                        $imagePath &&
                                                        \Illuminate\Support\Facades\Storage::disk('public')->exists(
                                                            $imagePath,
                                                        )
                                                            ? asset('storage/' . $imagePath)
                                                            : $defaultImage;
                                                @endphp
                                                <img src="{{ $imageUrl }}" alt="{{ $car->name }}" class="rounded"
                                                    style="width:50px; height:50px; object-fit:contain; border:1px solid #ddd; background:#fff; padding:2px;">
                                            </td>
                                            <td>{{ $car->name }}</td>
                                            <td>{{ $car->years }}</td>
                                            <td>{{ number_format($car->price, 2) }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('retro_cars.edit', $car->id) }}"
                                                    class="btn btn-sm btn-outline-primary open-card-modal"
                                                    data-title="Edit Retro Car">Edit</a>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-danger btn-delete delete-retro-car"
                                                    data-url="{{ route('retro_cars.destroy', $car->id) }}"
                                                    data-id="{{ $car->id }}">
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">No retro cars found.</td>
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
        $(document).on('submit', '#retroCarForm', function(e) {
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
                        toastr.success(res.message || 'share saved successfully');
                        loadRetroCarsTable();
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

        function loadRetroCarsTable() {
            $.ajax({
                url: '{{ route('retro_cars.index') }}',
                type: 'GET',
                dataType: 'html',
                success: function(response) {
                    const rows = $(response).find('#retro-cars-table-rows').html();
                    $('#retro-cars-table-rows').html(rows);
                },
                error: function() {
                    toastr.error('Failed to reload retro cars table.');
                }
            });
        }

        let deleteCardId = null;

        $(document).ready(function() {
            $(document).on('click', '.delete-retro-car', function() {
                deleteCardId = $(this).data('id');
                $('#confirmDeleteMessage').html('<p>Are you sure you want to delete this Retro Car ?</p>');
                $('#confirmDeleteModal').modal('show');
            });

            $('#confirmDeleteBtn').on('click', function() {
                if (deleteCardId) {
                    let url = $('.delete-retro-car[data-id="' + deleteCardId + '"]').data('url');

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            _method: 'DELETE',
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(res) {
                            $('#confirmDeleteModal').modal('hide');

                            // if you return JSON { success: true }
                            if (res.success) {
                                $('#car-row-' + deleteCardId).remove();
                                toastr.success(res.message ||
                                    'Retro Car deleted successfully.');
                            } else {
                                toastr.success('Retro Car deleted successfully.');
                                $('#car-row-' + deleteCardId).remove();
                            }
                        },
                        error: function(xhr) {
                            $('#confirmDeleteModal').modal('hide');
                            toastr.error('Failed to delete retro car.');
                        }
                    });
                }
            });

        });
    </script>
@endpush
