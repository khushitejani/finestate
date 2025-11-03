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
                                <a href="{{ route('cards.create') }}" class="btn btn-primary btn-sm open-card-modal"
                                    data-title="Create New Card">
                                    <i class="icon-plus"></i> Create New Card
                                </a>
                                <a class="btn btn-success btn-sm open-import-modal" data-title="Cards Bulk Import"
                                    data-form-url="{{ route('bulk.import.form') }}"
                                    data-form-submit="{{ route('cards.bulk.import') }}"
                                    data-form-demo-download="{{ route('cards.demo.download') }}">
                                    <i class="bi bi-cloud-arrow-up me-1"></i> Bulk Import
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
                                <tbody id="cards-table-rows">
                                    @forelse($cards as $card)
                                        <tr id="card-row-{{ $card->id }}">
                                            <td>
                                                @php
                                                    $imageUrl = $card->image
                                                        ? getImageUrl($card->image)
                                                        : asset('default.jpeg');

                                                    if (
                                                        $card->image &&
                                                        !Storage::disk('public')->exists($card->image)
                                                    ) {
                                                        $imageUrl = asset('default.jpeg');
                                                    }
                                                @endphp

                                                <img src="{{ $imageUrl }}" alt="{{ $card->name }}" class="rounded"
                                                    style="width:70px;height:40px; object-fit: contain; border: 1px solid #ddd; background-color: #fff; padding: 2px;">
                                            </td>
                                            <td>{{ $card->name }}</td>
                                            <td>{{ $card->sign_price }} {{ number_format($card->price, 2) }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('cards.edit', $card->id) }}"
                                                    class="btn btn-sm btn-outline-primary open-card-modal"
                                                    data-title="Edit Card - {{ $card->name }}">Edit</a>
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-card"
                                                    data-id="{{ $card->id }}"
                                                    data-url="{{ route('cards.destroy', $card->id) }}">Delete</button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">No cards found.</td>
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

    <form id="deleteCardForm" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('scripts')
    <script>
        function loadCardsTable() {
            $.ajax({
                url: '{{ route('cards.index') }}',
                type: 'GET',
                dataType: 'html',
                success: function(response) {
                    const newRows = $(response).find('#cards-table-rows').html();
                    $('#cards-table-rows').html(newRows);
                },
                error: function() {
                    toastr.error('Failed to reload cards table.');
                }
            });
        }

        // Save (Create/Update)
        $(document).on('submit', '#cardForm', function(e) {
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
                        toastr.success(res.message || 'Card saved successfully');
                        loadCardsTable();
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

        // Delete card
        let deleteCardId = null;
        $(document).on('click', '.delete-card', function() {
            deleteCardId = $(this).data('id');
            $('#confirmDeleteMessage').html('<p>Are you sure you want to delete this card?</p>');
            $('#confirmDeleteModal').modal('show');
        });

        $('#confirmDeleteBtn').on('click', function() {
            if (deleteCardId) {
                $.ajax({
                    url: '/cards/' + deleteCardId,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        if (res.success) {
                            $('#confirmDeleteModal').modal('hide');
                            loadCardsTable();
                            toastr.success(res.message || 'Card deleted successfully.');
                        } else {
                            toastr.error(res.message || 'Something went wrong.');
                        }
                    },
                    error: function() {
                        $('#confirmDeleteModal').modal('hide');
                        toastr.error('Failed to delete card.');
                    }
                });
            }
        });
    </script>
@endpush
