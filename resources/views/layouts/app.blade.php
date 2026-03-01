<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div id="app">

    <nav class="navbar">
        <div class="nav-left">
            <a class="nav-brand" href="{{ url('/') }}">{{ config('app.name', 'Pokédex') }}</a>
        </div>

        <div class="nav-right">
            @guest
                @if (Route::has('login'))
                    <a class="nav-link" href="{{ route('login') }}">Login</a>
                @endif
                @if (Route::has('register'))
                    <a class="nav-link" href="{{ route('register') }}">Register</a>
                @endif
            @else
                <a class="nav-link" href="{{ route('pokemons.index') }}">Pokédex</a>
                <a class="nav-link" href="{{ route('teams.index') }}">Teams</a>

                <span class="nav-user">{{ Auth::user()->name }}</span>

                <a class="nav-link"
                   href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Logout
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            @endguest
        </div>
    </nav>

    <main class="py-4">
        @yield('content')
    </main>

</div>
</body>
</html>
