<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PokemonSeeder extends Seeder
{
    public function run(): void
    {
        $jsonPath = database_path('seeders/data/pokemon.json');
        $data = json_decode(File::get($jsonPath), true);

        $now = Carbon::now();

        // Regroupement par "espèce de base" robuste (gère ho-oh, type-null, deoxys-normal, aegislash-blade, etc.)
        $formsByBase = [];
        foreach ($data as $row) {
            $api = strtolower($row['form_name_api'] ?? '');
            if (!$api) continue;

            $base = $this->baseSpeciesName($row);
            if (!$base) continue;

            $formsByBase[$base][] = $row;
        }

        // On garde uniquement 1 entrée par numéro de Pokédex (1..1025)
        // => la table pokemons contient les Pokémon "principaux", et la colonne forms contient toutes les formes.
        $basePokemons = [];
        foreach ($data as $row) {
            $dex = (int)($row['form_id'] ?? 0);
            if ($dex >= 1 && $dex <= 1025 && !isset($basePokemons[$dex])) {
                $basePokemons[$dex] = $row;
            }
        }
        ksort($basePokemons);

        $rows = [];

        foreach ($basePokemons as $dex => $pokemon) {

            $baseName = $this->baseSpeciesName($pokemon);
            if (!$baseName) continue;

            $types = $pokemon['types'] ?? [];
            $stats = $pokemon['stats'] ?? [];
            $sprites = $pokemon['sprites']['official'] ?? [];

            $imageDefault = $this->normalizeImage($sprites['default_file'] ?? null);
            $imageShiny   = $this->normalizeImage($sprites['shiny_file'] ?? null);

            // ✅ forms devient un objet structuré: suffix => {label, images, types, stats}
            $forms = [];

            if (!empty($formsByBase[$baseName])) {
                foreach ($formsByBase[$baseName] as $form) {

                    $api = strtolower($form['form_name_api'] ?? '');
                    if (!$api) continue;

                    // On évite de dupliquer exactement la forme de base si elle a le même api
                    // (ex: un Pokémon sans forme)
                    if ($this->baseSpeciesName($form) === $baseName && $api === $baseName) {
                        continue;
                    }

                    // Suffix = partie après "<base>-"
                    // Exemple: base=aegislash => aegislash-blade => blade
                    // Exemple: base=deoxys => deoxys-normal => normal
                    $suffix = $this->suffixFromApi($baseName, $api, $form);

                    // Si on ne peut pas calculer un suffix propre, on skip
                    if (!$suffix) continue;

                    $fTypes = $form['types'] ?? [];
                    $fStats = $form['stats'] ?? [];
                    $fSprites = $form['sprites']['official'] ?? [];

                    $fDefault = $this->normalizeImage($fSprites['default_file'] ?? null);
                    $fShiny   = $this->normalizeImage($fSprites['shiny_file'] ?? null);

                    // Sans image -> inutile à afficher
                    if (!$fDefault) continue;

                    $forms[$suffix] = [
                        'label' => $this->labelForForm($suffix, $form),
                        'image_default' => $fDefault,
                        'image_shiny' => $fShiny, // peut être null
                        'type1' => $fTypes[0] ?? null,
                        'type2' => $fTypes[1] ?? null,
                        'stats' => [
                            'hp' => (int)($fStats['hp'] ?? 0),
                            'attack' => (int)($fStats['attack'] ?? 0),
                            'defense' => (int)($fStats['defense'] ?? 0),
                            'special_attack' => (int)($fStats['special-attack'] ?? 0),
                            'special_defense' => (int)($fStats['special-defense'] ?? 0),
                            'speed' => (int)($fStats['speed'] ?? 0),
                        ],
                    ];
                }
            }

            $rows[] = [
                'name' => $baseName,
                'slug' => Str::slug($baseName),
                'pokedex_number' => $dex,
                'generation' => $this->generationFromDex($dex),

                'type1' => $types[0] ?? null,
                'type2' => $types[1] ?? null,

                'hp' => (int)($stats['hp'] ?? 0),
                'attack' => (int)($stats['attack'] ?? 0),
                'defense' => (int)($stats['defense'] ?? 0),
                'special_attack' => (int)($stats['special-attack'] ?? 0),
                'special_defense' => (int)($stats['special-defense'] ?? 0),
                'speed' => (int)($stats['speed'] ?? 0),

                'image_default' => $imageDefault,
                'image_shiny' => $imageShiny,

                // ✅ array cast côté Model -> on peut stocker direct un array
                'forms' => json_encode($forms),

                'is_legendary' => (int)($pokemon['is_legendary'] ?? 0),
                'is_fabulous' => (int)($pokemon['is_fabulous'] ?? 0),
                'is_ultra_beast' => (int)($pokemon['is_ultra_beast'] ?? 0),
                'is_paradox' => (int)($pokemon['is_paradox'] ?? 0),

                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('pokemons')->truncate();

        foreach (array_chunk($rows, 200) as $chunk) {
            DB::table('pokemons')->insert($chunk);
        }

        $this->command->info(count($rows) . " Pokémon insérés (forms + stats + images).");
    }

    private function baseSpeciesName(array $row): ?string
    {
        $api = strtolower($row['form_name_api'] ?? '');
        $base = strtolower($row['base_pokemon'] ?? $api);
        if (!$base) return null;

        $label = strtolower($row['form_name_label'] ?? '');
        if ($label) {
            $suffix = '-' . $label;
            if (str_ends_with($base, $suffix)) {
                $base = substr($base, 0, -strlen($suffix));
            }
        }

        return $base ?: null;
    }

    /**
     * Calcule un suffix de forme propre (ex: mega-x, gmax, blade, shield, alola, etc.)
     */
    private function suffixFromApi(string $baseName, string $api, array $row): ?string
    {
        // Cas standard: "<base>-<suffix>"
        if (str_starts_with($api, $baseName . '-')) {
            return substr($api, strlen($baseName) + 1);
        }

        // Fallback: utilise form_name_label si présent
        $label = strtolower($row['form_name_label'] ?? '');
        if ($label) return $label;

        return null;
    }

    private function labelForForm(string $suffix, array $row): string
    {
        $k = strtolower($suffix);

        if ($k === 'gmax' || $k === 'gigantamax') return 'GMAX';
        if ($k === 'alola') return 'ALOLA';
        if ($k === 'galar') return 'GALAR';
        if ($k === 'hisui') return 'HISUI';

        // mega, mega-x, mega-y, mega-z
        if (str_starts_with($k, 'mega')) {
            $parts = explode('-', $k);
            if (count($parts) >= 2 && !empty($parts[1])) {
                return 'MEGA ' . strtoupper($parts[1]);
            }
            return 'MEGA';
        }

        // ex: blade => BLADE, shield => SHIELD, zen => ZEN, etc.
        return strtoupper(str_replace('-', ' ', $k));
    }

    private function normalizeImage(?string $file): ?string
    {
        if (!$file) return null;
        if (str_starts_with($file, 'images/')) return $file;
        return 'images/default/' . $file;
    }

    private function generationFromDex(int $dex): int
    {
        return match (true) {
            $dex <= 151 => 1,
            $dex <= 251 => 2,
            $dex <= 386 => 3,
            $dex <= 493 => 4,
            $dex <= 649 => 5,
            $dex <= 721 => 6,
            $dex <= 809 => 7,
            $dex <= 905 => 8,
            default => 9,
        };
    }
}
