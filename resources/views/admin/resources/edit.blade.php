@extends('admin.layouts.app')

@section('title', 'Edit Resource')
@section('page', 'resources')

@section('content')
<div class="page-head">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.resources.index') }}">Resources</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
        <h1>Edit Resource: {{ $resource->title }}</h1>
    </div>
</div>

<form action="{{ route('admin.resources.update', $resource->id) }}" method="POST" id="resourceForm">
    @csrf
    @method('PUT')
    <div class="row">
        <!-- Main Content Column -->
        <div class="col-xl-9 col-lg-8">

            <!-- Basic Info Panel -->
            <div class="panel mb-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">Title *</label>
                    <input type="text" name="title" id="title" class="form-control form-control-lg" value="{{ $resource->title }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Slug *</label>
                    <input type="text" name="slug" id="slug" class="form-control" value="{{ $resource->slug }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Short Description</label>
                    <textarea name="short_description" class="form-control" rows="2">{{ $resource->short_description }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Full Description</label>
                    <input type="hidden" name="description" id="resourceDescriptionInput">
                    <div id="resourceEditor" style="height: 300px; font-size: 16px;">{!! $resource->description !!}</div>
                </div>
            </div>

            <!-- File Attachment Panel -->
            <div class="panel mb-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-paperclip me-2"></i>File Attachment</h5>
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label">File URL / Path</label>
                        <input type="text" name="file_path" class="form-control" value="{{ $resource->file }}" placeholder="e.g. /storage/files/document.pdf">
                        <small class="text-muted">Enter the file path or URL to the downloadable file</small>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">File Type</label>
                        <select name="file_type" class="form-select">
                            <option value="">— Select —</option>
                            @foreach(['pdf','doc','docx','xls','xlsx','ppt','pptx','zip','mp4','mp3','other'] as $type)
                                <option value="{{ $type }}" {{ $resource->file_type === $type ? 'selected' : '' }}>{{ strtoupper($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- SEO Panel -->
            <div class="panel mb-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-search me-2"></i>SEO Settings</h5>
                <div class="mb-3">
                    <label class="form-label">Meta Title</label>
                    <input type="text" name="meta_title" class="form-control" value="{{ $resource->meta_title }}" placeholder="Leave blank to use resource title">
                </div>
                <div class="mb-3">
                    <label class="form-label">Meta Description</label>
                    <textarea name="meta_description" class="form-control" rows="2">{{ $resource->meta_description }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Meta Keywords</label>
                    <input type="text" name="meta_keywords" class="form-control" value="{{ $resource->meta_keywords }}" placeholder="e.g. agriculture, farming, guide">
                    <small class="text-muted">Separate keywords with commas</small>
                </div>
            </div>

        </div>

        <!-- Sidebar Column -->
        <div class="col-xl-3 col-lg-4">

            <!-- Publish Panel -->
            <div class="panel mb-4">
                <h6 class="fw-bold mb-3 border-bottom pb-2">Publish</h6>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="1" {{ $resource->status ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ !$resource->status ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-outline-secondary" onclick="saveAsInactive()">Save Inactive</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </div>

            <!-- Featured Image Panel -->
            <div class="panel mb-4">
                <h6 class="fw-bold mb-3 border-bottom pb-2">Featured Image</h6>
                <div class="text-center">
                    <div id="featuredImagePreviewContainer" class="mb-3 {{ $resource->featured_image ? '' : 'd-none' }}">
                        <img src="{{ $resource->featured_image }}" id="featuredImagePreview" class="img-fluid rounded border" alt="Featured Image">
                    </div>
                    <input type="hidden" name="featured_image" id="featuredImageInput" value="{{ $resource->featured_image }}">
                    <button type="button" class="btn btn-outline-primary w-100 {{ $resource->featured_image ? 'd-none' : '' }}" id="setFeaturedImageBtn" onclick="openFeaturedImageModal()">
                        <i class="bi bi-image me-1"></i> Set Featured Image
                    </button>
                    <button type="button" class="btn btn-outline-danger w-100 mt-2 {{ $resource->featured_image ? '' : 'd-none' }}" id="removeFeaturedImageBtn" onclick="removeFeaturedImage()">
                        Remove Image
                    </button>
                </div>
            </div>

        </div>
    </div>
</form>

<!-- Media Library Modal -->
<div class="modal fade" id="mediaLibraryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white" style="border-bottom: none;">
                <h5 class="modal-title fw-bold">Media Library</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" style="min-height: 500px;">
                <ul class="nav nav-tabs px-3 pt-3 border-bottom-0" id="mediaTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold border-0" id="upload-tab" data-bs-toggle="tab" data-bs-target="#upload-panel" type="button" role="tab">Upload Files</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold border-0" id="library-tab" data-bs-toggle="tab" data-bs-target="#library-panel" type="button" role="tab" onclick="loadMedia()">Media Library</button>
                    </li>
                </ul>
                <div class="tab-content border-top border-secondary-subtle" id="mediaTabsContent" style="padding: 20px; min-height: 450px;">
                    <div class="tab-pane fade" id="upload-panel" role="tabpanel">
                        <div class="border border-2 border-dashed rounded text-center p-5 border-primary" id="uploadZone" style="cursor:pointer;">
                            <i class="bi bi-cloud-arrow-up fs-1 text-primary"></i>
                            <h4 class="mt-3 text-dark fw-bold">Drop files here or click to upload</h4>
                            <p class="text-muted">Maximum upload file size: 5 MB.</p>
                            <input type="file" id="mediaFileInput" class="d-none" accept="image/jpeg,image/png,image/gif,image/webp">
                        </div>
                        <div id="uploadProgress" class="progress mt-3 d-none" style="height: 10px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%;"></div>
                        </div>
                    </div>
                    <div class="tab-pane fade show active" id="library-panel" role="tabpanel">
                        <div class="row g-2" id="mediaGrid">
                            <div class="col-12 text-center text-muted p-5">Loading media...</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-3 pb-4 px-4">
                <input type="hidden" id="mediaTarget" value="">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary px-4" id="insertMediaBtn" disabled>Insert Selected Image</button>
            </div>
        </div>
    </div>
</div>

<!-- Quill Editor -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {

        // Auto-generate slug from title
        document.getElementById('title').addEventListener('input', function () {
            let slug = this.value.toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/[\s_-]+/g, '-')
                .replace(/^-+|-+$/g, '');
            document.getElementById('slug').value = slug;
        });

        // Initialize Quill
        window.quill = new Quill('#resourceEditor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'align': [] }],
                    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                    ['link', 'code-block'],
                    ['clean']
                ]
            }
        });

        // AJAX form submit
        document.getElementById('resourceForm').addEventListener('submit', function (e) {
            e.preventDefault();
            document.getElementById('resourceDescriptionInput').value = window.quill.root.innerHTML;

            const formData = new FormData(this);
            fetch(this.action, {
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
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Resource updated successfully!',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = '{{ route("admin.resources.index") }}';
                    });
                } else {
                    Swal.fire('Error', 'Error updating resource.', 'error');
                }
            })
            .catch(() => Swal.fire('Error', 'An error occurred.', 'error'));
        });
    });

    function saveAsInactive() {
        document.querySelector('select[name="status"]').value = '0';
        document.getElementById('resourceForm').dispatchEvent(new Event('submit'));
    }

    function openFeaturedImageModal() {
        document.getElementById('mediaTarget').value = 'featured';
        loadMedia();
        new bootstrap.Modal(document.getElementById('mediaLibraryModal')).show();
    }

    function removeFeaturedImage() {
        document.getElementById('featuredImageInput').value = '';
        document.getElementById('featuredImagePreview').src = '';
        document.getElementById('featuredImagePreviewContainer').classList.add('d-none');
        document.getElementById('removeFeaturedImageBtn').classList.add('d-none');
        document.getElementById('setFeaturedImageBtn').classList.remove('d-none');
    }

    let selectedMedia = null;

    const uploadZone = document.getElementById('uploadZone');
    const mediaFileInput = document.getElementById('mediaFileInput');
    uploadZone.addEventListener('click', () => mediaFileInput.click());
    uploadZone.addEventListener('dragover', e => { e.preventDefault(); uploadZone.classList.add('bg-secondary', 'text-white'); });
    uploadZone.addEventListener('dragleave', e => { e.preventDefault(); uploadZone.classList.remove('bg-secondary', 'text-white'); });
    uploadZone.addEventListener('drop', e => {
        e.preventDefault();
        uploadZone.classList.remove('bg-secondary', 'text-white');
        if (e.dataTransfer.files.length) uploadFile(e.dataTransfer.files[0]);
    });
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
            else Swal.fire('Upload Failed', xhr.responseText, 'error');
            document.getElementById('uploadProgress').classList.add('d-none');
            progressBar.style.width = '0%';
        };
        xhr.send(formData);
    }

    function loadMedia(page = 1) {
        fetch(`{{ route("admin.media.index") }}?page=${page}`, {
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
                        <div class="card h-100 media-item" data-url="${url}" data-id="${media.id}" data-alt="${media.alt_text}" style="cursor:pointer;" onclick="selectMedia(this)">
                            <img src="${url}" class="card-img-top object-fit-cover" style="height: 100px;" alt="${media.alt_text}">
                        </div>
                    </div>`;
            });
        });
    }

    function selectMedia(element) {
        document.querySelectorAll('.media-item').forEach(el => el.classList.remove('border-primary', 'border-3'));
        element.classList.add('border-primary', 'border-3');
        selectedMedia = { url: element.dataset.url, id: element.dataset.id, alt: element.dataset.alt };
        document.getElementById('insertMediaBtn').disabled = false;
    }

    document.getElementById('insertMediaBtn').addEventListener('click', function () {
        if (!selectedMedia) return;
        document.getElementById('featuredImageInput').value = selectedMedia.url;
        document.getElementById('featuredImagePreview').src = selectedMedia.url;
        document.getElementById('featuredImagePreviewContainer').classList.remove('d-none');
        document.getElementById('setFeaturedImageBtn').classList.add('d-none');
        document.getElementById('removeFeaturedImageBtn').classList.remove('d-none');
        bootstrap.Modal.getInstance(document.getElementById('mediaLibraryModal')).hide();
        selectedMedia = null;
        document.getElementById('insertMediaBtn').disabled = true;
    });
</script>
@endpush
