@extends('layouts.app')

@vite(['resources/css/teams.css'])

@section('content')
<div class="teams-container">
    <div class="teams-topbar">
        <div>
            <h1 class="teams-title">Créer une Team</h1>
            <p class="teams-sub">Donne un nom à ta team, puis tu pourras choisir tes 6 slots.</p>
        </div>
        <div class="teams-actions">
            <a class="btn secondary" href="{{ route('teams.index') }}">← Mes teams</a>
        </div>
    </div>

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

            <label class="label" for="name">Nom de la team</label>
            <input class="input" id="name" name="name" type="text" maxlength="40" placeholder="ex: Team Kanto" value="{{ old('name') }}" required>

            <div class="form-actions">
                <button class="btn" type="submit">Créer</button>
                <a class="btn secondary" href="{{ route('teams.index') }}">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
