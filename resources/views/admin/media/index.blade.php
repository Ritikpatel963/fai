@extends('admin.layouts.app')

@section('title', 'Media Library')
@section('page', 'media')

@section('content')
<div class="page-head">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item active">Media Library</li>
            </ol>
        </nav>
        <h1>Media Library</h1>
    </div>
</div>

<section class="panel">
    <!-- Filters -->
    <div class="row g-3 align-items-end mb-4">
        <div class="col-lg-5 col-md-6">
            <label class="form-label text-muted small fw-semibold mb-1">Search</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text"
                       id="searchMedia"
                       class="form-control border-start-0 ps-0"
                       placeholder="Search by title, keyword..."
                       style="box-shadow:none;">
                <button class="btn btn-outline-secondary border-start-0" type="button"
                        onclick="document.getElementById('searchMedia').value=''; loadMediaLibrary(1);"
                        title="Clear search">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <label class="form-label text-muted small fw-semibold mb-1">From Date</label>
            <input type="date" id="startDate" class="form-control" title="Start Date">
        </div>
        <div class="col-lg-3 col-md-6">
            <label class="form-label text-muted small fw-semibold mb-1">To Date</label>
            <input type="date" id="endDate" class="form-control" title="End Date">
        </div>
        <div class="col-lg-1 col-md-6">
            <button class="btn btn-primary w-100" onclick="loadMediaLibrary(1)" title="Apply Filter">
                <i class="bi bi-funnel-fill"></i>
            </button>
        </div>
    </div>

    <!-- Media Grid -->
    <div class="row g-3" id="mediaLibraryGrid">
        <div class="col-12 text-center text-muted p-5">Loading media...</div>
    </div>

    <!-- Pagination -->
    <div class="mt-4 d-flex justify-content-center d-none" id="mediaPagination">
        <button class="btn btn-outline-primary" id="loadMoreBtn" onclick="loadMore()">Load More</button>
    </div>
</section>

<!-- Edit Media Modal -->
<div class="modal fade" id="editMediaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white" style="border-bottom: none;">
                <h5 class="modal-title fw-bold">Edit Media</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="editMediaForm">
                    <input type="hidden" id="edit_media_id">
                    <div class="text-center mb-3">
                        <img id="edit_media_preview" src="" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Title</label>
                        <input type="text" class="form-control border-0" id="edit_title" name="title">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Alt Text</label>
                        <input type="text" class="form-control border-0" id="edit_alt_text" name="alt_text">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Keywords</label>
                        <input type="text" class="form-control border-0" id="edit_keywords" name="keywords" placeholder="Comma separated keywords">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Caption</label>
                        <textarea class="form-control border-0" id="edit_caption" name="caption" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary px-4" id="saveMediaBtn" onclick="saveMedia()">Save Changes</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let currentPage = 1;
    let lastPage = 1;

    function loadMediaLibrary(page = 1) {
        currentPage = page;
        const search = document.getElementById('searchMedia').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        if (page === 1) {
            document.getElementById('mediaLibraryGrid').innerHTML = '<div class="col-12 text-center p-5"><div class="spinner-border text-primary" role="status"></div></div>';
        }

        fetch(`{{ route('admin.media.index') }}?page=${page}&search=${search}&start_date=${startDate}&end_date=${endDate}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            lastPage = data.last_page;
            
            let html = '';
            if (data.data.length === 0 && page === 1) {
                html = '<div class="col-12 text-center text-muted p-5">No media found.</div>';
            } else {
                data.data.forEach(media => {
                    const url = `/storage/${media.path}`;
                    html += `
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6" id="media-card-${media.id}">
                            <div class="card h-100 position-relative group border-0 shadow-sm">
                                <img src="${url}" class="card-img-top object-fit-cover" style="height: 150px; cursor: pointer;" alt="${media.alt_text || ''}" onclick='openEditModal(${JSON.stringify(media)})'>
                                <div class="card-body p-2">
                                    <small class="d-block text-truncate text-muted fw-bold" title="${media.title || media.original_filename}">${media.title || media.original_filename}</small>
                                </div>
                                <button class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" onclick="deleteMedia(${media.id}, this)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    `;
                });
            }

            if (page === 1) {
                document.getElementById('mediaLibraryGrid').innerHTML = html;
            } else {
                document.getElementById('mediaLibraryGrid').insertAdjacentHTML('beforeend', html);
            }

            if (currentPage < lastPage) {
                document.getElementById('mediaPagination').classList.remove('d-none');
            } else {
                document.getElementById('mediaPagination').classList.add('d-none');
            }
        });
    }

    function loadMore() {
        if (currentPage < lastPage) {
            loadMediaLibrary(currentPage + 1);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadMediaLibrary(1);

        document.getElementById('searchMedia').addEventListener('input', function() {
            clearTimeout(this._searchTimer);
            this._searchTimer = setTimeout(() => loadMediaLibrary(1), 400);
        });
    });

    let editModal;
    document.addEventListener('DOMContentLoaded', () => {
        editModal = new bootstrap.Modal(document.getElementById('editMediaModal'));
    });

    function openEditModal(media) {
        document.getElementById('edit_media_id').value = media.id;
        document.getElementById('edit_title').value = media.title || '';
        document.getElementById('edit_alt_text').value = media.alt_text || '';
        document.getElementById('edit_keywords').value = media.keywords || '';
        document.getElementById('edit_caption').value = media.caption || '';
        document.getElementById('edit_media_preview').src = `/storage/${media.path}`;
        
        editModal.show();
    }

    function saveMedia() {
        const id = document.getElementById('edit_media_id').value;
        const btn = document.getElementById('saveMediaBtn');
        btn.disabled = true;
        btn.innerHTML = 'Saving...';

        const data = {
            _method: 'PUT',
            _token: '{{ csrf_token() }}',
            title: document.getElementById('edit_title').value,
            alt_text: document.getElementById('edit_alt_text').value,
            keywords: document.getElementById('edit_keywords').value,
            caption: document.getElementById('edit_caption').value,
        };

        fetch(`/admin/media/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = 'Save Changes';
            if (data.success) {
                editModal.hide();
                Swal.fire({
                    icon: 'success',
                    title: 'Saved',
                    text: 'Media updated successfully!',
                    timer: 1500,
                    showConfirmButton: false
                });
                // Optional: reload grid to reflect new title
                loadMediaLibrary(1);
            } else {
                Swal.fire('Error', 'Failed to update media.', 'error');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = 'Save Changes';
            Swal.fire('Error', 'An error occurred.', 'error');
        });
    }

    function deleteMedia(id, btn) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to delete this media file?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/media/${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById(`media-card-${id}`).remove();
                        Swal.fire('Deleted!', 'Media has been deleted.', 'success');
                    }
                });
            }
        });
    }
</script>
@endpush
