@extends('admin.layouts.app')

@section('title', 'Projects')
@section('page', 'projects.all')

@section('content')
<div class="page-head">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item active">Projects</li>
            </ol>
        </nav>
        <h1>Projects</h1>
    </div>
    <div class="d-flex gap-2 mt-3">
        <a href="{{ route('admin.projects.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Add New Project
        </a>
    </div>
</div>

<section class="panel mb-4">
    <form method="GET" action="{{ route('admin.projects.index') }}" class="row g-3 align-items-center mb-3">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Search by title, crop, client, location..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Published</option>
                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Draft</option>
            </select>
        </div>
        <div class="col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-outline-primary">Filter</button>
            @if(request('search') || request('status') !== null)
                <a href="{{ route('admin.projects.index') }}" class="btn btn-outline-secondary">Reset</a>
            @endif
        </div>
    </form>

    <div class="table-responsive">
        <table class="table data-table align-middle w-100" id="projectsTable">
            <thead>
                <tr>
                    <th style="width: 70px;">Image</th>
                    <th>Project</th>
                    <th>Crop / Type</th>
                    <th>Location & Client</th>
                    <th>Area</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($projects as $project)
                <tr id="row-{{ $project->id }}">
                    <td>
                        @if($project->featured_image)
                            <img src="{{ $project->featured_image }}" alt="{{ $project->title }}" class="rounded object-fit-cover border" style="width: 54px; height: 42px;">
                        @else
                            <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted border" style="width: 54px; height: 42px;">
                                <i class="bi bi-image" style="font-size: 1.2rem;"></i>
                            </div>
                        @endif
                    </td>
                    <td>
                        <strong class="d-block">{{ $project->title }}</strong>
                        <small class="text-muted">/{{ $project->slug }}</small>
                        @if($project->is_featured)
                            <span class="badge bg-warning text-dark ms-1" style="font-size: 0.7rem;">Featured</span>
                        @endif
                    </td>
                    <td>
                        <div><strong>{{ $project->crop ?? '—' }}</strong></div>
                        <small class="text-muted"><i class="bi bi-tag me-1"></i>{{ $project->category->name ?? ($project->project_type ?? 'Polyhouse') }}</small>
                    </td>
                    <td>
                        <div><i class="bi bi-geo-alt text-danger me-1"></i>{{ $project->location ?? '—' }}</div>
                        <small class="text-muted">{{ $project->client_name ?? '—' }}</small>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">{{ $project->area ?? '—' }}</span>
                    </td>
                    <td>
                        @if($project->status)
                            <span class="badge bg-success">Published</span>
                        @else
                            <span class="badge bg-secondary">Draft</span>
                        @endif
                        <span class="badge bg-info-subtle text-info-emphasis border ms-1">{{ $project->project_status ?? 'Completed' }}</span>
                    </td>
                    <td>
                        <a href="{{ route('admin.projects.edit', $project->id) }}" class="btn btn-sm btn-info text-white me-1" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <button class="btn btn-sm btn-danger" onclick="deleteProject({{ $project->id }})" title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <i class="bi bi-briefcase fs-1 d-block mb-2"></i>
                        No projects found. <a href="{{ route('admin.projects.create') }}">Add your first project</a>.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        {{ $projects->links('pagination::bootstrap-5') }}
    </div>
</section>
@endsection

@push('scripts')
<script>
    function deleteProject(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This project will be moved to trash.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/projects/${id}`, {
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
                            text: data.message || 'Project has been deleted.',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    } else {
                        Swal.fire('Error', data.message || 'Error deleting project.', 'error');
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

