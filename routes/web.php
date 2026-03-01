<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PokemonController;
use App\Http\Controllers\UserPokemonController;
use App\Http\Controllers\TeamController;

Auth::routes();

Route::get('/', [PokemonController::class, 'home'])->name('home');

Route::get('/pokemons', [PokemonController::class, 'index'])
    ->middleware('auth')
    ->name('pokemons.index');

Route::get('/pokemons/{pokemon}', [PokemonController::class, 'show'])
    ->name('pokemons.show');

Route::post('/pokemons/{pokemon}/unlock', [UserPokemonController::class, 'store'])
    ->middleware('auth')
    ->name('pokemons.unlock');

Route::post('/pokemons/unlock-all', [UserPokemonController::class, 'unlockAll'])
    ->middleware('auth')
    ->name('pokemons.unlockAll');

Route::post('/pokemons/lock-all', [UserPokemonController::class, 'lockAll'])
    ->middleware('auth')
    ->name('pokemons.lockAll');

Route::middleware('auth')->group(function () {
    Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
    Route::get('/teams/create', [TeamController::class, 'create'])->name('teams.create');
    Route::post('/teams', [TeamController::class, 'store'])->name('teams.store');
    Route::get('/teams/{team}/edit', [TeamController::class, 'edit'])->name('teams.edit');
    Route::put('/teams/{team}', [TeamController::class, 'update'])->name('teams.update');
    Route::delete('/teams/{team}', [TeamController::class, 'destroy'])->name('teams.destroy');

    Route::get('/teams/{team}/pick/{slot}', [TeamController::class, 'pick'])->name('teams.pick');

    Route::post('/teams/{team}/slot/{slot}', [TeamController::class, 'setSlot'])->name('teams.slot.set');
    Route::delete('/teams/{team}/slot/{slot}', [TeamController::class, 'clearSlot'])->name('teams.slot.clear');
});
