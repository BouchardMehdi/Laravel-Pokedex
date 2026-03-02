<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mot de passe oublié</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/auth.css'])
</head>
<body>

<div class="auth-card">
    <div class="auth-title">Mot de passe oublié</div>

    <p style="margin-top:-10px; margin-bottom:18px; color:#9fb0c0; font-size:13px; line-height:1.5;">
        Entre ton adresse email et on t’enverra un lien pour réinitialiser ton mot de passe.
    </p>

    {{-- Message de succès --}}
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
            <label for="email">Adresse email</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autocomplete="email"
                autofocus
                placeholder="ex: mehdi@gmail.com"
            >

            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-auth">
            Envoyer le lien de réinitialisation
        </button>
    </form>

    <div class="auth-links" style="margin-top:16px;">
        <a href="{{ route('login') }}">← Retour à la connexion</a>
        <span style="opacity:.4; margin:0 8px;">•</span>
        <a href="{{ route('register') }}">Créer un compte</a>
    </div>
</div>

</body>
</html>