@extends('admin.layouts.app')

@section('title', 'Project Categories')
@section('page', 'project-categories')

@section('content')
<div class="page-head">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.projects.index') }}">Projects</a></li>
                <li class="breadcrumb-item active">Categories</li>
            </ol>
        </nav>
        <h1>Project Categories (Structure Types)</h1>
    </div>
    <div class="d-flex gap-2 mt-3">
        <button class="btn btn-primary" onclick="showCreateModal()">
            <i class="bi bi-plus-circle me-2"></i>Add Category
        </button>
    </div>
</div>

<section class="panel">
    <div class="table-responsive">
        <table class="table data-table align-middle w-100" id="categoriesTable">
            <thead>
                <tr>
                    <th style="width: 70px;">ID</th>
                    <th>Category Name</th>
                    <th>Slug</th>
                    <th>Description</th>
                    <th>Projects</th>
                    <th>Status</th>
                    <th style="width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories ?? [] as $category)
                <tr id="row-{{ $category->id }}">
                    <td>{{ $category->id }}</td>
                    <td>
                        <strong class="d-block">{{ $category->name }}</strong>
                    </td>
                    <td><code>{{ $category->slug }}</code></td>
                    <td>
                        <small class="text-muted">{{ Str::limit($category->description, 50) ?: '—' }}</small>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">{{ $category->projects_count }} projects</span>
                    </td>
                    <td>
                        @if($category->status)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-sm btn-info text-white me-1" onclick="editCategory({{ $category->id }}, '{{ addslashes($category->name) }}', '{{ $category->slug }}', '{{ addslashes($category->description ?? '') }}', {{ $category->status ? 1 : 0 }})" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteCategory({{ $category->id }})" title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <i class="bi bi-tags fs-1 d-block mb-2"></i>
                        No project categories found. Click "Add Category" to create your first structure type.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

{{-- Create / Edit Modal --}}
<div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white" style="border-bottom: none;">
                <h5 class="modal-title fw-bold" id="modalTitle">Add Project Category</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="categoryForm">
                @csrf
                <input type="hidden" id="categoryId" name="id">
                <input type="hidden" id="methodField" name="_method" value="POST">

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Category Name *</label>
                        <input type="text" class="form-control" id="catName" name="name" placeholder="e.g. Naturally Ventilated Polyhouse (NVPH)" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Slug</label>
                        <input type="text" class="form-control" id="catSlug" name="slug" placeholder="auto-generated-slug">
                        <small class="text-muted">Unique URL identifier (leave empty to auto-generate)</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea class="form-control" id="catDescription" name="description" rows="3" placeholder="Brief description of this structure type..."></textarea>
                    </div>

                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="catStatus" name="status" value="1" checked>
                        <label class="form-check-label fw-bold" for="catStatus">Active</label>
                    </div>
                </div>

                <div class="modal-footer border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4" id="submitBtn">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let categoryModal = null;

    document.addEventListener('DOMContentLoaded', function() {
        categoryModal = new bootstrap.Modal(document.getElementById('categoryModal'));

        // Auto-slug generation
        document.getElementById('catName').addEventListener('input', function() {
            if (!document.getElementById('categoryId').value) {
                const slug = this.value
                    .toLowerCase()
                    .trim()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/[\s_-]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                document.getElementById('catSlug').value = slug;
            }
        });

        // Form submission via AJAX
        document.getElementById('categoryForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;

            const categoryId = document.getElementById('categoryId').value;
            const isEdit = Boolean(categoryId);
            const url = isEdit ? `/admin/project-categories/${categoryId}` : '{{ route("admin.project-categories.store") }}';

            const formData = new FormData(this);
            if (isEdit) {
                formData.append('_method', 'PUT');
            }

            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json().then(data => ({ ok: res.ok, data })))
            .then(({ ok, data }) => {
                if (ok && data.success) {
                    categoryModal.hide();
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message || 'Category saved successfully.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    const msg = data.errors
                        ? Object.values(data.errors).flat().join('\n')
                        : (data.message || 'Error saving category.');
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

    function showCreateModal() {
        document.getElementById('categoryForm').reset();
        document.getElementById('categoryId').value = '';
        document.getElementById('methodField').value = 'POST';
        document.getElementById('modalTitle').textContent = 'Add Project Category';
        document.getElementById('catStatus').checked = true;
        document.getElementById('submitBtn').disabled = false;
        categoryModal.show();
    }

    function editCategory(id, name, slug, description, status) {
        document.getElementById('categoryForm').reset();
        document.getElementById('categoryId').value = id;
        document.getElementById('methodField').value = 'PUT';
        document.getElementById('modalTitle').textContent = 'Edit Project Category';
        document.getElementById('catName').value = name;
        document.getElementById('catSlug').value = slug;
        document.getElementById('catDescription').value = description || '';
        document.getElementById('catStatus').checked = (status === 1);
        document.getElementById('submitBtn').disabled = false;
        categoryModal.show();
    }

    function deleteCategory(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This project category will be permanently removed.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/project-categories/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const row = document.getElementById(`row-${id}`);
                        if (row) {
                            row.style.transition = 'opacity 0.3s ease';
                            row.style.opacity = '0';
                            setTimeout(() => row.remove(), 300);
                        }
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: data.message || 'Category deleted.',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2500
                        });
                    } else {
                        Swal.fire('Cannot Delete', data.message || 'Error deleting category.', 'error');
                    }
                })
                .catch(() => {
                    Swal.fire('Error', 'An error occurred while deleting.', 'error');
                });
            }
        });
    }
</script>
@endpush

