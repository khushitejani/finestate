<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Stellar Admin</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="assets/vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="assets/vendors/flag-icon-css/css/flag-icons.min.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="assets/vendors/font-awesome/css/font-awesome.min.css" />
    <link rel="stylesheet" href="assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="assets/vendors/jvectormap/jquery-jvectormap.css">
    <link rel="stylesheet" href="assets/vendors/daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="assets/vendors/chartist/chartist.min.css">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="assets/css/vertical-light-layout/style.css">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="assets/images/favicon.png" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    @stack('styles')
</head>

<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper">
            @include('layouts.navigation')
            @include('layouts.sidebar')
            <div class="main-panel">
                @yield('content')
                {{-- <div id="dynamic-content">
                    @yield('content')
                </div> --}}
                @include('layouts.footer')
            </div>
        </div>
    </div>
    <div class="modal fade" id="commonmodal" tabindex="-1" aria-labelledby="createCardModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-centered ">
            <div class="modal-content bg-white">
                <div class="modal-header">
                    <h5 class="modal-title" id="createCardModalLabel">Create New Card</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="commonmodal-body">
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="confirmDeleteLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body" id="confirmDeleteMessage">
                    <!-- Filled by JS -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Result Modal -->
    <div class="modal fade" id="resultModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content bg-white p-2 text-center" style="border-radius:12px; position: relative;">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-1"
                    data-bs-dismiss="modal"></button>
                <video id="resultVideo" width="100%" autoplay muted loop style="border-radius: 12px;">
                    <source src="" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
        </div>
    </div>


    {{-- <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body" id="confirmDeleteMessage"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger btn-sm" id="confirmDeleteBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="cropModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Adjust Crop</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="imageToCrop" style="max-width:100%; display:block;">
                </div>
                <div class="modal-footer">
                    <button id="cropButton" type="button" class="btn btn-primary">Crop & Use</button>
                </div>
            </div>
        </div>
    </div> --}}


    <script src="assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="assets/vendors/chart.js/chart.umd.js"></script>
    <script src="assets/vendors/jvectormap/jquery-jvectormap.min.js"></script>
    <script src="assets/vendors/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
    <script src="assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <script src="assets/vendors/moment/moment.min.js"></script>
    <script src="assets/vendors/daterangepicker/daterangepicker.js"></script>
    <script src="assets/vendors/chartist/chartist.min.js"></script>
    <script src="assets/vendors/progressbar.js/progressbar.min.js"></script>
    <script src="assets/js/jquery.cookie.js"></script>
    <script src="assets/js/off-canvas.js"></script>
    <script src="assets/js/hoverable-collapse.js"></script>
    <script src="assets/js/misc.js"></script>
    <script src="assets/js/settings.js"></script>
    <script src="assets/js/todolist.js"></script>
    <script src="assets/js/dashboard.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    @stack('scripts')

    {{-- <script>
        (function() {
            const SIDE_MARGIN_X = 4; // px gap left/right
            const SIDE_MARGIN_Y = 2; // px gap top/bottom

            function initCardImageUploader(container) {
                if (!container) return;
                const outputWidth = parseInt(container.dataset.outputWidth || '400');
                const outputHeight = parseInt(container.dataset.outputHeight || '136');
                const aspectRatio = parseFloat(container.dataset.aspectRatio || (outputWidth / outputHeight));

                const box = container.querySelector('.ciu-box');
                const input = container.querySelector('.ciu-input');
                const preview = container.querySelector('.ciu-preview');
                const placeholder = container.querySelector('.ciu-placeholder');
                const controls = container.querySelector('.ciu-controls');
                const zoomWrap = container.querySelector('.ciu-zoom-wrapper');
                const zoomSlider = container.querySelector('.ciu-zoom');
                const btnCrop = container.querySelector('.ciu-crop');
                const btnReset = container.querySelector('.ciu-reset');
                const btnResel = container.querySelector('.ciu-reselect');
                const output = container.querySelector('.ciu-output');

                let cropper = null;
                let origOverflow = box.style.overflow || '';
                let finalized = false;

                // Open picker when clicking box
                box.addEventListener('click', function(e) {
                    if (finalized) return;
                    if (cropper) {
                        const onCropper = e.target.closest('.cropper-container') || e.target === preview;
                        if (onCropper) return;
                    }
                    input.value = '';
                    input.click();
                });

                btnResel.addEventListener('click', function() {
                    finalized = false;
                    input.value = '';
                    input.click();
                });

                input.addEventListener('change', function(e) {
                    const file = e.target.files && e.target.files[0];
                    if (!file) return;

                    const reader = new FileReader();
                    reader.onload = function(ev) {
                        preview.src = ev.target.result;
                        preview.style.display = 'block';
                        placeholder.style.display = 'none';

                        preview.onload = function() {
                            preview.style.maxWidth = '100%';
                            origOverflow = box.style.overflow || '';
                            box.style.overflow = 'visible';

                            if (cropper) {
                                cropper.destroy();
                                cropper = null;
                            }

                            cropper = new Cropper(preview, {
                                aspectRatio: aspectRatio,
                                viewMode: 2,
                                dragMode: 'move',
                                autoCropArea: 1,
                                zoomable: true,
                                movable: true,
                                cropBoxResizable: false,
                                cropBoxMovable: false,
                                background: false,
                                responsive: true,
                                ready() {
                                    const c = this.cropper;
                                    const containerData = c.getContainerData();

                                    const cropWidth = containerData.width;
                                    const cropHeight = cropWidth / aspectRatio;
                                    const top = (containerData.height - cropHeight) / 2;

                                    c.setCropBoxData({
                                        left: 0,
                                        top: top,
                                        width: cropWidth,
                                        height: cropHeight
                                    });

                                    const imageData = c.getImageData();
                                    const cb = c.getCropBoxData();
                                    const minZoom = Math.max(cb.width / imageData.naturalWidth,
                                        cb.height / imageData.naturalHeight);
                                    c.zoomTo(minZoom);

                                    if (zoomSlider) {
                                        zoomSlider.min = minZoom.toFixed(2);
                                        zoomSlider.value = minZoom.toFixed(2);
                                    }
                                }
                            });

                            finalized = false;
                            if (zoomSlider) zoomSlider.value = 1;
                            zoomWrap.classList.remove('d-none');
                            controls.classList.remove('d-none');
                            btnCrop.classList.remove('d-none');
                        };
                    };
                    reader.readAsDataURL(file);
                });

                // Zoom slider
                if (zoomSlider) {
                    zoomSlider.addEventListener('input', function() {
                        if (!cropper) return;
                        const desired = parseFloat(this.value);
                        const imageData = cropper.getImageData();
                        const cb = cropper.getCropBoxData();
                        const minZoom = Math.max(cb.width / imageData.naturalWidth, cb.height / imageData
                            .naturalHeight);
                        const z = Math.max(desired, minZoom);
                        cropper.zoomTo(z);
                        this.value = z.toFixed(2);
                    });
                }

                // Crop and finalize
                btnCrop.addEventListener('click', function() {
                    if (!cropper) return;

                    // Use original image size for sharpness
                    const cropData = cropper.getData(true); // get data in original image pixels
                    const canvas = cropper.getCroppedCanvas({
                        width: Math.round(cropData.width),
                        height: Math.round(cropData.height),
                        imageSmoothingEnabled: false, // disable smoothing
                    });

                    // Optional: rounded corners
                    const tempCanvas = document.createElement('canvas');
                    tempCanvas.width = canvas.width;
                    tempCanvas.height = canvas.height;

                    const ctx = tempCanvas.getContext('2d');
                    ctx.clearRect(0, 0, tempCanvas.width, tempCanvas.height);

                    const radius = 20;
                    ctx.beginPath();
                    ctx.moveTo(radius, 0);
                    ctx.lineTo(tempCanvas.width - radius, 0);
                    ctx.quadraticCurveTo(tempCanvas.width, 0, tempCanvas.width, radius);
                    ctx.lineTo(tempCanvas.width, tempCanvas.height - radius);
                    ctx.quadraticCurveTo(tempCanvas.width, tempCanvas.height, tempCanvas.width - radius,
                        tempCanvas.height);
                    ctx.lineTo(radius, tempCanvas.height);
                    ctx.quadraticCurveTo(0, tempCanvas.height, 0, tempCanvas.height - radius);
                    ctx.lineTo(0, radius);
                    ctx.quadraticCurveTo(0, 0, radius, 0);
                    ctx.closePath();
                    ctx.clip();

                    ctx.drawImage(canvas, 0, 0, tempCanvas.width, tempCanvas.height);

                    const dataURL = tempCanvas.toDataURL('image/png', 1.0); // max quality
                    output.value = dataURL;
                    preview.src = dataURL;

                    cropper.destroy();
                    cropper = null;
                    finalized = true;

                    zoomWrap.classList.add('d-none');
                    btnCrop.classList.add('d-none');
                    box.style.overflow = origOverflow || 'hidden';
                });

                // Reset
                btnReset.addEventListener('click', function() {
                    if (cropper) {
                        cropper.destroy();
                        cropper = null;
                    }
                    preview.removeAttribute('src');
                    preview.style.display = 'none';
                    placeholder.style.display = 'inline';
                    input.value = '';
                    output.value = '';
                    finalized = false;

                    controls.classList.add('d-none');
                    zoomWrap.classList.add('d-none');
                    btnCrop.classList.remove('d-none');
                    box.style.overflow = origOverflow || 'hidden';
                });

                // Auto-crop if form submits without pressing Crop
                const form = container.closest('form');
                if (form) {
                    form.addEventListener('submit', function() {
                        if (cropper && !finalized) {
                            const scaleFactor = 2;
                            const canvas = cropper.getCroppedCanvas({
                                width: outputWidth * scaleFactor,
                                height: outputHeight * scaleFactor,
                                imageSmoothingEnabled: true,
                                imageSmoothingQuality: 'high'
                            });
                            output.value = canvas.toDataURL('image/png', 1.0);
                            cropper.destroy();
                            cropper = null;
                            finalized = true;
                        }
                    });
                }
            }
                       <div id="card-image-uploader-modal" data-output-width="400" data-output-height="136"
                data-aspect-ratio="{{ 400 / 136 }}">
                <label class="form-label fw-bold d-block">Card Image</label>

                <div class="ciu-box mx-auto position-relative"
                    style="width:100%; max-width:400px; height:136px; cursor:pointer;
                        display:flex; align-items:center; justify-content:center;
                        background:#f9f9f9; border:2px dashed #ccc; border-radius:12px; overflow:hidden;">
                    <img class="ciu-preview" alt="Preview" style="width:100%; max-width:500px; height:300px; display:none;">
                    <span class="ciu-placeholder" style="font-size:2rem; color:#888;">+ Image</span>
                </div>

                <!-- Zoom Slider -->
                <div class="ciu-zoom-wrapper d-none mt-2">
                    <input type="range" class="ciu-zoom form-range" min="0.1" max="3" step="0.01"
                        value="1">
                </div>

                <div class="ciu-controls d-none mt-2" style="display:flex; gap:8px; justify-content:flex-end;">
                    <button type="button" class="btn btn-sm btn-secondary ciu-reselect">Choose another</button>
                    <button type="button" class="btn btn-sm btn-primary ciu-crop">Crop</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary ciu-reset">Reset</button>
                </div>

                <input type="file" class="ciu-input d-none" accept="image/*">
                <input type="hidden" name="cropped_image" class="ciu-output">
            </div>

            document.addEventListener('DOMContentLoaded', function() {
                const pageContainer = document.getElementById('card-image-uploader-modal');
                if (pageContainer) initCardImageUploader(pageContainer);
            });

            const modalEl = document.getElementById('commonmodal');
            if (modalEl) {
                modalEl.addEventListener('shown.bs.modal', function() {
                    const modalContainer = modalEl.querySelector('#card-image-uploader-modal');
                    if (modalContainer) initCardImageUploader(modalContainer);
                });
            }
        })();
    </script> --}}
    <script>
        $(document).on('click', '.open-card-modal', function(e) {
            e.preventDefault();
            let url = $(this).attr('href');
            const title = $(this).data('title') || 'Create Card';
            $('#commonmodal .modal-title').text(title);
            $('#commonmodal .modal-body').load(url, function() {
                $('#commonmodal').modal('show');
                const box = document.querySelector("#commonmodal .ciu-box");
                const fileInput = document.querySelector("#commonmodal #imageInput");
                const preview = document.querySelector("#commonmodal #imagePreview");
                const placeholder = document.querySelector("#commonmodal .ciu-placeholder");

                if (box && fileInput) {
                    box.addEventListener("click", function() {
                        fileInput.click();
                    });

                    fileInput.addEventListener("change", function() {
                        const file = this.files[0];
                        if (!file) return;

                        const reader = new FileReader();
                        reader.onload = function(e) {
                            preview.src = e.target.result;
                            preview.style.display = "block";
                            placeholder.style.display = "none";
                        };
                        reader.readAsDataURL(file);
                    });
                }
            });
        });

        $(document).on('click', '.open-import-modal', function(e) {
            e.preventDefault();
            const title = $(this).data('title') || 'Form';
            const formUrl = $(this).data('form-url');
            const submitRoute = $(this).data('form-submit');
            const demoDownload = $(this).data('form-demo-download');
            $('#createCardModalLabel').text(title);
            $('#commonmodal-body').load(formUrl, function() {
                $('#commonmodal-body form').attr('action', submitRoute);
                $('#commonmodal-body #downloadDemoBtn').attr('href', demoDownload);
                const modalDialog = $('#commonmodal .modal-dialog');
                modalDialog.addClass('modal-xl');
                var myModal = new bootstrap.Modal(document.getElementById('commonmodal'));
                myModal.show();
                $('#commonmodal').on('hidden.bs.modal', function() {
                    modalDialog.removeClass('modal-xl');
                });
            });
        });

        $(document).on('submit', '#bulkImportForm', function(e) {
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
                    $('#commonmodal').modal('hide');

                    var videoSource = $('#resultVideo source');
                    var video = document.getElementById('resultVideo');

                    if (res.success) {
                        videoSource.attr('src', "{{ asset('Success.mp4') }}");
                    } else {
                        videoSource.attr('src', "{{ asset('Close.mp4') }}");
                    }
                    video.load();
                    video.loop = false;
                    video.currentTime = 0;
                    var resultModal = new bootstrap.Modal(document.getElementById('resultModal'));
                    resultModal.show();
                    video.onended = function() {
                        setTimeout(() => {
                            resultModal.hide();
                        }, 1000);
                        video.onended = null;
                    };
                },
                error: function(xhr) {
                    $('#commonmodal').modal('hide');
                    var videoSource = $('#resultVideo source');
                    var video = document.getElementById('resultVideo');
                    var message = $('#resultVideoMessage');

                    videoSource.attr('src', "{{ asset('Close.mp4') }}");
                    message.text('Unexpected error occurred');
                    message.removeClass('text-success').addClass('text-danger');

                    video.load();
                    video.loop = false; // remove loop
                    video.currentTime = 0;

                    var resultModal = new bootstrap.Modal(document.getElementById('resultModal'));
                    resultModal.show();

                    video.onended = function() {
                        setTimeout(() => {
                            resultModal.hide();
                        }, 1000);
                        video.onended = null;
                    };
                }

            });
        });
    </script>
</body>

</html>
