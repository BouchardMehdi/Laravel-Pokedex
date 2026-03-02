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

    <div class="teams-grid">
        @forelse($teams as $team)
            @php
                // construit un tableau slots 1..6
                $slots = array_fill(1, 6, null);
                foreach ($team->pokemons as $p) {
                    $s = (int) (optional($p->pivot)->slot ?? 0);
                    if ($s >= 1 && $s <= 6) $slots[$s] = $p;
                }

                $filled = 0;
                foreach ($slots as $sp) if ($sp) $filled++;
            @endphp

            <div class="team-card">
                <div class="team-head">
                    <div class="team-meta">
                        <div class="team-name">{{ $team->name }}</div>
                        <div class="team-sub">Team #{{ $team->id }}</div>
                        <div class="team-sub">{{ $filled }}/6</div>
                    </div>

                    <div class="team-actions">
                        <a class="btn secondary tiny" href="{{ route('teams.edit', $team) }}">Modifier</a>

                        <form method="POST" action="{{ route('teams.destroy', $team) }}">
                            @csrf
                            @method('DELETE')
                            <button class="btn tiny danger" type="submit">Supprimer</button>
                        </form>
                    </div>
                </div>

                <div class="team-sprites">
                    @for($i = 1; $i <= 6; $i++)
                        @php
                            $p = $slots[$i];

                            if ($p) {
                                $form = optional($p->pivot)->form ?? 'normal';

                                $forms = is_array($p->forms)
                                    ? $p->forms
                                    : json_decode($p->forms ?? '{}', true);

                                if (!is_array($forms)) $forms = [];

                                if ($form === 'normal') {
                                    $img = $p->image_default;
                                } else {
                                    $img = $forms[$form]['image_default'] ?? $p->image_default;
                                }
                            }
                        @endphp

                        @if($p)
                            <div class="mini-sprite" title="{{ $p->name }} (slot {{ $i }})">
                                <img src="{{ asset($img) }}" alt="{{ $p->name }}">
                            </div>
                        @else
                            <div class="mini-sprite empty" title="Slot {{ $i }} vide"></div>
                        @endif
                    @endfor
                </div>
            </div>

        @empty
            <div class="panel">
                <div class="teams-empty-title">Aucune team</div>
                <div class="teams-empty-sub">Crée ta première équipe pour commencer.</div>
                <a class="btn" href="{{ route('teams.create') }}">Créer une team</a>
            </div>
        @endforelse
    </div>

</div>
@endsection