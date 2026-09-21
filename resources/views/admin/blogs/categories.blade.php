@extends('admin.layouts.app')

@section('title', 'Categories')
@section('page', 'categories')

@section('content')
<div class="page-head">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.blogs.index') }}">Blogs</a></li>
                <li class="breadcrumb-item active">Categories</li>
            </ol>
        </nav>
        <h1>Categories</h1>
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
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Parent</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories ?? [] as $category)
                <tr id="row-{{ $category->id }}">
                    <td>{{ $category->id }}</td>
                    <td>
                        @if($category->image)
                            <img src="{{ Storage::disk('public')->url($category->image) }}" alt="{{ $category->name }}" width="50" height="50" class="object-fit-cover rounded">
                        @else
                            <span class="text-muted small">No Image</span>
                        @endif
                    </td>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->slug }}</td>
                    <td>{{ $category->parent ? $category->parent->name : 'None' }}</td>
                    <td>
                        <button class="btn btn-sm btn-info text-white me-1" onclick="editCategory({{ $category->id }}, '{{ addslashes($category->name) }}', '{{ $category->slug }}', '{{ $category->parent_id }}')" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteCategory({{ $category->id }})" title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

<!-- Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="categoryForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="category_id" name="id">
                <div class="modal-header bg-primary text-white" style="border-bottom: none;">
                    <h5 class="modal-title fw-bold" id="categoryModalLabel">Category</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="form-label fw-bold text-secondary">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg bg-light border-0" id="name" name="name" placeholder="e.g. Technology" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold text-secondary">Slug</label>
                        <input type="text" class="form-control bg-light border-0" id="slug" name="slug" placeholder="auto-generated-slug">
                        <small class="text-muted">Leave empty to auto-generate from name.</small>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold text-secondary">Parent Category</label>
                        <select class="form-select bg-light border-0" id="parent_id" name="parent_id">
                            <option value="">None (Top Level)</option>
                            @foreach($categories ?? [] as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Image</label>
                        <input type="file" class="form-control bg-light border-0" id="image" name="image" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4" id="saveBtn">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let categoryModal;
    
    $(document).ready(function() {
        categoryModal = new bootstrap.Modal(document.getElementById('categoryModal'));

        $('#name').on('input', function() {
            let title = $(this).val();
            let slug = title.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
            $('#slug').val(slug);
        });

        $('#categoryForm').on('submit', function(e) {
            e.preventDefault();
            let id = $('#category_id').val();
            let url = id ? `/admin/categories/${id}` : '/admin/categories';
            
            let formData = new FormData(this);
            if (id) {
                formData.append('_method', 'PUT');
            }
            
            $('#saveBtn').prop('disabled', true).text('Saving...');
            
            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        categoryModal.hide();
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Category saved successfully.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    }
                },
                error: function(xhr) {
                    let msg = 'Error saving category.';
                    if(xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire('Error', msg, 'error');
                },
                complete: function() {
                    $('#saveBtn').prop('disabled', false).text('Save Category');
                }
            });
        });
    });

    function showCreateModal() {
        $('#categoryForm')[0].reset();
        $('#category_id').val('');
        $('#categoryModalLabel').text('Add Category');
        categoryModal.show();
    }

    function editCategory(id, name, slug, parent_id) {
        $('#categoryForm')[0].reset();
        $('#category_id').val(id);
        $('#name').val(name);
        $('#slug').val(slug);
        $('#parent_id').val(parent_id);
        $('#categoryModalLabel').text('Edit Category');
        categoryModal.show();
    }

    function deleteCategory(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to delete this category?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/categories/${id}`,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            $(`#row-${id}`).fadeOut();
                            Swal.fire('Deleted!', 'Category has been deleted.', 'success');
                        } else {
                            Swal.fire('Error', response.message || 'Error deleting category', 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Error deleting category.', 'error');
                    }
                });
            }
        });
    }
</script>
@endpush
