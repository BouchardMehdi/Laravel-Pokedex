@extends('layouts.app')

@vite(['resources/css/teams.css'])

@section('content')
<div class="teams-container create">

    <div class="teams-topbar">
        <div>
            <h1 class="teams-title">Create a Team</h1>
            <p class="teams-sub">Create your team (max 6 Pokémon).</p>
        </div>

        <div class="teams-actions">
            <a class="btn secondary" href="{{ route('teams.index') }}">← My teams</a>
            <a class="btn secondary" href="{{ route('pokemons.index') }}">Pokédex</a>
        </div>
    </div>

    @if(session('success'))
        <div class="flash success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="flash error">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="flash error">
            @foreach($errors->all() as $e)
                <div>• {{ $e }}</div>
            @endforeach
        </div>
    @endif

    <div class="panel">
        <form method="POST" action="{{ route('teams.store') }}" class="team-form">
            @csrf

            <label class="label" for="name">Team name</label>
            <input class="input"
                   id="name"
                   name="name"
                   type="text"
                   maxlength="40"
                   value="{{ old('name') }}"
                   placeholder="exemple: Kanto team"
                   required>

            <div class="form-actions">
                <button class="btn" type="submit">Create</button>
                <a class="btn secondary" href="{{ route('teams.index') }}">Cancel</a>
            </div>
        </form>
    </div>

</div>
@endsection