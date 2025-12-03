{{-- @extends('layouts.app')
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
@endpush --}}
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
                                                {{-- Use first item to get headers --}}
                                                @foreach ($items->first()->getAttributes() as $col => $val)
                                                    @if (!in_array($col, ['created_at', 'updated_at','Description','years','deleted_at', 'day_prices']))
                                                        <th>{{ ucfirst(str_replace('_', ' ', $col)) }}</th>
                                                    @endif
                                                @endforeach
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($items as $item)
                                                <tr>
                                                    @foreach ($item->getAttributes() as $col => $val)
                                                        @if (!in_array($col, ['created_at', 'updated_at','Description','years','deleted_at', 'day_prices']))
                                                            {{-- <td>
                                                                @if (str_contains($col, 'image') && $val)
                                                                    @php
                                                                        $img = $val;
                                                                        if (
                                                                            is_string($val) &&
                                                                            (str_starts_with($val, '[') ||
                                                                                str_starts_with($val, '{'))
                                                                        ) {
                                                                            $arr = json_decode($val, true);
                                                                            if (is_array($arr) && count($arr) > 0) {
                                                                                $img = $arr[0];
                                                                            }
                                                                        } elseif (is_array($val) && count($val) > 0) {
                                                                            $img = $val[0];
                                                                        }
                                                                    @endphp
                                                                    <img src="{{ asset('storage/' . $img) }}" alt="image"
                                                                        width="60" height="60" class="rounded"
                                                                         style="width: 50px; height: 50px; object-fit: contain; border: 1px solid #ddd; background-color: #fff; padding: 2px;">
                                                                @elseif (str_contains($col, 'description') && $val)
                                                                    {{ \Illuminate\Support\Str::words($val, 20, '...') }}
                                                                @else
                                                                    {{ $val }}
                                                                @endif
                                                            </td> --}}

                                                            {{-- <td>
                                                                @if (str_contains($col, 'image') && $val)
                                                                    @php
                                                                        $images = [];
                                                                        if (
                                                                            is_string($val) &&
                                                                            (str_starts_with($val, '[') ||
                                                                                str_starts_with($val, '{'))
                                                                        ) {
                                                                            $images = json_decode($val, true);
                                                                           
                                                                        } elseif (is_array($val)) {
                                                                            $images = $val;
                                                                        } else {
                                                                            $images = [$val]; // single image string
                                                                        }
                                                                    @endphp
                                                                    @foreach ($images as $img)
                                                                        <img src="{{ asset('storage/' . $img) }}"
                                                                            alt="image" width="60" height="60"
                                                                            style="object-fit:cover; border-radius:4px; margin-right:4px;">
                                                                    @endforeach
                                                                @elseif (str_contains($col, 'description') && $val)
                                                                    {{ \Illuminate\Support\Str::words($val, 20, '...') }}
                                                                @else
                                                                    {{ $val }}
                                                                @endif
                                                            </td> --}}
                                                            <td>
                                                                @if (str_contains($col, 'image') && $val)
                                                                    @php
                                                                        $firstImage = null;

                                                                        // Remove extra quotes
                                                                        $cleanVal = str_replace(
                                                                            ['&quot;', '\"'],
                                                                            '"',
                                                                            $val,
                                                                        );
                                                                        $cleanVal = trim($cleanVal, '"'); // remove leading/trailing quotes

                                                                        // Decode JSON
                                                                        $arr = json_decode($cleanVal, true);

                                                                        if (is_array($arr) && count($arr) > 0) {
                                                                            $firstImage = $arr[0]; // pick only first image
                                                                        } else {
                                                                            $firstImage = $cleanVal; // fallback: use as is
                                                                        }
                                                                    @endphp

                                                                    @if (!empty($firstImage))
                                                                        <img src="{{ asset('storage/' . ltrim($firstImage, '/')) }}"
                                                                            alt="image" width="60" height="60" class="rounded"
                                                                             style="width: 50px; height: 50px; object-fit: contain; border: 1px solid #ddd; background-color: #fff; padding: 2px;">
                                                                    @endif
                                                                @elseif (str_contains($col, 'description') && $val)
                                                                    {{ \Illuminate\Support\Str::words($val, 20, '...') }}
                                                                @else
                                                                    {{ $val }}
                                                                @endif
                                                            </td>
                                                        @endif
                                                    @endforeach
                                                    <td>
                                                        <button class="btn btn-success btn-sm restore-btn"
                                                            data-table="{{ $table }}" data-id="{{ $item->id }}">
                                                            Restore
                                                        </button>

                                                        <button class="btn btn-danger btn-sm delete-btn"
                                                            data-table="{{ $table }}"
                                                            data-id="{{ $item->id }}">
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
