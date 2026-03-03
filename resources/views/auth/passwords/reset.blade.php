@extends('layouts.app')
@vite(['resources/css/auth.css'])
@section('content')
<div class="auth-wrapper">
    <div class="auth-card">

        <h1 class="auth-title">New Password</h1>

        <p class="auth-sub">
            Choose a secure new password.
        </p>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            {{-- Email --}}
            <div class="form-group">
                <label for="email">Email Address</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ $email ?? old('email') }}"
                    required
                    autocomplete="email"
                >
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- New password --}}
            <div class="form-group">
                <label for="password">New Password</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="new-password"
                    placeholder="Minimum 8 characters"
                >
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Confirmation --}}
            <div class="form-group">
                <label for="password-confirm">Confirm Password</label>
                <input
                    id="password-confirm"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="Confirm your password"
                >
            </div>

            <button type="submit" class="btn-auth">
                Reset Password
            </button>
        </form>

        <div class="auth-links">
            <a href="{{ route('login') }}">← Back to Login</a>
        </div>

    </div>
</div>
@endsection