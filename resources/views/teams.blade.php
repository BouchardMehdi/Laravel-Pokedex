@extends('layouts.app')

@vite(['resources/css/teams.css'])

@section('content')
<div class="teams-container show">

    <div class="teams-topbar">
        <div>
            <h1 class="teams-title">My Teams</h1>
            <p class="teams-sub">Create and manage your teams (max 6 Pokémon).</p>
        </div>

        <div class="teams-actions">
            <a class="btn secondary" href="{{ route('pokemons.index') }}">← Pokédex</a>
            <a class="btn" href="{{ route('teams.create') }}">+ New Team</a>
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
                        <a class="btn secondary tiny" href="{{ route('teams.edit', $team) }}">Edit</a>

                        <form method="POST" action="{{ route('teams.destroy', $team) }}">
                            @csrf
                            @method('DELETE')
                            <button class="btn tiny danger" type="submit">Delete</button>
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

                                $img = ($form === 'normal')
                                    ? $p->image_default
                                    : ($forms[$form]['image_default'] ?? $p->image_default);
                            }
                        @endphp

                        @if($p)
                            <div class="mini-sprite" title="{{ $p->name }} (slot {{ $i }})">
                                <img src="{{ asset($img) }}" alt="{{ $p->name }}">
                            </div>
                        @else
                            <div class="mini-sprite empty" title="Empty slot {{ $i }}"></div>
                        @endif
                    @endfor
                </div>
            </div>

        @empty
            <div class="panel">
                <div class="teams-empty-title">No teams yet</div>
                <div class="teams-empty-sub">Create your first team to get started.</div>
                <a class="btn" href="{{ route('teams.create') }}">Create a team</a>
            </div>
        @endforelse
    </div>

</div>
@endsection