@extends('admin.layouts.app')

@section('title', 'Resources')
@section('page', 'resources')

@section('content')
<div class="page-head">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item active">Resources</li>
            </ol>
        </nav>
        <h1>Resources</h1>
    </div>
    <div class="d-flex gap-2 mt-3">
        <a href="{{ route('admin.resources.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Add Resource
        </a>
    </div>
</div>

<section class="panel">
    <div class="table-responsive">
        <table class="table data-table align-middle w-100" id="resourcesTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>File Type</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($resources as $resource)
                <tr id="row-{{ $resource->id }}">
                    <td>{{ $resource->id }}</td>
                    <td>
                        {{ $resource->title }}
                        @if($resource->short_description)
                            <br><small class="text-muted">{{ \Illuminate\Support\Str::limit($resource->short_description, 60) }}</small>
                        @endif
                    </td>
                    <td>
                        @if($resource->file_type)
                            <span class="badge bg-info text-dark text-uppercase">{{ $resource->file_type }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @if($resource->status)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </td>
                    <td>{{ $resource->created_at->format('M d, Y') }}</td>
                    <td>
                        <a href="{{ route('admin.resources.edit', $resource->id) }}" class="btn btn-sm btn-info text-white me-1" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <button class="btn btn-sm btn-danger" onclick="deleteResource({{ $resource->id }})" title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        {{ $resources->links('pagination::bootstrap-5') }}
    </div>
</section>
@endsection

@push('scripts')
<script>
    function deleteResource(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to delete this resource?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/resources/${id}`,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            $(`#row-${id}`).fadeOut();
                            Swal.fire('Deleted!', 'Resource has been deleted.', 'success');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Error deleting resource.', 'error');
                    }
                });
            }
        });
    }
</script>
@endpush
