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
                        @if($blog->status === 'published')
                            <span class="badge bg-success">Published</span>
                        @elseif($blog->status === 'scheduled')
                            <span class="badge bg-warning text-dark">Scheduled</span>
                        @else
                            <span class="badge bg-secondary">Draft</span>
                        @endif
                    </td>
                    <td>{{ $blog->created_at->format('M d, Y') }}</td>
                    <td>
                        <a href="{{ route('admin.blogs.edit', $blog->id) }}" class="btn btn-sm btn-info text-white me-1" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
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

@endsection

@push('scripts')
<script>
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
</script>
@endpush
