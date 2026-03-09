<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Pokémon</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/pokemon-manage.css'])
</head>
<body>
<div class="manage-page">
    <div class="manage-topbar">
        <div>
            <h1>Manage Pokémon</h1>
            <p>Create, edit and delete Pokémon from your Pokédex.</p>
        </div>

        <div class="manage-actions">
            <a href="{{ route('pokemons.index') }}" class="manage-btn secondary">← Back to Pokédex</a>
            <a href="{{ route('pokemons.create') }}" class="manage-btn primary">+ Add Pokémon</a>
        </div>
    </div>

    @if(session('success'))
        <div class="flash-success">{{ session('success') }}</div>
    @endif

    <form method="GET" action="{{ route('pokemons.manage') }}" class="manage-filters">
        <input
            type="text"
            name="q"
            value="{{ request('q') }}"
            placeholder="Search by name or Pokédex number"
        >

        <select name="generation">
            <option value="">All generations</option>
            @for($gen = 1; $gen <= 10; $gen++)
                <option value="{{ $gen }}" {{ (string)request('generation') === (string)$gen ? 'selected' : '' }}>
                    Generation {{ $gen }}
                </option>
            @endfor
        </select>

        <button type="submit" class="manage-btn primary">Filter</button>
        <a href="{{ route('pokemons.manage') }}" class="manage-btn secondary">Reset</a>
    </form>

    <div class="table-card">
        <table class="manage-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Pokédex ID</th>
                    <th>Name</th>
                    <th>Generation</th>
                    <th>Types</th>
                    <th>Forms</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pokemons as $pokemon)
                    <tr>
                        <td>
                            @if($pokemon->image_default)
                                <img src="{{ asset($pokemon->image_default) }}" alt="{{ $pokemon->name }}" class="mini-sprite">
                            @endif
                        </td>
                        <td>#{{ str_pad($pokemon->pokedex_number, 4, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ ucfirst($pokemon->name) }}</td>
                        <td>{{ $pokemon->generation }}</td>
                        <td>
                            <div class="type-list">
                                <span class="type-pill">{{ ucfirst($pokemon->type1) }}</span>
                                @if($pokemon->type2)
                                    <span class="type-pill">{{ ucfirst($pokemon->type2) }}</span>
                                @endif
                            </div>
                        </td>
                        <td>{{ is_array($pokemon->forms) ? count($pokemon->forms) : 0 }}</td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('pokemons.show', $pokemon) }}" class="manage-btn secondary">View</a>
                                <a href="{{ route('pokemons.edit', $pokemon) }}" class="manage-btn primary">Edit</a>

                                <form method="POST" action="{{ route('pokemons.destroy', $pokemon) }}" onsubmit="return confirm('Delete this Pokémon?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="manage-btn danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-cell">No Pokémon found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrap">
        {{ $pokemons->links() }}
    </div>
</div>
</body>
</html>