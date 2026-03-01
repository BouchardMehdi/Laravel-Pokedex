@extends('layouts.app')

@vite(['resources/css/teams.css'])

@section('content')
<div class="teams-container">

    <div class="teams-topbar">
        <div>
            <h1 class="teams-title">Mes Teams</h1>
            <p class="teams-sub">Crée et gère tes équipes (max 6 Pokémon).</p>
        </div>

        <div class="teams-actions">
            <a class="btn secondary" href="{{ route('pokemons.index') }}">← Pokédex</a>
            <a class="btn" href="{{ route('teams.create') }}">+ Nouvelle team</a>
        </div>
    </div>

    @if(session('success'))
        <div class="flash success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="flash error">{{ session('error') }}</div>
    @endif

    @if($teams->count() === 0)
        <div class="empty">
            <p>Aucune team pour l’instant.</p>
            <a class="btn" href="{{ route('teams.create') }}">Créer ma première team</a>
        </div>
    @else
        <div class="teams-grid">
            @foreach($teams as $team)
                @php
                    $slotMap = [];
                    foreach($team->pokemons as $p){
                        $s = (int) ($p->pivot->slot ?? 0);
                        if($s >= 1 && $s <= 6) $slotMap[$s] = $p;
                    }
                @endphp

                <div class="team-card">
                    <div class="team-card-top">
                        <div>
                            <h3 class="team-name">{{ $team->name }}</h3>
                            <div class="team-meta">Team #{{ $team->id }} • {{ count($slotMap) }}/6</div>
                        </div>

                        {{-- ✅ Boutons actions --}}
                        <div class="team-card-actions" style="margin-left:65%;">
                            <a class="btn tiny secondary" href="{{ route('teams.edit', $team) }}" style="background: #0b1220; border: none;">Modifier</a>

                            <form method="POST" action="{{ route('teams.destroy', $team) }}"
                                  onsubmit="return confirm('Supprimer cette team ?')" style="margin:0;">
                                @csrf
                                @method('DELETE')
                                <button class="btn tiny danger" type="submit">Supprimer</button>
                            </form>
                        </div>
                    </div>

                    {{-- Sprites preview --}}
                    <div class="team-sprites">
                        @for($i=1; $i<=6; $i++)
                            @php
                                $p = $slotMap[$i] ?? null;
                                $img = $p?->image_default ?: ($p ? ('images/default/' . ($p->slug ?? $p->name) . '.png') : null);
                            @endphp

                            <div class="team-sprite">
                                @if($p && $img)
                                    <img src="{{ asset($img) }}" alt="{{ $p->name }}" loading="lazy" onerror="this.style.display='none'">
                                @else
                                    <div class="team-sprite-empty">+</div>
                                @endif
                            </div>
                        @endfor
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
