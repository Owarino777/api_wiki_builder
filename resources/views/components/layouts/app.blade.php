<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'Wiki Builder API') }}</title>
    <style>
        :root{
            --bg:#08111f;
            --bg-elev:#0d1728;
            --surface:#111d31;
            --surface-2:#16243a;
            --surface-3:#1c2d46;
            --surface-soft:rgba(255,255,255,.03);
            --line:rgba(144,170,214,.14);
            --line-strong:rgba(144,170,214,.24);
            --text:#f4f7fb;
            --muted:#95a9c8;
            --muted-2:#6f84a2;
            --accent:#5aa2ff;
            --accent-strong:#2f6fe2;
            --accent-soft:rgba(90,162,255,.12);
            --success:#68d5a7;
            --warn:#efb45b;
            --danger:#ff7f91;
            --danger-soft:rgba(255,127,145,.12);
            --radius-xl:30px;
            --radius-lg:24px;
            --radius-md:18px;
            --radius-sm:14px;
            --shadow:0 24px 70px rgba(2,8,20,.42);
            --content-width:1360px;
        }
        *{box-sizing:border-box}
        html{color-scheme:dark}
        body{
            margin:0;
            font-family:"Aptos","Segoe UI Variable Text","Trebuchet MS","Segoe UI",sans-serif;
            color:var(--text);
            background:
                radial-gradient(circle at top left,rgba(90,162,255,.18),transparent 24rem),
                radial-gradient(circle at top right,rgba(104,213,167,.08),transparent 18rem),
                linear-gradient(180deg,#09111d,#0b1525 30%,#08111f);
        }
        body::before{
            content:"";
            position:fixed;
            inset:0;
            pointer-events:none;
            background:
                linear-gradient(rgba(255,255,255,.018) 1px,transparent 1px),
                linear-gradient(90deg,rgba(255,255,255,.018) 1px,transparent 1px);
            background-size:48px 48px;
            mask-image:linear-gradient(180deg,rgba(0,0,0,.35),transparent 60%);
            opacity:.2;
        }
        a,button,input,select,textarea{font:inherit}
        a{color:inherit;text-decoration:none}
        button{border:0;cursor:pointer}
        h1,h2,h3,h4,p{margin:0}
        .shell{
            width:min(var(--content-width),calc(100vw - 32px));
            margin:20px auto 36px;
            display:grid;
            gap:24px;
            position:relative;
            z-index:1;
        }
        .panel{
            background:linear-gradient(180deg,rgba(255,255,255,.03),rgba(255,255,255,.015)),var(--surface);
            border:1px solid var(--line);
            border-radius:var(--radius-xl);
            box-shadow:var(--shadow);
        }
        .content{display:grid;gap:24px}
        .card{padding:28px}
        .app-header{
            display:grid;
            grid-template-columns:minmax(240px,.95fr) minmax(0,1.1fr) auto;
            gap:18px;
            align-items:center;
            padding:18px 22px;
            background:
                radial-gradient(circle at left top,rgba(90,162,255,.12),transparent 16rem),
                linear-gradient(180deg,rgba(255,255,255,.02),rgba(255,255,255,.01)),
                var(--surface);
            backdrop-filter:blur(12px);
        }
        .brand-lockup{display:flex;align-items:center;gap:14px;min-width:0}
        .brand-mark{
            width:48px;
            height:48px;
            border-radius:16px;
            display:grid;
            place-items:center;
            background:linear-gradient(135deg,var(--accent),var(--accent-strong));
            color:#fff;
            font-weight:800;
            font-size:22px;
            box-shadow:0 16px 32px rgba(47,111,226,.28);
        }
        .brand-copy{display:grid;gap:4px;min-width:0}
        .brand-copy strong{
            font-size:22px;
            line-height:1;
            letter-spacing:-.03em;
        }
        .brand-copy span{
            color:var(--muted);
            font-size:13px;
            line-height:1.4;
        }
        .main-nav,.header-actions,.actions,.meta,.grid,.stats,.hero-actions,.section-actions{
            display:flex;
            flex-wrap:wrap;
            gap:10px;
        }
        .main-nav{justify-content:center}
        .header-actions{justify-content:flex-end;align-items:center}
        .nav-link,.btn{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            min-height:44px;
            padding:10px 16px;
            border-radius:999px;
            border:1px solid var(--line);
            background:rgba(255,255,255,.02);
            color:var(--text);
            transition:.2s ease;
            white-space:nowrap;
        }
        .nav-link:hover,.btn:hover{transform:translateY(-1px);border-color:var(--line-strong)}
        .nav-link.active,.btn.primary{
            border-color:transparent;
            background:linear-gradient(135deg,var(--accent),var(--accent-strong));
            color:#fff;
            box-shadow:0 14px 28px rgba(47,111,226,.24);
        }
        .btn.secondary,.nav-link{
            background:rgba(255,255,255,.025);
        }
        .btn.ghost{
            background:var(--surface-2);
            border-color:var(--line);
        }
        .btn.subtle{
            background:transparent;
            color:var(--muted);
        }
        .btn.danger{
            background:var(--danger-soft);
            border-color:rgba(255,127,145,.26);
            color:#ffd9df;
        }
        .inline-form{margin:0}
        .user-chip{
            display:inline-flex;
            align-items:center;
            gap:10px;
            min-height:44px;
            padding:8px 14px;
            border-radius:999px;
            border:1px solid var(--line);
            background:rgba(255,255,255,.02);
            color:var(--muted);
        }
        .user-chip strong{color:var(--text);font-size:14px}
        .flash{
            padding:14px 18px;
            border-radius:18px;
            border:1px solid rgba(90,162,255,.28);
            background:rgba(90,162,255,.1);
            color:#dceaff;
        }
        .pill,.eyebrow{
            display:inline-flex;
            align-items:center;
            min-height:28px;
            padding:6px 10px;
            border-radius:999px;
            border:1px solid rgba(90,162,255,.24);
            background:var(--accent-soft);
            color:#d8e8ff;
            font-size:11px;
            font-weight:700;
            letter-spacing:.12em;
            text-transform:uppercase;
        }
        .eyebrow{width:max-content}
        .helper{color:var(--muted);line-height:1.6}
        .helper.compact{font-size:14px}
        .muted{color:var(--muted)}
        .error{color:#ffd0d6;font-size:13px}
        form{display:grid;gap:14px}
        .field{display:grid;gap:8px}
        .field label{
            color:var(--muted);
            font-size:12px;
            font-weight:700;
            letter-spacing:.08em;
            text-transform:uppercase;
        }
        input,select,textarea{
            width:100%;
            min-height:50px;
            padding:12px 14px;
            border-radius:16px;
            border:1px solid var(--line);
            background:var(--surface-2);
            color:var(--text);
            outline:none;
        }
        textarea{min-height:110px;resize:vertical}
        input[type=checkbox]{width:auto;min-height:auto;padding:0}
        .checkbox{
            display:flex;
            gap:10px;
            align-items:flex-start;
            color:var(--muted);
            line-height:1.5;
        }
        .checkbox input{margin-top:4px}
        input:focus-visible,select:focus-visible,textarea:focus-visible,.btn:focus-visible,.nav-link:focus-visible{
            border-color:var(--accent);
            box-shadow:0 0 0 4px rgba(90,162,255,.16);
        }
        .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:18px}
        .hero-panel{
            padding:32px;
            background:
                radial-gradient(circle at top right,rgba(90,162,255,.16),transparent 16rem),
                linear-gradient(180deg,rgba(255,255,255,.03),rgba(255,255,255,.01)),
                var(--surface);
        }
        .hero-main{
            display:grid;
            grid-template-columns:minmax(0,1.1fr) minmax(340px,.9fr);
            gap:24px;
            align-items:stretch;
        }
        .hero-copy{
            display:grid;
            gap:16px;
            align-content:start;
        }
        .hero-copy h1{
            font-size:clamp(42px,5vw,64px);
            line-height:.92;
            letter-spacing:-.055em;
            max-width:14ch;
        }
        .hero-copy .lead{
            max-width:40rem;
            color:#d7e2f2;
            font-size:17px;
            line-height:1.6;
        }
        .hero-note{
            display:flex;
            flex-wrap:wrap;
            gap:10px;
            color:var(--muted);
            font-size:14px;
        }
        .hero-side,.stack{display:grid;gap:16px}
        .signal-grid{
            display:grid;
            grid-template-columns:repeat(2,minmax(0,1fr));
            gap:12px;
        }
        .signal{
            padding:16px 18px;
            border-radius:22px;
            border:1px solid var(--line);
            background:linear-gradient(180deg,rgba(255,255,255,.03),rgba(255,255,255,.01)),var(--surface-2);
            display:grid;
            gap:6px;
            min-height:118px;
            align-content:end;
        }
        .signal .label{
            color:var(--muted);
            font-size:13px;
        }
        .signal .value{
            font-size:clamp(24px,2.7vw,38px);
            line-height:.95;
            letter-spacing:-.04em;
            overflow-wrap:anywhere;
        }
        .signal.accent{
            background:linear-gradient(145deg,rgba(90,162,255,.14),rgba(255,255,255,.015)),var(--surface-2);
        }
        .signal.success{
            background:linear-gradient(145deg,rgba(104,213,167,.12),rgba(255,255,255,.015)),var(--surface-2);
        }
        .spotlight-card,.section-card,.action-card,.info-card{
            padding:22px;
            border-radius:26px;
            border:1px solid var(--line);
            background:linear-gradient(180deg,rgba(255,255,255,.025),rgba(255,255,255,.01)),var(--surface-2);
        }
        .spotlight-card{display:grid;gap:14px;min-height:100%}
        .spotlight-card h2,.section-head h2{
            font-size:30px;
            line-height:1;
            letter-spacing:-.035em;
        }
        .spotlight-card h3,.list-row strong,.action-card h3,.info-card h3{
            font-size:20px;
            line-height:1.15;
            letter-spacing:-.02em;
        }
        .section-shell{
            display:grid;
            grid-template-columns:minmax(0,1.15fr) minmax(300px,.85fr);
            gap:20px;
            align-items:start;
        }
        .section-card{display:grid;gap:18px}
        .section-head{
            display:flex;
            justify-content:space-between;
            gap:14px;
            align-items:flex-start;
        }
        .section-copy{display:grid;gap:8px}
        .list-rows{display:grid}
        .list-row{
            display:grid;
            grid-template-columns:minmax(0,1fr) auto;
            gap:16px;
            align-items:center;
            padding:18px 0;
        }
        .list-row + .list-row{border-top:1px solid var(--line)}
        .row-main{display:grid;gap:10px;min-width:0}
        .row-main p,.row-main small{color:var(--muted)}
        .row-meta{display:flex;flex-wrap:wrap;gap:8px}
        .tag{
            display:inline-flex;
            align-items:center;
            min-height:28px;
            padding:5px 10px;
            border-radius:999px;
            background:rgba(255,255,255,.04);
            border:1px solid var(--line);
            color:#dce7f8;
            font-size:12px;
        }
        .empty-block{
            min-height:220px;
            display:grid;
            gap:10px;
            place-content:center;
            text-align:left;
            border-radius:22px;
            border:1px dashed var(--line-strong);
            background:rgba(255,255,255,.018);
            padding:24px;
        }
        .empty-block strong{font-size:24px;line-height:1}
        .progress{
            width:100%;
            height:10px;
            border-radius:999px;
            overflow:hidden;
            background:rgba(255,255,255,.06);
        }
        .progress > span{
            display:block;
            height:100%;
            border-radius:inherit;
            background:linear-gradient(90deg,var(--accent),#7bc0ff);
        }
        .info-grid{
            display:grid;
            grid-template-columns:repeat(4,minmax(0,1fr));
            gap:14px;
        }
        .info-card{
            padding:18px;
            gap:8px;
        }
        .info-card span{
            color:var(--muted);
            font-size:12px;
            font-weight:700;
            letter-spacing:.08em;
            text-transform:uppercase;
        }
        .info-card strong{
            font-size:22px;
            line-height:1.08;
            overflow-wrap:anywhere;
        }
        .duo-grid{
            display:grid;
            grid-template-columns:repeat(2,minmax(0,1fr));
            gap:20px;
        }
        .action-card{
            display:grid;
            gap:16px;
        }
        .action-card.danger{
            border-color:rgba(255,127,145,.2);
            background:linear-gradient(180deg,rgba(255,127,145,.06),rgba(255,255,255,.01)),var(--surface-2);
        }
        .action-list{
            display:grid;
            gap:12px;
        }
        .action-list article{
            padding:16px 18px;
            border-radius:20px;
            border:1px solid var(--line);
            background:rgba(255,255,255,.02);
            display:grid;
            gap:6px;
        }
        .policy-grid{
            display:grid;
            grid-template-columns:1.1fr .9fr;
            gap:20px;
        }
        .policy-columns{
            display:grid;
            grid-template-columns:repeat(2,minmax(0,1fr));
            gap:20px;
        }
        .feature-list{
            display:grid;
            gap:12px;
        }
        .feature-list article{
            padding:16px 0;
            border-bottom:1px solid var(--line);
            display:grid;
            gap:6px;
        }
        .feature-list article:last-child{border-bottom:0;padding-bottom:0}
        .feature-list article:first-child{padding-top:0}
        .keyline{
            display:flex;
            justify-content:space-between;
            gap:10px;
            align-items:center;
            padding:12px 0;
            border-bottom:1px solid var(--line);
            color:var(--muted);
        }
        .keyline:last-child{border-bottom:0;padding-bottom:0}
        .keyline strong{color:var(--text);font-size:14px}
        .auth-card{max-width:620px;margin:0 auto}
        @media (max-width:1120px){
            .app-header,.hero-main,.section-shell,.policy-grid,.policy-columns,.duo-grid{grid-template-columns:1fr}
            .main-nav{justify-content:flex-start}
            .header-actions{justify-content:flex-start}
            .info-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
        }
        @media (max-width:760px){
            .shell{width:min(calc(100vw - 12px),var(--content-width));margin:8px auto 18px}
            .hero-panel,.card,.app-header{padding:18px}
            .main-nav,.header-actions,.actions,.hero-actions,.section-actions{display:grid;grid-template-columns:1fr}
            .signal-grid,.info-grid,.policy-columns{grid-template-columns:1fr}
            .list-row{grid-template-columns:1fr}
            .nav-link,.btn,.user-chip{width:100%}
        }
    </style>
</head>
<body>
    <div class="shell">
        <header class="panel app-header">
            <a class="brand-lockup" href="{{ route('home') }}">
                <div class="brand-mark">W</div>
                <div class="brand-copy">
                    <strong>Wiki Builder</strong>
                    <span>Dashboard, compte, confidentialité et pilotage produit.</span>
                </div>
            </a>

            <nav class="main-nav" aria-label="Navigation principale">
                <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Accueil</a>
                @auth
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                    <a class="nav-link {{ request()->routeIs('account') ? 'active' : '' }}" href="{{ route('account') }}">Mon compte</a>
                @endauth
                <a class="nav-link {{ request()->routeIs('privacy') ? 'active' : '' }}" href="{{ route('privacy') }}">Confidentialité</a>
                <a class="nav-link" href="/api/v1">API</a>
            </nav>

            <div class="header-actions">
                @auth
                    <span class="user-chip">
                        <span class="muted">Connecté</span>
                        <strong>{{ auth()->user()->display_name ?: auth()->user()->username }}</strong>
                    </span>
                    <a class="btn ghost" href="{{ route('studio.games.create') }}">Créer un jeu</a>
                    <form class="inline-form" method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn danger" type="submit">Déconnexion</button>
                    </form>
                @else
                    <a class="btn ghost" href="{{ route('login') }}">Connexion</a>
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
