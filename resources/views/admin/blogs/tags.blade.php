@extends('admin.layouts.app')

@section('title', 'Tags')
@section('page', 'tags')

@section('content')
<div class="page-head">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.blogs.index') }}">Blogs</a></li>
                <li class="breadcrumb-item active">Tags</li>
            </ol>
        </nav>
        <h1>Tags</h1>
    </div>
    <div class="d-flex gap-2 mt-3">
        <button class="btn btn-primary" onclick="showCreateModal()">
            <i class="bi bi-plus-circle me-2"></i>Add Tag
        </button>
    </div>
</div>

<section class="panel">
    <div class="table-responsive">
        <table class="table data-table align-middle w-100" id="tagsTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach(\App\Models\Tag::all() as $tag)
                <tr id="row-{{ $tag->id }}">
                    <td>{{ $tag->id }}</td>
                    <td>{{ $tag->name }}</td>
                    <td>{{ $tag->slug }}</td>
                    <td>
                        <button class="btn btn-sm btn-info text-white me-1" onclick="editTag({{ $tag->id }}, '{{ addslashes($tag->name) }}', '{{ $tag->slug }}')" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteTag({{ $tag->id }})" title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

<!-- Tag Modal -->
<div class="modal fade" id="tagModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="tagForm">
                @csrf
                <input type="hidden" id="tag_id" name="id">
                <div class="modal-header bg-primary text-white" style="border-bottom: none;">
                    <h5 class="modal-title fw-bold" id="tagModalLabel">Tag</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="form-label fw-bold text-secondary">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg bg-light border-0" id="name" name="name" placeholder="e.g. Tips & Tricks" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Slug</label>
                        <input type="text" class="form-control bg-light border-0" id="slug" name="slug" placeholder="auto-generated-slug">
                        <small class="text-muted">Leave empty to auto-generate from name.</small>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4" id="saveBtn">Save Tag</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let tagModal;
    
    $(document).ready(function() {
        tagModal = new bootstrap.Modal(document.getElementById('tagModal'));

        $('#name').on('input', function() {
            let title = $(this).val();
            let slug = title.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
            $('#slug').val(slug);
        });

        $('#tagForm').on('submit', function(e) {
            e.preventDefault();
            let id = $('#tag_id').val();
            let url = id ? `/admin/tags/${id}` : '/admin/tags';
            let method = id ? 'PUT' : 'POST';
            
            $('#saveBtn').prop('disabled', true).text('Saving...');
            
            $.ajax({
                url: url,
                type: method,
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        tagModal.hide();
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Tag saved successfully.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Error saving tag.', 'error');
                },
                complete: function() {
                    $('#saveBtn').prop('disabled', false).text('Save Tag');
                }
            });
        });
    });

    function showCreateModal() {
        $('#tagForm')[0].reset();
        $('#tag_id').val('');
        $('#tagModalLabel').text('Add Tag');
        tagModal.show();
    }

    function editTag(id, name, slug) {
        $('#tagForm')[0].reset();
        $('#tag_id').val(id);
        $('#name').val(name);
        $('#slug').val(slug);
        $('#tagModalLabel').text('Edit Tag');
        tagModal.show();
    }

    function deleteTag(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to delete this tag?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/tags/${id}`,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            $(`#row-${id}`).fadeOut();
                            Swal.fire('Deleted!', 'Tag has been deleted.', 'success');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Error deleting tag.', 'error');
                    }
                });
            }
        });
    }
</script>
@endpush
