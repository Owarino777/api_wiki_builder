<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'Wiki Builder API') }}</title>
    <style>
        :root{--bg:#0f1620;--panel:#16212f;--panel-2:#1f2c3d;--line:#31455d;--text:#f5f7fb;--muted:#94a4b7;--accent:#2f8fff;--accent-2:#1d5fc0;--danger:#ff7f7f;--shadow:0 20px 45px rgba(0,0,0,.25)}
        *{box-sizing:border-box}body{margin:0;font-family:"Trebuchet MS","Segoe UI",sans-serif;background:radial-gradient(circle at top,rgba(47,143,255,.12),transparent 24rem),var(--bg);color:var(--text)}a,button,input{font:inherit}a{text-decoration:none;color:inherit}button{border:0;cursor:pointer}h1,h2,h3,p{margin:0}
        .shell{width:min(1120px,calc(100vw - 24px));margin:14px auto 28px;display:grid;gap:18px}.panel{background:linear-gradient(180deg,rgba(255,255,255,.03),rgba(255,255,255,.015)),var(--panel);border:1px solid var(--line);border-radius:24px;box-shadow:var(--shadow)}
        .nav,.content,.card{padding:20px}.nav{display:flex;justify-content:space-between;align-items:center;gap:16px}.brand{display:grid;gap:4px}.brand strong{font-size:22px}.brand span{color:var(--muted);font-size:13px}
        .nav-links,.actions,.grid,.stats{display:flex;flex-wrap:wrap;gap:10px}.content{display:grid;gap:18px}.card{display:grid;gap:14px}.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:16px}.stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px}
        .btn{display:inline-flex;align-items:center;justify-content:center;min-height:44px;padding:10px 16px;border-radius:14px;border:1px solid var(--line);background:var(--panel-2);color:var(--text);transition:.2s ease}.btn.primary{background:linear-gradient(135deg,var(--accent),var(--accent-2));border-color:transparent}.btn.danger{border-color:rgba(255,127,127,.35);color:#ffd6d6;background:rgba(255,127,127,.08)}.btn:hover{transform:translateY(-1px)}
        form{display:grid;gap:14px}.field{display:grid;gap:8px}.field label{font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--muted)}input{width:100%;min-height:48px;padding:12px 14px;border-radius:14px;border:1px solid var(--line);background:var(--panel-2);color:var(--text);outline:none}input:focus-visible,.btn:focus-visible{border-color:var(--accent);box-shadow:0 0 0 4px rgba(47,143,255,.14)}
        .helper{color:var(--muted);line-height:1.55}.error{color:#ffd1d1;font-size:13px}.flash{padding:14px 16px;border-radius:16px;border:1px solid rgba(47,143,255,.25);background:rgba(47,143,255,.08)}.metric{padding:16px;border-radius:18px;border:1px solid var(--line);background:var(--panel-2)}.metric strong{display:block;font-size:24px}.metric span{display:block;margin-top:6px;color:var(--muted);font-size:13px}.list{display:grid;gap:10px}.list article{padding:14px;border-radius:18px;border:1px solid var(--line);background:var(--panel-2);display:grid;gap:6px}.meta{display:flex;flex-wrap:wrap;gap:8px}.pill{display:inline-flex;align-items:center;min-height:30px;padding:6px 10px;border-radius:999px;border:1px solid rgba(47,143,255,.25);background:rgba(47,143,255,.1);color:#cde2ff;font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase}
        @media (max-width:720px){.shell{width:min(calc(100vw - 12px),1120px);margin:8px auto 18px}.nav{flex-direction:column;align-items:stretch}.nav-links,.actions{display:grid;grid-template-columns:1fr}}
    </style>
</head>
<body>
    <div class="shell">
        <header class="panel nav">
            <div class="brand">
                <strong>Wiki Builder</strong>
                <span>Auth web, drafts utilisateur et builder multi-jeux.</span>
            </div>
            <div class="nav-links">
                <a class="btn" href="{{ route('home') }}">Accueil</a>
                <a class="btn" href="/api/v1">API</a>
                @auth
                    <a class="btn" href="{{ route('studio.games.create') }}">Créer un jeu</a>
                    <a class="btn primary" href="{{ route('dashboard') }}">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn danger" type="submit">Déconnexion</button>
                    </form>
                @else
                    <a class="btn" href="{{ route('login') }}">Connexion</a>
                    <a class="btn primary" href="{{ route('register') }}">Créer un compte</a>
                @endauth
            </div>
        </header>

        <main class="content">
            @if (session('status'))
                <div class="flash">{{ session('status') }}</div>
            @endif

            {{ $slot }}
        </main>
    </div>
</body>
</html>
