@extends('admin.layouts.app')

@section('title', 'Contact Lead Details')
@section('page', 'contact-leads')

@section('content')
<div class="page-head">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.contact-leads.index') }}">Contact Leads</a></li>
                <li class="breadcrumb-item active">#{{ $contactLead->id }} — {{ $contactLead->name }}</li>
            </ol>
        </nav>
        <h1>Lead Details</h1>
    </div>
    <div class="d-flex gap-2 mt-3">
        <a href="{{ route('admin.contact-leads.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Leads
        </a>
    </div>
</div>

<div class="row">

    {{-- Lead Info --}}
    <div class="col-lg-8">
        <section class="panel mb-4">
            <h5 class="fw-bold mb-4 border-bottom pb-2"><i class="bi bi-person me-2"></i>Contact Information</h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="text-muted small fw-semibold">Full Name</label>
                    <p class="mb-0 fw-bold">{{ $contactLead->name }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="text-muted small fw-semibold">Company</label>
                    <p class="mb-0">{{ $contactLead->company ?? '—' }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="text-muted small fw-semibold">Email</label>
                    <p class="mb-0">
                        @if($contactLead->email)
                            <a href="mailto:{{ $contactLead->email }}">{{ $contactLead->email }}</a>
                        @else
                            —
                        @endif
                    </p>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="text-muted small fw-semibold">Phone</label>
                    <p class="mb-0">
                        @if($contactLead->phone)
                            <a href="tel:{{ $contactLead->phone }}">{{ $contactLead->phone }}</a>
                        @else
                            —
                        @endif
                    </p>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="text-muted small fw-semibold">Source</label>
                    <p class="mb-0">{{ $contactLead->source ?? '—' }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="text-muted small fw-semibold">Received On</label>
                    <p class="mb-0">{{ $contactLead->created_at->format('M d, Y \a\t h:i A') }}</p>
                </div>
            </div>
        </section>

        <section class="panel mb-4">
            <h5 class="fw-bold mb-4 border-bottom pb-2"><i class="bi bi-chat-left-text me-2"></i>Message</h5>
            <div class="mb-3">
                <label class="text-muted small fw-semibold">Subject</label>
                <p class="mb-0 fw-bold">{{ $contactLead->subject ?? '—' }}</p>
            </div>
            <div>
                <label class="text-muted small fw-semibold">Message</label>
                <p class="mb-0" style="white-space: pre-line;">{{ $contactLead->message ?? '—' }}</p>
            </div>
        </section>
    </div>

    {{-- Sidebar: Status + Notes --}}
    <div class="col-lg-4">
        <section class="panel mb-4">
            <h5 class="fw-bold mb-4 border-bottom pb-2"><i class="bi bi-sliders me-2"></i>Manage Lead</h5>

            <form id="updateLeadForm">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" id="leadStatus" class="form-select">
                        @php
                            $statuses = [
                                'new'         => 'New',
                                'contacted'   => 'Contacted',
                                'in_progress' => 'In Progress',
                                'converted'   => 'Converted',
                                'closed'      => 'Closed',
                            ];
                        @endphp
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}" {{ $contactLead->status === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Internal Notes</label>
                    <textarea name="notes" id="leadNotes" class="form-control" rows="5" placeholder="Add internal notes about this lead...">{{ $contactLead->notes }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-save me-1"></i> Save Changes
                </button>
            </form>

            <hr class="my-4">

            <button class="btn btn-outline-danger w-100" onclick="deleteLead({{ $contactLead->id }})">
                <i class="bi bi-trash me-1"></i> Delete Lead
            </button>
        </section>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.getElementById('updateLeadForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const status = document.getElementById('leadStatus').value;
        const notes  = document.getElementById('leadNotes').value;

        const formData = new FormData();
        formData.append('_method', 'PUT');
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('status', status);
        formData.append('notes', notes);

        fetch('/admin/contact-leads/{{ $contactLead->id }}', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Updated!',
                    text: 'Lead has been updated successfully.',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => location.reload());
            } else {
                Swal.fire('Error', 'Failed to update lead.', 'error');
            }
        })
        .catch(() => Swal.fire('Error', 'An error occurred.', 'error'));
    });

    function deleteLead(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This lead will be deleted permanently.",
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
                            Swal.fire('Deleted!', 'Lead has been deleted.', 'success')
                            .then(() => window.location.href = '{{ route('admin.contact-leads.index') }}');
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
