@extends('layouts.app')
@section('title', 'Shares')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-sm-flex align-items-center mb-4">
                            <h4 class="card-title mb-sm-0">Products Inventory</h4>
                            <div class="ms-auto d-flex align-items-center gap-2">
                                <a href="{{ route('properties.create') }}" class="btn btn-primary btn-sm open-card-modal"
                                    data-title="Create New Property">
                                    <i class="icon-plus"></i> Create New Property
                                </a>
                                <a class="btn btn-success btn-sm open-import-modal text-end"
                                    data-title="Properties Bulk Import" data-form-url="{{ route('bulk.import.form') }}"
                                    data-form-submit="{{ route('properties.bulk.import') }}"
                                    data-form-demo-download="{{ route('properties.demo.download') }}">
                                    <i class="bi bi-cloud-arrow-up me-1"></i> Bulk Import
                                </a>
                            </div>
                        </div>

                        <div class="table-responsive border rounded p-1">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>No</th>
                                        <th>Location</th>
                                        <th>Price</th>
                                        <th>Income Per Hour</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($properties as $property)
                                        <tr id="property-row-{{ $property->id }}">
                                            <td>
                                                @php
                                                    $images = $property->image
                                                        ? json_decode($property->image, true)
                                                        : [];
                                                    $firstImage = count($images) ? $images[0] : null;
                                                    $imagePath =
                                                        $firstImage &&
                                                        file_exists(public_path('storage/' . $firstImage))
                                                            ? asset('storage/' . $firstImage)
                                                            : asset('default.png');
                                                @endphp

                                                <img src="{{ $imagePath }}" alt="Property image" class="rounded"
                                                    style="width:50px; height:50px; object-fit:cover; border:1px solid #ddd; background-color:#fff; padding:2px;">
                                            </td>
                                            <td>{{ $property->no ?? 'N/A' }}</td>
                                            <td>{{ $property->address }}</td>
                                            <td>${{ number_format($property->price, 2) }}</td>
                                            <td>${{ number_format($property->income_per_hour, 2) }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('properties.edit', $property->id) }}"
                                                    class="btn btn-sm btn-outline-primary open-card-modal"
                                                    data-title="Edit Property">Edit</a>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-danger delete-properties"
                                                    data-url="{{ route('properties.destroy', $property->id) }}"
                                                    data-id="{{ $property->id }}">
                                                    Delete
                                                </button>

                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">No properties found.</td>
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
        function initPropertyImagePreview(container) {
            const form = container.querySelector('#propertyForm');
            if (!form || form.dataset.previewInit === "true") return;
            form.dataset.previewInit = "true";

            const input = form.querySelector('#propertyImages');
            const preview = form.querySelector('#imagePreview');
            if (!input || !preview) return;

            let filesArray = [];

            function updatePreview() {
                preview.innerHTML = '';
                filesArray.forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const wrapper = document.createElement('div');
                        wrapper.classList.add('position-relative', 'me-2', 'mb-2');
                        wrapper.style.width = '100px';
                        wrapper.style.height = '80px';
                        wrapper.dataset.index = index;
                        wrapper.innerHTML = `
                    <img src="${e.target.result}" class="rounded border" style="width:100%; height:100%; object-fit:cover;">
                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 remove-image">×</button>
                `;
                        preview.appendChild(wrapper);
                    };
                    reader.readAsDataURL(file);
                });
            }

            input.addEventListener('change', function() {
                const newFiles = Array.from(this.files);
                newFiles.forEach(file => {
                    if (!filesArray.some(f => f.name === file.name && f.size === file.size)) {
                        filesArray.push(file);
                    }
                });
                updatePreview();
                this.value = '';
            });

            preview.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-image')) {
                    const wrapper = e.target.closest('div.position-relative');
                    const index = parseInt(wrapper.dataset.index);
                    if (!isNaN(index)) {
                        filesArray.splice(index, 1);
                        updatePreview();
                    }
                }
            });

            form.addEventListener('submit', function(e) {
                const dataTransfer = new DataTransfer();
                filesArray.forEach(f => dataTransfer.items.add(f));
                input.files = dataTransfer.files;
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            initPropertyImagePreview(document);
        });

        $(document).on('shown.bs.modal', '#commonmodal', function() {
            const container = this.querySelector('.modal-body');
            initPropertyImagePreview(container);
        });

        let deletePropertyId = null;

        $(document).on('click', '.delete-properties', function() {
            deletePropertyId = $(this).data('id');
            $('#confirmDeleteMessage').html('<p>Are you sure you want to delete this property?</p>');
            $('#confirmDeleteModal').modal('show');
        });

        $('#confirmDeleteBtn').on('click', function() {
            if (deletePropertyId) {
                $.ajax({
                    url: '/properties/' + deletePropertyId,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        if (res.success) {
                            $('#confirmDeleteModal').modal('hide');
                            $('#property-row-' + deletePropertyId).remove();
                            toastr.success(res.message || 'Property deleted successfully.');
                        } else {
                            toastr.error(res.message || 'Something went wrong.');
                        }
                    },
                    error: function() {
                        $('#confirmDeleteModal').modal('hide');
                        toastr.error('Failed to delete property.');
                    }
                });
            }
        });
    </script>
@endpush
