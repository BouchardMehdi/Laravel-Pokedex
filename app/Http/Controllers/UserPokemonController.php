<?php

namespace App\Http\Controllers;

use App\Models\Pokemon;
use Illuminate\Http\Request;

class UserPokemonController extends Controller
{
    public function store(Request $request, Pokemon $pokemon)
    {
        $user = $request->user();

        $user->pokemons()->syncWithoutDetaching([$pokemon->id]);

        return response()->json([
            'success' => true,
            'pokemon_id' => $pokemon->id,
        ]);
    }

    public function unlockAll(Request $request)
    {
        $user = $request->user();

        $ids = Pokemon::pluck('id')->all();
        // attach sans détacher
        $user->pokemons()->syncWithoutDetaching($ids);

        return response()->json([
            'success' => true,
            'count' => count($ids),
        ]);
    }

    public function lockAll(Request $request)
    {
        $user = $request->user();

        $user->pokemons()->detach();

        return response()->json([
            'success' => true,
        ]);
    }
}
