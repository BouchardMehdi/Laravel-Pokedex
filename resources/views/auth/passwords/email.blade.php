<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/auth.css'])
</head>
<body>

<div class="auth-card">
    <div class="auth-title">Forgot Password</div>

    <p style="margin-top:-10px; margin-bottom:18px; color:#9fb0c0; font-size:13px; line-height:1.5;">
        Enter your email address and we will send you a link to reset your password.
    </p>

    {{-- Success message --}}
    @if (session('status'))
        <div style="padding:10px 12px; border-radius:14px; margin-bottom:14px;
                    background:rgba(34,197,94,.10); border:1px solid rgba(34,197,94,.35);">
            <strong style="color:#bbf7d0;">✅</strong>
            <span style="color:#bbf7d0; font-weight:700; font-size:13px;">
                {{ session('status') }}
            </span>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="form-group">
            <label for="email">Email Address</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autocomplete="email"
                autofocus
                placeholder="e.g. mehdi@gmail.com"
            >

            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-auth">
            Send Password Reset Link
        </button>
    </form>

    <div class="auth-links" style="margin-top:16px;">
        <a href="{{ route('login') }}">← Back to Login</a>
        <span style="opacity:.4; margin:0 8px;">•</span>
        <a href="{{ route('register') }}">Create an Account</a>
    </div>
</div>

</body>
</html>