  {{-- <script>
        // toaster
        toastr.options = {
            progressBar: true,
            timeOut: 3000,
            extendedTimeOut: 1000,
            closeButton: true,
            newestOnTop: true,
            preventDuplicates: true
        };

        // crop images
        (function() {
            const SIDE_MARGIN_X = 4; // px gap left/right
            const SIDE_MARGIN_Y = 2; // px gap top/bottom

            function initCardImageUploader(container) {
                if (!container) return;
                const outputWidth = parseInt(container.dataset.outputWidth || '100');
                const outputHeight = parseInt(container.dataset.outputHeight || '100');
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

                // Open picker when clicking box (unless finalized / cropper UI)
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
                                dragMode: 'move', // move image under fixed crop box
                                autoCropArea: 1,
                                zoomable: true,
                                movable: true,
                                cropBoxResizable: false, // fixed crop box
                                cropBoxMovable: false, // keep it centered
                                background: false,
                                responsive: true,
                                ready() {
                                    const c = this.cropper;
                                    const containerData = c.getContainerData();

                                    // Set crop box full width
                                    const cropWidth = containerData.width; // full width
                                    const cropHeight = cropWidth / aspectRatio; // keep aspect ratio
                                    const top = (containerData.height - cropHeight) / 2; // center vertically

                                    c.setCropBoxData({
                                        left: 0, // remove left padding
                                        top: top,
                                        width: cropWidth,
                                        height: cropHeight
                                    });

                                    // Minimum zoom so image covers crop box
                                    const imageData = c.getImageData();
                                    const cb = c.getCropBoxData();
                                    const minZoom = Math.max(cb.width / imageData.naturalWidth, cb.height / imageData.naturalHeight);
                                    c.zoomTo(minZoom);

                                    // Show zoom slider if needed
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

                // Zoom slider: never allow gaps
                if (zoomSlider) {
                    zoomSlider.addEventListener('input', function() {
                        if (!cropper) return;
                        const desired = parseFloat(this.value);
                        const imageData = cropper.getImageData();
                        const cb = cropper.getCropBoxData();
                        const minZoom = Math.max(
                            cb.width / imageData.naturalWidth,
                            cb.height / imageData.naturalHeight
                        );
                        const z = Math.max(desired, minZoom);
                        cropper.zoomTo(z);
                        this.value = z.toFixed(2);
                    });
                }

                // Crop and finalize
                btnCrop.addEventListener('click', function() {
                    if (!cropper) return;

                    const canvas = cropper.getCroppedCanvas({
                        width: outputWidth,
                        height: outputHeight
                    });

                    const radius = 20; // px or half for circle
                    const tempCanvas = document.createElement('canvas');
                    tempCanvas.width = canvas.width;
                    tempCanvas.height = canvas.height;

                    const ctx = tempCanvas.getContext('2d');
                    ctx.clearRect(0, 0, tempCanvas.width, tempCanvas.height);

                    // Draw rounded rectangle path
                    ctx.beginPath();
                    ctx.moveTo(radius, 0);
                    ctx.lineTo(tempCanvas.width - radius, 0);
                    ctx.quadraticCurveTo(tempCanvas.width, 0, tempCanvas.width, radius);
                    ctx.lineTo(tempCanvas.width, tempCanvas.height - radius);
                    ctx.quadraticCurveTo(tempCanvas.width, tempCanvas.height, tempCanvas.width - radius, tempCanvas.height);
                    ctx.lineTo(radius, tempCanvas.height);
                    ctx.quadraticCurveTo(0, tempCanvas.height, 0, tempCanvas.height - radius);
                    ctx.lineTo(0, radius);
                    ctx.quadraticCurveTo(0, 0, radius, 0);
                    ctx.closePath();

                    ctx.clip(); // apply clipping

                    ctx.drawImage(canvas, 0, 0, tempCanvas.width, tempCanvas.height);

                    const dataURL = tempCanvas.toDataURL('image/png');
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

                // Auto-crop if user submits without pressing Crop
                const form = container.closest('form');
                if (form) {
                    form.addEventListener('submit', function() {
                        if (cropper && !finalized) {
                            const canvas = cropper.getCroppedCanvas({
                                width: outputWidth,
                                height: outputHeight
                            });
                            output.value = canvas.toDataURL('image/png');
                            cropper.destroy();
                            cropper = null;
                            finalized = true;
                            zoomWrap.classList.add('d-none');
                            btnCrop.classList.add('d-none');
                            box.style.overflow = origOverflow || 'hidden';
                        }
                    });
                }
            }

            // Init
            document.addEventListener('DOMContentLoaded', function() {
                const pageContainer = document.getElementById('card-image-uploader-modal');
                if (pageContainer) initCardImageUploader(pageContainer);
            });

            // If inside a Bootstrap modal that appears later
            const modalEl = document.getElementById('commonmodal');
            if (modalEl) {
                modalEl.addEventListener('shown.bs.modal', function() {
                    const modalContainer = modalEl.querySelector('#card-image-uploader-modal');
                    if (modalContainer) initCardImageUploader(modalContainer);
                });
            }
        })(); --}}