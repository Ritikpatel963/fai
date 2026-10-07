@extends('admin.layouts.app')

@section('title', 'Site Settings')
@section('page', 'settings')

@section('content')
<div class="page-head">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item active">Site Settings</li>
            </ol>
        </nav>
        <h1>Site Settings</h1>
    </div>
</div>

<div class="row">

    {{-- ======================== --}}
    {{-- HEADER SETTINGS SECTION --}}
    {{-- ======================== --}}
    <div class="col-12 mb-4">
        <section class="panel">
            <h5 class="fw-bold mb-4 border-bottom pb-2">
                <i class="bi bi-layout-text-window-reverse me-2"></i>Header Settings
            </h5>

            <form id="headerSettingsForm" novalidate>
                @csrf
                <div class="row align-items-end">
                    <div class="col-lg-6 mb-3">
                        <label class="form-label fw-semibold">Header Logo</label>
                        <div class="input-group">
                            <input type="text"
                                   name="header_logo"
                                   id="header_logo"
                                   class="form-control"
                                   value="{{ $header['header_logo'] ?? '' }}"
                                   placeholder="Select from media library or enter URL"
                                   maxlength="500">
                            <button type="button" class="btn btn-outline-secondary" onclick="openMediaPicker('header_logo', 'header_logo_preview')">
                                <i class="bi bi-image"></i> Browse
                            </button>
                        </div>
                        <div class="invalid-feedback d-block" id="header_logo_error"></div>
                        <small class="text-muted">Recommended size: 200x60px. Supports JPG, PNG, WebP.</small>
                    </div>

                    <div class="col-lg-3 mb-3">
                        <div id="header_logo_preview_container" class="{{ ($header['header_logo'] ?? '') ? '' : 'd-none' }}">
                            <label class="form-label fw-semibold">Preview</label>
                            <div class="border rounded p-2 bg-light text-center">
                                <img src="{{ $header['header_logo'] ?? '' }}"
                                     id="header_logo_preview"
                                     class="img-fluid"
                                     style="max-height: 60px;"
                                     alt="Header Logo Preview">
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 mb-3">
                        <button type="submit" class="btn btn-primary w-100" id="saveHeaderBtn" style="white-space: nowrap;">
                            <i class="bi bi-save me-1"></i> Save Header
                        </button>
                    </div>
                </div>
            </form>
        </section>
    </div>

    {{-- ======================== --}}
    {{-- FOOTER SETTINGS SECTION --}}
    {{-- ======================== --}}
    <div class="col-12">
        <section class="panel">
            <h5 class="fw-bold mb-4 border-bottom pb-2">
                <i class="bi bi-layout-text-sidebar-reverse me-2"></i>Footer Settings
            </h5>

            <form id="footerSettingsForm" novalidate>
                @csrf
                <div class="row">

                    {{-- Footer Logo --}}
                    <div class="col-lg-8 mb-3">
                        <label class="form-label fw-semibold">Footer Logo</label>
                        <div class="input-group">
                            <input type="text"
                                   name="footer_logo"
                                   id="footer_logo"
                                   class="form-control"
                                   value="{{ $footer['footer_logo'] ?? '' }}"
                                   placeholder="Select from media library or enter URL"
                                   maxlength="500">
                            <button type="button" class="btn btn-outline-secondary" onclick="openMediaPicker('footer_logo', 'footer_logo_preview')">
                                <i class="bi bi-image"></i> Browse
                            </button>
                        </div>
                        <div class="invalid-feedback" id="footer_logo_error"></div>
                        <small class="text-muted">Recommended size: 200x60px.</small>
                    </div>

                    <div class="col-lg-4 mb-3">
                        <div id="footer_logo_preview_container" class="{{ ($footer['footer_logo'] ?? '') ? '' : 'd-none' }}">
                            <label class="form-label fw-semibold">Preview</label>
                            <div class="border rounded p-2 bg-light text-center">
                                <img src="{{ $footer['footer_logo'] ?? '' }}"
                                     id="footer_logo_preview"
                                     class="img-fluid"
                                     style="max-height: 60px;"
                                     alt="Footer Logo Preview">
                            </div>
                        </div>
                    </div>

                    {{-- Short Description --}}
                    <div class="col-12 mb-3">
                        <label class="form-label fw-semibold">Short Description</label>
                        <textarea name="footer_description"
                                  id="footer_description"
                                  class="form-control"
                                  rows="3"
                                  maxlength="500"
                                  placeholder="A brief description shown in the footer...">{{ $footer['footer_description'] ?? '' }}</textarea>
                        <div class="d-flex justify-content-between">
                            <div class="invalid-feedback" id="footer_description_error"></div>
                            <small class="text-muted ms-auto"><span id="desc_count">{{ strlen($footer['footer_description'] ?? '') }}</span>/500</small>
                        </div>
                    </div>

                    {{-- Contact Info --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email"
                                   name="footer_email"
                                   id="footer_email"
                                   class="form-control"
                                   value="{{ $footer['footer_email'] ?? '' }}"
                                   placeholder="contact@example.com"
                                   maxlength="255">
                        </div>
                        <div class="invalid-feedback d-block" id="footer_email_error"></div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Contact Number</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                            <input type="text"
                                   name="footer_phone"
                                   id="footer_phone"
                                   class="form-control"
                                   value="{{ $footer['footer_phone'] ?? '' }}"
                                   placeholder="+91 98765 43210"
                                   maxlength="20">
                        </div>
                        <div class="invalid-feedback d-block" id="footer_phone_error"></div>
                    </div>

                    {{-- Social Links --}}
                    <div class="col-12 mb-2">
                        <label class="form-label fw-bold text-secondary">Social Media Links</label>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Facebook</label>
                        <div class="input-group">
                            <span class="input-group-text text-white" style="background:#1877F2;"><i class="bi bi-facebook"></i></span>
                            <input type="url"
                                   name="footer_facebook"
                                   id="footer_facebook"
                                   class="form-control"
                                   value="{{ $footer['footer_facebook'] ?? '' }}"
                                   placeholder="https://facebook.com/yourpage">
                        </div>
                        <div class="invalid-feedback d-block" id="footer_facebook_error"></div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Instagram</label>
                        <div class="input-group">
                            <span class="input-group-text text-white" style="background:#E1306C;"><i class="bi bi-instagram"></i></span>
                            <input type="url"
                                   name="footer_instagram"
                                   id="footer_instagram"
                                   class="form-control"
                                   value="{{ $footer['footer_instagram'] ?? '' }}"
                                   placeholder="https://instagram.com/yourhandle">
                        </div>
                        <div class="invalid-feedback d-block" id="footer_instagram_error"></div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Twitter / X</label>
                        <div class="input-group">
                            <span class="input-group-text text-white" style="background:#000;"><i class="bi bi-twitter-x"></i></span>
                            <input type="url"
                                   name="footer_twitter"
                                   id="footer_twitter"
                                   class="form-control"
                                   value="{{ $footer['footer_twitter'] ?? '' }}"
                                   placeholder="https://x.com/yourhandle">
                        </div>
                        <div class="invalid-feedback d-block" id="footer_twitter_error"></div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">YouTube</label>
                        <div class="input-group">
                            <span class="input-group-text text-white" style="background:#FF0000;"><i class="bi bi-youtube"></i></span>
                            <input type="url"
                                   name="footer_youtube"
                                   id="footer_youtube"
                                   class="form-control"
                                   value="{{ $footer['footer_youtube'] ?? '' }}"
                                   placeholder="https://youtube.com/yourchannel">
                        </div>
                        <div class="invalid-feedback d-block" id="footer_youtube_error"></div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">LinkedIn</label>
                        <div class="input-group">
                            <span class="input-group-text text-white" style="background:#0077B5;"><i class="bi bi-linkedin"></i></span>
                            <input type="url"
                                   name="footer_linkedin"
                                   id="footer_linkedin"
                                   class="form-control"
                                   value="{{ $footer['footer_linkedin'] ?? '' }}"
                                   placeholder="https://linkedin.com/company/yourpage">
                        </div>
                        <div class="invalid-feedback d-block" id="footer_linkedin_error"></div>
                    </div>

                    <div class="col-12 mt-2">
                        <button type="submit" class="btn btn-primary px-5" id="saveFooterBtn">
                            <i class="bi bi-save me-1"></i> Save Footer Settings
                        </button>
                    </div>

                </div>
            </form>
        </section>
    </div>

</div>

{{-- Media Library Modal --}}
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
                        <div class="mb-3">
                            <input type="text" id="mediaSearchInput" class="form-control" placeholder="Search by title or keyword..." oninput="loadMedia(1)">
                        </div>
                        <div class="row g-2" id="mediaGrid">
                            <div class="col-12 text-center text-muted p-5">Loading media...</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-3 pb-4 px-4">
                <input type="hidden" id="mediaTargetInput" value="">
                <input type="hidden" id="mediaTargetPreview" value="">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary px-4" id="insertMediaBtn" disabled>Select Image</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ─── Character Counter for Description ───────────────────────────────────────
document.getElementById('footer_description').addEventListener('input', function () {
    document.getElementById('desc_count').textContent = this.value.length;
});

// ─── Frontend Validation Helpers ─────────────────────────────────────────────
function isValidUrl(str) {
    if (!str) return true; // optional field
    try { new URL(str); return true; } catch { return false; }
}

function isValidEmail(str) {
    if (!str) return true; // optional field
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(str);
}

function showError(fieldId, message) {
    const field = document.getElementById(fieldId);
    const error = document.getElementById(fieldId + '_error');
    if (field) field.classList.add('is-invalid');
    if (error) { error.textContent = message; error.style.display = 'block'; }
}

function clearErrors(formId) {
    document.querySelectorAll('#' + formId + ' .is-invalid').forEach(el => el.classList.remove('is-invalid'));
    document.querySelectorAll('#' + formId + ' .invalid-feedback').forEach(el => { el.textContent = ''; el.style.display = 'none'; });
}

function showSuccess(message) {
    Swal.fire({ icon: 'success', title: 'Saved!', text: message, timer: 2000, showConfirmButton: false });
}

function showBackendErrors(errors) {
    Object.keys(errors).forEach(field => {
        showError(field, errors[field][0]);
    });
}

// ─── Header Form Submit ───────────────────────────────────────────────────────
document.getElementById('headerSettingsForm').addEventListener('submit', function (e) {
    e.preventDefault();
    clearErrors('headerSettingsForm');

    // Frontend validation
    const headerLogo = document.getElementById('header_logo').value;
    let valid = true;

    if (headerLogo && headerLogo.length > 500) {
        showError('header_logo', 'Header logo path must not exceed 500 characters.');
        valid = false;
    }

    if (!valid) return;

    const btn = document.getElementById('saveHeaderBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

    const formData = new FormData(this);
    fetch('{{ route("admin.settings.header") }}', {
        method: 'POST',
        body: formData,
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(res => res.json().then(data => ({ ok: res.ok, data })))
    .then(({ ok, data }) => {
        if (ok && data.success) {
            showSuccess(data.message);
        } else if (data.errors) {
            showBackendErrors(data.errors);
        } else {
            Swal.fire('Error', data.message || 'An error occurred.', 'error');
        }
    })
    .catch(() => Swal.fire('Error', 'An error occurred while saving.', 'error'))
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-save me-1"></i> Save Header';
    });
});

// ─── Footer Form Submit ───────────────────────────────────────────────────────
document.getElementById('footerSettingsForm').addEventListener('submit', function (e) {
    e.preventDefault();
    clearErrors('footerSettingsForm');

    // Frontend validation
    let valid = true;

    const email = document.getElementById('footer_email').value;
    if (email && !isValidEmail(email)) {
        showError('footer_email', 'Please enter a valid email address.');
        valid = false;
    }

    const phone = document.getElementById('footer_phone').value;
    if (phone && phone.length > 20) {
        showError('footer_phone', 'Phone number must not exceed 20 characters.');
        valid = false;
    }

    const desc = document.getElementById('footer_description').value;
    if (desc && desc.length > 500) {
        showError('footer_description', 'Description must not exceed 500 characters.');
        valid = false;
    }

    const urlFields = ['footer_facebook', 'footer_instagram', 'footer_twitter', 'footer_youtube', 'footer_linkedin'];
    const urlLabels = { footer_facebook: 'Facebook', footer_instagram: 'Instagram', footer_twitter: 'Twitter/X', footer_youtube: 'YouTube', footer_linkedin: 'LinkedIn' };
    urlFields.forEach(field => {
        const val = document.getElementById(field).value;
        if (val && !isValidUrl(val)) {
            showError(field, urlLabels[field] + ' must be a valid URL starting with https://');
            valid = false;
        }
    });

    if (!valid) return;

    const btn = document.getElementById('saveFooterBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

    const formData = new FormData(this);
    fetch('{{ route("admin.settings.footer") }}', {
        method: 'POST',
        body: formData,
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(res => res.json().then(data => ({ ok: res.ok, data })))
    .then(({ ok, data }) => {
        if (ok && data.success) {
            showSuccess(data.message);
        } else if (data.errors) {
            showBackendErrors(data.errors);
        } else {
            Swal.fire('Error', data.message || 'An error occurred.', 'error');
        }
    })
    .catch(() => Swal.fire('Error', 'An error occurred while saving.', 'error'))
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-save me-1"></i> Save Footer Settings';
    });
});

// ─── Media Library ────────────────────────────────────────────────────────────
let selectedMedia = null;
let activeInputId  = null;
let activePreviewId = null;

function openMediaPicker(inputId, previewId) {
    activeInputId   = inputId;
    activePreviewId = previewId;
    selectedMedia   = null;
    document.getElementById('insertMediaBtn').disabled = true;
    loadMedia();
    new bootstrap.Modal(document.getElementById('mediaLibraryModal')).show();
}

const uploadZone    = document.getElementById('uploadZone');
const mediaFileInput = document.getElementById('mediaFileInput');
uploadZone.addEventListener('click', () => mediaFileInput.click());
uploadZone.addEventListener('dragover',  e => { e.preventDefault(); uploadZone.classList.add('bg-secondary','text-white'); });
uploadZone.addEventListener('dragleave', e => { e.preventDefault(); uploadZone.classList.remove('bg-secondary','text-white'); });
uploadZone.addEventListener('drop', e => {
    e.preventDefault();
    uploadZone.classList.remove('bg-secondary','text-white');
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
        else Swal.fire('Upload Failed', 'Could not upload the file.', 'error');
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
                    <div class="card h-100 media-item" data-url="${url}" style="cursor:pointer;" onclick="selectMedia(this)">
                        <img src="${url}" class="card-img-top object-fit-cover" style="height:100px;" alt="">
                    </div>
                </div>`;
        });
    });
}

function selectMedia(element) {
    document.querySelectorAll('.media-item').forEach(el => el.classList.remove('border-primary','border-3'));
    element.classList.add('border-primary','border-3');
    selectedMedia = element.dataset.url;
    document.getElementById('insertMediaBtn').disabled = false;
}

document.getElementById('insertMediaBtn').addEventListener('click', function () {
    if (!selectedMedia || !activeInputId) return;

    // Set input value
    document.getElementById(activeInputId).value = selectedMedia;

    // Show preview
    const previewContainer = document.getElementById(activeInputId + '_container') 
                          || document.getElementById(activePreviewId + '_container')
                          || document.getElementById(activeInputId.replace('_logo', '_logo_preview_container'));

    const previewImg = document.getElementById(activePreviewId);

    if (previewImg) {
        previewImg.src = selectedMedia;
        // Find the container and show it
        const containerId = activeInputId === 'header_logo' ? 'header_logo_preview_container' : 'footer_logo_preview_container';
        const container = document.getElementById(containerId);
        if (container) container.classList.remove('d-none');
    }

    bootstrap.Modal.getInstance(document.getElementById('mediaLibraryModal')).hide();
    selectedMedia = null;
    document.getElementById('insertMediaBtn').disabled = true;
});
</script>
@endpush
