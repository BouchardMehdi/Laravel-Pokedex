<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laradex — Pokédex & Teams</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/home.css'])
</head>
<body>

@php
    $teams = $teams ?? collect();

    // Desktop: 5 colonnes
    $chunksDesktop = $pokemons->chunk(5);

    // Mobile: 2 colonnes
    $chunksMobile  = $pokemons->chunk(2);
@endphp

<div class="bg">

    <div class="marquee-wrap marquee-desktop">
        <div class="marquee">
            @foreach([$chunksDesktop, $chunksDesktop] as $set)
                @foreach($set as $row)
                    <div class="row row-5">
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

    <div class="marquee-wrap marquee-mobile">
        <div class="marquee">
            @foreach([$chunksMobile, $chunksMobile] as $set)
                @foreach($set as $row)
                    <div class="row row-2">
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
        <h1>Pokédex Tracking & Team Builder</h1>

        <p>
            A Laravel project to track the Pokémon you own.
            Unlock them in your personal Pokédex and build your strategic teams.
        </p>

        <div class="actions">
            @guest
                <a class="btn primary" href="/login">Login</a>
                <a class="btn secondary" href="/register">Create account</a>
            @else
                <a class="btn primary" href="{{ route('pokemons.index') }}">My Pokédex</a>

                <form method="POST" action="/logout" style="margin:0;">
                    @csrf
                    <button class="btn secondary" type="submit">Logout</button>
                </form>
            @endguest
        </div>

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