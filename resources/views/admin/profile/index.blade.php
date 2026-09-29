@extends('admin.layouts.app')

@section('title', 'My Profile')
@section('page', 'profile')

@section('content')
<div class="page-head">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item active">Profile</li>
            </ol>
        </nav>
        <h1>My Profile</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <section class="panel">
            <h4 class="mb-4">Profile Details</h4>
            <div class="mb-3">
                <label class="form-label text-muted">Name</label>
                <input type="text" class="form-control" value="{{ auth()->user()->name }}" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Email</label>
                <input type="email" class="form-control" value="{{ auth()->user()->email }}" readonly>
            </div>
        </section>
    </div>

    <div class="col-md-6">
        <section class="panel">
            <h4 class="mb-4">Change Password</h4>
            
            @if(session('success'))
                <div class="alert alert-success border-0 bg-success text-white">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.profile.change-password') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Current Password</label>
                    <div class="input-group">
                        <input type="password" name="current_password" id="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <div class="input-group">
                        <input type="password" name="password" id="new_password" class="form-control @error('password') is-invalid @enderror" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Confirm New Password</label>
                    <div class="input-group">
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">Update Password</button>
            </form>
        </section>
    </div>
</div>

@push('scripts')
<script>
    function togglePassword(fieldId, btn) {
        const input = document.getElementById(fieldId);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    }
</script>
@endpush
@endsection
