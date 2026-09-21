<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Bio Agriculture</title>
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
            <h4>Bio Agriculture</h4>
            <small>Admin Panel Login</small>
        </div>
        <div class="login-body">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-4">
                    <label for="email" class="form-label text-muted fw-semibold">Email Address</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="admin@gmail.com">
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label text-muted fw-semibold">Password</label>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="••••••••">
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-4 d-flex justify-content-between align-items-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label text-muted" for="remember">
                            Remember Me
                        </label>
                    </div>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-decoration-none" style="color: #1D3621; font-weight: 600; font-size: 0.9rem;">
                            Forgot Password?
                        </a>
                    @endif
                </div>

                <button type="submit" class="btn btn-login">
                    Sign In <i class="bi bi-arrow-right ms-2"></i>
                </button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
