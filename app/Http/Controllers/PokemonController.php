<?php

namespace App\Http\Controllers;

use App\Models\Pokemon;
use App\Models\UserTeam;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PokemonController extends Controller
{

    private function filteredQuery(Request $request)
    {
        $query = Pokemon::query();

        /**
         * -------------------------------
         * EXCLUSION DES FORMES SPÉCIALES
         * -------------------------------
         * On exclut :
         * - formes Alola
         * - formes Galar
         * - formes Hisui
         * - formes Paldea
         * - Méga-évolutions
         * - Gigantamax
         */
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

        /**
         * ----------------------------------------
         * FILTRES CLASSIQUES
         * ----------------------------------------
         */

        // nom
        if ($q = $request->query('q')) {
            $query->where('name', 'like', "%{$q}%");
        }

        // generation
        if ($gen = $request->query('generation')) {
            $query->where('generation', (int) $gen);
        }

        // type
        if ($type = $request->query('type')) {
            $query->where(function ($q) use ($type) {
                $q->where('type1', $type)
                  ->orWhere('type2', $type);
            });
        }

        // special (légendaire, fabuleux, ultre chimère, paradox)
        if ($special = $request->query('special')) {
            match ($special) {
                'legendary' => $query->where('is_legendary', true),
                'fabulous'  => $query->where('is_fabulous', true),
                'ultra'     => $query->where('is_ultra_beast', true),
                'paradox'   => $query->where('is_paradox', true),
                default     => null,
            };
        }

        /**
         * ----------------------------------------
         * FILTRE SUR LES FORMES DISPONIBLES
         * ----------------------------------------
         */
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

    // Page d’accueil
    public function home(Request $request)
    {
        // Affiche les 450 premiers Pokémon filtrés
        $pokemons = $this->filteredQuery($request)
            ->orderBy('pokedex_number')
            ->limit(450)
            ->get(['id', 'name', 'slug', 'pokedex_number', 'image_default']);

        $teams = collect();

        // Si utilisateur connecté → on charge ses équipes
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


     // Page liste complète (avec pagination)
     public function index(Request $request)
    {
        // Liste paginée (18 par page)
        $pokemons = $this->filteredQuery($request)
            ->orderBy('pokedex_number')
            ->paginate(18)
            ->withQueryString();

        // Liste des générations disponibles
        $generations = Pokemon::select('generation')
            ->distinct()
            ->orderBy('generation')
            ->pluck('generation');

        // Liste des types disponibles
        $types = Pokemon::select('type1')
            ->whereNotNull('type1')
            ->distinct()
            ->orderBy('type1')
            ->pluck('type1');

        // Pokémon débloqués par l’utilisateur
        $unlockedIds = Auth::user()
            ->pokemons()
            ->pluck('pokemon_id')
            ->toArray();

        return view('index', compact('pokemons', 'generations', 'types', 'unlockedIds'));
    }

     // Page détail d’un Pokémon
    public function show(Pokemon $pokemon)
    {
        // Pokémon précédent
        $prevPokemon = Pokemon::where('pokedex_number', '<', $pokemon->pokedex_number)
            ->orderBy('pokedex_number', 'desc')
            ->first();

        // Pokémon suivant
        $nextPokemon = Pokemon::where('pokedex_number', '>', $pokemon->pokedex_number)
            ->orderBy('pokedex_number', 'asc')
            ->first();

        return view('show', compact('pokemon', 'prevPokemon', 'nextPokemon'));
    }
}