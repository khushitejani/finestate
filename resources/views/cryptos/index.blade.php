    @extends('layouts.app')
    @section('title', 'Cryptocurrencies')
    @section('content')
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-sm-flex align-items-center mb-4">
                                <h4 class="card-title mb-sm-0">Cryptocurrency Inventory</h4>
                                <div class="ms-auto d-flex align-items-center">
                                    <a href="{{ route('cryptos.create') }}"
                                        class="btn btn-primary btn-sm me-3 open-card-modal" data-title="Create New Crypto">
                                        <i class="icon-plus"></i> Create New Crypto
                                    </a>
                                    <a class="btn btn-success btn-sm open-import-modal"
                                        data-title="Cryptocurrency Bulk Import"
                                        data-form-url="{{ route('bulk.import.form') }}"
                                        data-form-submit="{{ route('cryptos.bulk.import') }}"
                                        data-form-demo-download="{{ route('cryptos.demo.download') }}">
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
                                            <th>Price</th>
                                            <th>Market Cap</th>
                                            <th>Available Amount</th>
                                            <th class="text-end" style="width:220px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="crypto-table-rows">
                                        @forelse($cryptos as $crypto)
                                            <tr>
                                                <td>
                                                    @if ($crypto->image)
                                                        <img src="{{ asset('storage/' . $crypto->image) }}" class="rounded"
                                                            style="height:50px;width:auto;">
                                                    @else
                                                        <span class="text-muted">No image</span>
                                                    @endif
                                                </td>
                                                <td>{{ $crypto->no ?? 'N/A' }}</td>
                                                <td>{{ $crypto->name }}</td>
                                                <td>{{ number_format($crypto->price, 8) }}</td>
                                                <td>{{ number_format($crypto->cryptocurrencies_cap, 2) }}</td>
                                                <td>{{ $crypto->available_for_purchase ?? 0 }}</td>
                                                <td class="text-end">
                                                    <a href="{{ route('cryptos.edit', $crypto->id) }}"
                                                        class="btn btn-sm btn-outline-primary open-card-modal">Edit</a>
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-danger delete-crypto"
                                                        data-id="{{ $crypto->id }}">Delete</button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">No cryptocurrencies
                                                    found.</td>
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
        <form id="deleteCryptoForm" method="POST" style="display:none;">
            @csrf
            @method('DELETE')
        </form>
    @endsection

    @push('scripts')
        <script>
            function loadCryptoTable() {
                $.ajax({
                    url: '{{ route('cryptos.index') }}',
                    type: 'GET',
                    dataType: 'html',
                    success: function(response) {
                        const rows = $(response).find('#crypto-table-rows').html();
                        $('#crypto-table-rows').html(rows);
                    }
                });
            }

            $(document).on('submit', '#cryptoForm', function(e) {
                e.preventDefault();

                let form = $(this);
                let url = form.attr('action'); // will be either store or update route
                let method = form.find('input[name="_method"]').val() || 'POST';
                // if @method('PUT') exists, it's update. Else, it's create.

                let formData = new FormData(this);

                // If updating, ensure _method=PUT is present
                if (method === 'PUT') {
                    formData.append('_method', 'PUT');
                }

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
                            loadCryptoTable(); // refresh table
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
            let deleteCryptoId = null;

            $(document).on('click', '.delete-crypto', function() {
                deleteCryptoId = $(this).data('id');
                $('#confirmDeleteMessage').html('<p>Are you sure you want to delete this cryptocurrency?</p>');
                $('#confirmDeleteModal').modal('show');
            });

            $('#confirmDeleteBtn').on('click', function() {
                if (deleteCryptoId) {
                    $.ajax({
                        url: '/cryptos/' + deleteCryptoId,
                        type: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(res) {
                            if (res.success) {
                                $('#confirmDeleteModal').modal('hide');

                                $('button.delete-crypto[data-id="' + deleteCryptoId + '"]').closest('tr')
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
