@extends('admin.layouts.app')

@section('title', 'Add New Resource')
@section('page', 'resources')

@section('content')
<div class="page-head">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.resources.index') }}">Resources</a></li>
                <li class="breadcrumb-item active">Add New</li>
            </ol>
        </nav>
        <h1>Add New Resource</h1>
    </div>
</div>

<form action="{{ route('admin.resources.store') }}" method="POST" id="resourceForm" enctype="multipart/form-data">
    @csrf
    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10">
            <div class="panel mb-4">

                {{-- Title --}}
                <div class="mb-4">
                    <label class="form-label fw-bold">Title *</label>
                    <input type="text" name="title" id="title" class="form-control form-control-lg" placeholder="Enter resource title" required>
                </div>

                {{-- File Upload --}}
                <div class="mb-4">
                    <label class="form-label fw-bold">File *</label>
                    <input type="file" name="file_upload" id="file_upload" class="form-control"
                           accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.mp4,.mp3">
                    <small class="text-muted">Max size: 10MB &nbsp;|&nbsp; Accepted: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, ZIP, MP4, MP3</small>
                </div>

                {{-- Image --}}
                <div class="mb-4">
                    <label class="form-label fw-bold">Image</label>
                    <div id="imagePreviewContainer" class="mb-2 d-none">
                        <img src="" id="imagePreview" class="img-fluid rounded border" style="max-height: 200px;" alt="Preview">
                    </div>
                    <input type="hidden" name="featured_image" id="featuredImageInput">
                    <button type="button" class="btn btn-outline-primary" id="setImageBtn" onclick="openImageModal()">
                        <i class="bi bi-image me-1"></i> Select Image
                    </button>
                    <button type="button" class="btn btn-outline-danger ms-2 d-none" id="removeImageBtn" onclick="removeImage()">
                        Remove
                    </button>
                </div>

                {{-- Actions --}}
                <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                    <a href="{{ route('admin.resources.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary px-5">
                        <i class="bi bi-save me-1"></i> Save Resource
                    </button>
                </div>

            </div>
        </div>
    </div>
</form>

{{-- Media Library Modal --}}
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
                        <button class="nav-link fw-bold border-0" id="upload-tab" data-bs-toggle="tab" data-bs-target="#upload-panel" type="button" role="tab">Upload</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold border-0" id="library-tab" data-bs-toggle="tab" data-bs-target="#library-panel" type="button" role="tab" onclick="loadMedia()">Media Library</button>
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
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%;"></div>
                        </div>
                    </div>
                    <div class="tab-pane fade show active" id="library-panel" role="tabpanel">
                        <div class="mb-3">
                            <input type="text" id="mediaSearchInput" class="form-control" placeholder="Search by title or keyword..." oninput="loadMedia(1)">
                        </div>
                        <div class="row g-2" id="mediaGrid">
                            <div class="col-12 text-center text-muted p-5">Loading media...</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 pb-4 px-4">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary px-4" id="insertMediaBtn" disabled>Use Selected Image</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let selectedMedia = null;

    function openImageModal() {
        selectedMedia = null;
        document.getElementById('insertMediaBtn').disabled = true;
        loadMedia();
        (bootstrap.Modal.getInstance(document.getElementById('mediaLibraryModal')) || new bootstrap.Modal(document.getElementById('mediaLibraryModal'))).show();
    }

    function removeImage() {
        document.getElementById('featuredImageInput').value = '';
        document.getElementById('imagePreview').src = '';
        document.getElementById('imagePreviewContainer').classList.add('d-none');
        document.getElementById('removeImageBtn').classList.add('d-none');
        document.getElementById('setImageBtn').classList.remove('d-none');
    }

    // Form submit
    document.getElementById('resourceForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire({ icon: 'success', title: 'Saved!', text: 'Resource saved successfully.', timer: 1500, showConfirmButton: false })
                .then(() => window.location.href = data.redirect || '{{ route("admin.resources.index") }}');
            } else {
                Swal.fire('Error', 'Could not save resource.', 'error');
            }
        })
        .catch(() => Swal.fire('Error', 'An error occurred.', 'error'));
    });

    // Media Library
    const uploadZone = document.getElementById('uploadZone');
    const mediaFileInput = document.getElementById('mediaFileInput');
    uploadZone.addEventListener('click', () => mediaFileInput.click());
    uploadZone.addEventListener('dragover', e => { e.preventDefault(); uploadZone.classList.add('bg-light'); });
    uploadZone.addEventListener('dragleave', e => { e.preventDefault(); uploadZone.classList.remove('bg-light'); });
    uploadZone.addEventListener('drop', e => { e.preventDefault(); uploadZone.classList.remove('bg-light'); if (e.dataTransfer.files.length) uploadFile(e.dataTransfer.files[0]); });
    mediaFileInput.addEventListener('change', function () { if (this.files.length) uploadFile(this.files[0]); });

    function uploadFile(file) {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('_token', '{{ csrf_token() }}');
        const progressBar = document.querySelector('#uploadProgress .progress-bar');
        document.getElementById('uploadProgress').classList.remove('d-none');
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '{{ route("admin.media.store") }}', true);
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.upload.onprogress = e => { if (e.lengthComputable) progressBar.style.width = ((e.loaded / e.total) * 100) + '%'; };
        xhr.onload = function () {
            if (xhr.status === 200) { document.getElementById('library-tab').click(); loadMedia(); }
            else Swal.fire('Upload Failed', 'Could not upload file.', 'error');
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

    document.getElementById('insertMediaBtn').addEventListener('click', function () {
        if (!selectedMedia) return;
        document.getElementById('featuredImageInput').value = selectedMedia;
        document.getElementById('imagePreview').src = selectedMedia;
        document.getElementById('imagePreviewContainer').classList.remove('d-none');
        document.getElementById('setImageBtn').classList.add('d-none');
        document.getElementById('removeImageBtn').classList.remove('d-none');
        bootstrap.Modal.getInstance(document.getElementById('mediaLibraryModal')).hide();
        selectedMedia = null;
        document.getElementById('insertMediaBtn').disabled = true;
    });
</script>
@endpush
