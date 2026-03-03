@extends('layouts.app')
@vite(['resources/css/pokemon-show.css', 'resources/js/pokemon-show.js'])

@section('content')
@php
  $pickTeamId = request('pick_team');
  $pickSlot = request('slot');
  $isPickMode = !empty($pickTeamId) && !empty($pickSlot);

  $forms = $pokemon->forms;
  if (is_string($forms)) {
    $decoded = json_decode($forms, true);
    $forms = is_array($decoded) ? $decoded : [];
  }
  if (!is_array($forms)) $forms = [];

  $variants = [
    'normal' => [
      'key' => 'normal',
      'label' => 'Normal',
      'image_default' => $pokemon->image_default ? asset($pokemon->image_default) : null,
      'image_shiny'   => $pokemon->image_shiny ? asset($pokemon->image_shiny) : null,
      'type1' => $pokemon->type1,
      'type2' => $pokemon->type2,
      'stats' => [
        'hp' => (int)$pokemon->hp,
        'attack' => (int)$pokemon->attack,
        'defense' => (int)$pokemon->defense,
        'special_attack' => (int)$pokemon->special_attack,
        'special_defense' => (int)$pokemon->special_defense,
        'speed' => (int)$pokemon->speed,
      ],
    ],
  ];

  foreach ($forms as $suffix => $f) {
    if (!is_array($f)) continue;
    $imgDef = $f['image_default'] ?? null;
    if (!$imgDef) continue;

    $variants[$suffix] = [
      'key' => $suffix,
      'label' => $f['label'] ?? strtoupper(str_replace('-', ' ', $suffix)),
      'image_default' => asset($imgDef),
      'image_shiny'   => !empty($f['image_shiny']) ? asset($f['image_shiny']) : null,
      'type1' => $f['type1'] ?? null,
      'type2' => $f['type2'] ?? null,
      'stats' => [
        'hp' => (int)($f['stats']['hp'] ?? 0),
        'attack' => (int)($f['stats']['attack'] ?? 0),
        'defense' => (int)($f['stats']['defense'] ?? 0),
        'special_attack' => (int)($f['stats']['special_attack'] ?? 0),
        'special_defense' => (int)($f['stats']['special_defense'] ?? 0),
        'speed' => (int)($f['stats']['speed'] ?? 0),
      ],
    ];
  }

  $navQuery = $isPickMode ? ['pick_team' => $pickTeamId, 'slot' => $pickSlot] : [];
@endphp

<div class="pokemon-show-wrap">
<div class="link type-badge">
  <a href="{{ route('pokemons.index') }}{{ $isPickMode ? ('?pick_team='.$pickTeamId.'&slot='.$pickSlot) : '' }}">← Back to Pokédex</a>
</div>
  <div class="pokemon-card">

    @if(!empty($prevPokemon))
      <a class="nav-chevron left"
         href="{{ route('pokemons.show', $prevPokemon) }}{{ $isPickMode ? ('?pick_team='.$pickTeamId.'&slot='.$pickSlot) : '' }}"
         aria-label="Previous Pokémon">
        ‹
      </a>
    @endif

    @if(!empty($nextPokemon))
      <a class="nav-chevron right"
         href="{{ route('pokemons.show', $nextPokemon) }}{{ $isPickMode ? ('?pick_team='.$pickTeamId.'&slot='.$pickSlot) : '' }}"
         aria-label="Next Pokémon">
        ›
      </a>
    @endif

    <div class="pokemon-visual">
      <img
        id="pokemonImage"
        src="{{ asset($pokemon->image_default) }}"
        alt="{{ $pokemon->name }}"
      >
    </div>

    <div class="pokemon-info">
      <h1>#{{ str_pad($pokemon->pokedex_number, 4, '0', STR_PAD_LEFT) }} {{ ucfirst($pokemon->name) }}</h1>

      <div class="pokemon-types" id="pokemonTypes">
        @if(!empty($pokemon->type1))
          <span class="type-badge">{{ ucfirst($pokemon->type1) }}</span>
        @endif
        @if(!empty($pokemon->type2))
          <span class="type-badge">{{ ucfirst($pokemon->type2) }}</span>
        @endif
      </div>

      @if($isPickMode)
        <div class="pick-actions">
          <form method="POST"
                action="{{ route('teams.slot.set', ['team' => $pickTeamId, 'slot' => (int)$pickSlot]) }}"
                class="pick-form">
              @csrf
              <input type="hidden" name="pokemon_id" value="{{ $pokemon->id }}">
              <input type="hidden" name="form" id="selectedFormInput" value="normal">
            <button class="variant-btn" type="submit">
              ➕ Add to team (slot {{ (int)$pickSlot }})
            </button>
          </form>

          <a class="variant-btn"
             href="{{ route('teams.edit', $pickTeamId) }}">
              ← Back to team
          </a>
        </div>
      @endif

      <div class="stats" id="pokemonStats">
        <div class="stat-row">
          <div class="stat-name">HP</div>
          <div class="bar"><span id="bar-hp"></span></div>
          <div class="stat-value" id="val-hp">{{ $pokemon->hp }}</div>
        </div>

        <div class="stat-row">
          <div class="stat-name">Attack</div>
          <div class="bar"><span id="bar-attack"></span></div>
          <div class="stat-value" id="val-attack">{{ $pokemon->attack }}</div>
        </div>

        <div class="stat-row">
          <div class="stat-name">Defense</div>
          <div class="bar"><span id="bar-defense"></span></div>
          <div class="stat-value" id="val-defense">{{ $pokemon->defense }}</div>
        </div>

        <div class="stat-row">
          <div class="stat-name">Sp. Atk</div>
          <div class="bar"><span id="bar-special_attack"></span></div>
          <div class="stat-value" id="val-special_attack">{{ $pokemon->special_attack }}</div>
        </div>

        <div class="stat-row">
          <div class="stat-name">Sp. Def</div>
          <div class="bar"><span id="bar-special_defense"></span></div>
          <div class="stat-value" id="val-special_defense">{{ $pokemon->special_defense }}</div>
        </div>

        <div class="stat-row">
          <div class="stat-name">Speed</div>
          <div class="bar"><span id="bar-speed"></span></div>
          <div class="stat-value" id="val-speed">{{ $pokemon->speed }}</div>
        </div>
      </div>

      <div
        id="pokemonVariantData"
        data-variants='@json($variants, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)'
      ></div>

      <div class="variant-buttons" id="variantButtons">
        @foreach($variants as $key => $v)
          <button
            type="button"
            class="variant-btn {{ $key === 'normal' ? 'active' : '' }}"
            data-variant="{{ $key }}"
          >
            {{ $v['label'] }}
          </button>
        @endforeach

        <button type="button" class="variant-btn shiny-toggle" id="shinyToggle">
          ✨ Shiny
        </button>
      </div>

    </div>
  </div>
</div>
@endsection