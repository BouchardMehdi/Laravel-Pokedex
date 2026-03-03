<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pokédex — Tracking & Teams</title>
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

            {{-- Duplicate for infinite scroll --}}
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
        <div class="kicker">✨ Laradex</div>
        <h1>Pokédex Tracking & Pokémon Team Builder</h1>

        <p>
            A Laravel project that allows you to track the Pokémon you own.
            Unlock them in your personal Pokédex and soon build your own strategic teams.
        </p>

        <div class="actions">
            @guest
                <a class="btn primary" href="/login">Sign In</a>
                <a class="btn secondary" href="/register">Create an Account</a>
            @else
                <a class="btn primary" href="{{ route('pokemons.index') }}">My Pokédex</a>

                <form method="POST" action="/logout" style="margin:0;">
                    @csrf
                    <button class="btn secondary" type="submit">Logout</button>
                </form>
            @endguest
        </div>

        {{-- If home also displays teams, this won't break on logout --}}
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