<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Pokédex</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/pokemons.css', 'resources/js/pokemons.js'])
</head>
<body>
<div class="container">

    @php
        $pickTeamId = request('pick_team');
        $pickSlot = request('slot');
        $isPickMode = !empty($pickTeamId) && !empty($pickSlot);

        $formValue = request('form', '');
    @endphp

    <div class="topbar">
        <div>
            <h1 class="title">My Pokédex</h1>
            <p class="sub">Unlock your Pokémon 🔓 • ✨ to switch to shiny</p>

            @if($isPickMode)
                <p class="sub" style="margin-top:6px;">
                    ✅ Add to team mode — Slot {{ (int)$pickSlot }}
                    • <a class="btn secondary" style="padding:6px 10px;border-radius:10px;font-size:12px;" href="{{ route('teams.edit', $pickTeamId) }}">Back to team</a>
                </p>
            @endif
        </div>

        <div class="right">
            <a class="btn secondary" href="{{ route('home') }}">Home</a>

            @if(!$isPickMode)
                <button type="button" class="btn secondary" id="toggleAllShinyBtn">✨ All shiny</button>

                <button type="button" class="btn" id="unlockAllBtn" data-url="{{ route('pokemons.unlockAll') }}">
                    🔓 Unlock all
                </button>

                <button type="button" class="btn secondary" id="lockAllBtn" data-url="{{ route('pokemons.lockAll') }}">
                    🔒 Lock all
                </button>

                <button type="button" class="btn" id="unlockPageBtn" data-url="{{ route('pokemons.unlockPage') }}">
                    🔓 Unlock this page
                </button>

                <button type="button" class="btn secondary" id="unlockGenBtn" data-url="{{ route('pokemons.unlockGeneration') }}">
                    🔓 Unlock Generation
                </button>
            @endif

            <a class="btn secondary" href="{{ route('teams.index') }}">My teams</a>

            <form method="POST" action="/logout" style="margin:0;">
                @csrf
                <button class="btn danger" type="submit">Logout</button>
            </form>
        </div>
    </div>

    <form class="filters" method="GET" action="{{ route('pokemons.index') }}">
        @if($isPickMode)
            <input type="hidden" name="pick_team" value="{{ $pickTeamId }}">
            <input type="hidden" name="slot" value="{{ $pickSlot }}">
        @endif

        <div class="filters-grid">
            <div class="field">
                <label for="q">Name (search)</label>
                <input id="q" type="text" name="q" value="{{ request('q') }}" placeholder="e.g. Pikachu">
            </div>

            <div class="field">
                <label for="generation">Generation</label>
                <select id="generation" name="generation">
                    <option value="">All</option>
                    @foreach($generations as $gen)
                        <option value="{{ $gen }}" {{ (string)$gen === (string)request('generation') ? 'selected' : '' }}>
                            Gen {{ $gen }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label for="type">Type</label>
                <select id="type" name="type">
                    <option value="">All</option>
                    @foreach($types as $t)
                        <option value="{{ $t }}" {{ (string)$t === (string)request('type') ? 'selected' : '' }}>
                            {{ $t }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label for="special">Special category</label>
                <select id="special" name="special">
                    <option value="">All</option>
                    <option value="legendary" {{ request('special') === 'legendary' ? 'selected' : '' }}>Legendary</option>
                    <option value="fabulous"  {{ request('special') === 'fabulous'  ? 'selected' : '' }}>Mythical</option>
                    <option value="ultra"     {{ request('special') === 'ultra'     ? 'selected' : '' }}>Ultra Beast</option>
                    <option value="paradox"   {{ request('special') === 'paradox'   ? 'selected' : '' }}>Paradox</option>
                </select>
            </div>

            {{-- Form filter --}}
            <div class="field">
                <label for="form">Form</label>
                <select id="form" name="form">
                    <option value="" {{ $formValue === '' ? 'selected' : '' }}>All</option>
                    <option value="mega"  {{ $formValue === 'mega'  ? 'selected' : '' }}>Mega</option>
                    <option value="gmax"  {{ $formValue === 'gmax'  ? 'selected' : '' }}>Gmax</option>
                    <option value="alola" {{ $formValue === 'alola' ? 'selected' : '' }}>Alola</option>
                    <option value="galar" {{ $formValue === 'galar' ? 'selected' : '' }}>Galar</option>
                    <option value="hisui" {{ $formValue === 'hisui' ? 'selected' : '' }}>Hisui</option>
                    <option value="paldea" {{ $formValue === 'paldea' ? 'selected' : '' }}>Paldea</option>
                    <option value="other" {{ $formValue === 'other' ? 'selected' : '' }}>Other forms</option>
                </select>
            </div>
        </div>

        <div class="row-actions">
            <div class="left-actions">
                <button class="btn" type="submit">Apply</button>
                <a class="btn secondary" href="{{ route('pokemons.index', $isPickMode ? ['pick_team'=>$pickTeamId,'slot'=>$pickSlot] : []) }}">Reset</a>
            </div>
            <div class="small">
                Results: {{ $pokemons->total() }} • Page {{ $pokemons->currentPage() }} / {{ $pokemons->lastPage() }}
            </div>
        </div>
    </form>

    @if($pokemons->count() === 0)
        <p>No Pokémon found with these filters.</p>
    @else
        <div class="grid">
            @foreach($pokemons as $pokemon)
                @php
                    $isUnlocked = in_array($pokemon->id, $unlockedIds ?? []);
                    $normal = $pokemon->image_default ?: ('images/default/' . ($pokemon->slug ?? $pokemon->name) . '.png');
                    $shiny = $pokemon->image_shiny ?: '';
                    $hasShiny = !empty($pokemon->image_shiny);
                @endphp

                <div class="card"
                     data-card-id="{{ $pokemon->id }}"
                     data-pokemon-id="{{ $pokemon->id }}"
                     data-unlocked="{{ $isUnlocked ? '1' : '0' }}">
                    <div class="card-top">
                        <div class="sprite {{ $isUnlocked ? '' : 'locked' }}">
                            <button
                                type="button"
                                class="shiny-btn {{ $hasShiny ? '' : 'disabled' }}"
                                data-has-shiny="{{ $hasShiny ? '1' : '0' }}"
                                {{ $hasShiny ? '' : 'disabled' }}
                            >✨</button>

                            <img class="poke-img"
                                 src="{{ asset($normal) }}"
                                 data-normal="{{ asset($normal) }}"
                                 data-shiny="{{ $hasShiny ? asset($shiny) : '' }}"
                                 alt="{{ $pokemon->name }}"
                                 onerror="this.style.display='none'"
                                 loading="lazy">
                        </div>

                        <div class="meta">
                            <div class="id">#{{ $pokemon->pokedex_number }}</div>
                            <h3 class="name">{{ $pokemon->name }}</h3>

                            <div class="badges">
                                <span class="badge">{{ $pokemon->type1 }}</span>
                                @if($pokemon->type2)<span class="badge">{{ $pokemon->type2 }}</span>@endif

                                @if($pokemon->is_legendary)<span class="badge legend">Legendary</span>@endif
                                @if($pokemon->is_fabulous)<span class="badge fabulous">Mythical</span>@endif
                                @if($pokemon->is_ultra_beast)<span class="badge ultra">Ultra Beast</span>@endif
                                @if($pokemon->is_paradox)<span class="badge paradox">Paradox</span>@endif
                            </div>
                        </div>
                    </div>

                    <div class="card-bottom">
                        <div class="bottom-left">
                            <div class="small">Generation {{ $pokemon->generation }}</div>

                            @if($isPickMode)
                                <form method="POST" action="{{ route('teams.slot.set', ['team' => $pickTeamId, 'slot' => (int)$pickSlot]) }}" style="margin:0;">
                                    @csrf
                                    <input type="hidden" name="pokemon_id" value="{{ $pokemon->id }}">
                                    <button class="unlock-btn" type="submit" {{ $isUnlocked ? '' : 'disabled' }}>
                                        ➕ <span class="unlock-text">{{ $isUnlocked ? 'Add' : 'Locked' }}</span>
                                    </button>
                                </form>
                            @else
                                <button
                                    type="button"
                                    class="unlock-btn"
                                    data-id="{{ $pokemon->id }}"
                                    data-url="{{ route('pokemons.unlock', $pokemon) }}"
                                    {{ $isUnlocked ? 'disabled' : '' }}
                                >
                                    🔓 <span class="unlock-text">{{ $isUnlocked ? 'Unlocked' : 'Unlock' }}</span>
                                </button>
                            @endif
                        </div>

                        <a class="btn" href="{{ route('pokemons.show', $pokemon) }}{{ $isPickMode ? ('?pick_team='.$pickTeamId.'&slot='.$pickSlot) : '' }}">
                            View more →
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pagination" id="pagination">
            @if ($pokemons->onFirstPage())
                <span class="page disabled">← Previous</span>
            @else
                <a class="page" href="{{ $pokemons->appends(request()->query())->previousPageUrl() }}">← Previous</a>
            @endif

            <span class="page current">Page {{ $pokemons->currentPage() }} / {{ $pokemons->lastPage() }}</span>

            @if ($pokemons->hasMorePages())
                <a class="page" href="{{ $pokemons->appends(request()->query())->nextPageUrl() }}">Next →</a>
            @else
                <span class="page disabled">Next →</span>
            @endif
        </div>
    @endif

</div>
</body>
</html>