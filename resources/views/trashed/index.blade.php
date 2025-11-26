@extends('layouts.app')
@section('title', 'Recently Deleted Items')

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-sm-flex align-items-center mb-4">
                            <h4 class="card-title mb-sm-0">Recently Deleted</h4>
                        </div>
                        <form method="GET" class="mb-4">
                            <label for="table">Select Deleted Table</label>
                            <select name="table" id="table" class="form-control">
                                @foreach ($trashedData as $table => $items)
                                    <option value="{{ $table }}">{{ ucfirst(str_replace('_', ' ', $table)) }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                        @foreach ($trashedData as $table => $items)
                            <div class="table-section" id="section-{{ $table }}" style="display:none;">
                                <h5 class="mt-4">{{ ucfirst(str_replace('_', ' ', $table)) }}</h5>
                                @if ($items->isNotEmpty())
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                @foreach ($items->first()->getAttributes() as $col => $val)
                                                    <th>{{ ucfirst(str_replace('_', ' ', $col)) }}</th>
                                                @endforeach
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($items as $item)
                                                <tr>
                                                    @foreach ($item->getAttributes() as $val)
                                                        <td>{{ $val }}</td>
                                                    @endforeach
                                                    <td>
                                                        <button class="btn btn-success btn-sm restore-btn"
                                                            data-table="{{ $table }}" data-id="{{ $item->id }}">
                                                            Restore
                                                        </button>

                                                        <button class="btn btn-danger btn-sm delete-btn"
                                                            data-table="{{ $table }}" data-id="{{ $item->id }}">
                                                            Delete Permanently
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <p class="text-muted">No deleted records found in {{ ucfirst($table) }}.</p>
                                @endif

                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const dropdown = document.getElementById("table");
            const sections = document.querySelectorAll(".table-section");

            function showSelected() {
                const selected = dropdown.value;
                sections.forEach(sec => sec.style.display = "none");
                const section = document.getElementById("section-" + selected);
                if (section) section.style.display = "block";
            }
            dropdown.addEventListener("change", showSelected);
            showSelected();
            $(document).on("click", ".restore-btn", function(e) {
                e.preventDefault();                                                                                                                                    
                let table = $(this).data("table");
                let id = $(this).data("id");
                let row = $(this).closest("tr");
                $.ajax({
                    url: "/trashed/restore/" + table + "/" + id,
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        row.fadeOut(400, () => row.remove());
                        toastr.success("Record Restored Successfully!");
                    }
                });
            });

            $(document).on("click", ".delete-btn", function(e) {
                e.preventDefault();
                let table = $(this).data("table");
                let id = $(this).data("id");
                let row = $(this).closest("tr");
                $.ajax({
                    url: "/trashed/force-delete/" + table + "/" + id,
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        row.fadeOut(400, () => row.remove());
                        toastr.success("Record Deleted Permanently!");
                    }
                });
            });
        });
    </script>
@endpush
