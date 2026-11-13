@extends('admin.layouts.app')

@section('title', 'Add New Project')
@section('page', 'projects.create')

@section('content')
<div class="page-head">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.projects.index') }}">Projects</a></li>
                <li class="breadcrumb-item active">Add New</li>
            </ol>
        </nav>
        <h1>Add New Project</h1>
    </div>
</div>

<form action="{{ route('admin.projects.store') }}" method="POST" id="projectForm">
    @csrf
    <div class="row">
        <!-- Main Content Column -->
        <div class="col-xl-9 col-lg-8">
            {{-- Basic Overview --}}
            <div class="panel mb-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-info-circle me-2"></i>Project Overview</h5>

                <div class="mb-3">
                    <label class="form-label fw-bold">Project Title *</label>
                    <input type="text" name="title" id="title" class="form-control form-control-lg" placeholder="e.g. Sharma Floriculture Farm" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Slug *</label>
                        <input type="text" name="slug" id="slug" class="form-control" placeholder="auto-generated-slug" required>
                        <small class="text-muted">Unique URL identifier</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Subtitle / Eyebrow</label>
                        <input type="text" name="subtitle" class="form-control" placeholder="e.g. Case Study / Protected Farming">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Excerpt / Banner Summary</label>
                    <textarea name="excerpt" class="form-control" rows="3" placeholder="A short 1-2 sentence description displayed in the hero banner..."></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">About This Project (Content)</label>
                    <button type="button" class="btn btn-sm btn-outline-primary mb-2 d-block" onclick="openEditorImageModal()">
                        <i class="bi bi-image me-1"></i> Insert Image from Media Library
                    </button>
                    <textarea name="content" id="projectEditor" class="form-control" placeholder="Detailed project case study narrative..."></textarea>
                </div>
            </div>

            {{-- Project Facts & Specifications --}}
            <div class="panel mb-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-clipboard-data me-2"></i>Project Facts & Specifications</h5>
                <p class="text-muted small">These specifications appear in the "Project Facts" sidebar card on the case study page.</p>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Farm / Client Name</label>
                        <input type="text" name="client_name" class="form-control" placeholder="e.g. Sharma Floriculture Farm">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Location</label>
                        <input type="text" name="location" class="form-control" placeholder="e.g. Indore, Madhya Pradesh">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Crop</label>
                        <input type="text" name="crop" class="form-control" placeholder="e.g. Gerbera, Bell Pepper, Strawberry">
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-bold mb-0">Project Category / Structure *</label>
                            <a href="{{ route('admin.project-categories.index') }}" target="_blank" class="small text-decoration-none">
                                <i class="bi bi-plus-circle me-1"></i>Manage Categories
                            </a>
                        </div>
                        <select name="project_category_id" id="projectCategorySelect" class="form-select" required>
                            <option value="">-- Select Category / Structure --</option>
                            @foreach($categories ?? [] as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Select the structure type category</small>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Area</label>
                        <input type="text" name="area" class="form-control" placeholder="e.g. 4,000 sq. m. or 2 Acres">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Project Status Badge</label>
                        <select name="project_status" class="form-select">
                            <option value="Completed" selected>Completed</option>
                            <option value="Ongoing">Ongoing</option>
                            <option value="Upcoming">Upcoming</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Completed On</label>
                        <input type="date" name="completed_at" class="form-control">
                    </div>
                </div>
            </div>

            {{-- Photo Gallery --}}
            <div class="panel mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold mb-1"><i class="bi bi-images me-2"></i>Photo Gallery</h5>
                        <p class="text-muted small mb-0">Add multiple photos for the interactive switcher on the project details page.</p>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="openGalleryImageModal()">
                        <i class="bi bi-plus-circle me-1"></i> Add Gallery Image
                    </button>
                </div>

                <div class="row g-2" id="galleryContainer">
                    {{-- Gallery images added via modal will appear here --}}
                </div>
                <div id="emptyGalleryNotice" class="text-center py-4 text-muted border border-dashed rounded">
                    <i class="bi bi-images fs-2 d-block mb-1"></i>
                    No gallery images added yet. Click "+ Add Gallery Image" to select from media.
                </div>
            </div>

            {{-- Custom Contact & CTA Overrides --}}
            <div class="panel mb-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-telephone me-2"></i>Custom CTA & Contact (Optional)</h5>
                <p class="text-muted small">Leave blank to use the site-wide contact info.</p>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Contact Phone</label>
                        <input type="text" name="contact_phone" class="form-control" placeholder="e.g. +91 7879183931">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">WhatsApp Number</label>
                        <input type="text" name="contact_whatsapp" class="form-control" placeholder="e.g. 917879183931 (with country code)">
                    </div>
                </div>
            </div>

            {{-- SEO Panel --}}
            <div class="panel mb-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-search me-2"></i>SEO Settings</h5>
                <div class="mb-3">
                    <label class="form-label">Meta Title</label>
                    <input type="text" name="meta_title" class="form-control" placeholder="SEO page title">
                </div>
                <div class="mb-3">
                    <label class="form-label">Meta Description</label>
                    <textarea name="meta_description" class="form-control" rows="2" placeholder="SEO description..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Meta Keywords</label>
                    <input type="text" name="meta_keywords" class="form-control" placeholder="e.g. floriculture, gerbera polyhouse, protected farming indore">
                    <small class="text-muted">Separate keywords with commas</small>
                </div>
            </div>
        </div>

        <!-- Sidebar Column -->
        <div class="col-xl-3 col-lg-4">
            {{-- Publish Panel --}}
            <div class="panel mb-4">
                <h6 class="fw-bold mb-3 border-bottom pb-2">Publish</h6>
                <div class="mb-3">
                    <label class="form-label fw-bold">Publication Status</label>
                    <select name="status" class="form-select">
                        <option value="1" selected>Published</option>
                        <option value="0">Draft</option>
                    </select>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="isFeaturedSwitch">
                    <label class="form-check-label fw-bold" for="isFeaturedSwitch">Featured Project</label>
                    <small class="text-muted d-block">Show on homepage or top of list</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Display Order</label>
                    <input type="number" name="sort_order" class="form-control" value="0" min="0">
                    <small class="text-muted">Lower numbers show first</small>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-outline-secondary" onclick="saveAsDraft()">Save Draft</button>
                    <button type="submit" class="btn btn-primary">Save Project</button>
                </div>
            </div>

            {{-- Featured Image (Cover Photo) --}}
            <div class="panel mb-4">
                <h6 class="fw-bold mb-3 border-bottom pb-2">Featured Image (Cover)</h6>
                <div id="featuredImagePreviewContainer" class="mb-3 d-none">
                    <img src="" id="featuredImagePreview" class="img-fluid rounded border w-100 object-fit-cover" style="max-height: 180px;" alt="Cover Preview">
                </div>
                <input type="hidden" name="featured_image" id="featuredImageInput">
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-primary" id="setFeaturedImageBtn" onclick="openFeaturedImageModal()">
                        <i class="bi bi-image me-1"></i> Select Cover Image
                    </button>
                    <button type="button" class="btn btn-outline-danger d-none" id="removeFeaturedImageBtn" onclick="removeFeaturedImage()">
                        <i class="bi bi-trash me-1"></i> Remove Cover
                    </button>
                </div>
                <small class="text-muted d-block mt-2">Used as the main thumbnail and primary view in the gallery.</small>
            </div>

            {{-- Hero Banner Background Image --}}
            <div class="panel mb-4">
                <h6 class="fw-bold mb-3 border-bottom pb-2">Hero Banner Background</h6>
                <div id="bannerImagePreviewContainer" class="mb-3 d-none">
                    <img src="" id="bannerImagePreview" class="img-fluid rounded border w-100 object-fit-cover" style="max-height: 140px;" alt="Banner Preview">
                </div>
                <input type="hidden" name="banner_image" id="bannerImageInput">
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-primary" id="setBannerImageBtn" onclick="openBannerImageModal()">
                        <i class="bi bi-image me-1"></i> Select Banner Image
                    </button>
                    <button type="button" class="btn btn-outline-danger d-none" id="removeBannerImageBtn" onclick="removeBannerImage()">
                        <i class="bi bi-trash me-1"></i> Remove Banner
                    </button>
                </div>
                <small class="text-muted d-block mt-2">Background image for the top case study hero header.</small>
            </div>
        </div>
    </div>
</form>

{{-- Media Library Modal --}}
<div class="modal fade" id="mediaLibraryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white" style="border-bottom: none;">
                <h5 class="modal-title fw-bold" id="mediaModalTitle">Select Image</h5>
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
                <input type="hidden" id="mediaTarget" value="">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary px-4" id="insertMediaBtn" disabled>Use Selected Image</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-generate slug from title
        const titleInput = document.getElementById('title');
        const slugInput = document.getElementById('slug');

        titleInput.addEventListener('input', function() {
            const slug = this.value
                .toLowerCase()
                .trim()
                .replace(/[^\w\s-]/g, '')
                .replace(/[\s_-]+/g, '-')
                .replace(/^-+|-+$/g, '');
            slugInput.value = slug;
        });

        // Initialize CKEditor 5
        ClassicEditor
            .create(document.querySelector('#projectEditor'), {
                toolbar: [
                    'heading', '|', 'bold', 'italic', 'link', '|',
                    'bulletedList', 'numberedList', 'outdent', 'indent', '|',
                    'blockQuote', 'insertTable', '|',
                    'undo', 'redo'
                ]
            })
            .then(editor => {
                window.projectEditor = editor;
            })
            .catch(error => console.error('CKEditor error:', error));

        // Form submission via AJAX
        const projectForm = document.getElementById('projectForm');
        projectForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (window.projectEditor) {
                window.projectEditor.updateSourceElement();
            }

            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;

            fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json().then(data => ({ ok: res.ok, data })))
            .then(({ ok, data }) => {
                if (ok && data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: data.message || 'Project saved successfully!',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = data.redirect || '{{ route("admin.projects.index") }}';
                    });
                } else {
                    const msg = data.errors
                        ? Object.values(data.errors).flat().join('\n')
                        : (data.message || 'Error saving project.');
                    Swal.fire({ icon: 'error', title: 'Error', text: msg });
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'An unexpected error occurred.', 'error');
                submitBtn.disabled = false;
            });
        });
    });

    function saveAsDraft() {
        document.querySelector('select[name="status"]').value = '0';
        document.getElementById('projectForm').dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    }

    // Media Modal handling
    let selectedMedia = null;

    function openFeaturedImageModal() {
        document.getElementById('mediaTarget').value = 'featured';
        document.getElementById('mediaModalTitle').textContent = 'Select Cover Image';
        openMediaModal();
    }

    function openBannerImageModal() {
        document.getElementById('mediaTarget').value = 'banner';
        document.getElementById('mediaModalTitle').textContent = 'Select Banner Image';
        openMediaModal();
    }

    function openGalleryImageModal() {
        document.getElementById('mediaTarget').value = 'gallery';
        document.getElementById('mediaModalTitle').textContent = 'Select Image for Gallery';
        openMediaModal();
    }

    function openEditorImageModal() {
        document.getElementById('mediaTarget').value = 'ckeditor';
        document.getElementById('mediaModalTitle').textContent = 'Insert Image into Content';
        openMediaModal();
    }

    function openMediaModal() {
        selectedMedia = null;
        document.getElementById('insertMediaBtn').disabled = true;
        loadMedia();
        (bootstrap.Modal.getInstance(document.getElementById('mediaLibraryModal')) || new bootstrap.Modal(document.getElementById('mediaLibraryModal'))).show();
    }

    function removeFeaturedImage() {
        document.getElementById('featuredImageInput').value = '';
        document.getElementById('featuredImagePreview').src = '';
        document.getElementById('featuredImagePreviewContainer').classList.add('d-none');
        document.getElementById('removeFeaturedImageBtn').classList.add('d-none');
        document.getElementById('setFeaturedImageBtn').classList.remove('d-none');
    }

    function removeBannerImage() {
        document.getElementById('bannerImageInput').value = '';
        document.getElementById('bannerImagePreview').src = '';
        document.getElementById('bannerImagePreviewContainer').classList.add('d-none');
        document.getElementById('removeBannerImageBtn').classList.add('d-none');
        document.getElementById('setBannerImageBtn').classList.remove('d-none');
    }

    function addGalleryImageToGrid(url) {
        const container = document.getElementById('galleryContainer');
        const emptyNotice = document.getElementById('emptyGalleryNotice');
        if (emptyNotice) emptyNotice.classList.add('d-none');

        const col = document.createElement('div');
        col.className = 'col-md-3 col-sm-4 col-6 gallery-item position-relative mb-2';
        col.innerHTML = `
            <div class="card h-100 border shadow-sm overflow-hidden">
                <img src="${url}" class="card-img-top object-fit-cover" style="height: 110px;" alt="Gallery Image">
                <input type="hidden" name="gallery_images[]" value="${url}">
                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 rounded-circle p-0 d-flex align-items-center justify-content-center shadow" style="width:24px; height:24px;" onclick="removeGalleryItem(this)" title="Remove">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        `;
        container.appendChild(col);
    }

    function removeGalleryItem(btn) {
        const item = btn.closest('.gallery-item');
        if (item) {
            item.remove();
        }
        const remaining = document.querySelectorAll('#galleryContainer .gallery-item');
        if (remaining.length === 0) {
            const emptyNotice = document.getElementById('emptyGalleryNotice');
            if (emptyNotice) emptyNotice.classList.remove('d-none');
        }
    }

    // Media library logic
    const uploadZone = document.getElementById('uploadZone');
    const mediaFileInput = document.getElementById('mediaFileInput');

    uploadZone.addEventListener('click', () => mediaFileInput.click());
    uploadZone.addEventListener('dragover', (e) => { e.preventDefault(); uploadZone.classList.add('bg-secondary', 'text-white'); });
    uploadZone.addEventListener('dragleave', (e) => { e.preventDefault(); uploadZone.classList.remove('bg-secondary', 'text-white'); });
    uploadZone.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadZone.classList.remove('bg-secondary', 'text-white');
        if (e.dataTransfer.files.length) uploadFile(e.dataTransfer.files[0]);
    });

    mediaFileInput.addEventListener('change', function() {
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

        xhr.upload.onprogress = function(e) {
            if (e.lengthComputable) progressBar.style.width = ((e.loaded / e.total) * 100) + '%';
        };

        xhr.onload = function() {
            if (xhr.status === 200) {
                document.getElementById('library-tab').click();
                loadMedia();
                Swal.fire({ icon: 'success', title: 'Uploaded!', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
            } else {
                const errText = xhr.responseText.length < 300 ? xhr.responseText : 'Server error. Please check the logs.';
                Swal.fire({ icon: 'error', title: 'Upload Failed', text: errText });
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
            if (!data.data || data.data.length === 0) {
                grid.innerHTML = '<div class="col-12 text-center text-muted p-5">No media found. Upload some files!</div>';
                return;
            }
            data.data.forEach(media => {
                const url = '/storage/' + media.path;
                grid.innerHTML += `
                    <div class="col-md-2 col-sm-3 col-4 mb-2">
                        <div class="card h-100 media-item" data-url="${esc(url)}" data-id="${esc(media.id)}" data-alt="${esc(media.alt_text)}" style="cursor:pointer;" onclick="selectMedia(this)">
                            <img src="${esc(url)}" class="card-img-top object-fit-cover" style="height: 100px;" alt="${esc(media.alt_text)}">
                        </div>
                    </div>`;
            });
        })
        .catch(() => {
            const grid = document.getElementById('mediaGrid');
            if (grid) grid.innerHTML = '<div class="col-12 text-center text-muted p-5">Could not load media. Please try again.</div>';
        });
    }

    function esc(str) {
        if (!str) return '';
        return String(str).replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function selectMedia(element) {
        document.querySelectorAll('.media-item').forEach(el => el.classList.remove('border-primary', 'border-3'));
        element.classList.add('border-primary', 'border-3');
        selectedMedia = { url: element.dataset.url, id: element.dataset.id, alt: element.dataset.alt };
        document.getElementById('insertMediaBtn').disabled = false;
    }

    document.getElementById('insertMediaBtn').addEventListener('click', function() {
        if (!selectedMedia) return;

        const target = document.getElementById('mediaTarget').value;

        if (target === 'ckeditor') {
            if (window.projectEditor) {
                try {
                    window.projectEditor.execute('insertImage', {
                        source: [{ src: selectedMedia.url, alt: selectedMedia.alt }]
                    });
                } catch (err) {
                    window.projectEditor.model.change(writer => {
                        const img = writer.createElement('imageBlock', { src: selectedMedia.url, alt: selectedMedia.alt });
                        window.projectEditor.model.insertContent(img);
                    });
                }
            }
        } else if (target === 'featured') {
            document.getElementById('featuredImageInput').value = selectedMedia.url;
            document.getElementById('featuredImagePreview').src = selectedMedia.url;
            document.getElementById('featuredImagePreviewContainer').classList.remove('d-none');
            document.getElementById('setFeaturedImageBtn').classList.add('d-none');
            document.getElementById('removeFeaturedImageBtn').classList.remove('d-none');
        } else if (target === 'banner') {
            document.getElementById('bannerImageInput').value = selectedMedia.url;
            document.getElementById('bannerImagePreview').src = selectedMedia.url;
            document.getElementById('bannerImagePreviewContainer').classList.remove('d-none');
            document.getElementById('setBannerImageBtn').classList.add('d-none');
            document.getElementById('removeBannerImageBtn').classList.remove('d-none');
        } else if (target === 'gallery') {
            addGalleryImageToGrid(selectedMedia.url);
        }

        bootstrap.Modal.getInstance(document.getElementById('mediaLibraryModal')).hide();
        selectedMedia = null;
        document.getElementById('insertMediaBtn').disabled = true;
    });
</script>
@endpush

