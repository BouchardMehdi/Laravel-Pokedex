<?php

namespace App\Http\Controllers;

use App\Models\Pokemon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserPokemonController extends Controller
{
    public function store(Pokemon $pokemon)
    {
        DB::table('pokemon_user')->updateOrInsert(
            [
                'user_id' => Auth::id(),
                'pokemon_id' => $pokemon->id,
            ],
            [
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        return response()->json(['success' => true]);
    }

    public function unlockAll()
    {
        $ids = Pokemon::pluck('id')->toArray();

        foreach ($ids as $pokemonId) {
            DB::table('pokemon_user')->updateOrInsert(
                [
                    'user_id' => Auth::id(),
                    'pokemon_id' => $pokemonId,
                ],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        return response()->json(['success' => true]);
    }

    public function lockAll()
    {
        DB::table('pokemon_user')
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json(['success' => true]);
    }

    public function unlockPage(Request $request)
    {
        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:pokemons,id'],
        ]);

        foreach ($request->ids as $pokemonId) {
            DB::table('pokemon_user')->updateOrInsert(
                [
                    'user_id' => Auth::id(),
                    'pokemon_id' => $pokemonId,
                ],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        return response()->json(['success' => true]);
    }

    public function unlockGeneration(Request $request)
    {
        $request->validate([
            'generation' => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        $ids = Pokemon::where('generation', (int) $request->generation)
            ->pluck('id')
            ->toArray();

        foreach ($ids as $pokemonId) {
            DB::table('pokemon_user')->updateOrInsert(
                [
                    'user_id' => Auth::id(),
                    'pokemon_id' => $pokemonId,
                ],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        return response()->json([
            'success' => true,
            'count' => count($ids),
        ]);
    }
}