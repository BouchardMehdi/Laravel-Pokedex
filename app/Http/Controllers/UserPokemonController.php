<?php

namespace App\Http\Controllers;

use App\Models\Pokemon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserPokemonController extends Controller
{
    public function store(Pokemon $pokemon)
    {
        $user = Auth::user();

        $user->pokemons()->syncWithoutDetaching([$pokemon->id]);

        return response()->json(['success' => true]);
    }

    public function unlockAll()
    {
        $user = Auth::user();
        $ids = Pokemon::pluck('id')->toArray();

        $user->pokemons()->syncWithoutDetaching($ids);

        return response()->json(['success' => true]);
    }

    public function lockAll()
    {
        $user = Auth::user();
        $user->pokemons()->detach();

        return response()->json(['success' => true]);
    }

    // ✅ NOUVEAU : débloquer les pokémons affichés sur la page
    public function unlockPage(Request $request)
    {
        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:pokemons,id'],
        ]);

        $user = Auth::user();
        $user->pokemons()->syncWithoutDetaching($request->ids);

        return response()->json(['success' => true]);
    }

    // ✅ NOUVEAU : débloquer une génération entière
    public function unlockGeneration(Request $request)
    {
        $request->validate([
            'generation' => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        $ids = Pokemon::where('generation', (int) $request->generation)->pluck('id')->toArray();

        Auth::user()->pokemons()->syncWithoutDetaching($ids);

        return response()->json(['success' => true, 'count' => count($ids)]);
    }
}