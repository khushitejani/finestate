<form id="bulkImportForm" method="POST" action="" enctype="multipart/form-data"> @csrf <div
        class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="text-success fw-bold mb-0"> <i class="bi bi-cloud-arrow-up me-2"></i> Bulk Upload </h5> <a
            href="" class="btn btn-info btn-sm" id="downloadDemoBtn" target="_blank"> <i
                class="bi bi-file-earmark-spreadsheet me-1"></i> Download Demo Excel </a>
    </div>
    <div id="dropZone" class="drop-zone text-center mb-3"> <i
            class="bi bi-cloud-arrow-up fs-1 text-secondary d-block mb-2"></i>
        <p class="mb-1 text-muted"> Drop file here or <span class="text-success fw-semibold">click to upload</span> </p>
        <small class="text-muted">Only one file (CSV, Excel) under 5MB</small> <input type="file" id="fileInput"
            name="import_file" hidden>
    </div>
    <div id="uploadedList" class="uploaded-files mb-3"></div> <input type="hidden" name="folder" id="folderInput"
        value="">
    <div class="text-end mt-3"> <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-success" id="submitBtn">Submit</button>
    </div>
</form>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold text-success mb-0"> <i class="bi bi-folder2-open me-2"></i> Storage Explorer </h4>
</div>

<div class="d-flex justify-content-between align-items-center mb-3" id="breadcrumbContainer">
    <small class="text-muted mb-0">
        <i class="bi bi-folder2-open me-2 text-success"></i>
        <span id="breadcrumbPath" class="fw-semibold text-success" data-path="">Root</span>
    </small>

    <div class="d-flex align-items-center gap-2">
        <button type="button" id="uploadBtn" class="btn btn-outline-success btn-sm" disabled>
            <i class="bi bi-cloud-arrow-up me-1"></i> Upload
        </button>
        <div class="dropdown">
            <button class="btn btn-outline-secondary btn-sm" type="button" id="menuDropdown" data-bs-toggle="dropdown"
                aria-expanded="false">
                <i class="bi bi-three-dots-vertical"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="menuDropdown">
                <li><button type="button" class="dropdown-item" id="btnNewFolderDropdown"><i
                            class="bi bi-folder-plus me-2"></i> New Folder</button></li>
                <li><button type="button" class="dropdown-item" id="btnCopy" disabled><i
                            class="bi bi-files me-2"></i>Copy</button></li>
                <li><button type="button" class="dropdown-item" id="btnPaste" disabled><i
                            class="bi bi-clipboard-check me-2"></i>Paste</button></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><button type="button" class="dropdown-item" id="btnRename" disabled><i
                            class="bi bi-pencil-square me-2"></i>Rename</button></li>
                <li><button type="button" class="dropdown-item" id="btnDelete" disabled><i
                            class="bi bi-trash me-2"></i>Delete</button></li>
                <li><button type="button" class="dropdown-item" id="btnMove" disabled><i
                            class="bi bi-arrows-move me-2"></i>Move</button></li>
            </ul>
        </div>

    </div>
</div>

{{-- <div class="d-flex justify-content-between align-items-center mb-3" id="breadcrumbContainer"> <small
        class="text-muted mb-0"> <i class="bi bi-folder2-open me-2 text-success"></i> <span id="breadcrumbPath"
            class="fw-semibold text-success" data-path="">Root</span> </small> <button type="button" id="uploadBtn"
        class="btn btn-outline-success btn-sm" disabled> <i class="bi bi-cloud-arrow-up me-1"></i> Upload </button>
</div>
<div class="mb-3">
    <button id="btnCopy" class="btn btn-sm btn-outline-primary" disabled>Copy</button> <button id="btnCut"
        class="btn btn-sm btn-outline-warning" disabled>Cut</button> <button id="btnPaste"
        class="btn btn-sm btn-outline-success" disabled>Paste</button> <button id="btnRename"
        class="btn btn-sm btn-outline-info" disabled>Rename</button> <button id="btnDelete"
        class="btn btn-sm btn-outline-danger" disabled>Delete</button> <button id="btnMove"
        class="btn btn-sm btn-outline-secondary" disabled>Move</button>
</div> --}}
<div id="folderContents" class="row mt-4">
    @foreach ($subfolders as $folder)
        <div class="col-6 col-sm-4 col-md-3 col-lg-2 text-center mb-4">
            <div class="folder-item" data-path="{{ $folder }}"> <i
                    class="bi bi-folder-fill fs-1 d-block mb-2"></i>
                <div class="folder-name small text-truncate">{{ basename($folder) }}</div>
            </div>
        </div>
    @endforeach
</div>
<div id="uploadLoader"
    class="d-none position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-flex justify-content-center align-items-center"
    style="z-index: 1050;">
    <div class="text-center text-white">
        <div class="spinner-border text-success" role="status"> <span class="visually-hidden">Loading...</span>
        </div>
        <div class="mt-2 fw-bold">Uploading... <span id="uploadPercent">0%</span></div>
    </div>
</div> <!-- Move Modal -->
<div class="modal fade" id="moveModal" tabindex="-1" aria-labelledby="moveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="moveModalLabel">Move Items</h5> <button type="button" class="btn-close"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body"> <small class="text-muted mb-2">Current Path: <span
                        id="moveCurrentPath">Root</span></small>
                <div id="moveFolderContainer" class="row"> <!-- Folder tree will be dynamically loaded here -->
                </div>
            </div>
            <div class="modal-footer"> <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">Cancel</button> <button type="button" class="btn btn-primary"
                    id="confirmMoveBtn" disabled>Move Here</button> </div>
        </div>
    </div>
</div>


<input type="file" id="storageFileInput" multiple webkitdirectory mozdirectory msdirectory odirectory hidden>
<style>
    .folder-item.selected {
        background-color: #e9f7ef;
        border-radius: 6px;
    }

    .image-item.selected {
        border: 2px solid #0f5132;
        border-radius: 4px;
        background-color: #e9f7ef33;
    }

    .folder-item,
    .image-item {
        transition: all 0.2s ease;
    }

    .folder-item:hover,
    .image-item:hover {
        cursor: pointer;
        background-color: #f1f5f9;
    }

    .folder-item {
        cursor: pointer;
        padding: 8px;
        transition: all 0.2s ease;
    }

    .folder-item:hover {
        background-color: #f1f5f9;
    }
</style>
<script>
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const uploadedList = document.getElementById('uploadedList');
    const folderInput = document.getElementById('folderInput');
    const validExtensions = ['csv', 'xls', 'xlsx'];
    const storageFileInput = document.getElementById('storageFileInput');
    const uploadBtn = document.getElementById('uploadBtn');

    let selectedItems = [];
    let lastClickedIndex = null;
    let clipboard = {
        mode: null,
        items: []
    };
    let moveSelectedFolder = null;
    let moveCurrentPath = '';

    // -----------------------------
    // Utility Functions
    // -----------------------------

    function updateActionButtons() {
        const enable = selectedItems.length > 0;
        $('#btnCopy').prop('disabled', !enable);
        $('#btnRename').prop('disabled', selectedItems.length !== 1);
        $('#btnDelete').prop('disabled', !enable);
        $('#btnPaste').prop('disabled', clipboard.items.length === 0);
        $('#btnMove').prop('disabled', !enable);
        $('#btnPaste').prop('disabled', clipboard.items.length === 0);

        // $('#menuDropdown').prop('disabled', !enable);
    }

    function getFileIcon(fileName) {
        const ext = fileName.split('.').pop().toLowerCase();
        if (ext === 'csv' || ext === 'xls' || ext === 'xlsx') return 'bi-file-earmark-spreadsheet-fill';
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
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width:0%"></div>
                </div>
            </div>
            <span class="text-muted small percent">0%</span>
        `;
        uploadedList.appendChild(fileEl);
        animateProgress(fileEl, isValid);
    }

    // -----------------------------
    // Drag & Drop Upload
    // -----------------------------
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

    // -----------------------------
    // Load Folder Contents
    // -----------------------------
    function loadFolder(folder = '') {
        $.get('{{ route('bulk.import.folder') }}', {
            folder
        }, function(res) {
            const folderContents = $('#folderContents');
            const breadcrumbPath = $('#breadcrumbPath');
            folderContents.empty();

            // Breadcrumbs
            let pathParts = folder ? folder.split('/') : [];
            let breadcrumbHTML = `<a href="#" class="breadcrumb-link text-success" data-path="">Root</a>`;
            let fullPath = '';

            pathParts.forEach((part, index) => {
                fullPath += (index === 0 ? '' : '/') + part;
                if (index === pathParts.length - 1) {
                    breadcrumbHTML +=
                        ` <span class="text-muted">›</span> <span class="fw-bold text-success">${part}</span>`;
                } else {
                    breadcrumbHTML +=
                        ` <span class="text-muted">›</span> <a href="#" class="breadcrumb-link text-success" data-path="${fullPath}">${part}</a>`;
                }
            });

            breadcrumbPath.html(breadcrumbHTML);
            breadcrumbPath.attr('data-path', folder);
            folderInput.value = folder;

            // Subfolders
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

            // Images
            res.images.forEach(img => {
                folderContents.append(`
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2 text-center mb-4 image-item">
                        <img src="${img.url}" alt="${img.name}" class="rounded mb-2" style="width:120px;height:120px;object-fit:contain;border:1px solid #e5e5e5;background:#fff;padding:4px;">
                        <div class="small fw-semibold text-dark text-truncate w-100" title="${img.name}">${img.name}</div>
                    </div>
                `);
            });

            if (!res.subfolders.length && !res.images.length) {
                folderContents.html(`
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-folder-x fs-1 d-block mb-2"></i>
                        <p>No content in this folder.</p>
                    </div>
                `);
            }

            uploadBtn.disabled = folder === '';
        }).fail(() => {
            $('#folderContents').html(`
                <div class="text-center text-danger py-5">
                    <i class="bi bi-exclamation-triangle fs-1 d-block mb-2"></i>
                    <p>Error loading folder. Please try again.</p>
                </div>
            `);
        });
    }

    // -----------------------------
    // Move Folder Modal
    // -----------------------------
    function loadMoveFolder(path = '') {
        $.get('{{ route('bulk.import.folder') }}', {
            folder: path
        }, function(res) {
            const container = $('#moveFolderContainer');
            container.empty();
            moveCurrentPath = path;

            // Breadcrumb in modal
            let pathParts = path ? path.split('/') : [];
            let breadcrumbHTML = `<a href="#" class="move-breadcrumb-link text-success" data-path="">Root</a>`;
            let fullPath = '';

            pathParts.forEach((part, index) => {
                fullPath += (index === 0 ? '' : '/') + part;
                if (index === pathParts.length - 1) {
                    breadcrumbHTML += ` › <span class="fw-bold text-success">${part}</span>`;
                } else {
                    breadcrumbHTML +=
                        ` › <a href="#" class="move-breadcrumb-link text-success" data-path="${fullPath}">${part}</a>`;
                }
            });

            $('#moveCurrentPath').html(breadcrumbHTML);

            // List subfolders
            res.subfolders.forEach(sub => {
                container.append(`
                    <div class="col-6 col-sm-4 col-md-3 text-center mb-3">
                        <div class="move-folder-item p-2 border rounded" data-path="${sub}" style="cursor:pointer;">
                            <i class="bi bi-folder-fill fs-2 d-block mb-1"></i>
                            <div class="small text-truncate">${sub.split('/').pop()}</div>
                        </div>
                    </div>
                `);
            });

            moveSelectedFolder = null;
            $('#confirmMoveBtn').prop('disabled', true);
        });
    }

    // -----------------------------
    // Move Folder Events
    // -----------------------------
    $(document).on('click', '.move-folder-item', function() {
        $('.move-folder-item').removeClass('bg-success text-white');
        $(this).addClass('bg-success text-white');
        moveSelectedFolder = $(this).data('path');
        $('#confirmMoveBtn').prop('disabled', false);
    });

    $(document).on('dblclick', '.move-folder-item', function() {
        const path = $(this).data('path');
        loadMoveFolder(path);
    });

    $(document).on('click', '.move-breadcrumb-link', function(e) {
        e.preventDefault();
        const path = $(this).data('path');
        loadMoveFolder(path);
    });

    // -----------------------------
    // Folder Navigation
    // -----------------------------
    $(document).on('dblclick', '.folder-item', function() {
        loadFolder($(this).data('path'));
        selectedItems = [];
        updateActionButtons();
    });

    $(document).on('click', '.breadcrumb-link', function(e) {
        e.preventDefault();
        loadFolder($(this).data('path'));
    });

    // -----------------------------
    // Selection Handling
    // -----------------------------
    function handleSelection(el, type, e) {
        const items = Array.from(document.querySelectorAll(`.${type}-item`));
        const index = items.indexOf(el);

        if (e.shiftKey && lastClickedIndex !== null) {
            const start = Math.min(index, lastClickedIndex);
            const end = Math.max(index, lastClickedIndex);
            selectedItems = [];
            items.forEach((item, i) => {
                if (i >= start && i <= end) {
                    item.classList.add('selected');
                    if (item.dataset.path) selectedItems.push(item.dataset.path);
                } else {
                    item.classList.remove('selected');
                }
            });
        } else if (e.ctrlKey || e.metaKey) {
            const path = el.dataset.path;
            if (!path) return;
            if (selectedItems.includes(path)) {
                selectedItems = selectedItems.filter(p => p !== path);
                el.classList.remove('selected');
            } else {
                selectedItems.push(path);
                el.classList.add('selected');
            }
        } else {
            selectedItems = [];
            items.forEach(item => item.classList.remove('selected'));
            if (el.dataset.path) {
                selectedItems.push(el.dataset.path);
                el.classList.add('selected');
            }
        }

        lastClickedIndex = index;
        updateActionButtons();
    }

    $(document).on('click', '.folder-item', function(e) {
        $('.folder-item').removeClass('active');
        $(this).addClass('active');
        handleSelection(this, 'folder', e);
    });

    // -----------------------------
    // Upload Events
    // -----------------------------
    uploadBtn.addEventListener('click', function() {
        if (this.disabled) {
            alert('⚠️ Please open a folder first.');
            return;
        }
        storageFileInput.click();
    });

    storageFileInput.addEventListener('change', function() {
        if (!this.files.length) return;

        const currentFolder = folderInput.value || '';
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('folder', currentFolder);

        Array.from(this.files).forEach((file, index) => {
            formData.append(`files[${index}]`, file);
            formData.append(`paths[${index}]`, file.webkitRelativePath);
        });

        const xhr = new XMLHttpRequest();
        xhr.open('POST', '{{ route('bulk.import.upload') }}', true);

        const loader = document.getElementById('uploadLoader');
        const percentText = document.getElementById('uploadPercent');
        loader.classList.remove('d-none');
        percentText.textContent = '0%';

        xhr.upload.onprogress = e => {
            if (e.lengthComputable) {
                const percent = Math.round((e.loaded / e.total) * 100);
                percentText.textContent = percent + '%';
            }
        };

        xhr.onload = function() {
            loader.classList.add('d-none');
            if (xhr.status === 200) {
                loadFolder(currentFolder);
                console.log('✅ Upload complete:', JSON.parse(xhr.responseText));
            } else {
                console.error('Server error during upload.');
            }
        };

        xhr.onerror = function() {
            loader.classList.add('d-none');
            console.error('Network error during upload.');
        };

        xhr.send(formData);
    });

    // -----------------------------
    // Rename
    // -----------------------------
    $('#btnRename').click(() => {
        if (selectedItems.length !== 1) return;

        const itemPath = selectedItems[0];
        const itemEl = $(`[data-path="${itemPath}"]`);
        const nameEl = itemEl.find('.folder-name, .text-truncate');
        if (nameEl.find('input').length > 0) return;

        const oldName = nameEl.text();
        nameEl.html(
            `<input type="text" class="form-control form-control-sm rename-input" value="${oldName}" style="width:100%">`
        );

        const input = nameEl.find('input');
        input.focus().select();

        function saveName() {
            const newName = input.val().trim();
            if (!newName || newName === oldName) {
                nameEl.text(oldName);
                return;
            }
            $.post('{{ route('bulk.import.rename') }}', {
                _token: '{{ csrf_token() }}',
                path: itemPath,
                newName: newName
            }, () => {
                nameEl.text(newName);
                selectedItems = [];
                updateActionButtons();
                loadFolder($('#breadcrumbPath').data('path') || '');
            });
        }

        input.on('blur', saveName);
        input.on('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                saveName();
            }
            if (e.key === 'Escape') {
                nameEl.text(oldName);
            }
        });
    });

    // -----------------------------
    // Move
    // -----------------------------
    $('#btnMove').click(() => {
        if (!selectedItems.length) return;
        const moveModal = new bootstrap.Modal(document.getElementById('moveModal'));
        moveModal.show();
        loadMoveFolder('');
    });

    $('#confirmMoveBtn').click(() => {
        if (!moveSelectedFolder) {
            alert('Please select a destination folder.');
            return;
        }

        $.post('{{ route('bulk.import.move') }}', {
            _token: '{{ csrf_token() }}',
            items: selectedItems,
            target: moveSelectedFolder
        }, (res) => {
            selectedItems.forEach(path => {
                $(`[data-path="${path}"]`).closest('.col-6, .col-sm-4, .col-md-3, .col-lg-2')
                    .remove();
            });
            selectedItems = [];
            updateActionButtons();
            loadFolder($('#breadcrumbPath').data('path') || '');

            const moveModal = bootstrap.Modal.getInstance(document.getElementById('moveModal'));
            moveModal.hide();
            console.log('Moved successfully:', res);
        }).fail(xhr => {
            console.error('Move failed:', xhr.responseText);
            alert('Failed to move items. Please try again.');
        });
    });

    // -----------------------------
    // Delete with Modal Confirmation
    // -----------------------------

    let itemsToDelete = [];

    $('#btnDelete').click(() => {
        if (!selectedItems.length) {
            alert('⚠️ Please select at least one item to delete.');
            return;
        }

        // Store selected items temporarily
        itemsToDelete = [...selectedItems];

        // Update modal message
        $('#confirmDeleteMessage').html(
            `<p>Are you sure you want to delete <strong>${itemsToDelete.length}</strong> item(s)?</p>
         <p class="text-danger small mb-0">This action cannot be undone.</p>`
        );

        // Show delete confirmation modal
        const deleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
        deleteModal.show();
    });

    // When user confirms delete in modal
    $('#confirmDeleteBtn').click(() => {
        if (!itemsToDelete.length) return;

        // Disable button to prevent double-click
        $('#confirmDeleteBtn').prop('disabled', true).text('Deleting...');

        $.ajax({
            url: '{{ route('bulk.import.delete') }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                items: itemsToDelete
            },
            success: function(res) {
                // Hide modal
                const deleteModalEl = document.getElementById('confirmDeleteModal');
                const deleteModal = bootstrap.Modal.getInstance(deleteModalEl);
                deleteModal.hide();

                // Re-enable button
                $('#confirmDeleteBtn').prop('disabled', false).text('Delete');

                if (res.success) {
                    const currentPath = $('#breadcrumbPath').data('path') || '';
                    loadFolder(currentPath);
                    selectedItems = [];
                    updateActionButtons();
                    showToast('Items deleted successfully.', 'success');
                } else {
                    showToast('Failed to delete items.', 'danger');
                }
            },
            error: function(xhr) {
                $('#confirmDeleteBtn').prop('disabled', false).text('Delete');
                showToast('Error while deleting items. Please try again.', 'danger');
                console.error('Delete failed:', xhr.responseText);
            }
        });
    });

    // Optional: helper toast function (for nice messages)
    function showToast(message, type = 'info') {
        const toast = $(`
        <div class="toast align-items-center text-white bg-${type} border-0 position-fixed bottom-0 end-0 m-3"
            role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="2500">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `);
        $('body').append(toast);
        new bootstrap.Toast(toast[0]).show();
        setTimeout(() => toast.remove(), 3000);
    }

    // -----------------------------
    // COPY
    // -----------------------------
    $('#btnCopy').click(() => {
        if (!selectedItems.length) {
            alert('⚠️ Please select at least one item to copy.');
            return;
        }

        clipboard.mode = 'copy';
        clipboard.items = [...selectedItems];

        showToast(`📋 ${selectedItems.length} item(s) copied.`, 'info');
        updateActionButtons();
    });
    // -----------------------------
    // PASTE
    // -----------------------------
    $('#btnPaste').click(() => {
        if (clipboard.items.length === 0) {
            alert('⚠️ Nothing to paste.');
            return;
        }

        // 👇 Get the current folder path from the breadcrumb
        const currentFolder = $('#breadcrumbPath').attr('data-path') || '';

        // Optional loader while copying
        const loader = $('#uploadLoader');
        const percentText = $('#uploadPercent');
        loader.removeClass('d-none');
        percentText.text('Copying...');

        $.post('{{ route('bulk.import.paste') }}', {
                _token: '{{ csrf_token() }}',
                mode: 'copy',
                items: clipboard.items,
                target: currentFolder
            })
            .done(res => {
                loader.addClass('d-none');
                showToast('✅ Items copied successfully.', 'success');

                // Reload the folder shown in breadcrumb
                loadFolder(currentFolder);
                updateActionButtons();
            })
            .fail(() => {
                loader.addClass('d-none');
                showToast('❌ Failed to paste items.', 'danger');
            });
    });
    // -----------------------------
    // New Folder
    // -----------------------------
    $('#btnNewFolderDropdown').click(() => {
        const folderContents = $('#folderContents');
        const currentFolder = $('#breadcrumbPath').attr('data-path') || '';

        const newFolderCol = $(`
        <div class="col-6 col-sm-4 col-md-3 col-lg-2 text-center mb-4">
            <div class="folder-item border p-2">
                <i class="bi bi-folder-fill fs-1 d-block mb-2"></i>
                <div class="folder-name small text-truncate">
                    <input type="text" class="form-control form-control-sm new-folder-input" value="New Folder" style="width:100%">
                </div>
            </div>
        </div>
    `);

        folderContents.prepend(newFolderCol);
        const input = newFolderCol.find('input');
        input.focus().select();

        function createFolderOnServer(name) {
            const folderPath = $('#breadcrumbPath').attr('data-path') || '';
            $.post('{{ route('bulk.import.createFolder') }}', {
                _token: '{{ csrf_token() }}',
                folder: folderPath,
                name: name
            }).done(res => {
                if (res.success) {
                    newFolderCol.find('.folder-item').attr('data-path', res.folder);
                    input.parent().text(res.folder.split('/').pop());
                    const currentFolder = $('#breadcrumbPath').attr('data-path') || '';
                    loadFolder(currentFolder);
                } else {
                    alert(res.message || 'Failed to create folder');
                    newFolderCol.remove();
                }
            }).fail(() => {
                alert('Error creating folder');
                newFolderCol.remove();
            });
        }

        function saveFolderName() {
            let folderName = input.val().trim();
            if (!folderName) folderName = 'New Folder';
            createFolderOnServer(folderName);
        }

        input.on('blur', saveFolderName);
        input.on('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                saveFolderName();
            }
            if (e.key === 'Escape') {
                newFolderCol.remove();
            }
        });
    });
</script>
