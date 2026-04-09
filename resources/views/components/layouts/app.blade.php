@props([
    'title' => config('app.name', 'Wiki Builder API'),
    'bodyClass' => '',
    'shellClass' => '',
    'mainClass' => '',
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0b1220">
    <title>{{ $title }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Orbitron:wght@700;800;900&display=swap" rel="stylesheet">
</head>
<body class="{{ trim('theme-dark '.$bodyClass) }}">
    <div class="{{ trim('shell '.$shellClass) }}">
        <header class="panel app-header">
            <a class="brand-lockup" href="{{ route('home') }}">
                <span class="brand-mark" aria-hidden="true">W</span>
                <span class="brand-copy">
                    <strong>Wiki Builder</strong>
                    <span>Builder, studio et API Laravel.</span>
                </span>
            </a>

            <nav id="main-nav" class="main-nav" aria-label="Navigation principale">
                <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Accueil</a>
                @auth
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                    <a class="nav-link {{ request()->routeIs('studio.games.create') ? 'active' : '' }}" href="{{ route('studio.games.create') }}">Studio</a>
                    <a class="nav-link {{ request()->routeIs('account') ? 'active' : '' }}" href="{{ route('account') }}">Mon compte</a>
                @endauth
                <a class="nav-link {{ request()->routeIs('privacy') ? 'active' : '' }}" href="{{ route('privacy') }}">Confidentialité</a>
                <a class="nav-link" href="/api/v1">API</a>
            </nav>

            <div class="header-actions">
                @auth
                    <span class="user-chip">
                        <span class="user-chip-label">Session</span>
                        <strong>{{ auth()->user()->display_name ?: auth()->user()->username }}</strong>
                    </span>
                    <a class="btn primary" href="{{ route('studio.games.create') }}">Nouveau draft</a>
                    <form class="inline-form" method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn danger" type="submit">Déconnexion</button>
                    </form>
                @else
                    <a class="btn ghost" href="{{ route('login') }}">Connexion</a>
                    <a class="btn primary" href="{{ route('register') }}">Créer un compte</a>
                @endauth
            </div>

            <button
                type="button"
                class="header-burger"
                data-header-burger
                aria-controls="main-nav"
                aria-expanded="false"
                aria-label="Ouvrir le menu principal"
            >
                <span></span>
                <span></span>
                <span></span>
            </button>
        </header>

        <main class="{{ trim('content '.$mainClass) }}">
            @if (session('status'))
                <div class="flash" role="status">{{ session('status') }}</div>
            @endif

            {{ $slot }}
        </main>
    </div>

    <script>
        (() => {
            const burger = document.querySelector('[data-header-burger]');
            const header = burger?.closest('.app-header');
            const nav = document.getElementById('main-nav');
            if (!burger || !header || !nav) return;

            const closeMenu = () => {
                header.classList.remove('menu-open');
                burger.setAttribute('aria-expanded', 'false');
            };

            burger.addEventListener('click', () => {
                const opened = header.classList.toggle('menu-open');
                burger.setAttribute('aria-expanded', opened ? 'true' : 'false');
            });

            document.addEventListener('click', (event) => {
                if (!header.contains(event.target)) closeMenu();
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') closeMenu();
            });

            nav.addEventListener('click', (event) => {
                if (event.target.closest('.nav-link')) closeMenu();
            });

            const desktopQuery = window.matchMedia('(min-width: 1101px)');
            desktopQuery.addEventListener?.('change', (event) => {
                if (event.matches) closeMenu();
            });
        })();
    </script>
</body>
</html>
