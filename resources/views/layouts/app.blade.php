<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/auth.css'])
</head>
<body>

{{-- ✅ On cache le header si on est sur une page auth --}}
@php
    $hideHeader = request()->routeIs('password.*') 
                  || request()->routeIs('login') 
                  || request()->routeIs('register');
@endphp

@if(!$hideHeader)
    <div style="position:absolute;top:15px;left:0;right:0;text-align:center;">
        @guest
            <a href="{{ route('login') }}" style="color:#9fb0c0;text-decoration:none;margin-right:10px;">Login</a>
            <a href="{{ route('register') }}" style="color:#9fb0c0;text-decoration:none;">Register</a>
        @endguest
    </div>
@endif

@yield('content')

</body>
</html>