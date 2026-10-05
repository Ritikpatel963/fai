@extends('admin.layouts.app')

@section('title', 'SEO Settings')
@section('page', 'seo-settings')

@section('content')
<div class="page-head">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item active">SEO Settings</li>
            </ol>
        </nav>
        <h1>SEO Settings</h1>
    </div>
</div>

<p class="text-muted mb-4">
    Manage meta tags, Open Graph, and technical SEO settings for each page of the website.
    Each page saves independently.
</p>

{{-- ====================================================== --}}
{{-- ACCORDION — one panel per page                        --}}
{{-- ====================================================== --}}
<div class="accordion" id="seoAccordion">

    @php
        $pageIcons = [
            'home'          => 'bi-house-door',
            'about-us'      => 'bi-info-circle',
            'our-projects'  => 'bi-kanban',
            'resources'     => 'bi-folder',
            'blogs-listing' => 'bi-file-text',
            'contact-us'    => 'bi-envelope',
        ];
        $first = true;
    @endphp

    @foreach($pages as $routeName => $label)
        @php
            $s        = $settings[$routeName] ?? null;
            $safeId   = str_replace('-', '_', $routeName);
            $icon     = $pageIcons[$routeName] ?? 'bi-globe';
            $isOpen   = $first;
            $first    = false;
        @endphp

        <div class="accordion-item border-0 shadow-sm mb-3 rounded">

            {{-- Accordion Header --}}
            <h2 class="accordion-header" id="heading_{{ $safeId }}">
                <button class="accordion-button {{ $isOpen ? '' : 'collapsed' }} fw-bold rounded"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#collapse_{{ $safeId }}"
                        aria-expanded="{{ $isOpen ? 'true' : 'false' }}"
                        aria-controls="collapse_{{ $safeId }}">
                    <i class="bi {{ $icon }} me-2 text-primary"></i>
                    {{ $label }}
                    @if($s && ($s->meta_title || $s->meta_description || $s->og_title))
                        <span class="badge bg-success ms-2 fw-normal" style="font-size:0.7rem;">Configured</span>
                    @else
                        <span class="badge bg-secondary ms-2 fw-normal" style="font-size:0.7rem;">Not set</span>
                    @endif
                </button>
            </h2>

            {{-- Accordion Body --}}
            <div id="collapse_{{ $safeId }}"
                 class="accordion-collapse collapse {{ $isOpen ? 'show' : '' }}"
                 aria-labelledby="heading_{{ $safeId }}"
                 data-bs-parent="#seoAccordion">

                <div class="accordion-body px-4 pb-4">
                    <form class="seo-page-form" data-route="{{ $routeName }}" data-label="{{ $label }}" novalidate>
                        @csrf
                        <input type="hidden" name="route_name" value="{{ $routeName }}">

                        {{-- ── Basic Meta ───────────────────────────────── --}}
                        <h6 class="fw-bold text-secondary mb-3 mt-2">
                            <i class="bi bi-tags me-1"></i> Basic Meta Tags
                        </h6>
                        <div class="row">
                            <div class="col-lg-6 mb-3">
                                <label class="form-label fw-semibold">Meta Title</label>
                                <input type="text"
                                       name="meta_title"
                                       class="form-control"
                                       value="{{ $s->meta_title ?? '' }}"
                                       placeholder="e.g. Bio Agriculture — Home"
                                       maxlength="255">
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">Recommended: 50–60 characters</small>
                                    <small class="text-muted char-count">
                                        <span class="meta-title-count">{{ strlen($s->meta_title ?? '') }}</span>/255
                                    </small>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-semibold">Meta Description</label>
                                <textarea name="meta_description"
                                          class="form-control"
                                          rows="2"
                                          maxlength="500"
                                          placeholder="Brief description shown in search engine results...">{{ $s->meta_description ?? '' }}</textarea>
                                <small class="text-muted">Recommended: 150–160 characters</small>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-semibold">Meta Keywords</label>
                                <input type="text"
                                       name="meta_keywords"
                                       class="form-control"
                                       value="{{ $s->meta_keywords ?? '' }}"
                                       placeholder="e.g. bio agriculture, organic farming, india"
                                       maxlength="500">
                                <small class="text-muted">Separate with commas</small>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-semibold">Canonical URL</label>
                                <input type="url"
                                       name="canonical_url"
                                       class="form-control"
                                       value="{{ $s->canonical_url ?? '' }}"
                                       placeholder="https://example.com/page">
                                <small class="text-muted">Leave blank to use the page's default URL</small>
                            </div>
                        </div>

                        {{-- ── Open Graph ──────────────────────────────── --}}
                        <h6 class="fw-bold text-secondary mb-3 mt-2 border-top pt-3">
                            <i class="bi bi-share me-1"></i> Open Graph (Social Media)
                        </h6>
                        <div class="row">
                            <div class="col-lg-6 mb-3">
                                <label class="form-label fw-semibold">OG Title</label>
                                <input type="text"
                                       name="og_title"
                                       class="form-control"
                                       value="{{ $s->og_title ?? '' }}"
                                       placeholder="Leave blank to use Meta Title"
                                       maxlength="255">
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label fw-semibold">OG Image URL</label>
                                <div class="input-group">
                                    <input type="text"
                                           name="og_image"
                                           id="og_image_{{ $safeId }}"
                                           class="form-control"
                                           value="{{ $s->og_image ?? '' }}"
                                           placeholder="Select from media library or enter URL">
                                    <button type="button"
                                            class="btn btn-outline-secondary"
                                            onclick="openSeoImagePicker('og_image_{{ $safeId }}')">
                                        <i class="bi bi-image"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Recommended: 1200×630px</small>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-semibold">OG Description</label>
                                <textarea name="og_description"
                                          class="form-control"
                                          rows="2"
                                          maxlength="500"
                                          placeholder="Leave blank to use Meta Description">{{ $s->og_description ?? '' }}</textarea>
                            </div>
                        </div>

                        {{-- ── Save Button ─────────────────────────────── --}}
                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit"
                                    class="btn btn-primary px-5 seo-save-btn"
                                    data-label="{{ $label }}">
                                <i class="bi bi-save me-1"></i> Save {{ $label }}
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

    @endforeach
</div>

{{-- Media Library Modal --}}
<div class="modal fade" id="seoMediaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white" style="border-bottom:none;">
                <h5 class="modal-title fw-bold">Select OG Image</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" style="min-height:500px;">
                <ul class="nav nav-tabs px-3 pt-3 border-bottom-0" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold border-0" id="seo-upload-tab"
                                data-bs-toggle="tab" data-bs-target="#seo-upload-panel"
                                type="button" role="tab">Upload</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold border-0" id="seo-library-tab"
                                data-bs-toggle="tab" data-bs-target="#seo-library-panel"
                                type="button" role="tab" onclick="loadSeoMedia()">Media Library</button>
                    </li>
                </ul>
                <div class="tab-content border-top border-secondary-subtle p-4" style="min-height:450px;">
                    <div class="tab-pane fade" id="seo-upload-panel" role="tabpanel">
                        <div class="border border-2 border-dashed rounded text-center p-5 border-primary"
                             id="seoUploadZone" style="cursor:pointer;">
                            <i class="bi bi-cloud-arrow-up fs-1 text-primary"></i>
                            <h5 class="mt-3 fw-bold">Drop image here or click to upload</h5>
                            <p class="text-muted small">JPG, PNG, WebP — Max 5MB</p>
                            <input type="file" id="seoMediaFileInput" class="d-none"
                                   accept="image/jpeg,image/png,image/gif,image/webp">
                        </div>
                        <div id="seoUploadProgress" class="progress mt-3 d-none" style="height:8px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                 role="progressbar" style="width:0%;"></div>
                        </div>
                    </div>
                    <div class="tab-pane fade show active" id="seo-library-panel" role="tabpanel">
                        <div class="mb-3">
                            <input type="text" id="seoMediaSearch" class="form-control"
                                   placeholder="Search by title or keyword..." oninput="loadSeoMedia(1)">
                        </div>
                        <div class="row g-2" id="seoMediaGrid">
                            <div class="col-12 text-center text-muted p-5">Loading media...</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 pb-4 px-4">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary px-4" id="seoInsertMediaBtn" disabled>
                    Use Selected Image
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ─── Per-form AJAX save ───────────────────────────────────────────────────────
document.querySelectorAll('.seo-page-form').forEach(function (form) {
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const btn   = form.querySelector('.seo-save-btn');
        const label = btn.dataset.label;
        const orig  = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

        // Clear previous inline errors
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback-inline').forEach(el => el.remove());

        const formData = new FormData(form);

        fetch('{{ route("admin.seo-settings.save") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json().then(data => ({ ok: res.ok, data })))
        .then(({ ok, data }) => {
            if (ok && data.success) {
                // Update badge to "Configured"
                const accordionBtn = form.closest('.accordion-item').querySelector('.accordion-button');
                const badge = accordionBtn.querySelector('.badge');
                badge.className = 'badge bg-success ms-2 fw-normal';
                badge.textContent = 'Configured';
                badge.style.fontSize = '0.7rem';

                Swal.fire({
                    icon: 'success',
                    title: 'Saved!',
                    text: data.message,
                    timer: 1800,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            } else if (data.errors) {
                // Show inline errors
                Object.keys(data.errors).forEach(field => {
                    const input = form.querySelector(`[name="${field}"]`);
                    if (input) {
                        input.classList.add('is-invalid');
                        const errDiv = document.createElement('div');
                        errDiv.className = 'invalid-feedback d-block invalid-feedback-inline';
                        errDiv.textContent = data.errors[field][0];
                        input.closest('.mb-3').appendChild(errDiv);
                    }
                });
            } else {
                Swal.fire('Error', data.message || 'Could not save settings.', 'error');
            }
        })
        .catch(() => Swal.fire('Error', 'An error occurred.', 'error'))
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = orig;
        });
    });
});

// ─── OG Image Media Picker ────────────────────────────────────────────────────
let _seoTargetInputId = null;
let _seoSelectedMedia = null;

function openSeoImagePicker(inputId) {
    _seoTargetInputId = inputId;
    _seoSelectedMedia = null;
    document.getElementById('seoInsertMediaBtn').disabled = true;
    document.getElementById('seoMediaSearch').value = '';
    loadSeoMedia();
    const el = document.getElementById('seoMediaModal');
    (bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el)).show();
}

document.getElementById('seoInsertMediaBtn').addEventListener('click', function () {
    if (!_seoSelectedMedia || !_seoTargetInputId) return;
    document.getElementById(_seoTargetInputId).value = _seoSelectedMedia;
    bootstrap.Modal.getInstance(document.getElementById('seoMediaModal')).hide();
    _seoSelectedMedia = null;
    _seoTargetInputId = null;
    this.disabled = true;
});

// ─── Media Library (SEO modal) ────────────────────────────────────────────────
const seoUploadZone    = document.getElementById('seoUploadZone');
const seoMediaFileInput = document.getElementById('seoMediaFileInput');
seoUploadZone.addEventListener('click', () => seoMediaFileInput.click());
seoUploadZone.addEventListener('dragover',  e => { e.preventDefault(); seoUploadZone.classList.add('bg-light'); });
seoUploadZone.addEventListener('dragleave', e => { e.preventDefault(); seoUploadZone.classList.remove('bg-light'); });
seoUploadZone.addEventListener('drop', e => {
    e.preventDefault();
    seoUploadZone.classList.remove('bg-light');
    if (e.dataTransfer.files.length) seoUploadFile(e.dataTransfer.files[0]);
});
seoMediaFileInput.addEventListener('change', function () {
    if (this.files.length) seoUploadFile(this.files[0]);
});

function seoUploadFile(file) {
    const formData = new FormData();
    formData.append('file', file);
    formData.append('_token', '{{ csrf_token() }}');
    const bar = document.querySelector('#seoUploadProgress .progress-bar');
    document.getElementById('seoUploadProgress').classList.remove('d-none');
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '{{ route("admin.media.store") }}', true);
    xhr.setRequestHeader('Accept', 'application/json');
    xhr.upload.onprogress = e => { if (e.lengthComputable) bar.style.width = ((e.loaded / e.total) * 100) + '%'; };
    xhr.onload = function () {
        if (xhr.status === 200) { document.getElementById('seo-library-tab').click(); loadSeoMedia(); }
        else Swal.fire('Upload Failed', 'Could not upload file.', 'error');
        document.getElementById('seoUploadProgress').classList.add('d-none');
        bar.style.width = '0%';
    };
    xhr.send(formData);
}

function loadSeoMedia(page = 1) {
    const search = document.getElementById('seoMediaSearch')?.value || '';
    fetch(`{{ route("admin.media.index") }}?page=${page}&search=${encodeURIComponent(search)}`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        const grid = document.getElementById('seoMediaGrid');
        grid.innerHTML = '';
        if (!data.data || data.data.length === 0) {
            grid.innerHTML = '<div class="col-12 text-center text-muted p-5">No media found.</div>';
            return;
        }
        data.data.forEach(media => {
            const url = '/storage/' + media.path;
            grid.innerHTML += `
                <div class="col-md-2 col-sm-3 col-4 mb-2">
                    <div class="card h-100 seo-media-item border-2"
                         data-url="${url}" style="cursor:pointer;" onclick="selectSeoMedia(this)">
                        <img src="${url}" class="card-img-top object-fit-cover" style="height:100px;" alt="">
                    </div>
                </div>`;
        });
    });
}

function selectSeoMedia(element) {
    document.querySelectorAll('.seo-media-item').forEach(el => el.classList.remove('border-primary'));
    element.classList.add('border-primary');
    _seoSelectedMedia = element.dataset.url;
    document.getElementById('seoInsertMediaBtn').disabled = false;
}
</script>
@endpush
