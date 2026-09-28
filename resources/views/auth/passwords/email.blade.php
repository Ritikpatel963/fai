<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Bio Agriculture</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
        }
        .login-header {
            background-color: #1D3621;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .login-header h4 {
            margin: 0;
            font-weight: 600;
        }
        .login-body {
            padding: 40px 30px;
            background-color: white;
        }
        .form-control {
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #e3e3e0;
        }
        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(29, 54, 33, 0.25);
            border-color: #1D3621;
        }
        .btn-login {
            background-color: #1D3621;
            color: white;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
        }
        .btn-login:hover {
            background-color: #122415;
            color: white;
        }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center">
    <div class="login-card">
        <div class="login-header">
            <h4>Reset Password</h4>
            <small>We'll send you a link to reset it</small>
        </div>
        <div class="login-body">
            @if (session('status'))
                <div class="alert alert-success border-0 bg-success text-white mb-4">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('reset_link'))
                <div class="alert mb-4" style="background-color: #e8f5e9; border: 1px solid #1D3621; border-radius: 8px;">
                    <p class="mb-2 fw-semibold" style="color: #1D3621;">
                        <i class="bi bi-check-circle me-1"></i> Reset link generated for <strong>{{ session('reset_email') }}</strong>
                    </p>
                    <p class="mb-2 text-muted small">Click the link below to reset your password:</p>
                    <a href="{{ session('reset_link') }}" class="btn btn-sm btn-success w-100">
                        <i class="bi bi-key me-1"></i> Click here to Reset Password
                    </a>
                    <p class="mt-2 mb-0 text-muted" style="font-size: 0.75rem; word-break: break-all;">{{ session('reset_link') }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="mb-4">
                    <label for="email" class="form-label text-muted fw-semibold">Email Address</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-login mb-3">
                    Send Password Reset Link
                </button>
                
                <div class="text-center mt-3">
                    <a href="{{ route('login') }}" class="text-decoration-none text-muted" style="font-weight: 600; font-size: 0.9rem;">
                        <i class="bi bi-arrow-left me-1"></i> Back to Login
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>
