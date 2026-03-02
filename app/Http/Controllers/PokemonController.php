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

        // Ne garder que les “pokemons de base” (pas les variantes slug)
        $query->where(function ($q) {
            $q->whereNull('slug')
              ->orWhere(function ($qq) {
                  $qq->where('slug', 'not like', '%-alola%')
                     ->where('slug', 'not like', '%-galar%')
                     ->where('slug', 'not like', '%-hisui%')
                     ->where('slug', 'not like', '%-mega%')
                     ->where('slug', 'not like', 'mega-%')
                     ->where('slug', 'not like', '%-gmax%')
                     ->where('slug', 'not like', '%-gigantamax%');
              });
        });

        if ($q = $request->query('q')) {
            $query->where('name', 'like', "%$q%");
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

        // ✅ NOUVEAU : filtre forme
        if ($form = $request->query('form')) {
            switch ($form) {
                case 'mega':
                    $query->whereNotNull('image_mega')->where('image_mega', '!=', '');
                    break;
                case 'gmax':
                    $query->whereNotNull('image_gmax')->where('image_gmax', '!=', '');
                    break;
                case 'alola':
                    $query->whereNotNull('image_alola')->where('image_alola', '!=', '');
                    break;
                case 'galar':
                    $query->whereNotNull('image_galar')->where('image_galar', '!=', '');
                    break;
                case 'hisui':
                    $query->whereNotNull('image_hisui')->where('image_hisui', '!=', '');
                    break;
                case 'other':
                    // formes “custom” stockées en JSON (Aegislash blade/shield, etc.)
                    $query->whereNotNull('forms')
                          ->where('forms', '!=', '')
                          ->where('forms', '!=', '[]')
                          ->where('forms', '!=', '{}');
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
                ->orderBy('created_at', 'desc')
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

        $unlockedIds = Auth::user()
            ->pokemons()
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
}