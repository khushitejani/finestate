<script>
(function() {
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

        // Reselect
        btnResel.addEventListener('click', function() {
            finalized = false;
            input.value = '';
            input.click();
        });

        // File input
        input.addEventListener('change', function(e) {
            const file = e.target.files && e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(ev) {
                preview.src = ev.target.result;
                preview.style.display = 'block';
                placeholder.style.display = 'none';

                preview.onload = function() {
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
                const minZoom = Math.max(cb.width / imageData.naturalWidth,
                                         cb.height / imageData.naturalHeight);
                const z = Math.max(desired, minZoom);
                cropper.zoomTo(z);
                this.value = z.toFixed(2);
            });
        }

        // Crop button
        btnCrop.addEventListener('click', function() {
            if (!cropper) return;

            const cropData = cropper.getData(true); // original pixels
            const imgData = cropper.imageData;

            const scaleX = imgData.naturalWidth / imgData.width;
            const scaleY = imgData.naturalHeight / imgData.height;

            const cropX = cropData.x * scaleX;
            const cropY = cropData.y * scaleY;
            const cropWidth = cropData.width * scaleX;
            const cropHeight = cropData.height * scaleY;

            const canvas = document.createElement('canvas');
            canvas.width = cropWidth;
            canvas.height = cropHeight;

            const ctx = canvas.getContext('2d');
            ctx.imageSmoothingEnabled = false; // no blur
            ctx.drawImage(
                cropper.image,
                cropX, cropY, cropWidth, cropHeight,
                0, 0, cropWidth, cropHeight
            );

            const dataURL = canvas.toDataURL('image/png', 1.0);
            preview.src = dataURL;
            output.value = dataURL;

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

        // Auto-crop on form submit
        const form = container.closest('form');
        if (form) {
            form.addEventListener('submit', function() {
                if (cropper && !finalized) {
                    const cropData = cropper.getData(true);
                    const imgData = cropper.imageData;

                    const scaleX = imgData.naturalWidth / imgData.width;
                    const scaleY = imgData.naturalHeight / imgData.height;

                    const cropX = cropData.x * scaleX;
                    const cropY = cropData.y * scaleY;
                    const cropWidth = cropData.width * scaleX;
                    const cropHeight = cropData.height * scaleY;

                    const canvas = document.createElement('canvas');
                    canvas.width = cropWidth;
                    canvas.height = cropHeight;
                    const ctx = canvas.getContext('2d');
                    ctx.imageSmoothingEnabled = false;
                    ctx.drawImage(
                        cropper.image,
                        cropX, cropY, cropWidth, cropHeight,
                        0, 0, cropWidth, cropHeight
                    );
                    output.value = canvas.toDataURL('image/png', 1.0);
                    cropper.destroy();
                    cropper = null;
                    finalized = true;
                }
            });
        }
    }

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
</script>
