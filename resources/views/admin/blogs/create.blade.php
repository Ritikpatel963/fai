@extends('admin.layouts.app')

@section('title', 'Add New Blog')
@section('page', 'blogs.create')

@section('content')
<div class="page-head">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.blogs.index') }}">Blogs</a></li>
                <li class="breadcrumb-item active">Add New</li>
            </ol>
        </nav>
        <h1>Add New Blog</h1>
    </div>
</div>

<form action="{{ route('admin.blogs.store') }}" method="POST" id="blogForm">
    @csrf
    <div class="row">
        <!-- Main Content Column -->
        <div class="col-xl-9 col-lg-8">
            <div class="panel mb-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">Blog Title *</label>
                    <input type="text" name="title" id="title" class="form-control form-control-lg" placeholder="Enter blog title here" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Slug *</label>
                    <input type="text" name="slug" id="slug" class="form-control" placeholder="auto-generated-slug" required>
                </div>
                
                <div class="mb-3">
                    <button type="button" class="btn btn-outline-secondary btn-sm mb-2" id="openMediaLibraryBtn">
                        <i class="bi bi-card-image me-1"></i> Add Media
                    </button>
                    <input type="hidden" name="content" id="blogContentInput">
                    <div id="blogEditor" style="height: 400px; font-size: 16px;"></div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Excerpt / Short Description</label>
                    <textarea name="excerpt" class="form-control" rows="3" placeholder="A brief summary of the blog..."></textarea>
                </div>
            </div>
            
            <!-- SEO Panel -->
            <div class="panel mb-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-search me-2"></i>SEO Settings</h5>
                <div class="mb-3">
                    <label class="form-label">Meta Title</label>
                    <input type="text" name="seo_title" class="form-control" placeholder="Leave blank to use blog title">
                </div>
                <div class="mb-3">
                    <label class="form-label">Meta Description</label>
                    <textarea name="seo_description" class="form-control" rows="2"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Meta Keywords</label>
                    <input type="text" name="seo_keywords" class="form-control" placeholder="e.g. laravel, php, web development">
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
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                        <option value="scheduled">Scheduled</option>
                    </select>
                </div>
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-outline-secondary" onclick="saveAsDraft()">Save Draft</button>
                    <button type="submit" class="btn btn-primary">Publish</button>
                </div>
            </div>
            
            <!-- Categories Panel -->
            <div class="panel mb-4">
                <h6 class="fw-bold mb-3 border-bottom pb-2">Categories</h6>
                <div class="category-list" style="max-height: 200px; overflow-y: auto;">
                    @foreach($categories ?? [] as $category)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="categories[]" value="{{ $category->id }}" id="cat-{{ $category->id }}">
                        <label class="form-check-label" for="cat-{{ $category->id }}">
                            {{ $category->name }}
                        </label>
                    </div>
                    @endforeach
                    @if(empty($categories))
                    <p class="text-muted small">No categories found.</p>
                    @endif
                </div>
            </div>
            
            <!-- Tags Panel -->
            <div class="panel mb-4">
                <h6 class="fw-bold mb-3 border-bottom pb-2">Tags</h6>
                <div class="tag-list" style="max-height: 200px; overflow-y: auto;">
                    @foreach($tags ?? [] as $tag)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="tags[]" value="{{ $tag->id }}" id="tag-{{ $tag->id }}">
                        <label class="form-check-label" for="tag-{{ $tag->id }}">
                            {{ $tag->name }}
                        </label>
                    </div>
                    @endforeach
                    @if(empty($tags))
                    <p class="text-muted small">No tags found.</p>
                    @endif
                </div>
            </div>

            <!-- Featured Image Panel -->
            <div class="panel mb-4">
                <h6 class="fw-bold mb-3 border-bottom pb-2">Featured Image</h6>
                <div class="text-center">
                    <div id="featuredImagePreviewContainer" class="mb-3 d-none">
                        <img src="" id="featuredImagePreview" class="img-fluid rounded border" alt="Featured Image">
                    </div>
                    <input type="hidden" name="featured_image" id="featuredImageInput">
                    <button type="button" class="btn btn-outline-primary w-100" id="setFeaturedImageBtn" onclick="openFeaturedImageModal()">
                        <i class="bi bi-image me-1"></i> Set Featured Image
                    </button>
                    <button type="button" class="btn btn-outline-danger w-100 mt-2 d-none" id="removeFeaturedImageBtn" onclick="removeFeaturedImage()">
                        Remove Image
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Media Library Modal for Featured Image -->
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
                    <!-- Upload Panel -->
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
                    
                    <!-- Library Panel -->
                    <div class="tab-pane fade show active" id="library-panel" role="tabpanel">
                        <div class="row g-2" id="mediaGrid">
                            <div class="col-12 text-center text-muted p-5">Loading media...</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-3 pb-4 px-4">
                <input type="hidden" id="mediaTarget" value=""> <!-- 'tinymce' or 'featured' -->
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary px-4" id="insertMediaBtn" disabled>Insert Selected Image</button>
            </div>
        </div>
    </div>
</div>

<!-- Quill Script -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-generate slug from title
        const titleInput = document.getElementById('title');
        const slugInput = document.getElementById('slug');
        
        titleInput.addEventListener('input', function() {
            let slug = this.value.toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/[\s_-]+/g, '-')
                .replace(/^-+|-+$/g, '');
            slugInput.value = slug;
        });

        // Initialize Quill
        window.quill = new Quill('#blogEditor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'align': [] }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }, { 'indent': '-1'}, { 'indent': '+1' }],
                    ['link', 'video', 'code-block'],
                    ['clean']
                ]
            }
        });
        
        // Handle form submission via AJAX
        const blogForm = document.getElementById('blogForm');
        blogForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Sync Quill content
            document.getElementById('blogContentInput').value = window.quill.root.innerHTML;
            
            const formData = new FormData(this);
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Blog saved successfully!',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = data.redirect || '{{ route("admin.blogs.index") }}';
                    });
                } else {
                    Swal.fire('Error', 'Error saving blog.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'An error occurred.', 'error');
            });
        });
    });
    
    function saveAsDraft() {
        document.querySelector('select[name="status"]').value = 'draft';
        document.getElementById('blogForm').dispatchEvent(new Event('submit'));
    }

    let selectedMedia = null;

    function openFeaturedImageModal() {
        document.getElementById('mediaTarget').value = 'featured';
        loadMedia();
        const modal = new bootstrap.Modal(document.getElementById('mediaLibraryModal'));
        modal.show();
    }

    function removeFeaturedImage() {
        document.getElementById('featuredImageInput').value = '';
        document.getElementById('featuredImagePreview').src = '';
        document.getElementById('featuredImagePreviewContainer').classList.add('d-none');
        document.getElementById('removeFeaturedImageBtn').classList.add('d-none');
        document.getElementById('setFeaturedImageBtn').classList.remove('d-none');
    }

    // Connect the "Add Media" button in the editor to the same modal
    document.getElementById('openMediaLibraryBtn').addEventListener('click', function() {
        document.getElementById('mediaTarget').value = 'tinymce';
        loadMedia();
        const modal = new bootstrap.Modal(document.getElementById('mediaLibraryModal'));
        modal.show();
    });

    // Media Library Logic
    const uploadZone = document.getElementById('uploadZone');
    const mediaFileInput = document.getElementById('mediaFileInput');

    uploadZone.addEventListener('click', () => mediaFileInput.click());
    
    uploadZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadZone.classList.add('bg-secondary', 'text-white');
    });
    
    uploadZone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        uploadZone.classList.remove('bg-secondary', 'text-white');
    });
    
    uploadZone.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadZone.classList.remove('bg-secondary', 'text-white');
        if (e.dataTransfer.files.length) {
            uploadFile(e.dataTransfer.files[0]);
        }
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
            if (e.lengthComputable) {
                const percentComplete = (e.loaded / e.total) * 100;
                progressBar.style.width = percentComplete + '%';
            }
        };

        xhr.onload = function() {
            if (xhr.status === 200) {
                // Switch to library tab and reload
                document.getElementById('library-tab').click();
                loadMedia();
                Swal.fire({ icon: 'success', title: 'Uploaded!', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
            } else {
                Swal.fire('Upload Failed', xhr.responseText, 'error');
            }
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
                    </div>
                `;
            });
        });
    }

    function selectMedia(element) {
        document.querySelectorAll('.media-item').forEach(el => el.classList.remove('border-primary', 'border-3'));
        element.classList.add('border-primary', 'border-3');
        
        selectedMedia = {
            url: element.dataset.url,
            id: element.dataset.id,
            alt: element.dataset.alt
        };
        
        document.getElementById('insertMediaBtn').disabled = false;
    }

    document.getElementById('insertMediaBtn').addEventListener('click', function() {
        if (!selectedMedia) return;
        
        const target = document.getElementById('mediaTarget').value;
        if (target === 'tinymce') {
            const range = window.quill.getSelection(true);
            window.quill.insertEmbed(range.index, 'image', selectedMedia.url, Quill.sources.USER);
        } else if (target === 'featured') {
            document.getElementById('featuredImageInput').value = selectedMedia.url;
            document.getElementById('featuredImagePreview').src = selectedMedia.url;
            document.getElementById('featuredImagePreviewContainer').classList.remove('d-none');
            document.getElementById('setFeaturedImageBtn').classList.add('d-none');
            document.getElementById('removeFeaturedImageBtn').classList.remove('d-none');
        }
        
        bootstrap.Modal.getInstance(document.getElementById('mediaLibraryModal')).hide();
        selectedMedia = null;
        document.getElementById('insertMediaBtn').disabled = true;
    });
</script>
@endsection
