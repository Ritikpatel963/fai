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
                    <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                    @error('current_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Update Password</button>
            </form>
        </section>
    </div>
</div>
@endsection
