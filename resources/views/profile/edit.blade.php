@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">{{ ucfirst(str_replace('-', ' ', last(Request::segments()) ?: 'Dashboard')) }}</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    @foreach (Request::segments() as $i => $segment)
                        @php $url = url(implode('/', array_slice(Request::segments(), 0, $i+1))); @endphp
                        @if ($i + 1 < count(Request::segments()))
                            <li class="breadcrumb-item"><a
                                    href="{{ $url }}">{{ ucfirst(str_replace('-', ' ', $segment)) }}</a></li>
                        @else
                            <li class="breadcrumb-item active" aria-current="page">
                                {{ ucfirst(str_replace('-', ' ', $segment)) }}</li>
                        @endif
                    @endforeach
                </ol>
            </nav>
        </div>
        <div class="row">
            <div class="col-md-4 grid-margin">
                <div class="card">
                    <img src="{{ getImageUrl($user->avatar) }}" id="navbar-avatar" class="card-img-top object-fit-cover"
                        alt="Admin Image" style="{{ 'height: 356px;' }}">
                    <div class="card-body text-center">

                        <h4 class="card-title mb-1"><span class="badge bg-primary mb-1">Admin</span></h4>
                        <p class="card-text text-muted mb-1" id="card-email"> {{ $user->email ?? 'N/A' }}</p>
                        <ul class="list-group list-group-flush text-start">
                            <li class="list-group-item" id="card-name"><strong>Name:</strong> {{ $user->name ?? 'N/A' }}
                            </li>
                            <li class="list-group-item" id="card-phone"><strong>Phone:</strong> {{ $user->phone ?? 'N/A' }}
                            </li>
                            <li class="list-group-item"><strong>Joined:</strong> {{ $user->created_at->format('M j, Y') }}
                            </li>
                        </ul>
                        <div class="mt-1">
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger btn-sm">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" id="profile-update-form" action="{{ route('profile.update') }}"
                            class="mt-6 space-y-6" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="text-center mb-4">
                                <img id="preview-image" src="{{ getImageUrl($user->avatar) }}"
                                    class="rounded-circle profile-dotted-border" width="120" height="120"
                                    alt="Profile Image" style="cursor: pointer;"
                                    onclick="document.getElementById('avatar-input').click();">

                                <div class="mt-2">
                                    <input type="file" name="avatar" id="avatar-input" class="d-none" accept="image/*"
                                        onchange="previewImage(event)">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="name">Name</label>
                                    <input type="text" id="name" name="name" placeholder="Name"
                                        class="form-control" value="{{ $user->name }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="Email">Email</label>
                                    <input type="email" id="Email" name="email" placeholder="example@gmail.com"
                                        class="form-control" value="{{ $user->email }}">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="Phone">Phone</label>
                                    <input type="text" name="phone" placeholder="11223334455"
                                        value="{{ old('phone', $user->phone) }}" class="form-control" />
                                </div>
                                <div class="col-md-6 d-flex align-items-end justify-content-end">
                                    <button type="submit" class="btn btn-primary btn-lg w-100">Update Profile</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card mt-4">
                    <div class="card-body">
                        <form method="post" id="change-password-form" action="{{ route('password.update') }}"
                            class="mt-6 space-y-6">
                            @csrf
                            @method('PUT')
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="update_password_current_password">Current Password</label>
                                    <input type="password" name="current_password" id="update_password_current_password"
                                        class="form-control" placeholder="********" autocomplete="current-password">
                                    <small id="error_current_password" class="text-danger"></small>
                                </div>

                                <div class="col-md-6">
                                    <label for="update_password_password">New Password</label>
                                    <input type="password" name="new_password" id="update_password_password"
                                        class="form-control" placeholder="********" autocomplete="new-password">
                                    <small id="error_new_password" class="text-danger"></small>
                                </div>
                            </div>

                            <div class="row mb-3 align-items-end">
                                <div class="col-md-6">
                                    <label for="update_password_password_confirmation">Confirm Password</label>
                                    <input type="password" name="new_password_confirmation"
                                        id="update_password_password_confirmation" class="form-control"
                                        placeholder="********" autocomplete="new-password">
                                    <small id="error_new_password_confirmation" class="text-danger"></small>
                                </div>

                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        Update Profile
                                    </button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('styles')
    <style>
        .profile-dotted-border {
            border: 4px double #1BDBE0;
            padding: 3px;
        }
    </style>
@endpush
@push('scripts')
    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview-image').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        }

        $(document).on('submit', '#profile-update-form', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status === true) {
                        $('#navbar-name').text(response.user.name);
                        $('#navbar-email').text(response.user.email);
                        $('#navbar-avatar').attr('src', response.avatar_url);
                        $('.navbar-avatar').attr('src', response.avatar_url);
                        $('#preview-image').attr('src', response.avatar_url);
                        $('#card-name').html('<strong>Name:</strong> ' + response.user.name);
                        $('#card-email').text(response.user.email);
                        $('#card-phone').html('<strong>Phone:</strong> ' + response.user.phone);
                        toastr.success(response.message);

                    }
                },
                error: function(xhr) {
                    toastr.error(response.message);
                }
            });
        });

        $(document).on('submit', '#change-password-form', function(e) {
            e.preventDefault();
            var $form = $(this);
            var formData = $form.serialize();
            $form.find('small.text-danger').text('');
            $form.find('.is-invalid').removeClass('is-invalid');
            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                data: formData,
                dataType: 'json',
                headers: {
                    'Accept': 'application/json'
                },
                success: function(res) {
                    if (res && res.status === true) {
                        toastr.success(res.message);
                    } else {
                        toastr.error(res && res.message ? res.message : 'Something went wrong.');
                    }
                },
                error: function(xhr) {
                    var res = xhr.responseJSON || {};
                    if (xhr.status === 422) {
                        if (res.errors) {
                            Object.keys(res.errors).forEach(function(field) {
                                var msgs = res.errors[field];
                                var $input = $form.find('[name="' + field + '"]');
                                $input.addClass('is-invalid');
                                $('#error_' + field).text(msgs[0]);
                            });
                        }
                        if (res.message && !res.errors) {
                            toastr.error(res.message);
                            $('#error_current_password').text(res.message);
                            $form.find('[name="current_password"]').addClass('is-invalid');
                        }
                    } else {
                        toastr.error(res.message || 'Unexpected error. Please try again.');
                    }
                }
            });
        });
    </script>
@endpush
