    <form id="bulkImportForm" method="POST" action="" enctype="multipart/form-data">
        @csrf
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="text-success fw-bold mb-0">
                <i class="bi bi-cloud-arrow-up me-2"></i> Bulk Upload
            </h5>
            <a href="" class="btn btn-info btn-sm" id="downloadDemoBtn" target="_blank">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i> Download Demo Excel
            </a>
        </div>

        <div id="dropZone" class="drop-zone text-center mb-3">
            <i class="bi bi-cloud-arrow-up fs-1 text-secondary d-block mb-2"></i>
            <p class="mb-1 text-muted">
                Drop file here or <span class="text-success fw-semibold">click to upload</span>
            </p>
            <small class="text-muted">Only one file (CSV, Excel) under 5MB</small>
            <input type="file" id="fileInput" name="import_file" hidden>
        </div>

        <div id="uploadedList" class="uploaded-files mb-3"></div>

        <input type="hidden" name="folder" id="folderInput" value="">

        <div class="text-end mt-3">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success" id="submitBtn">Submit</button>
        </div>
    </form>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-success mb-0">
            <i class="bi bi-folder2-open me-2"></i> Storage Explorer
        </h4>
    </div>
    <div class="d-flex justify-content-between align-items-center mb-3" id="breadcrumbContainer">
        <small class="text-muted mb-0">
            <i class="bi bi-folder2-open me-2 text-success"></i>
            <span id="breadcrumbPath" class="fw-semibold text-success" data-path="">Root</span>
        </small>
        <button type="button" id="uploadBtn" class="btn btn-outline-success btn-sm" disabled>
            <i class="bi bi-cloud-arrow-up me-1"></i> Upload
        </button>
    </div>

    <div id="folderContents" class="row mt-4">
        @foreach ($subfolders as $folder)
            <div class="col-6 col-sm-4 col-md-3 col-lg-2 text-center mb-4">
                <div class="folder-item" data-path="{{ $folder }}">
                    <i class="bi bi-folder-fill fs-1 d-block mb-2"></i>
                    <div class="folder-name small text-truncate">{{ basename($folder) }}</div>
                </div>
            </div>
        @endforeach
    </div>
    <input type="file" id="storageFileInput" multiple webkitdirectory mozdirectory msdirectory odirectory hidden>
    <script>
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileInput');
        const uploadedList = document.getElementById('uploadedList');
        const folderInput = document.getElementById('folderInput');
        const validExtensions = ['csv', 'xls', 'xlsx'];
        const storageFileInput = document.getElementById('storageFileInput');
        const uploadBtn = document.getElementById('uploadBtn');

        function getFileIcon(fileName) {
            const ext = fileName.split('.').pop().toLowerCase();
            if (ext === 'csv') return 'bi-file-earmark-spreadsheet';
            if (ext === 'xls' || ext === 'xlsx') return 'bi-file-earmark-spreadsheet-fill';
            return 'bi-file-earmark-text';
        }

        function animateProgress(fileEl, isValid) {
            let progress = 0;
            const progressBar = fileEl.querySelector('.progress-bar');
            const percentLabel = fileEl.querySelector('.percent');
            const interval = setInterval(() => {
                if (progress >= 100) {
                    clearInterval(interval);
                    const statusIcon = document.createElement('i');
                    statusIcon.classList.add('ms-2');
                    if (isValid) {
                        progressBar.classList.add('bg-success');
                        statusIcon.classList.add('bi', 'bi-check-circle-fill', 'text-success');
                    } else {
                        progressBar.classList.add('bg-danger');
                        statusIcon.classList.add('bi', 'bi-x-circle-fill', 'text-danger');
                    }
                    fileEl.appendChild(statusIcon);
                } else {
                    progress += 5;
                    progressBar.style.width = progress + '%';
                    percentLabel.textContent = progress + '%';
                }
            }, 50);
        }

        function handleFiles(files) {
            if (!files.length) return;
            const file = files[0];
            uploadedList.innerHTML = '';

            const ext = file.name.split('.').pop().toLowerCase();
            const isValid = validExtensions.includes(ext);

            const dt = new DataTransfer();
            if (isValid) dt.items.add(file);
            fileInput.files = dt.files;

            const fileEl = document.createElement('div');
            fileEl.classList.add('uploaded-file');
            fileEl.innerHTML = `
            <i class="bi ${getFileIcon(file.name)}"></i>
            <div class="file-info">
                <div class="fw-semibold">${file.name}</div>
                <div class="progress"><div class="progress-bar" role="progressbar" style="width:0%"></div></div>
            </div>
            <span class="text-muted small percent">0%</span>
        `;
            uploadedList.appendChild(fileEl);
            animateProgress(fileEl, isValid);
        }

        // Drag & click events
        dropZone.addEventListener('click', () => fileInput.click());
        dropZone.addEventListener('dragover', e => {
            e.preventDefault();
            dropZone.style.backgroundColor = '#e9f7ef';
        });
        dropZone.addEventListener('dragleave', () => dropZone.style.backgroundColor = '#f9fafb');
        dropZone.addEventListener('drop', e => {
            e.preventDefault();
            dropZone.style.backgroundColor = '#f9fafb';
            handleFiles(e.dataTransfer.files);
        });
        fileInput.addEventListener('change', e => handleFiles(e.target.files));

        // Load folder contents
        function loadFolder(folder = '') {
            $.get('{{ route('bulk.import.folder') }}', {
                folder: folder
            }, function(res) {
                const folderContents = $('#folderContents');
                const breadcrumbPath = $('#breadcrumbPath');
                folderContents.empty();

                // Breadcrumb
                let pathParts = folder ? folder.split('/') : [];
                let breadcrumbHTML = `<a href="#" class="breadcrumb-link text-success" data-path="">Root</a>`;
                let fullPath = '';
                pathParts.forEach((part, index) => {
                    fullPath += (index === 0 ? '' : '/') + part;
                    if (index === pathParts.length - 1) {
                        breadcrumbHTML +=
                            ' <span class="text-muted">›</span> <span class="fw-bold text-success">' +
                            part + '</span>';
                    } else {
                        breadcrumbHTML +=
                            ' <span class="text-muted">›</span> <a href="#" class="breadcrumb-link text-success" data-path="' +
                            fullPath + '">' + part + '</a>';
                    }
                });
                breadcrumbPath.html(breadcrumbHTML);
                breadcrumbPath.attr('data-path', folder);
                folderInput.value = folder; // update hidden input

                // Show subfolders
                res.subfolders.forEach(sub => {
                    folderContents.append(`
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2 text-center mb-4">
                        <div class="folder-item" data-path="${sub}">
                            <i class="bi bi-folder-fill fs-1 d-block mb-2"></i>
                            <div class="folder-name small text-truncate">${sub.split('/').pop()}</div>
                        </div>
                    </div>
                `);
                });

                // Show images
                res.images.forEach(img => {
                    folderContents.append(`
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2 text-center mb-4 image-item">
                        <img src="${img.url}" alt="${img.name}" class="rounded mb-2" style="width:120px;height:120px;object-fit:contain;border:1px solid #e5e5e5;background-color:#fff;padding:4px;">
                        <div class="small fw-semibold text-dark text-truncate w-100" title="${img.name}">${img.name}</div>
                    </div>
                `);
                    uploadBtn.removeAttribute('disabled');
                });

                if (res.subfolders.length === 0 && res.images.length === 0) {
                    folderContents.html(
                        `<div class="text-center text-muted py-5"><i class="bi bi-folder-x fs-1 d-block mb-2"></i><p>No content in this folder.</p></div>`
                    );
                }
            }).fail(function() {
                $('#folderContents').html(
                    `<div class="text-center text-danger py-5"><i class="bi bi-exclamation-triangle fs-1 d-block mb-2"></i><p>Error loading folder. Please try again.</p></div>`
                );
            });
            uploadBtn.removeAttribute('disabled');
        }

        // Folder clicks
        $(document).on('click', '.folder-item', function() {
            loadFolder($(this).data('path'));
        });
        $(document).on('click', '.breadcrumb-link', function(e) {
            e.preventDefault();
            loadFolder($(this).data('path'));
        });

        // Auto upload
        uploadBtn.addEventListener('click', function() {
            if (this.hasAttribute('disabled')) {
                alert('⚠️ Please open a folder first.');
                return;
            }
            storageFileInput.click();
        });
        storageFileInput.addEventListener('change', function() {
            if (!this.files.length) return;

            // Confirm before upload
            if (!confirm('⚠️ You are about to upload multiple folders. Do you trust this upload?')) {
                return;
            }

            const currentFolder = $('#breadcrumbPath').attr('data-path') || '';
            const formData = new FormData();

            formData.append('_token', '{{ csrf_token() }}');
            formData.append('folder', currentFolder);

            // Append all files and preserve relative paths
            Array.from(this.files).forEach((file, index) => {
                formData.append(`files[${index}]`, file);
                formData.append(`paths[${index}]`, file.webkitRelativePath);
            });

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '{{ route('bulk.import.upload') }}', true);

            xhr.upload.onprogress = function(e) {
                if (e.lengthComputable) {
                    const percent = Math.round((e.loaded / e.total) * 100);
                    console.log(`Uploading... ${percent}%`);
                }
            };

            xhr.onload = function() {
                if (xhr.status === 200) {
                    const res = JSON.parse(xhr.responseText);
                    if (res.success) {
                        alert('✅ All folders uploaded successfully!');
                        loadFolder(currentFolder);
                    } else {
                        alert('❌ Upload failed: ' + (res.message || 'Unknown error'));
                    }
                } else {
                    alert('❌ Server error during upload.');
                }
            };

            xhr.onerror = function() {
                alert('⚠️ Network error during upload.');
            };

            xhr.send(formData);
        });
    </script>

    {{-- <script>
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileInput');
        const uploadedList = document.getElementById('uploadedList');

        const validExtensions = ['csv', 'xls', 'xlsx'];

        function getFileIcon(fileName) {
            const ext = fileName.split('.').pop().toLowerCase();
            if (ext === 'csv') return 'bi-file-earmark-spreadsheet';
            if (ext === 'xls' || ext === 'xlsx') return 'bi-file-earmark-spreadsheet-fill';
            return 'bi-file-earmark-text';
        }

        function animateProgress(fileEl, isValid) {
            let progress = 0;
            const progressBar = fileEl.querySelector('.progress-bar');
            const percentLabel = fileEl.querySelector('.percent');

            const interval = setInterval(() => {
                if (progress >= 100) {
                    clearInterval(interval);
                    const statusIcon = document.createElement('i');
                    statusIcon.classList.add('ms-2');
                    if (isValid) {
                        progressBar.classList.add('bg-success');
                        statusIcon.classList.add('bi', 'bi-check-circle-fill', 'text-success');
                    } else {
                        progressBar.classList.add('bg-danger');
                        statusIcon.classList.add('bi', 'bi-x-circle-fill', 'text-danger');
                    }
                    fileEl.appendChild(statusIcon);
                } else {
                    progress += 5;
                    progressBar.style.width = progress + '%';
                    percentLabel.textContent = progress + '%';
                }
            }, 50);
        }

        function handleFiles(files) {
            if (!files.length) return;
            const file = files[0];
            uploadedList.innerHTML = ''; // clear previous

            const ext = file.name.split('.').pop().toLowerCase();
            const isValid = validExtensions.includes(ext);

            const dt = new DataTransfer();
            if (isValid) dt.items.add(file);
            fileInput.files = dt.files;

            const fileEl = document.createElement('div');
            fileEl.classList.add('uploaded-file');
            fileEl.innerHTML = `
            <i class="bi ${getFileIcon(file.name)}"></i>
            <div class="file-info">
                <div class="fw-semibold">${file.name}</div>
                <div class="progress"><div class="progress-bar" role="progressbar" style="width:0%"></div></div>
            </div>
            <span class="text-muted small percent">0%</span>
        `;
            uploadedList.appendChild(fileEl);

            animateProgress(fileEl, isValid);
        }
        // function handleFiles(files) {
        //     if (!files.length) return;
        //     uploadedList.innerHTML = ''; 

        //     const dt = new DataTransfer(); 

        //     Array.from(files).forEach(file => {
        //         const ext = file.name.split('.').pop().toLowerCase();
        //         const isValid = validExtensions.includes(ext);

        //         if (isValid) dt.items.add(file);

        //         const fileEl = document.createElement('div');
        //         fileEl.classList.add('uploaded-file');
        //         fileEl.innerHTML = `
        //         <i class="bi ${getFileIcon(file.name)}"></i>
        //         <div class="file-info">
        //             <div class="fw-semibold">${file.name}</div>
        //             <div class="progress"><div class="progress-bar" role="progressbar" style="width:0%"></div></div>
        //         </div>
        //         <span class="text-muted small percent">0%</span>
        //     `;
        //         uploadedList.appendChild(fileEl);

        //         animateProgress(fileEl, isValid);
        //     });

        //     fileInput.files = dt.files; // set all files to input
        // }

        // Click & drag/drop events
        dropZone.addEventListener('click', () => fileInput.click());
        dropZone.addEventListener('dragover', e => {
            e.preventDefault();
            dropZone.style.backgroundColor = '#e9f7ef';
        });
        dropZone.addEventListener('dragleave', () => dropZone.style.backgroundColor = '#f9fafb');
        dropZone.addEventListener('drop', e => {
            e.preventDefault();
            dropZone.style.backgroundColor = '#f9fafb';
            handleFiles(e.dataTransfer.files);
        });
        fileInput.addEventListener('change', e => handleFiles(e.target.files));

        function loadFolder(folder = '') {
            $.get('{{ route('bulk.import.folder') }}', {
                folder: folder
            }, function(res) {
                const folderContents = $('#folderContents');
                const breadcrumbPath = $('#breadcrumbPath');

                folderContents.empty();

                // Build breadcrumb (always starts with Root)
                let pathParts = folder ? folder.split('/') : [];
                let breadcrumbHTML = `<a href="#" class="breadcrumb-link text-success" data-path="">Root</a>`;

                let fullPath = '';
                pathParts.forEach((part, index) => {
                    fullPath += (index === 0 ? '' : '/') + part;

                    if (index === pathParts.length - 1) {
                        breadcrumbHTML +=
                            ' <span class="text-muted">›</span> ' +
                            `<span class="fw-bold text-success">${part}</span>`;
                    } else {
                        breadcrumbHTML +=
                            ' <span class="text-muted">›</span> ' +
                            `<a href="#" class="breadcrumb-link text-success" data-path="${fullPath}">${part}</a>`;
                    }

                });

                breadcrumbPath.html(breadcrumbHTML);
                breadcrumbPath.attr('data-path', folder);
                if (folder === '') {
                    if (res.subfolders.length === 0) {
                        folderContents.html(`
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-folder-x fs-1 d-block mb-2"></i>
                            <p>No folders found in root directory.</p>
                        </div>
                    `);
                        return;
                    }

                    res.subfolders.forEach(sub => {
                        const folderName = sub.split('/').pop();
                        const isActive = sub === folder;

                        folderContents.append(`
                            <div class="col-6 col-sm-4 col-md-3 col-lg-2 text-center mb-4">
                                <div class="folder-item ${isActive ? 'active' : ''}" data-path="${sub}">
                                    <i class="bi bi-folder-fill fs-1 d-block mb-2"></i>
                                    <div class="folder-name small text-truncate">${folderName}</div>
                                </div>
                            </div>
                        `);
                    });
                    return;
                }

                // If no subfolders or images in a subfolder
                if (res.subfolders.length === 0 && res.images.length === 0) {
                    folderContents.html(`
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-folder-x fs-1 d-block mb-2"></i>
                        <p>No subfolders or images found in this folder.</p>
                    </div>
                `);
                    return;
                }

                // Show subfolders
                res.subfolders.forEach(sub => {
                    folderContents.append(`
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2 text-center mb-4">
                        <div class="folder-item" data-path="${sub}">
                            <i class="bi bi-folder-fill fs-1 d-block mb-2"></i>
                            <div class="folder-name small text-truncate">${sub.split('/').pop()}</div>
                        </div>
                    </div>
                `);
                });

                res.images.forEach(img => {
                    folderContents.append(`
                        <div class="col-6 col-sm-4 col-md-3 col-lg-2 text-center mb-4 image-item">
                            <img src="${img.url}" alt="${img.name}"
                                class="rounded mb-2"
                                style="width: 120px; height: 120px; object-fit: contain; border: 1px solid #e5e5e5; background-color: #fff; padding: 4px;">
                            <div class="small fw-semibold text-dark text-truncate w-100" title="${img.name}">
                                ${img.name}
                            </div>
                        </div>
                    `);
                    uploadBtn.removeAttribute('disabled');
                });
            }).fail(function() {
                $('#folderContents').html(`
                <div class="text-center text-danger py-5">
                    <i class="bi bi-exclamation-triangle fs-1 d-block mb-2"></i>
                    <p>Error loading folder. Please try again.</p>
                </div>
            `);
            });

            uploadBtn.removeAttribute('disabled');

        }
        $(document).on('click', '.folder-item', function() {
            const folderPath = $(this).data('path');
            loadFolder(folderPath);
        });
        $(document).on('click', '.breadcrumb-link', function(e) {
            e.preventDefault();
            const path = $(this).data('path');
            loadFolder(path);
        });

        function initFolderExplorer() {
            $(document).off('click', '.folder-item');
            $(document).on('click', '.folder-item', function() {
                const folder = $(this).data('path');
                loadFolder(folder);
            });

            $(document).off('click', '#breadcrumbContainer button');
            $(document).on('click', '#breadcrumbContainer button', function() {
                const folder = $(this).data('folder');
                loadFolder(folder);
            });
        }

        $(document).ready(function() {
            initFolderExplorer();
        });

        // Storage Explorer auto-upload
        const storageFileInput = document.getElementById('storageFileInput');
        const uploadBtn = document.getElementById('uploadBtn');

        uploadBtn.addEventListener('click', function() {
            if (this.hasAttribute('disabled')) {
                alert('⚠️ Please open a folder first.');
                return;
            }
            storageFileInput.click(); // open file picker
        });
        storageFileInput.addEventListener('change', function() {
            if (!this.files.length) return;

            const file = this.files[0];
            const currentFolder = $('#breadcrumbPath').data('path') || '';

            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('import_file', file);
            formData.append('folder', currentFolder);

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '{{ route('bulk.import.upload') }}', true);

            xhr.onload = function() {
                if (xhr.status === 200) {
                    const res = JSON.parse(xhr.responseText);
                    if (res.success) {
                        alert('✅ File uploaded successfully!');
                        loadFolder(currentFolder); // refresh folder contents
                    } else {
                        alert('❌ Upload failed: ' + (res.message || 'Unknown error'));
                    }
                } else {
                    alert('❌ Server error during upload.');
                }
            };

            xhr.send(formData);
        });
    </script> --}}
