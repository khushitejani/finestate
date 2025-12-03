{{-- @extends('layouts.app')
@section('title', 'Policy')

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h3>{{ isset($policy) ? 'Edit Policy' : 'Add Policy' }}</h3>

                        <form method="POST" id="policyForm"
                            action="{{ isset($policy) ? route('policy.update', $policy->id) : route('policy.store') }}">
                            @csrf
                            @if (isset($policy))
                                @method('PUT')
                            @endif

                            <div class="form-group mb-3">
                                <label>Policy Title</label>
                                <input type="text" name="title" class="form-control"
                                    value="{{ $policy->title ?? old('title') }}" required>
                            </div>

                            <div class="form-group mb-3">
                                <label>Policy Content</label>
                                <textarea name="content" id="editor">{!! $policy->content ?? old('content') !!}</textarea>
                            </div>

                            <button class="btn btn-primary">Save</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <!-- TinyMCE Script -->
    <script src="https://cdn.tiny.cloud/1/al60krrjgs9dm4zbrxwbxcaj5ri12ru6itcb19ded8dofw6n/tinymce/8/tinymce.min.js" referrerpolicy="origin"></script>


    <script>
        tinymce.init({
            selector: '#editor',
            height: 400,
            plugins: [
                'anchor autolink charmap codesample emoticons link lists media searchreplace table visualblocks wordcount',
                'checklist mediaembed casechange formatpainter pageembed a11ychecker tinymcespellchecker permanentpen',
                'powerpaste advtable advcode advtemplate ai uploadcare mentions tinycomments tableofcontents footnotes',
                'mergetags autocorrect typography inlinecss markdown importword exportword exportpdf'
            ],
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | ' +
                'link media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography uploadcare | ' +
                'align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',

            tinycomments_mode: 'embedded',
            tinycomments_author: 'Admin',

            powerpaste_allow_local_images: true,
            paste_data_images: true,
            powerpaste_word_import: 'clean',
            powerpaste_html_import: 'clean',

            content_style: "body { font-family: Arial; font-size: 14px; }"
        });
    </script>
@endpush --}}

@extends('layouts.app')

@section('title', 'Policy')

@push('styles')
    <x-head.tinymce-config />
@endpush

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h3>{{ isset($policy) ? 'Edit Policy' : 'Add Policy' }}</h3>

                        <form method="POST"
                            action="{{ $policy && $policy->id ? route('policy.update', $policy->id) : route('policy.store') }}">
                            @csrf
                            @if ($policy && $policy->id)
                                @method('PUT')
                            @endif

                            <div class="form-group mb-3">
                                <label>Policy Title</label>
                                <input type="text" name="title" class="form-control"
                                    value="{{ $policy->title ?? old('title') }}" required>
                            </div>

                            <div class="form-group mb-3 w-100">
                                <label>Policy Content</label>
                                <x-forms.tinymce-editor name="content" class="">
                                    {!! $policy->content ?? old('content') !!}
                                </x-forms.tinymce-editor>
                            </div>

                            <button class="btn btn-primary">Save</button>
                        </form>

                        <form action="{{ route('policy.uploadPdf') }}" method="POST" enctype="multipart/form-data"
                            class="mt-3">
                            @csrf
                            <div class="input-group">
                                <input type="file" name="pdf_file" accept="application/pdf" class="form-control"
                                    required>
                                <button type="submit" class="btn btn-primary">Upload & Convert</button>
                            </div>
                        </form>


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


{{-- <script src="https://cdn.ckeditor.com/ckeditor5/41.2.0/super-build/ckeditor.js"></script> --}
@endpush
{{-- @push('styles')
    <link rel="stylesheet" href="{{ asset('cke/style.css') }}">
@endpush

@push('scripts')
    <script type="module" src="{{ asset('cke/main.js') }}"></script>
@endpush --}}
{{-- <script>
    $(document).ready(function() {
        $('#policyForm').submit(function(e) {
            e.preventDefault();

            // Update the textarea with CKEditor content
            $('textarea[name="content"]').val(editor.getData());

            let form = $(this);
            let url = form.attr('action');
            let method = form.find('input[name="_method"]').val() || 'POST';
            let formData = new FormData(this);

            // CSRF token
            formData.append('_token', '{{ csrf_token() }}');

            $.ajax({
                url: url,
                type: method,
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: response.message,
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                },
                error: function(xhr) {
                    let message = 'Something went wrong!';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: message,
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true
                    });
                }
            });
        });

    });
</script> --}}
