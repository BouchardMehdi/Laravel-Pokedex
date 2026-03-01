<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion</title>

    @vite(['resources/css/auth.css'])
</head>
<body>

<div class="auth-card">
    <h2 class="auth-title">Connexion</h2>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="email">Adresse e-mail</label>
            <input id="email"
                   type="email"
                   name="email"
                   value="{{ old('email') }}"
                   required
                   autofocus
                   autocomplete="email">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input id="password"
                   type="password"
                   name="password"
                   required
                   autocomplete="current-password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-check">
            <input id="remember" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
            <label for="remember" style="margin:0;">Se souvenir de moi</label>
        </div>

        <button type="submit" class="btn-auth">Se connecter</button>

        <div class="auth-links">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">Mot de passe oublié ?</a>
                <span style="opacity:.5;">•</span>
            @endif
            <a href="{{ route('register') }}">Créer un compte</a>
        </div>
    </form>
</div>

</body>
</html>
