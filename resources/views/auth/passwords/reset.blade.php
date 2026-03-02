@extends('layouts.app')
@vite(['resources/css/auth.css'])
@section('content')
<div class="auth-wrapper">
    <div class="auth-card">

        <h1 class="auth-title">Nouveau mot de passe</h1>

        <p class="auth-sub">
            Choisis un nouveau mot de passe sécurisé.
        </p>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            {{-- Email --}}
            <div class="form-group">
                <label for="email">Adresse email</label>
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

            {{-- Nouveau mot de passe --}}
            <div class="form-group">
                <label for="password">Nouveau mot de passe</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="new-password"
                    placeholder="Minimum 8 caractères"
                >
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Confirmation --}}
            <div class="form-group">
                <label for="password-confirm">Confirmer le mot de passe</label>
                <input
                    id="password-confirm"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="Confirme ton mot de passe"
                >
            </div>

            <button type="submit" class="btn-auth">
                Réinitialiser le mot de passe
            </button>
        </form>

        <div class="auth-links">
            <a href="{{ route('login') }}">← Retour à la connexion</a>
        </div>

    </div>
</div>
@endsection