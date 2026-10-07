@extends('admin.layouts.app')

@section('title', 'Resources')
@section('page', 'resources')

@section('content')
<div class="page-head">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item active">Resources</li>
            </ol>
        </nav>
        <h1>Resources</h1>
    </div>
    <div class="d-flex gap-2 mt-3">
        <button type="button" class="btn btn-primary" onclick="openAddResourceModal()">
            <i class="bi bi-plus-circle me-2"></i>Add Resource
        </button>
    </div>
</div>

<section class="panel">
    <div class="table-responsive">
        <table class="table data-table align-middle w-100" id="resourcesTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>File Type</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($resources as $resource)
                <tr id="row-{{ $resource->id }}">
                    <td>{{ $resource->id }}</td>
                    <td>{{ $resource->title }}</td>
                    <td>
                        @if($resource->file_type)
                            <span class="badge bg-info text-dark text-uppercase">{{ $resource->file_type }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @if($resource->status)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </td>
                    <td>{{ $resource->created_at->format('M d, Y') }}</td>
                    <td>
                        <a href="{{ route('admin.resources.edit', $resource->id) }}" class="btn btn-sm btn-info text-white me-1" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <button class="btn btn-sm btn-danger" onclick="deleteResource({{ $resource->id }})" title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        {{ $resources->links('pagination::bootstrap-5') }}
    </div>
</section>

{{-- ============================================================ --}}
{{-- ADD RESOURCE MODAL                                          --}}
{{-- ============================================================ --}}
<div class="modal fade" id="addResourceModal" tabindex="-1" aria-labelledby="addResourceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white" style="border-bottom:none;">
                <h5 class="modal-title fw-bold" id="addResourceModalLabel">
                    <i class="bi bi-folder-plus me-2"></i>Add New Resource
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="addResourceForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">

                    {{-- Title --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Title *</label>
                        <input type="text" name="title" id="resourceTitle" class="form-control form-control-lg"
                               placeholder="Enter resource title" required>
                    </div>

                    {{-- File Upload --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">File</label>
                        <input type="file" name="file_upload" id="resourceFileUpload" class="form-control"
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.mp4,.mp3">
                        <small class="text-muted">Max 10MB &nbsp;|&nbsp; PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, ZIP, MP4, MP3</small>
                    </div>

                    {{-- Image --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Image</label>
                        <div id="modalImagePreviewContainer" class="mb-2 d-none">
                            <img src="" id="modalImagePreview" class="img-fluid rounded border" style="max-height:150px;" alt="Preview">
                        </div>
                        <input type="hidden" name="featured_image" id="modalFeaturedImageInput">
                        <button type="button" class="btn btn-outline-primary btn-sm" id="modalSetImageBtn" onclick="openModalImagePicker()">
                            <i class="bi bi-image me-1"></i> Select Image
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm ms-2 d-none" id="modalRemoveImageBtn" onclick="removeModalImage()">
                            Remove
                        </button>
                    </div>

                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-5" id="saveResourceBtn">
                        <i class="bi bi-save me-1"></i> Save Resource
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MEDIA LIBRARY MODAL (for image selection inside resource modal) --}}
{{-- ============================================================ --}}
<div class="modal fade" id="mediaLibraryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white" style="border-bottom: none;">
                <h5 class="modal-title fw-bold">Select Image</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" style="min-height: 500px;">
                <ul class="nav nav-tabs px-3 pt-3 border-bottom-0" id="mediaTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold border-0" id="upload-tab" data-bs-toggle="tab"
                                data-bs-target="#upload-panel" type="button" role="tab">Upload</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold border-0" id="library-tab" data-bs-toggle="tab"
                                data-bs-target="#library-panel" type="button" role="tab" onclick="loadMedia()">Media Library</button>
                    </li>
                </ul>
                <div class="tab-content border-top border-secondary-subtle p-4" style="min-height: 450px;">
                    <div class="tab-pane fade" id="upload-panel" role="tabpanel">
                        <div class="border border-2 border-dashed rounded text-center p-5 border-primary" id="uploadZone" style="cursor:pointer;">
                            <i class="bi bi-cloud-arrow-up fs-1 text-primary"></i>
                            <h5 class="mt-3 fw-bold">Drop image here or click to upload</h5>
                            <p class="text-muted small">JPG, PNG, WebP — Max 5MB</p>
                            <input type="file" id="mediaFileInput" class="d-none" accept="image/jpeg,image/png,image/gif,image/webp">
                        </div>
                        <div id="uploadProgress" class="progress mt-3 d-none" style="height: 8px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                 role="progressbar" style="width: 0%;"></div>
                        </div>
                    </div>
                    <div class="tab-pane fade show active" id="library-panel" role="tabpanel">
                        <div class="mb-3">
                            <input type="text" id="mediaSearchInput" class="form-control"
                                   placeholder="Search by title or keyword..." oninput="loadMedia(1)">
                        </div>
                        <div class="row g-2" id="mediaGrid">
                            <div class="col-12 text-center text-muted p-5">Loading media...</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 pb-4 px-4">
                <button type="button" class="btn btn-light px-4" id="mediaBackBtn">Back</button>
                <button type="button" class="btn btn-primary px-4" id="insertMediaBtn" disabled>Use Selected Image</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // ─── Open Add Resource Modal ──────────────────────────────────────────────
    function openAddResourceModal() {
        // Reset form
        document.getElementById('addResourceForm').reset();
        document.getElementById('modalImagePreviewContainer').classList.add('d-none');
        document.getElementById('modalImagePreview').src = '';
        document.getElementById('modalFeaturedImageInput').value = '';
        document.getElementById('modalSetImageBtn').classList.remove('d-none');
        document.getElementById('modalRemoveImageBtn').classList.add('d-none');

        const el = document.getElementById('addResourceModal');
        const modal = bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el);
        modal.show();
    }

    // ─── Add Resource Form Submit (AJAX) ──────────────────────────────────────
    document.getElementById('addResourceForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const btn = document.getElementById('saveResourceBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

        const formData = new FormData(this);

        fetch('{{ route("admin.resources.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Close modal
                bootstrap.Modal.getInstance(document.getElementById('addResourceModal')).hide();

                Swal.fire({
                    icon: 'success',
                    title: 'Saved!',
                    text: 'Resource added successfully.',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    // Reload the page to show new row in DataTable
                    window.location.reload();
                });
            } else {
                Swal.fire('Error', 'Could not save resource.', 'error');
            }
        })
        .catch(() => Swal.fire('Error', 'An error occurred.', 'error'))
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-save me-1"></i> Save Resource';
        });
    });

    // ─── Delete Resource ──────────────────────────────────────────────────────
    function deleteResource(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to delete this resource?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/resources/${id}`,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            $(`#row-${id}`).fadeOut();
                            Swal.fire('Deleted!', 'Resource has been deleted.', 'success');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Error deleting resource.', 'error');
                    }
                });
            }
        });
    }

    // ─── Image Picker for Resource Modal ─────────────────────────────────────
    let selectedMedia = null;

    function openModalImagePicker() {
        selectedMedia = null;
        document.getElementById('insertMediaBtn').disabled = true;
        if (document.getElementById('mediaSearchInput')) {
            document.getElementById('mediaSearchInput').value = '';
        }
        loadMedia();
        const el = document.getElementById('mediaLibraryModal');
        const modal = bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el);
        modal.show();
    }

    function removeModalImage() {
        document.getElementById('modalFeaturedImageInput').value = '';
        document.getElementById('modalImagePreview').src = '';
        document.getElementById('modalImagePreviewContainer').classList.add('d-none');
        document.getElementById('modalRemoveImageBtn').classList.add('d-none');
        document.getElementById('modalSetImageBtn').classList.remove('d-none');
    }

    // Back button returns to resource modal
    document.getElementById('mediaBackBtn').addEventListener('click', function () {
        bootstrap.Modal.getInstance(document.getElementById('mediaLibraryModal')).hide();
        const el = document.getElementById('addResourceModal');
        const modal = bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el);
        modal.show();
    });

    // Insert selected image
    document.getElementById('insertMediaBtn').addEventListener('click', function () {
        if (!selectedMedia) return;
        document.getElementById('modalFeaturedImageInput').value = selectedMedia;
        document.getElementById('modalImagePreview').src = selectedMedia;
        document.getElementById('modalImagePreviewContainer').classList.remove('d-none');
        document.getElementById('modalSetImageBtn').classList.add('d-none');
        document.getElementById('modalRemoveImageBtn').classList.remove('d-none');

        bootstrap.Modal.getInstance(document.getElementById('mediaLibraryModal')).hide();
        // Return to resource modal
        const el = document.getElementById('addResourceModal');
        const modal = bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el);
        modal.show();

        selectedMedia = null;
        document.getElementById('insertMediaBtn').disabled = true;
    });

    // ─── Media Library ────────────────────────────────────────────────────────
    const uploadZone = document.getElementById('uploadZone');
    const mediaFileInput = document.getElementById('mediaFileInput');

    uploadZone.addEventListener('click', () => mediaFileInput.click());
    uploadZone.addEventListener('dragover', e => { e.preventDefault(); uploadZone.classList.add('bg-light'); });
    uploadZone.addEventListener('dragleave', e => { e.preventDefault(); uploadZone.classList.remove('bg-light'); });
    uploadZone.addEventListener('drop', e => {
        e.preventDefault();
        uploadZone.classList.remove('bg-light');
        if (e.dataTransfer.files.length) uploadFile(e.dataTransfer.files[0]);
    });
    mediaFileInput.addEventListener('change', function () {
        if (this.files.length) uploadFile(this.files[0]);
    });

    function uploadFile(file) {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('_token', '{{ csrf_token() }}');
        const progressBar = document.querySelector('#uploadProgress .progress-bar');
        document.getElementById('uploadProgress').classList.remove('d-none');
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '{{ route("admin.media.store") }}', true);
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.upload.onprogress = e => {
            if (e.lengthComputable) progressBar.style.width = ((e.loaded / e.total) * 100) + '%';
        };
        xhr.onload = function () {
            if (xhr.status === 200) {
                document.getElementById('library-tab').click();
                loadMedia();
                Swal.fire({ icon: 'success', title: 'Uploaded!', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
            } else {
                Swal.fire('Upload Failed', 'Could not upload file.', 'error');
            }
            document.getElementById('uploadProgress').classList.add('d-none');
            progressBar.style.width = '0%';
        };
        xhr.send(formData);
    }

    function loadMedia(page = 1) {
        const search = document.getElementById('mediaSearchInput')?.value || '';
        fetch(`{{ route("admin.media.index") }}?page=${page}&search=${encodeURIComponent(search)}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            const grid = document.getElementById('mediaGrid');
            grid.innerHTML = '';
            if (data.data.length === 0) {
                grid.innerHTML = '<div class="col-12 text-center text-muted p-5">No media found. Upload some files!</div>';
                return;
            }
            data.data.forEach(media => {
                const url = '/storage/' + media.path;
                grid.innerHTML += `
                    <div class="col-md-2 col-sm-3 col-4 mb-2">
                        <div class="card h-100 media-item border-2" data-url="${url}" style="cursor:pointer;" onclick="selectMedia(this)">
                            <img src="${url}" class="card-img-top object-fit-cover" style="height:100px;" alt="">
                        </div>
                    </div>`;
            });
        });
    }

    function selectMedia(element) {
        document.querySelectorAll('.media-item').forEach(el => el.classList.remove('border-primary'));
        element.classList.add('border-primary');
        selectedMedia = element.dataset.url;
        document.getElementById('insertMediaBtn').disabled = false;
    }
</script>
@endpush
