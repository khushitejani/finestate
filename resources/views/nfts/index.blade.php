@extends('layouts.app')
@section('title', 'NFTs')
@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-sm-flex align-items-center mb-4">
                        <h4 class="card-title mb-sm-0">NFTs Inventory</h4>
                        <a href="{{ route('nfts.create') }}" class="btn btn-primary btn-sm ms-3 open-card-modal"
                           data-title="Create New NFT">
                            <i class="icon-plus"></i> Create New NFT
                        </a>
                    </div>

                    <div class="table-responsive border rounded p-1">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th style="width:90px;">Image</th>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Price (ETH)</th>
                                    <th>Description</th>
                                    <th class="text-end" style="width:220px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="nfts-table-rows">
                                @forelse($nfts as $nft)
                                    <tr>
                                        <td>
                                            @if ($nft->image)
                                                <img src="{{ asset('storage/' . $nft->image) }}"
                                                     alt="{{ $nft->name }}" class="rounded"
                                                     style="width:70px;height:40px;object-fit:cover;">
                                            @else
                                                <span class="text-muted">No image</span>
                                            @endif
                                        </td>
                                         <td>{{ $nft->no ?? 'N/A' }}</td>
                                        <td>{{ $nft->name }}</td>
                                        <td>{{ number_format($nft->price, 2) }}</td>
                                        <td>{{ $nft->description }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('nfts.edit', $nft->id) }}"
                                               class="btn btn-sm btn-outline-primary open-card-modal"
                                               data-title="Edit NFT">Edit</a>
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-nft"
                                                data-id="{{ $nft->id }}"
                                                data-url="{{ route('nfts.destroy', $nft->id) }}">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">No NFTs found.</td>
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

<form id="deleteNFTForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    function loadNFTsTable() {
        $.ajax({
            url: '{{ route('nfts.index') }}',
            type: 'GET',
            dataType: 'html',
            success: function(response) {
                const newRows = $(response).find('#nfts-table-rows').html();
                $('#nfts-table-rows').html(newRows);
            },
            error: function() {
                toastr.error('Failed to reload table data.');
            }
        });
    }

    // Save (Create/Update)
    $(document).on('submit', '#nftForm', function(e) {
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
                    toastr.success(res.message || 'NFT saved successfully');
                    loadNFTsTable();
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
    let deleteNFTId = null;

    $(document).on('click', '.delete-nft', function() {
        deleteNFTId = $(this).data('id');
        $('#confirmDeleteMessage').html('<p>Are you sure you want to delete this NFT?</p>');
        $('#confirmDeleteModal').modal('show');
    });

    $('#confirmDeleteBtn').on('click', function() {
        if (deleteNFTId) {
            $.ajax({
                url: '/nfts/' + deleteNFTId,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    if (res.success) {
                        $('#confirmDeleteModal').modal('hide');
                        loadNFTsTable();
                        toastr.success(res.message || 'NFT deleted successfully.');
                    } else {
                        toastr.error(res.message || 'Something went wrong.');
                    }
                },
                error: function() {
                    $('#confirmDeleteModal').modal('hide');
                    toastr.error('Failed to delete NFT.');
                }
            });
        }
    });
</script>
@endpush
