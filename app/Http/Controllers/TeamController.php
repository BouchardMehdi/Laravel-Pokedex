<?php

namespace App\Http\Controllers;

use App\Models\UserTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $teams = Auth::user()
            ->teams()
            ->with(['pokemons' => function ($q) {
                $q->select('pokemons.id', 'name', 'slug', 'image_default', 'forms', 'pokedex_number')
                  ->orderBy('user_team_pokemon.slot');
            }])
            ->latest()
            ->get();

        return view('teams', compact('teams'));
    }

    public function create()
    {
        return view('team-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:40'],
        ]);

        $team = Auth::user()->teams()->create([
            'name' => $request->name,
        ]);

        return redirect()->route('teams.edit', $team->id)
            ->with('success', 'Team créée !');
    }

    public function edit(UserTeam $team)
    {
        $this->authorizeTeam($team);

        $team->load(['pokemons' => function ($q) {
            $q->select('pokemons.id', 'name', 'slug', 'image_default', 'forms', 'pokedex_number')
              ->orderBy('user_team_pokemon.slot');
        }]);

        $slots = $team->slots_map;

        return view('team-edit', compact('team', 'slots'));
    }

    public function update(Request $request, UserTeam $team)
    {
        $this->authorizeTeam($team);

        $request->validate([
            'name' => ['required', 'string', 'max:40'],
        ]);

        $team->update([
            'name' => $request->name,
        ]);

        return back()->with('success', 'Team enregistrée !');
    }

    public function destroy(UserTeam $team)
    {
        $this->authorizeTeam($team);

        $team->pokemons()->detach();
        $team->delete();

        return redirect()->route('teams.index')
            ->with('success', 'Team supprimée.');
    }

    public function pick(UserTeam $team, int $slot)
    {
        $this->authorizeTeam($team);

        $slot = max(1, min(6, $slot));

        return redirect()->route('pokemons.index', [
            'pick_team' => $team->id,
            'slot' => $slot,
        ]);
    }

    public function setSlot(Request $request, UserTeam $team, int $slot)
    {
        $this->authorizeTeam($team);

        $slot = max(1, min(6, $slot));

        $request->validate([
            'pokemon_id' => ['required', 'integer', 'exists:pokemons,id'],
            'form' => ['nullable', 'string'],
        ]);

        $pokemonId = (int) $request->pokemon_id;
        $form = $request->input('form', 'normal') ?: 'normal';

        $team->pokemons()->wherePivot('slot', $slot)->detach();

        $team->pokemons()->attach($pokemonId, [
            'slot' => $slot,
            'form' => $form,
        ]);

        return redirect()->route('teams.edit', $team->id)
            ->with('success', "Pokémon ajouté au slot $slot !");
    }

    public function clearSlot(UserTeam $team, int $slot)
    {
        $this->authorizeTeam($team);

        $slot = max(1, min(6, $slot));

        $team->pokemons()->wherePivot('slot', $slot)->detach();

        return back()->with('success', "Slot $slot vidé.");
    }

    private function authorizeTeam(UserTeam $team): void
    {
        if ($team->user_id !== Auth::id()) {
            abort(403);
        }
    }
}