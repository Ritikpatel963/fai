@extends('admin.layouts.app')

@section('title', 'Testimonials')
@section('page', 'testimonials')

@section('content')
<div class="page-head">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item active">Testimonials</li>
            </ol>
        </nav>
        <h1>Testimonials</h1>
    </div>
    <div class="d-flex gap-2 mt-3">
        <button type="button" class="btn btn-primary" onclick="openAddModal()">
            <i class="bi bi-plus-circle me-2"></i>Add Testimonial
        </button>
    </div>
</div>

<section class="panel">
    <div class="table-responsive">
        <table class="table data-table align-middle w-100" id="testimonialsTable">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Name</th>
                    <th>Rating</th>
                    <th>Message</th>
                    <th>Image</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($testimonials as $testimonial)
                <tr id="row-{{ $testimonial->id }}">
                    <td>{{ $testimonial->order }}</td>
                    <td class="fw-semibold">{{ $testimonial->name }}</td>
                    <td>
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi bi-star{{ $i <= $testimonial->rating ? '-fill text-warning' : '' }}"
                               style="font-size: 0.85rem;"></i>
                        @endfor
                    </td>
                    <td>
                        <span class="text-muted">
                            {{ \Illuminate\Support\Str::limit($testimonial->message, 60) }}
                        </span>
                    </td>
                    <td>
                        @if($testimonial->image)
                            <img src="{{ $testimonial->image }}"
                                 class="rounded-circle border"
                                 style="width:40px; height:40px; object-fit:cover;"
                                 alt="{{ $testimonial->name }}">
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @if($testimonial->status)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-sm btn-info text-white me-1"
                                onclick="openEditModal({{ $testimonial->id }})" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-danger"
                                onclick="deleteTestimonial({{ $testimonial->id }})" title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        {{-- DataTables handles pagination automatically --}}
    </div>
</section>

{{-- ============================================================ --}}
{{-- ADD / EDIT TESTIMONIAL MODAL                                --}}
{{-- ============================================================ --}}
<div class="modal fade" id="testimonialModal" tabindex="-1" aria-labelledby="testimonialModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white" style="border-bottom:none;">
                <h5 class="modal-title fw-bold" id="testimonialModalLabel">
                    <i class="bi bi-chat-quote me-2"></i>
                    <span id="modalTitleText">Add Testimonial</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="testimonialForm" novalidate>
                @csrf
                <input type="hidden" id="testimonialId" value="">
                <input type="hidden" id="methodField" value="POST">

                <div class="modal-body p-4">
                    <div class="row">

                        {{-- Left — Image --}}
                        <div class="col-md-3 text-center mb-4 mb-md-0">
                            <label class="form-label fw-semibold d-block">Photo</label>
                            <div class="mb-2">
                                <img src="" id="imagePreview"
                                     class="rounded-circle border"
                                     style="width:90px; height:90px; object-fit:cover; display:none;"
                                     alt="Preview">
                                <div id="imagePlaceholder"
                                     class="rounded-circle border bg-light d-flex align-items-center justify-content-center mx-auto"
                                     style="width:90px; height:90px;">
                                    <i class="bi bi-person fs-2 text-muted"></i>
                                </div>
                            </div>
                            <input type="hidden" name="image" id="imageInput">
                            <button type="button" class="btn btn-outline-primary btn-sm w-100 mt-1"
                                    onclick="openTestimonialImagePicker()">
                                <i class="bi bi-image me-1"></i> Select
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm w-100 mt-1 d-none"
                                    id="removeImageBtn" onclick="removeTestimonialImage()">
                                Remove
                            </button>
                        </div>

                        {{-- Right — Fields --}}
                        <div class="col-md-9">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Name *</label>
                                <input type="text" name="name" id="testimonialName"
                                       class="form-control" placeholder="e.g. Rajesh Kumar" required maxlength="255">
                                <div class="invalid-feedback d-block" id="err_name"></div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Rating *</label>
                                <div class="d-flex gap-2 align-items-center" id="starRatingContainer">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star star-btn fs-4"
                                           data-value="{{ $i }}"
                                           style="cursor:pointer; color:#ccc;"
                                           onclick="setRating({{ $i }})"></i>
                                    @endfor
                                    <span class="text-muted small ms-2" id="ratingLabel">Click to rate</span>
                                </div>
                                <input type="hidden" name="rating" id="testimonialRating" value="">
                                <div class="invalid-feedback d-block" id="err_rating"></div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Message *</label>
                                <textarea name="message" id="testimonialMessage"
                                          class="form-control" rows="4"
                                          placeholder="Enter testimonial text..."
                                          maxlength="2000" required></textarea>
                                <div class="d-flex justify-content-between">
                                    <div class="invalid-feedback d-block" id="err_message"></div>
                                    <small class="text-muted ms-auto">
                                        <span id="msgCount">0</span>/2000
                                    </small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Display Order</label>
                                    <input type="number" name="order" id="testimonialOrder"
                                           class="form-control" value="0" min="0">
                                    <small class="text-muted">Lower number = shown first</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Status</label>
                                    <select name="status" id="testimonialStatus" class="form-select">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="button" class="btn btn-outline-secondary px-4"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-5" id="saveTestimonialBtn">
                        <i class="bi bi-save me-1"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MEDIA LIBRARY MODAL                                         --}}
{{-- ============================================================ --}}
<div class="modal fade" id="testimonialMediaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white" style="border-bottom:none;">
                <h5 class="modal-title fw-bold">Select Photo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" style="min-height:500px;">
                <ul class="nav nav-tabs px-3 pt-3 border-bottom-0" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold border-0" id="tm-upload-tab"
                                data-bs-toggle="tab" data-bs-target="#tm-upload-panel"
                                type="button" role="tab">Upload</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold border-0" id="tm-library-tab"
                                data-bs-toggle="tab" data-bs-target="#tm-library-panel"
                                type="button" role="tab" onclick="loadTmMedia()">Media Library</button>
                    </li>
                </ul>
                <div class="tab-content border-top border-secondary-subtle p-4" style="min-height:450px;">
                    <div class="tab-pane fade" id="tm-upload-panel" role="tabpanel">
                        <div class="border border-2 border-dashed rounded text-center p-5 border-primary"
                             id="tmUploadZone" style="cursor:pointer;">
                            <i class="bi bi-cloud-arrow-up fs-1 text-primary"></i>
                            <h5 class="mt-3 fw-bold">Drop image here or click to upload</h5>
                            <p class="text-muted small">JPG, PNG, WebP — Max 5MB</p>
                            <input type="file" id="tmMediaFileInput" class="d-none"
                                   accept="image/jpeg,image/png,image/gif,image/webp">
                        </div>
                        <div id="tmUploadProgress" class="progress mt-3 d-none" style="height:8px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                 role="progressbar" style="width:0%;"></div>
                        </div>
                    </div>
                    <div class="tab-pane fade show active" id="tm-library-panel" role="tabpanel">
                        <div class="mb-3">
                            <input type="text" id="tmMediaSearch" class="form-control"
                                   placeholder="Search by title or keyword..." oninput="loadTmMedia(1)">
                        </div>
                        <div class="row g-2" id="tmMediaGrid">
                            <div class="col-12 text-center text-muted p-5">Loading media...</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 pb-4 px-4">
                <button type="button" class="btn btn-light px-4" id="tmMediaBackBtn">Back</button>
                <button type="button" class="btn btn-primary px-4" id="tmInsertMediaBtn" disabled>
                    Use Selected Photo
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ─── Helpers ──────────────────────────────────────────────────────────────────
function clearModalErrors() {
    ['name', 'rating', 'message'].forEach(f => {
        const el = document.getElementById('err_' + f);
        if (el) el.textContent = '';
        const input = document.querySelector(`[name="${f}"]`);
        if (input) input.classList.remove('is-invalid');
    });
}

function showErrors(errors) {
    Object.keys(errors).forEach(field => {
        const el = document.getElementById('err_' + field);
        if (el) el.textContent = errors[field][0];
        const input = document.querySelector(`[name="${field}"]`);
        if (input) input.classList.add('is-invalid');
    });
}

function resetModal() {
    document.getElementById('testimonialForm').reset();
    document.getElementById('testimonialId').value = '';
    document.getElementById('methodField').value = 'POST';
    document.getElementById('imageInput').value = '';
    document.getElementById('imagePreview').style.display = 'none';
    document.getElementById('imagePlaceholder').style.display = 'flex';
    document.getElementById('removeImageBtn').classList.add('d-none');
    document.getElementById('ratingLabel').textContent = 'Click to rate';
    document.getElementById('testimonialRating').value = '';
    document.getElementById('msgCount').textContent = '0';
    setRatingDisplay(0);
    clearModalErrors();
}

// ─── Star Rating ──────────────────────────────────────────────────────────────
function setRating(value) {
    document.getElementById('testimonialRating').value = value;
    const labels = ['', 'Poor', 'Fair', 'Good', 'Very Good', 'Excellent'];
    document.getElementById('ratingLabel').textContent = labels[value] + ' (' + value + '/5)';
    setRatingDisplay(value);
}

function setRatingDisplay(value) {
    document.querySelectorAll('.star-btn').forEach(star => {
        const v = parseInt(star.dataset.value);
        star.style.color = v <= value ? '#f59e0b' : '#ccc';
        star.className = v <= value ? 'bi bi-star-fill star-btn fs-4' : 'bi bi-star star-btn fs-4';
    });
}

// Star hover effect + character counter — deferred until DOM is ready
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.star-btn').forEach(star => {
        star.addEventListener('mouseenter', function () {
            const v = parseInt(this.dataset.value);
            document.querySelectorAll('.star-btn').forEach(s => {
                s.style.color = parseInt(s.dataset.value) <= v ? '#f59e0b' : '#ccc';
            });
        });
        star.addEventListener('mouseleave', function () {
            const current = parseInt(document.getElementById('testimonialRating').value) || 0;
            setRatingDisplay(current);
        });
    });

    document.getElementById('testimonialMessage').addEventListener('input', function () {
        document.getElementById('msgCount').textContent = this.value.length;
    });
});

// ─── Open Add Modal ────────────────────────────────────────────────────────────
function openAddModal() {
    resetModal();
    document.getElementById('modalTitleText').textContent = 'Add Testimonial';
    const el = document.getElementById('testimonialModal');
    (bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el)).show();
}

// ─── Open Edit Modal ──────────────────────────────────────────────────────────
function openEditModal(id) {
    resetModal();
    document.getElementById('modalTitleText').textContent = 'Edit Testimonial';

    fetch(`{{ rtrim(route('admin.testimonials.index'), '/') }}/${id}`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) { Swal.fire('Error', 'Could not load testimonial.', 'error'); return; }
        const t = data.testimonial;

        document.getElementById('testimonialId').value    = t.id;
        document.getElementById('methodField').value      = 'PUT';
        document.getElementById('testimonialName').value  = t.name;
        document.getElementById('testimonialMessage').value = t.message;
        document.getElementById('msgCount').textContent   = t.message.length;
        document.getElementById('testimonialOrder').value = t.order;
        document.getElementById('testimonialStatus').value = t.status ? '1' : '0';
        setRating(t.rating);

        if (t.image) {
            document.getElementById('imageInput').value      = t.image;
            document.getElementById('imagePreview').src      = t.image;
            document.getElementById('imagePreview').style.display = 'block';
            document.getElementById('imagePlaceholder').style.display = 'none';
            document.getElementById('removeImageBtn').classList.remove('d-none');
        }

        const el = document.getElementById('testimonialModal');
        (bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el)).show();
    })
    .catch(() => Swal.fire('Error', 'An error occurred.', 'error'));
}

// ─── Form Submit (Create + Update) ────────────────────────────────────────────
document.getElementById('testimonialForm').addEventListener('submit', function (e) {
    e.preventDefault();
    clearModalErrors();

    const id     = document.getElementById('testimonialId').value;
    const method = document.getElementById('methodField').value;
    const url    = id ? `{{ rtrim(route('admin.testimonials.index'), '/') }}/${id}` : '{{ route("admin.testimonials.store") }}';

    const btn = document.getElementById('saveTestimonialBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

    const formData = new FormData(this);
    if (method === 'PUT') formData.append('_method', 'PUT');

    fetch(url, {
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
            bootstrap.Modal.getInstance(document.getElementById('testimonialModal')).hide();
            Swal.fire({
                icon: 'success', title: 'Saved!', text: data.message,
                timer: 1500, showConfirmButton: false
            }).then(() => window.location.reload());
        } else if (data.errors) {
            showErrors(data.errors);
        } else {
            Swal.fire('Error', data.message || 'Could not save.', 'error');
        }
    })
    .catch(() => Swal.fire('Error', 'An error occurred.', 'error'))
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-save me-1"></i> Save';
    });
});

// ─── Delete ────────────────────────────────────────────────────────────────────
function deleteTestimonial(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'This testimonial will be deleted.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then(result => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/admin/testimonials/${id}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function (response) {
                    if (response.success) {
                        $(`#row-${id}`).fadeOut();
                        Swal.fire('Deleted!', 'Testimonial has been deleted.', 'success');
                    }
                },
                error: function () {
                    Swal.fire('Error', 'Error deleting testimonial.', 'error');
                }
            });
        }
    });
}

// ─── Image Picker ──────────────────────────────────────────────────────────────
let _tmSelectedMedia = null;

function openTestimonialImagePicker() {
    _tmSelectedMedia = null;
    document.getElementById('tmInsertMediaBtn').disabled = true;
    document.getElementById('tmMediaSearch').value = '';
    loadTmMedia();
    const el = document.getElementById('testimonialMediaModal');
    (bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el)).show();
}

function removeTestimonialImage() {
    document.getElementById('imageInput').value = '';
    document.getElementById('imagePreview').src = '';
    document.getElementById('imagePreview').style.display = 'none';
    document.getElementById('imagePlaceholder').style.display = 'flex';
    document.getElementById('removeImageBtn').classList.add('d-none');
}

// Back button
document.getElementById('tmMediaBackBtn').addEventListener('click', function () {
    bootstrap.Modal.getInstance(document.getElementById('testimonialMediaModal')).hide();
    const el = document.getElementById('testimonialModal');
    (bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el)).show();
});

// Insert selected
document.getElementById('tmInsertMediaBtn').addEventListener('click', function () {
    if (!_tmSelectedMedia) return;
    document.getElementById('imageInput').value             = _tmSelectedMedia;
    document.getElementById('imagePreview').src             = _tmSelectedMedia;
    document.getElementById('imagePreview').style.display  = 'block';
    document.getElementById('imagePlaceholder').style.display = 'none';
    document.getElementById('removeImageBtn').classList.remove('d-none');
    bootstrap.Modal.getInstance(document.getElementById('testimonialMediaModal')).hide();
    const el = document.getElementById('testimonialModal');
    (bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el)).show();
    _tmSelectedMedia = null;
    this.disabled = true;
});

// ─── Media Library ──────────────────────────────────────────────────────────────
const tmUploadZone     = document.getElementById('tmUploadZone');
const tmMediaFileInput = document.getElementById('tmMediaFileInput');
tmUploadZone.addEventListener('click', () => tmMediaFileInput.click());
tmUploadZone.addEventListener('dragover',  e => { e.preventDefault(); tmUploadZone.classList.add('bg-light'); });
tmUploadZone.addEventListener('dragleave', e => { e.preventDefault(); tmUploadZone.classList.remove('bg-light'); });
tmUploadZone.addEventListener('drop', e => {
    e.preventDefault(); tmUploadZone.classList.remove('bg-light');
    if (e.dataTransfer.files.length) tmUploadFile(e.dataTransfer.files[0]);
});
tmMediaFileInput.addEventListener('change', function () { if (this.files.length) tmUploadFile(this.files[0]); });

function tmUploadFile(file) {
    const fd = new FormData();
    fd.append('file', file);
    fd.append('_token', '{{ csrf_token() }}');
    const bar = document.querySelector('#tmUploadProgress .progress-bar');
    document.getElementById('tmUploadProgress').classList.remove('d-none');
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '{{ route("admin.media.store") }}', true);
    xhr.setRequestHeader('Accept', 'application/json');
    xhr.upload.onprogress = e => { if (e.lengthComputable) bar.style.width = ((e.loaded / e.total) * 100) + '%'; };
    xhr.onload = function () {
        if (xhr.status === 200) { document.getElementById('tm-library-tab').click(); loadTmMedia(); }
        else Swal.fire('Upload Failed', 'Could not upload file.', 'error');
        document.getElementById('tmUploadProgress').classList.add('d-none');
        bar.style.width = '0%';
    };
    xhr.send(fd);
}

function loadTmMedia(page = 1) {
    const search = document.getElementById('tmMediaSearch')?.value || '';
    fetch(`{{ route("admin.media.index") }}?page=${page}&search=${encodeURIComponent(search)}`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        const grid = document.getElementById('tmMediaGrid');
        grid.innerHTML = '';
        if (!data.data || data.data.length === 0) {
            grid.innerHTML = '<div class="col-12 text-center text-muted p-5">No media found.</div>';
            return;
        }
        data.data.forEach(media => {
            const url = '/storage/' + media.path;
            grid.innerHTML += `
                <div class="col-md-2 col-sm-3 col-4 mb-2">
                    <div class="card h-100 tm-media-item border-2"
                         data-url="${url}" style="cursor:pointer;" onclick="selectTmMedia(this)">
                        <img src="${url}" class="card-img-top object-fit-cover" style="height:100px;" alt="">
                    </div>
                </div>`;
        });
    });
}

function selectTmMedia(element) {
    document.querySelectorAll('.tm-media-item').forEach(el => el.classList.remove('border-primary'));
    element.classList.add('border-primary');
    _tmSelectedMedia = element.dataset.url;
    document.getElementById('tmInsertMediaBtn').disabled = false;
}
</script>
@endpush
