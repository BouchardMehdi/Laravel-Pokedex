<?php

namespace App\Http\Controllers;

use App\Models\Pokemon;
use App\Models\UserTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PokemonController extends Controller
{
    private function filteredQuery(Request $request)
    {
        $query = Pokemon::query();

        $query->where(function ($q) {
            $q->whereNull('slug')
              ->orWhere(function ($qq) {
                  $qq->where('slug', 'not like', '%-alola%')
                     ->where('slug', 'not like', '%-galar%')
                     ->where('slug', 'not like', '%-hisui%')
                     ->where('slug', 'not like', '%-paldea%')
                     ->where('slug', 'not like', '%-mega%')
                     ->where('slug', 'not like', 'mega-%')
                     ->where('slug', 'not like', '%-gmax%')
                     ->where('slug', 'not like', '%-gigantamax%');
              });
        });

        if ($q = $request->query('q')) {
            $query->where('name', 'like', "%{$q}%");
        }

        if ($gen = $request->query('generation')) {
            $query->where('generation', (int) $gen);
        }

        if ($type = $request->query('type')) {
            $query->where(function ($q) use ($type) {
                $q->where('type1', $type)
                  ->orWhere('type2', $type);
            });
        }

        if ($special = $request->query('special')) {
            match ($special) {
                'legendary' => $query->where('is_legendary', true),
                'fabulous'  => $query->where('is_fabulous', true),
                'ultra'     => $query->where('is_ultra_beast', true),
                'paradox'   => $query->where('is_paradox', true),
                default     => null,
            };
        }

        if ($form = $request->query('form')) {
            $query->whereNotNull('forms')
                  ->where('forms', '!=', '')
                  ->where('forms', '!=', '[]')
                  ->where('forms', '!=', '{}');

            switch ($form) {
                case 'mega':
                    $query->where(function ($q) {
                        $q->where('forms', 'like', '%"mega%')
                          ->orWhere('forms', 'like', '%Mega%');
                    });
                    break;

                case 'gmax':
                    $query->where(function ($q) {
                        $q->where('forms', 'like', '%"gmax%')
                          ->orWhere('forms', 'like', '%"gigantamax%')
                          ->orWhere('forms', 'like', '%Gigantamax%');
                    });
                    break;

                case 'alola':
                    $query->where(function ($q) {
                        $q->where('forms', 'like', '%"alola%')
                          ->orWhere('forms', 'like', '%"alolan%')
                          ->orWhere('forms', 'like', '%Alola%')
                          ->orWhere('forms', 'like', '%Alolan%');
                    });
                    break;

                case 'galar':
                    $query->where(function ($q) {
                        $q->where('forms', 'like', '%"galar%')
                          ->orWhere('forms', 'like', '%Galar%');
                    });
                    break;

                case 'hisui':
                    $query->where(function ($q) {
                        $q->where('forms', 'like', '%"hisui%')
                          ->orWhere('forms', 'like', '%Hisui%');
                    });
                    break;

                case 'paldea':
                    $query->where(function ($q) {
                        $q->where('forms', 'like', '%"paldea%')
                          ->orWhere('forms', 'like', '%Paldea%');
                    });
                    break;

                case 'other':
                    $query->where(function ($q) {
                        $q->where('forms', 'not like', '%"mega%')
                          ->where('forms', 'not like', '%"gmax%')
                          ->where('forms', 'not like', '%"gigantamax%')
                          ->where('forms', 'not like', '%"alola%')
                          ->where('forms', 'not like', '%"alolan%')
                          ->where('forms', 'not like', '%"galar%')
                          ->where('forms', 'not like', '%"hisui%')
                          ->where('forms', 'not like', '%"paldea%');
                    });
                    break;
            }
        }

        return $query;
    }

    public function home(Request $request)
    {
        $pokemons = $this->filteredQuery($request)
            ->orderBy('pokedex_number')
            ->limit(450)
            ->get(['id', 'name', 'slug', 'pokedex_number', 'image_default']);

        $teams = collect();

        if (Auth::check()) {
            $teams = UserTeam::where('user_id', Auth::id())
                ->with(['pokemons' => function ($q) {
                    $q->select('pokemons.id', 'name', 'slug', 'image_default', 'forms')
                      ->orderBy('user_team_pokemon.slot');
                }])
                ->latest()
                ->get();
        }

        return view('home', compact('pokemons', 'teams'));
    }

    public function index(Request $request)
    {
        $pokemons = $this->filteredQuery($request)
            ->orderBy('pokedex_number')
            ->paginate(18)
            ->withQueryString();

        $generations = Pokemon::select('generation')
            ->distinct()
            ->orderBy('generation')
            ->pluck('generation');

        $types = Pokemon::select('type1')
            ->whereNotNull('type1')
            ->distinct()
            ->orderBy('type1')
            ->pluck('type1');

        $unlockedIds = DB::table('pokemon_user')
            ->where('user_id', Auth::id())
            ->pluck('pokemon_id')
            ->toArray();

        return view('index', compact('pokemons', 'generations', 'types', 'unlockedIds'));
    }

    public function show(Pokemon $pokemon)
    {
        $prevPokemon = Pokemon::where('pokedex_number', '<', $pokemon->pokedex_number)
            ->orderBy('pokedex_number', 'desc')
            ->first();

        $nextPokemon = Pokemon::where('pokedex_number', '>', $pokemon->pokedex_number)
            ->orderBy('pokedex_number', 'asc')
            ->first();

        return view('show', compact('pokemon', 'prevPokemon', 'nextPokemon'));
    }

    public function manage(Request $request)
    {
        $query = Pokemon::query();

        if ($request->filled('q')) {
            $search = trim($request->q);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('pokedex_number', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('generation')) {
            $query->where('generation', (int) $request->generation);
        }

        $pokemons = $query
            ->orderBy('pokedex_number')
            ->paginate(20)
            ->withQueryString();

        return view('manage-pokemons', compact('pokemons'));
    }

    public function create()
    {
        $pokemon = null;
        return view('create-pokemon', compact('pokemon'));
    }

    public function store(Request $request)
    {
        $validated = $this->validatePokemon($request);

        if (!$request->hasFile('image_default')) {
            return back()
                ->withErrors(['image_default' => 'L’image PNG normale est obligatoire.'])
                ->withInput();
        }

        $pokemon = new Pokemon();
        $this->fillPokemon($pokemon, $request, $validated, true);
        $pokemon->save();

        return redirect()
            ->route('pokemons.manage')
            ->with('success', 'Pokémon créé avec succès.');
    }

    public function edit(Pokemon $pokemon)
    {
        return view('edit-pokemon', compact('pokemon'));
    }

    public function update(Request $request, Pokemon $pokemon)
    {
        $validated = $this->validatePokemon($request, $pokemon);

        $this->fillPokemon($pokemon, $request, $validated, false);
        $pokemon->save();

        return redirect()
            ->route('pokemons.manage')
            ->with('success', 'Pokémon modifié avec succès.');
    }

    public function destroy(Pokemon $pokemon)
    {
        $this->deleteImageIfManaged($pokemon->image_default);
        $this->deleteImageIfManaged($pokemon->image_shiny);

        foreach (($pokemon->forms ?? []) as $form) {
            if (!is_array($form)) {
                continue;
            }

            $this->deleteImageIfManaged($form['image_default'] ?? null);
            $this->deleteImageIfManaged($form['image_shiny'] ?? null);
        }

        $pokemon->delete();

        return redirect()
            ->route('pokemons.manage')
            ->with('success', 'Pokémon supprimé avec succès.');
    }

    private function validatePokemon(Request $request, ?Pokemon $pokemon = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'pokedex_number' => ['required', 'integer', 'min:1', 'max:9999', 'unique:pokemons,pokedex_number,' . ($pokemon?->id ?? 'NULL')],
            'generation' => ['required', 'integer', 'min:1', 'max:10'],

            'type1' => ['required', 'string', 'max:30'],
            'type2' => ['nullable', 'string', 'max:30', 'different:type1'],

            'hp' => ['required', 'integer', 'min:1', 'max:255'],
            'attack' => ['required', 'integer', 'min:1', 'max:255'],
            'defense' => ['required', 'integer', 'min:1', 'max:255'],
            'special_attack' => ['required', 'integer', 'min:1', 'max:255'],
            'special_defense' => ['required', 'integer', 'min:1', 'max:255'],
            'speed' => ['required', 'integer', 'min:1', 'max:255'],

            'image_default' => ['nullable', 'file', 'mimes:png', 'max:4096'],
            'image_shiny' => ['nullable', 'file', 'mimes:png', 'max:4096'],

            'is_legendary' => ['nullable', 'boolean'],
            'is_fabulous' => ['nullable', 'boolean'],
            'is_ultra_beast' => ['nullable', 'boolean'],
            'is_paradox' => ['nullable', 'boolean'],

            'forms' => ['nullable', 'array'],
            'forms.*.key' => ['nullable', 'string', 'max:60'],
            'forms.*.label' => ['nullable', 'string', 'max:100'],
            'forms.*.type1' => ['nullable', 'string', 'max:30'],
            'forms.*.type2' => ['nullable', 'string', 'max:30'],
            'forms.*.hp' => ['nullable', 'integer', 'min:1', 'max:255'],
            'forms.*.attack' => ['nullable', 'integer', 'min:1', 'max:255'],
            'forms.*.defense' => ['nullable', 'integer', 'min:1', 'max:255'],
            'forms.*.special_attack' => ['nullable', 'integer', 'min:1', 'max:255'],
            'forms.*.special_defense' => ['nullable', 'integer', 'min:1', 'max:255'],
            'forms.*.speed' => ['nullable', 'integer', 'min:1', 'max:255'],
            'forms.*.image_default' => ['nullable', 'file', 'mimes:png', 'max:4096'],
            'forms.*.image_shiny' => ['nullable', 'file', 'mimes:png', 'max:4096'],
            'forms.*.existing_image_default' => ['nullable', 'string'],
            'forms.*.existing_image_shiny' => ['nullable', 'string'],
        ]);
    }

    private function fillPokemon(Pokemon $pokemon, Request $request, array $validated, bool $isCreate): void
    {
        $pokemon->name = trim($validated['name']);
        $pokemon->slug = $this->generateUniqueSlug($validated['name'], $pokemon->exists ? $pokemon->id : null);
        $pokemon->pokedex_number = (int) $validated['pokedex_number'];
        $pokemon->generation = (int) $validated['generation'];

        $pokemon->type1 = $validated['type1'];
        $pokemon->type2 = $validated['type2'] ?? null;

        $pokemon->hp = (int) $validated['hp'];
        $pokemon->attack = (int) $validated['attack'];
        $pokemon->defense = (int) $validated['defense'];
        $pokemon->special_attack = (int) $validated['special_attack'];
        $pokemon->special_defense = (int) $validated['special_defense'];
        $pokemon->speed = (int) $validated['speed'];

        $pokemon->is_legendary = $request->boolean('is_legendary');
        $pokemon->is_fabulous = $request->boolean('is_fabulous');
        $pokemon->is_ultra_beast = $request->boolean('is_ultra_beast');
        $pokemon->is_paradox = $request->boolean('is_paradox');

        if ($request->hasFile('image_default')) {
            $this->deleteImageIfManaged($pokemon->image_default);
            $pokemon->image_default = $this->storeImage($request->file('image_default'));
        } elseif ($isCreate && empty($pokemon->image_default)) {
            $pokemon->image_default = null;
        }

        if ($request->hasFile('image_shiny')) {
            $this->deleteImageIfManaged($pokemon->image_shiny);
            $pokemon->image_shiny = $this->storeImage($request->file('image_shiny'));
        }

        $pokemon->forms = $this->buildForms($request, $pokemon);
    }

    private function buildForms(Request $request, Pokemon $pokemon): array
    {
        $rows = $request->input('forms', []);
        $existingForms = is_array($pokemon->forms) ? $pokemon->forms : [];
        $forms = [];

        foreach ($rows as $index => $row) {
            if (!is_array($row)) {
                continue;
            }

            $key = trim($row['key'] ?? '');
            $label = trim($row['label'] ?? '');

            if ($key === '' && $label === '') {
                continue;
            }

            if ($key === '') {
                $key = Str::slug($label ?: 'form-' . $index);
            }

            $oldForm = $existingForms[$key] ?? [];

            $imageDefault = $row['existing_image_default'] ?? ($oldForm['image_default'] ?? null);
            $imageShiny = $row['existing_image_shiny'] ?? ($oldForm['image_shiny'] ?? null);

            if ($request->hasFile("forms.$index.image_default")) {
                $this->deleteImageIfManaged($imageDefault);
                $imageDefault = $this->storeImage($request->file("forms.$index.image_default"));
            }

            if ($request->hasFile("forms.$index.image_shiny")) {
                $this->deleteImageIfManaged($imageShiny);
                $imageShiny = $this->storeImage($request->file("forms.$index.image_shiny"));
            }

            if (!$imageDefault) {
                continue;
            }

            $forms[$key] = [
                'label' => $label !== '' ? $label : Str::upper(str_replace('-', ' ', $key)),
                'image_default' => $imageDefault,
                'image_shiny' => $imageShiny,
                'type1' => $row['type1'] ?? null,
                'type2' => $row['type2'] ?? null,
                'stats' => [
                    'hp' => (int) ($row['hp'] ?? 0),
                    'attack' => (int) ($row['attack'] ?? 0),
                    'defense' => (int) ($row['defense'] ?? 0),
                    'special_attack' => (int) ($row['special_attack'] ?? 0),
                    'special_defense' => (int) ($row['special_defense'] ?? 0),
                    'speed' => (int) ($row['speed'] ?? 0),
                ],
            ];
        }

        return $forms;
    }

    private function storeImage($file): string
    {
        $path = $file->store('pokemons', 'public');
        return 'storage/' . $path;
    }

    private function deleteImageIfManaged(?string $path): void
    {
        if (!$path || !str_starts_with($path, 'storage/')) {
            return;
        }

        $storagePath = Str::replaceFirst('storage/', '', $path);

        if (Storage::disk('public')->exists($storagePath)) {
            Storage::disk('public')->delete($storagePath);
        }
    }

    private function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base !== '' ? $base : 'pokemon';
        $i = 2;

        while (
            Pokemon::query()
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }
}