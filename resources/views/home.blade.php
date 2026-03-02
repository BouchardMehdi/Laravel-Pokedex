<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Pokédex — Suivi & Teams</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/home.css'])
</head>
<body>

@php
    $teams = $teams ?? collect();
@endphp

<div class="bg">
    <div class="marquee-wrap">
        <div class="marquee">

            @php
                $chunks = $pokemons->chunk(5);
            @endphp

            {{-- On duplique pour scroll infini --}}
            @foreach([$chunks, $chunks] as $set)
                @foreach($set as $row)
                    <div class="row">
                        @foreach($row as $p)
                            @php
                                $img = $p->image_default ?: ('images/default/' . ($p->slug ?? $p->name) . '.png');
                            @endphp
                            <div class="mini">
                                <img src="{{ asset($img) }}" alt="{{ $p->name }}" loading="lazy" onerror="this.style.display='none'">
                                <div class="n">{{ $p->name }}</div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            @endforeach

        </div>
    </div>
</div>

<div class="vignette"></div>

<div class="content">
    <div class="panel">
        <div class="kicker">✨ Projet Laravel</div>
        <h1>Suivi Pokédex & Création de Team Pokémon</h1>

        <p>
            Un projet Laravel permettant de suivre les Pokémon que tu possèdes.
            Débloque-les dans ton Pokédex personnel et bientôt crée tes propres équipes stratégiques.
        </p>

        <div class="actions">
            @guest
                <a class="btn primary" href="/login">Se connecter</a>
                <a class="btn secondary" href="/register">Créer un compte</a>
            @else
                <a class="btn primary" href="{{ route('pokemons.index') }}">Mon Pokédex</a>

                <form method="POST" action="/logout" style="margin:0;">
                    @csrf
                    <button class="btn secondary" type="submit">Logout</button>
                </form>
            @endguest
        </div>

        {{-- Si ta home affiche aussi les teams, maintenant ça ne cassera plus au logout --}}
        @auth
            @if($teams->count())
                <div style="margin-top:16px; display:grid; gap:12px;">
                    @foreach($teams as $team)
                        <div style="background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.08); border-radius:16px; padding:14px;">
                            <div style="font-weight:900; font-size:18px;">{{ $team->name ?? 'Team' }}</div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endauth

    </div>
</div>

</body>
</html>
