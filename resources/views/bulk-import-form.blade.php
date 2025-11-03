<form id="bulkImportForm" method="POST" action="/your-upload-route" enctype="multipart/form-data">
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

    <div id="uploadedList" class="uploaded-files"></div>

    <div class="text-end mt-3">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-success" id="submitBtn">Submit</button>
    </div>
</form>

{{-- <div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">

                <div class="d-sm-flex align-items-center mb-4">
                    <h4 class="card-title mb-sm-0">Stored Images</h4>

                    <form method="GET" class="ms-auto d-flex">
                        <select name="folder" class="form-select form-select-sm me-2" style="width:200px;">
                            <option value="">All Folders</option>
                            @foreach ($directories as $dir)
                                <option value="{{ $dir }}" {{ request('folder') === $dir ? 'selected' : '' }}>
                                    {{ ucfirst($dir) }}
                                </option>
                            @endforeach
                        </select>
                        <button class="btn btn-sm btn-primary">Filter</button>
                    </form>
                </div>

                <div class="row">
                    @forelse($images as $image)
                        <div class="col-md-2 col-sm-3 col-4 mb-4 text-center">
                            <div class="border rounded p-2">
                                <img src="{{ $image['url'] }}" class="img-fluid rounded mb-2"
                                    style="height:100px; object-fit:cover;">
                                <div class="small text-muted" style="word-break:break-all;">
                                    {{ $image['name'] }}
                                </div>
                                <div class="text-muted small">{{ $image['folder'] }}</div>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-muted mt-3">No images found.</p>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</div> --}}
<div class="folders-container mb-3"></div>
<div class="images-container"></div>

<style>
    .drop-zone {
        border: 2px dashed #198754;
        border-radius: 12px;
        padding: 40px 20px;
        background-color: #f9fafb;
        cursor: pointer;
        transition: 0.3s;
    }

    .drop-zone:hover {
        background-color: #e9f7ef;
        border-color: #157347;
    }

    .uploaded-file {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 10px 15px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .uploaded-file i,
    .uploaded-file img {
        width: 60px;
        height: 60px;
        flex-shrink: 0;
        object-fit: cover;
        border-radius: 4px;
        color: #198754;
        font-size: 2.5rem;
    }

    .file-info {
        flex-grow: 1;
    }

    .progress {
        height: 6px;
        border-radius: 4px;
        margin-top: 6px;
    }
</style>

<script>
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const uploadedList = document.getElementById('uploadedList');

    // Only CSV & Excel are valid
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
        const percent = fileEl.querySelector('.percent');

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
                percent.textContent = progress + '%';
            }
        }, 100);
    }

    function handleFiles(files) {
        if (files.length === 0) return;
        const file = files[0];

        uploadedList.innerHTML = ''; // clear previous
        const ext = file.name.split('.').pop().toLowerCase();
        const isValid = validExtensions.includes(ext);

        // Only assign valid file to form input
        const dt = new DataTransfer();
        if (isValid) dt.items.add(file);
        fileInput.files = dt.files;

        const fileEl = document.createElement('div');
        fileEl.classList.add('uploaded-file');

        const iconClass = getFileIcon(file.name);
        fileEl.innerHTML = `
        <i class="bi ${iconClass}"></i>
        <div class="file-info">
            <div class="fw-semibold">${file.name}</div>
            <div class="progress"><div class="progress-bar" role="progressbar" style="width:0%"></div></div>
        </div>
        <span class="text-muted small percent">0%</span>
    `;

        uploadedList.appendChild(fileEl);
        animateProgress(fileEl, isValid);
    }
    dropZone.addEventListener('click', () => fileInput.click());
    dropZone.addEventListener('dragover', e => {
        e.preventDefault();
        dropZone.style.backgroundColor = '#e9f7ef';
    });
    dropZone.addEventListener('dragleave', () => {
        dropZone.style.backgroundColor = '#f9fafb';
    });
    dropZone.addEventListener('drop', e => {
        e.preventDefault();
        dropZone.style.backgroundColor = '#f9fafb';
        handleFiles(e.dataTransfer.files);
    });

    fileInput.addEventListener('change', e => handleFiles(e.target.files));
</script>
<script>
    (function($) {
        const foldersContainer = $('.folders-container');
        const imagesContainer = $('.images-container');
        const breadcrumbContainer = $('.breadcrumb-container');

        let currentFolder = '';

        const loadFolders = (folder = '') => {
            $.get(`/bulk-import-form?folder=${folder}`).done((res) => {
                foldersContainer.empty();
                imagesContainer.empty();
                breadcrumbContainer.empty();

                // Breadcrumb
                const parts = folder ? folder.split('/') : [];
                let pathAccumulator = '';
                breadcrumbContainer.append(
                    `<button type="button" class="breadcrumb-btn btn btn-sm btn-light me-1 mb-1" data-folder="">Root</button>`
                );
                parts.forEach(part => {
                    pathAccumulator = pathAccumulator ? pathAccumulator + '/' + part : part;
                    breadcrumbContainer.append(
                        `<button type="button" class="breadcrumb-btn btn btn-sm btn-light me-1 mb-1" data-folder="${pathAccumulator}">${part}</button>`
                    );
                });

                // Subfolders
                if (res.subfolders && res.subfolders.length) {
                    res.subfolders.forEach(sub => {
                        const folderName = sub.split('/').pop();
                        foldersContainer.append(`
                        <button type="button" class="folder-btn btn btn-outline-secondary btn-sm me-2 mb-2" data-folder="${folderName}">
                            ${folderName}
                        </button>
                    `);
                    });
                }

                // Images
                if (res.images && res.images.length) {
                    res.images.forEach(img => {
                        imagesContainer.append(`
                        <div class="image-item d-inline-block m-1 text-center">
                            <img src="${img.url}" alt="${img.name}" width="80" style="object-fit:cover;">
                            <div class="text-truncate" style="max-width:80px;">${img.name}</div>
                        </div>
                    `);
                    });
                }
            });
        }

        // Click subfolder
        foldersContainer.on('click', '.folder-btn', function() {
            const folderName = $(this).data('folder');
            currentFolder = currentFolder ? currentFolder + '/' + folderName : folderName;
            loadFolders(currentFolder);
        });

        // Breadcrumb click
        breadcrumbContainer.on('click', '.breadcrumb-btn', function() {
            currentFolder = $(this).data('folder');
            loadFolders(currentFolder);
        });

        // Initial load
        loadFolders();

    })(jQuery);
</script>
