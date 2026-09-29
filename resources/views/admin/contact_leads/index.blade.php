@extends('admin.layouts.app')

@section('title', 'Contact Leads')
@section('page', 'contact-leads')

@section('content')
<div class="page-head">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item active">Contact Leads</li>
            </ol>
        </nav>
        <h1>Contact Leads</h1>
    </div>
</div>

{{-- Status Summary Badges --}}
<div class="d-flex flex-wrap gap-2 mb-4">
    <span class="badge bg-secondary fs-6 px-3 py-2">All <span class="ms-1 fw-bold">{{ $statusCounts['all'] }}</span></span>
    <span class="badge bg-primary fs-6 px-3 py-2">New <span class="ms-1 fw-bold">{{ $statusCounts['new'] }}</span></span>
    <span class="badge bg-info text-dark fs-6 px-3 py-2">Contacted <span class="ms-1 fw-bold">{{ $statusCounts['contacted'] }}</span></span>
    <span class="badge bg-warning text-dark fs-6 px-3 py-2">In Progress <span class="ms-1 fw-bold">{{ $statusCounts['in_progress'] }}</span></span>
    <span class="badge bg-success fs-6 px-3 py-2">Converted <span class="ms-1 fw-bold">{{ $statusCounts['converted'] }}</span></span>
    <span class="badge bg-dark fs-6 px-3 py-2">Closed <span class="ms-1 fw-bold">{{ $statusCounts['closed'] }}</span></span>
</div>

<section class="panel">
    <div class="table-responsive">
        <table class="table data-table align-middle w-100" id="leadsTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Received</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($leads as $lead)
                <tr id="row-{{ $lead->id }}">
                    <td>{{ $lead->id }}</td>
                    <td>
                        {{ $lead->name }}
                        @if($lead->company)
                            <br><small class="text-muted">{{ $lead->company }}</small>
                        @endif
                    </td>
                    <td>{{ $lead->email ?? '—' }}</td>
                    <td>{{ $lead->phone ?? '—' }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($lead->subject ?? '—', 40) }}</td>
                    <td>
                        @php
                            $statusMap = [
                                'new'         => 'bg-primary',
                                'contacted'   => 'bg-info text-dark',
                                'in_progress' => 'bg-warning text-dark',
                                'converted'   => 'bg-success',
                                'closed'      => 'bg-dark',
                            ];
                            $badgeClass = $statusMap[$lead->status] ?? 'bg-secondary';
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $lead->status)) }}</span>
                    </td>
                    <td>{{ $lead->created_at->format('M d, Y') }}</td>
                    <td>
                        <a href="{{ route('admin.contact-leads.show', $lead->id) }}" class="btn btn-sm btn-info text-white me-1" title="View">
                            <i class="bi bi-eye"></i>
                        </a>
                        <button class="btn btn-sm btn-danger" onclick="deleteLead({{ $lead->id }})" title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        {{ $leads->links('pagination::bootstrap-5') }}
    </div>
</section>
@endsection

@push('scripts')
<script>
    function deleteLead(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to delete this contact lead?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/contact-leads/${id}`,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            $(`#row-${id}`).fadeOut();
                            Swal.fire('Deleted!', 'Lead has been deleted.', 'success');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Error deleting lead.', 'error');
                    }
                });
            }
        });
    }
</script>
@endpush
