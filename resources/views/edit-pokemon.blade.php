<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Pokémon</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/pokemon-manage.css'])
</head>
<body>
@php
    $types = [
        'normal', 'fire', 'water', 'electric', 'grass', 'ice',
        'fighting', 'poison', 'ground', 'flying', 'psychic', 'bug',
        'rock', 'ghost', 'dragon', 'dark', 'steel', 'fairy'
    ];

    $initialForms = old('forms');

    if ($initialForms === null) {
        $initialForms = [];

        if ($pokemon && is_array($pokemon->forms)) {
            foreach ($pokemon->forms as $key => $form) {
                $initialForms[] = [
                    'key' => $key,
                    'label' => $form['label'] ?? '',
                    'type1' => $form['type1'] ?? '',
                    'type2' => $form['type2'] ?? '',
                    'hp' => $form['stats']['hp'] ?? '',
                    'attack' => $form['stats']['attack'] ?? '',
                    'defense' => $form['stats']['defense'] ?? '',
                    'special_attack' => $form['stats']['special_attack'] ?? '',
                    'special_defense' => $form['stats']['special_defense'] ?? '',
                    'speed' => $form['stats']['speed'] ?? '',
                    'existing_image_default' => $form['image_default'] ?? '',
                    'existing_image_shiny' => $form['image_shiny'] ?? '',
                ];
            }
        }
    }
@endphp

<div class="manage-page">
    <div class="manage-topbar">
        <div>
            <h1>Edit {{ ucfirst($pokemon->name) }}</h1>
            <p>Update this Pokémon, its images, stats and forms.</p>
        </div>

        <div class="manage-actions">
            <a href="{{ route('pokemons.manage') }}" class="manage-btn secondary">← Back to manage</a>
            <a href="{{ route('pokemons.show', $pokemon) }}" class="manage-btn primary">View page</a>
        </div>
    </div>

    @if ($errors->any())
        <div class="flash-error">
            <strong>There are errors:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('pokemons.update', $pokemon) }}" enctype="multipart/form-data" class="pokemon-form">
        @csrf
        @method('PUT')

        <div class="form-grid">
            <div class="form-card">
                <h2>Main info</h2>

                <div class="field-grid">
                    <div class="field">
                        <label>Name</label>
                        <input type="text" name="name" value="{{ old('name', $pokemon->name) }}" required>
                    </div>

                    <div class="field">
                        <label>Pokédex number</label>
                        <input type="number" name="pokedex_number" value="{{ old('pokedex_number', $pokemon->pokedex_number) }}" min="1" required>
                    </div>

                    <div class="field">
                        <label>Generation</label>
                        <select name="generation" required>
                            @for($gen = 1; $gen <= 10; $gen++)
                                <option value="{{ $gen }}" {{ (string)old('generation', $pokemon->generation) === (string)$gen ? 'selected' : '' }}>
                                    Generation {{ $gen }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="field">
                        <label>Type 1</label>
                        <select name="type1" required>
                            @foreach($types as $type)
                                <option value="{{ $type }}" {{ old('type1', $pokemon->type1) === $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field">
                        <label>Type 2</label>
                        <select name="type2">
                            <option value="">None</option>
                            @foreach($types as $type)
                                <option value="{{ $type }}" {{ old('type2', $pokemon->type2) === $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="checkbox-row">
                    <label><input type="checkbox" name="is_legendary" value="1" {{ old('is_legendary', $pokemon->is_legendary) ? 'checked' : '' }}> Legendary</label>
                    <label><input type="checkbox" name="is_fabulous" value="1" {{ old('is_fabulous', $pokemon->is_fabulous) ? 'checked' : '' }}> Mythical</label>
                    <label><input type="checkbox" name="is_ultra_beast" value="1" {{ old('is_ultra_beast', $pokemon->is_ultra_beast) ? 'checked' : '' }}> Ultra Beast</label>
                    <label><input type="checkbox" name="is_paradox" value="1" {{ old('is_paradox', $pokemon->is_paradox) ? 'checked' : '' }}> Paradox</label>
                </div>
            </div>

            <div class="form-card">
                <h2>Main images</h2>

                <div class="field-grid">
                    <div class="field">
                        <label>Normal PNG</label>
                        <input type="file" name="image_default" accept=".png,image/png">
                        @if(!empty($pokemon->image_default))
                            <img src="{{ asset($pokemon->image_default) }}" alt="" class="preview-image">
                        @endif
                    </div>

                    <div class="field">
                        <label>Shiny PNG</label>
                        <input type="file" name="image_shiny" accept=".png,image/png">
                        @if(!empty($pokemon->image_shiny))
                            <img src="{{ asset($pokemon->image_shiny) }}" alt="" class="preview-image">
                        @endif
                    </div>
                </div>
            </div>

            <div class="form-card">
                <h2>Main stats</h2>

                <div class="field-grid stats-grid">
                    <div class="field">
                        <label>HP</label>
                        <input type="number" name="hp" min="1" max="255" value="{{ old('hp', $pokemon->hp) }}" required>
                    </div>

                    <div class="field">
                        <label>Attack</label>
                        <input type="number" name="attack" min="1" max="255" value="{{ old('attack', $pokemon->attack) }}" required>
                    </div>

                    <div class="field">
                        <label>Defense</label>
                        <input type="number" name="defense" min="1" max="255" value="{{ old('defense', $pokemon->defense) }}" required>
                    </div>

                    <div class="field">
                        <label>Sp. Attack</label>
                        <input type="number" name="special_attack" min="1" max="255" value="{{ old('special_attack', $pokemon->special_attack) }}" required>
                    </div>

                    <div class="field">
                        <label>Sp. Defense</label>
                        <input type="number" name="special_defense" min="1" max="255" value="{{ old('special_defense', $pokemon->special_defense) }}" required>
                    </div>

                    <div class="field">
                        <label>Speed</label>
                        <input type="number" name="speed" min="1" max="255" value="{{ old('speed', $pokemon->speed) }}" required>
                    </div>
                </div>
            </div>

            <div class="form-card full-width">
                <div class="form-card-header">
                    <h2>Forms</h2>
                    <button type="button" class="manage-btn primary" id="add-form-btn">+ Add form</button>
                </div>

                <div id="forms-container" class="forms-container">
                    @foreach($initialForms as $index => $form)
                        <div class="form-variant-card">
                            <div class="form-card-header">
                                <h3>Form</h3>
                                <button type="button" class="manage-btn danger remove-form-btn">Delete</button>
                            </div>

                            <div class="field-grid">
                                <div class="field">
                                    <label>Key</label>
                                    <input type="text" name="forms[{{ $index }}][key]" value="{{ $form['key'] ?? '' }}">
                                </div>

                                <div class="field">
                                    <label>Label</label>
                                    <input type="text" name="forms[{{ $index }}][label]" value="{{ $form['label'] ?? '' }}">
                                </div>

                                <div class="field">
                                    <label>Type 1</label>
                                    <select name="forms[{{ $index }}][type1]">
                                        <option value="">None</option>
                                        @foreach($types as $type)
                                            <option value="{{ $type }}" {{ (($form['type1'] ?? '') === $type) ? 'selected' : '' }}>
                                                {{ ucfirst($type) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="field">
                                    <label>Type 2</label>
                                    <select name="forms[{{ $index }}][type2]">
                                        <option value="">None</option>
                                        @foreach($types as $type)
                                            <option value="{{ $type }}" {{ (($form['type2'] ?? '') === $type) ? 'selected' : '' }}>
                                                {{ ucfirst($type) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="field-grid stats-grid">
                                <div class="field"><label>HP</label><input type="number" name="forms[{{ $index }}][hp]" value="{{ $form['hp'] ?? '' }}" min="1" max="255"></div>
                                <div class="field"><label>Attack</label><input type="number" name="forms[{{ $index }}][attack]" value="{{ $form['attack'] ?? '' }}" min="1" max="255"></div>
                                <div class="field"><label>Defense</label><input type="number" name="forms[{{ $index }}][defense]" value="{{ $form['defense'] ?? '' }}" min="1" max="255"></div>
                                <div class="field"><label>Sp. Attack</label><input type="number" name="forms[{{ $index }}][special_attack]" value="{{ $form['special_attack'] ?? '' }}" min="1" max="255"></div>
                                <div class="field"><label>Sp. Defense</label><input type="number" name="forms[{{ $index }}][special_defense]" value="{{ $form['special_defense'] ?? '' }}" min="1" max="255"></div>
                                <div class="field"><label>Speed</label><input type="number" name="forms[{{ $index }}][speed]" value="{{ $form['speed'] ?? '' }}" min="1" max="255"></div>
                            </div>

                            <div class="field-grid">
                                <div class="field">
                                    <label>Normal PNG</label>
                                    <input type="hidden" name="forms[{{ $index }}][existing_image_default]" value="{{ $form['existing_image_default'] ?? '' }}">
                                    <input type="file" name="forms[{{ $index }}][image_default]" accept=".png,image/png">
                                    @if(!empty($form['existing_image_default']))
                                        <img src="{{ asset($form['existing_image_default']) }}" alt="" class="preview-image">
                                    @endif
                                </div>

                                <div class="field">
                                    <label>Shiny PNG</label>
                                    <input type="hidden" name="forms[{{ $index }}][existing_image_shiny]" value="{{ $form['existing_image_shiny'] ?? '' }}">
                                    <input type="file" name="forms[{{ $index }}][image_shiny]" accept=".png,image/png">
                                    @if(!empty($form['existing_image_shiny']))
                                        <img src="{{ asset($form['existing_image_shiny']) }}" alt="" class="preview-image">
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="form-submit-row">
            <a href="{{ route('pokemons.manage') }}" class="manage-btn secondary">Cancel</a>
            <button type="submit" class="manage-btn primary">Save changes</button>
        </div>
    </form>
</div>

<template id="form-row-template">
    <div class="form-variant-card">
        <div class="form-card-header">
            <h3>Form</h3>
            <button type="button" class="manage-btn danger remove-form-btn">Delete</button>
        </div>

        <div class="field-grid">
            <div class="field">
                <label>Key</label>
                <input type="text" name="forms[__INDEX__][key]">
            </div>

            <div class="field">
                <label>Label</label>
                <input type="text" name="forms[__INDEX__][label]">
            </div>

            <div class="field">
                <label>Type 1</label>
                <select name="forms[__INDEX__][type1]">
                    <option value="">None</option>
                    @foreach($types as $type)
                        <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label>Type 2</label>
                <select name="forms[__INDEX__][type2]">
                    <option value="">None</option>
                    @foreach($types as $type)
                        <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="field-grid stats-grid">
            <div class="field"><label>HP</label><input type="number" name="forms[__INDEX__][hp]" min="1" max="255"></div>
            <div class="field"><label>Attack</label><input type="number" name="forms[__INDEX__][attack]" min="1" max="255"></div>
            <div class="field"><label>Defense</label><input type="number" name="forms[__INDEX__][defense]" min="1" max="255"></div>
            <div class="field"><label>Sp. Attack</label><input type="number" name="forms[__INDEX__][special_attack]" min="1" max="255"></div>
            <div class="field"><label>Sp. Defense</label><input type="number" name="forms[__INDEX__][special_defense]" min="1" max="255"></div>
            <div class="field"><label>Speed</label><input type="number" name="forms[__INDEX__][speed]" min="1" max="255"></div>
        </div>

        <div class="field-grid">
            <div class="field">
                <label>Normal PNG</label>
                <input type="hidden" name="forms[__INDEX__][existing_image_default]" value="">
                <input type="file" name="forms[__INDEX__][image_default]" accept=".png,image/png">
            </div>

            <div class="field">
                <label>Shiny PNG</label>
                <input type="hidden" name="forms[__INDEX__][existing_image_shiny]" value="">
                <input type="file" name="forms[__INDEX__][image_shiny]" accept=".png,image/png">
            </div>
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('forms-container');
    const addBtn = document.getElementById('add-form-btn');
    const template = document.getElementById('form-row-template');

    let index = container.querySelectorAll('.form-variant-card').length;

    addBtn?.addEventListener('click', () => {
        const html = template.innerHTML.replaceAll('__INDEX__', index);
        container.insertAdjacentHTML('beforeend', html);
        index++;
    });

    document.addEventListener('click', (e) => {
        if (e.target.classList.contains('remove-form-btn')) {
            const card = e.target.closest('.form-variant-card');
            if (card) {
                card.remove();
            }
        }
    });
});
</script>
</body>
</html>