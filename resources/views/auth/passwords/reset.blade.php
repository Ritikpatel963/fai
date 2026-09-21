<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password - Bio Agriculture</title>
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
            <h4>Set New Password</h4>
            <small>Choose a secure password</small>
        </div>
        <div class="login-body">
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-4">
                    <label for="email" class="form-label text-muted fw-semibold">Email Address</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus readonly>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label text-muted fw-semibold">New Password</label>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password-confirm" class="form-label text-muted fw-semibold">Confirm Password</label>
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                </div>

                <button type="submit" class="btn btn-login mb-3">
                    Reset Password
                </button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
