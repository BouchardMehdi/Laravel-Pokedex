<?php

namespace App\Http\Controllers;

use App\Models\UserTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    public function index()
    {
        $teams = Auth::user()
            ->teams()
            ->with(['pokemons' => function ($q) {
                $q->orderBy('user_team_pokemon.slot');
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

        $team = UserTeam::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
        ]);

        return redirect()->route('teams.edit', $team)->with('success', 'Team créée !');
    }

    public function edit(UserTeam $team)
    {
        $this->authorizeTeam($team);

        $team->load('pokemons');
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

        return back()->with('success', 'Nom de la team mis à jour !');
    }

    public function destroy(UserTeam $team)
    {
        $this->authorizeTeam($team);

        $team->pokemons()->detach();
        $team->delete();

        return redirect()->route('teams.index')->with('success', 'Team supprimée.');
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
        ]);

        $pokemonId = (int) $request->pokemon_id;

        $isUnlocked = Auth::user()->pokemons()->where('pokemons.id', $pokemonId)->exists();
        if (!$isUnlocked) {
            return back()->with('error', "Tu dois d'abord débloquer ce Pokémon.");
        }

        $team->pokemons()->wherePivot('slot', $slot)->detach();
        $team->pokemons()->detach($pokemonId);
        $team->pokemons()->attach($pokemonId, ['slot' => $slot]);

        return redirect()->route('teams.edit', $team)->with('success', "Pokémon ajouté au slot $slot !");
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
