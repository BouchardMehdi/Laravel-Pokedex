@extends('layouts.app')

@vite(['resources/css/teams.css'])

@section('content')
<div class="teams-container">

    <div class="teams-topbar">
        <div>
            <h1 class="teams-title">Modifier la Team</h1>
            <p class="teams-sub">Clique sur un slot pour choisir un Pokémon (max 6).</p>
        </div>

        <div class="teams-actions">
            <a class="btn secondary" href="{{ route('teams.index') }}">← Mes teams</a>
            <a class="btn secondary" href="{{ route('pokemons.index') }}">Pokédex</a>
        </div>
    </div>

    @if(session('success'))
        <div class="flash success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="flash error">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="flash error">
            @foreach($errors->all() as $e)
                <div>• {{ $e }}</div>
            @endforeach
        </div>
    @endif

    <div class="panel">
        <form method="POST" action="{{ route('teams.update', $team) }}" class="team-form">
            @csrf
            @method('PUT')

            <label class="label" for="name">Nom de la team</label>
            <input class="input"
                   id="name"
                   name="name"
                   type="text"
                   maxlength="40"
                   value="{{ old('name', $team->name) }}"
                   required>

            <div class="form-actions">
                <button class="btn" type="submit">Enregistrer</button>
            </div>
        </form>
    </div>

    <div class="slots-grid">
        @for($i = 1; $i <= 6; $i++)
            @php
                $p = $slots[$i] ?? null;
            @endphp

            <div class="slot-card">
                <div class="slot-top">
                    <div class="slot-title">Slot {{ $i }}</div>

                    @if($p)
                        {{-- ✅ IMPORTANT : on utilise la route qui existe dans ton projet --}}
                        <form method="POST" action="{{ route('teams.slot.clear', ['team' => $team->id, 'slot' => $i]) }}">
                            @csrf
                            @method('DELETE')
                            <button class="btn tiny danger edit" type="submit">Retirer</button>
                        </form>
                    @endif
                </div>

                @if($p)
                    @php
                        $form = optional($p->pivot)->form ?? 'normal';

                        $forms = is_array($p->forms)
                            ? $p->forms
                            : json_decode($p->forms ?? '{}', true);

                        if (!is_array($forms)) {
                            $forms = [];
                        }

                        // Choix de l’image selon la forme stockée
                        if ($form === 'normal') {
                            $img = $p->image_default;
                        } else {
                            $img = $forms[$form]['image_default'] ?? $p->image_default;
                        }
                    @endphp

                    <div class="slot-body">
                        <div class="slot-sprite">
                            <img src="{{ asset($img) }}" alt="{{ $p->name }}">
                        </div>

                        <div class="slot-info">
                            <div class="slot-name">{{ $p->name }}</div>
                            <div class="slot-meta">#{{ $p->pokedex_number }}</div>

                            @if($form !== 'normal')
                                <div class="slot-form">{{ strtoupper(str_replace('-', ' ', $form)) }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="slot-actions">
                        <a class="btn tiny change"
                           href="{{ route('teams.pick', ['team' => $team->id, 'slot' => $i]) }}">
                            Changer
                        </a>

                        <a class="btn tiny"
                           href="{{ route('pokemons.show', $p) }}?pick_team={{ $team->id }}&slot={{ $i }}">
                            Voir stats
                        </a>
                    </div>
                @else
                    <div class="slot-empty">
                        <div class="slot-empty-text">Aucun Pokémon</div>
                        <a class="btn" href="{{ route('teams.pick', ['team' => $team->id, 'slot' => $i]) }}">Choisir</a>
                    </div>
                @endif
            </div>
        @endfor
    </div>

</div>
@endsection