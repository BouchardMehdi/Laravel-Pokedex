<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inscription</title>

    @vite(['resources/css/auth.css'])
</head>
<body>

<div class="auth-card">
    <h2 class="auth-title">Inscription</h2>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-group">
            <label for="name">Nom</label>
            <input id="name"
                   type="text"
                   name="name"
                   value="{{ old('name') }}"
                   required
                   autofocus
                   autocomplete="name">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="email">Adresse e-mail</label>
            <input id="email"
                   type="email"
                   name="email"
                   value="{{ old('email') }}"
                   required
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
                   autocomplete="new-password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password-confirm">Confirmer le mot de passe</label>
            <input id="password-confirm"
                   type="password"
                   name="password_confirmation"
                   required
                   autocomplete="new-password">
        </div>

        <button type="submit" class="btn-auth">Créer mon compte</button>

        <div class="auth-links">
            <a href="{{ route('login') }}">Déjà un compte ? Se connecter</a>
        </div>
    </form>
</div>

</body>
</html>
