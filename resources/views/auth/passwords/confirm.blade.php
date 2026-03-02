@extends('layouts.app')
@vite(['resources/css/auth.css'])
@section('content')
<div class="auth-wrapper">
    <div class="auth-card">

        <h1 class="auth-title">Confirmation requise</h1>

        <p class="auth-sub">
            Pour continuer, confirme ton mot de passe.
        </p>

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="Entre ton mot de passe"
                >

                @error('password')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-primary">
                Confirmer
            </button>
        </form>

        <div class="auth-links">
            <a href="{{ route('password.request') }}">Mot de passe oublié ?</a>
        </div>

    </div>
</div>
@endsection