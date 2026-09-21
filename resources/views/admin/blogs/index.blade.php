@extends('admin.layouts.app')

@section('title', 'Blogs')
@section('page', 'blogs')

@section('content')
<div class="page-head">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item active">Blogs</li>
            </ol>
        </nav>
        <h1>Blogs</h1>
    </div>
    <div class="d-flex gap-2 mt-3">
        <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Add Blog
        </a>
    </div>
</div>

<section class="panel">
    <div class="table-responsive">
        <table class="table data-table align-middle w-100" id="blogsTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($blogs as $blog)
                <tr id="row-{{ $blog->id }}">
                    <td>{{ $blog->id }}</td>
                    <td>{{ $blog->title }}</td>
                    <td>{{ $blog->author->name ?? 'Admin' }}</td>
                    <td>
                        @if($blog->status)
                            <span class="badge bg-success">Published</span>
                        @else
                            <span class="badge bg-secondary">Draft</span>
                        @endif
                    </td>
                    <td>{{ $blog->created_at->format('M d, Y') }}</td>
                    <td>
                        <button class="btn btn-sm btn-info text-white me-1" onclick="editBlog({{ $blog->id }})" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteBlog({{ $blog->id }})" title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        {{ $blogs->links('pagination::bootstrap-5') }}
    </div>
</section>

<!-- Blog Modal -->
<div class="modal fade" id="blogModal" tabindex="-1" aria-labelledby="blogModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="blogForm">
                @csrf
                <input type="hidden" id="blog_id" name="id">
                <div class="modal-header bg-primary text-white" style="border-bottom: none;">
                    <h5 class="modal-title fw-bold" id="blogModalLabel">Blog</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="title" class="form-label fw-bold text-secondary">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg bg-light border-0" id="title" name="title" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="slug" class="form-label fw-bold text-secondary">Slug <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg bg-light border-0" id="slug" name="slug" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="short_description" class="form-label fw-bold text-secondary">Short Description</label>
                        <textarea class="form-control bg-light border-0" id="short_description" name="short_description" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label fw-bold text-secondary">Status</label>
                        <select class="form-select bg-light border-0" id="status" name="status">
                            <option value="1">Published</option>
                            <option value="0">Draft</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary px-4" id="saveBtn">Save Blog</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let blogModal;
    
    $(document).ready(function() {
        blogModal = new bootstrap.Modal(document.getElementById('blogModal'));

        // Auto generate slug
        $('#title').on('input', function() {
            let title = $(this).val();
            let slug = title.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
            $('#slug').val(slug);
        });

        $('#blogForm').on('submit', function(e) {
            e.preventDefault();
            let id = $('#blog_id').val();
            let url = id ? `/admin/blogs/${id}` : '/admin/blogs';
            let method = id ? 'PUT' : 'POST';
            
            $('#saveBtn').prop('disabled', true).text('Saving...');
            
            $.ajax({
                url: url,
                type: method,
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        blogModal.hide();
                        showToast(response.message);
                        setTimeout(() => location.reload(), 1000);
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Error saving blog.', 'error');
                },
                complete: function() {
                    $('#saveBtn').prop('disabled', false).text('Save Blog');
                }
            });
        });
    });

    function showCreateModal() {
        $('#blogForm')[0].reset();
        $('#blog_id').val('');
        $('#blogModalLabel').text('Add Blog');
        blogModal.show();
    }

    function editBlog(id) {
        window.location.href = `/admin/blogs/${id}/edit`;
    }

    function deleteBlog(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to delete this blog post?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/blogs/${id}`,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            $(`#row-${id}`).fadeOut();
                            Swal.fire('Deleted!', 'Blog has been deleted.', 'success');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Error deleting blog.', 'error');
                    }
                });
            }
        });
    }

    function showToast(message) {
        $('#toastMessage').text(message);
        let toast = new bootstrap.Toast(document.getElementById('liveToast'));
        toast.show();
    }
</script>
@endpush
