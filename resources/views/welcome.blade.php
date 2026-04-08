<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Wiki Builder API') }}</title>
    <style>
        :root{--bg:#11161d;--panel:#18212c;--panel-2:#1e2936;--line:#2b3a4d;--text:#f6f8fb;--muted:#95a2b4;--accent:#3d82ff;--accent-2:#2454b7;--accent-soft:#15243f;--success:#c9ff2e;--shadow:0 20px 45px rgba(0,0,0,.28)}
        *{box-sizing:border-box}body{margin:0;font-family:"Trebuchet MS","Segoe UI",sans-serif;color:var(--text);background:radial-gradient(circle at top left,rgba(61,130,255,.12),transparent 28rem),var(--bg)}a,button,input,select{font:inherit}button{border:0}h1,h2,h3,h4,p{margin:0}
        .page{width:min(1800px,calc(100vw - 24px));margin:10px auto 30px;display:grid;gap:16px}.panel,.slot-card,.item-card,.modal-card,.api-card{background:linear-gradient(180deg,rgba(255,255,255,.02),rgba(255,255,255,.01)),var(--panel);border:1px solid var(--line);border-radius:24px;box-shadow:var(--shadow)}
        .hero,.top-controls,.catalog-panel,.builder-panel,.api-card{padding:22px}.hero{background:linear-gradient(145deg,#0a1020,#12213c 72%,#163368)}.hero-head,.hero-main,.hero-stats,.toolbar,.catalog-head,.builder-head,.slot-top,.slot-actions,.modal-head,.modal-actions,.api-head,.pager{display:flex;gap:12px}.hero-head,.toolbar,.catalog-head,.builder-head,.slot-top,.modal-head,.api-head{justify-content:space-between;align-items:flex-start}.hero-main{display:grid;grid-template-columns:minmax(0,1.3fr) minmax(360px,.7fr);gap:18px;margin-top:12px}.hero-copy{display:grid;gap:10px}.hero-copy h1{font-size:clamp(40px,5vw,72px);line-height:.92;letter-spacing:-.05em}.hero-copy p{color:rgba(246,248,251,.86);line-height:1.55;max-width:60rem}
        .pill,.mini{display:inline-flex;align-items:center;min-height:34px;padding:7px 12px;border-radius:999px;font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase};border:1px solid rgba(255,255,255,.1);color:#fff}.pill,.mini{background:var(--accent-soft);border:1px solid rgba(61,130,255,.18);color:#bfd4ff}
        .hero-links,.chips,.tags,.build-pills,.slot-grid,.stats-grid,.catalog-grid,.modal-grid,.accessory-grid{display:flex;flex-wrap:wrap;gap:10px;align-content:flex-start}.hero-links .btn{min-width:110px}.hero-stats{margin-top:16px;display:grid;grid-template-columns:repeat(4,minmax(0,1fr))}.hero-stat{padding:16px;border-radius:20px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.08)}.hero-stat strong{display:block;font-size:26px}.hero-stat span{display:block;margin-top:6px;color:rgba(246,248,251,.76);font-size:13px}
        .btn,.chip,.slot-button,.item-card,.icon-btn,.replace-btn,.summary{display:inline-flex;align-items:center;justify-content:center;text-decoration:none;cursor:pointer;transition:.2s ease}.btn,.chip,.slot-button,.replace-btn,.summary{min-height:46px;padding:11px 16px;border-radius:16px}.btn{border:1px solid rgba(255,255,255,.14);background:#fff;color:#11161d}.btn.primary{background:linear-gradient(135deg,var(--accent),var(--accent-2));border-color:transparent;color:#fff}.btn.ghost,.replace-btn{background:var(--panel-2);border:1px solid var(--line);color:var(--text)}.btn.neon{background:rgba(201,255,46,.12);border:1px solid rgba(201,255,46,.28);color:var(--success)}.btn:hover,.chip:hover,.slot-button:hover,.item-card:hover,.icon-btn:hover,.replace-btn:hover,.summary:hover{transform:translateY(-1px)}.btn:disabled,.icon-btn:disabled{opacity:.42;cursor:not-allowed;transform:none}
        .top-controls{display:grid;gap:18px}.section-head,.setup-main,.setup-side,.setup-card,.status-card,.advanced-body{display:grid;gap:14px}.section-head h2{font-size:clamp(28px,4vw,36px);line-height:1}.section-head p{max-width:62rem}.setup-shell{display:grid;grid-template-columns:minmax(0,1.35fr) minmax(320px,.65fr);gap:16px;align-items:start}.setup-card,.status-card{padding:18px;border-radius:22px;border:1px solid var(--line)}.setup-card{background:rgba(255,255,255,.02)}.setup-grid,.advanced-grid,.field-stack{display:grid;gap:14px}.setup-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.setup-grid .field.full{grid-column:1/-1}.field,.field-stack{display:grid;gap:8px;min-width:0}.field label{font-size:13px;font-weight:700;letter-spacing:.01em;color:var(--text)}input,select{width:100%;max-width:100%;min-height:48px;padding:12px 14px;border-radius:16px;border:1px solid var(--line);background:var(--panel-2);color:var(--text);outline:none}.helper,.muted,.helper-inline{color:var(--muted);line-height:1.5}.helper-inline{font-size:13px;min-height:20px}.status-card{background:linear-gradient(180deg,rgba(61,130,255,.08),rgba(255,255,255,.02)),var(--panel-2)}.status-card strong{font-size:24px;line-height:1.1}.advanced-panel{border:1px solid var(--line);border-radius:22px;background:var(--panel-2);overflow:hidden}.advanced-panel summary{padding:16px 18px}.advanced-body{padding:0 18px 18px}.advanced-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.advanced-actions{display:flex;justify-content:flex-start}.chip,.tag,.item-title strong,.slot-main h3,.replace-btn strong,.modal-head h2{overflow-wrap:anywhere}.field,.item-card,.slot-card,.modal-card,.modal-head>div,.modal-actions,.slot-main,.item-title{min-width:0}input:focus-visible,select:focus-visible,.btn:focus-visible,.chip:focus-visible,.item-card:focus-visible,.icon-btn:focus-visible,.summary:focus-visible,.replace-btn:focus-visible{border-color:var(--accent);box-shadow:0 0 0 4px rgba(61,130,255,.14)}
        .chip{border:1px solid var(--line);background:var(--panel-2);color:var(--text);padding:10px 14px;border-radius:999px}.chip[aria-pressed=true]{background:var(--accent-soft);border-color:rgba(61,130,255,.34);color:#d6e5ff}.build-pills .chip{min-height:40px}
        .workspace{display:grid;gap:16px;align-items:start}.workspace-top,.build-form-grid{display:grid;gap:16px}.workspace-top{grid-template-columns:minmax(0,1.08fr) minmax(0,.92fr);align-items:stretch}.build-form-grid{grid-template-columns:2fr 1fr 1fr;align-items:end}.catalog-panel{display:grid;gap:16px}.catalog-toolbar{display:grid;grid-template-columns:minmax(0,1fr) 180px 140px 170px;gap:12px}.catalog-grid{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:12px}.item-card{width:100%;height:100%;padding:16px;border:1px solid var(--line);background:linear-gradient(180deg,rgba(255,255,255,.03),rgba(255,255,255,.015)),var(--panel-2);color:var(--text);text-align:left;display:grid;gap:12px;align-content:start}.item-media,.slot-media{position:relative;overflow:hidden;border-radius:18px;border:1px solid rgba(255,255,255,.06);background:radial-gradient(circle at top right,rgba(61,130,255,.2),transparent 50%),linear-gradient(160deg,#101926,#1c2633)}.item-media{aspect-ratio:16/9;display:grid;place-items:center}.slot-media{min-height:150px;display:grid;place-items:center;margin-bottom:4px}.item-media img,.slot-media img{width:100%;height:100%;object-fit:contain;display:block}.item-media::after,.slot-media::after{content:"";position:absolute;inset:auto 0 0 0;height:40%;background:linear-gradient(180deg,transparent,rgba(0,0,0,.16))}.item-media-fallback{position:absolute;inset:0;display:grid;place-items:center;padding:14px;text-align:center;color:#bfd4ff;font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;background:linear-gradient(180deg,rgba(61,130,255,.12),rgba(255,255,255,.02))}.item-top{display:flex;justify-content:space-between;gap:12px}.item-title strong{display:block;font-size:22px;line-height:1}.item-title span{display:block;margin-top:6px;color:var(--muted)}.item-hint{color:#cfe0ff;font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase}
        .bars{display:grid;gap:8px}.bar{display:grid;grid-template-columns:66px 1fr 32px;gap:8px;align-items:center;color:var(--muted);font-size:13px}.track{height:8px;border-radius:999px;overflow:hidden;background:#2b3950}.fill{height:100%;border-radius:inherit;background:linear-gradient(90deg,#c9ff2e,var(--accent))}
        .pager{justify-content:space-between;align-items:center}.summary{padding:8px 14px;border:1px solid var(--line);background:var(--panel-2);color:var(--muted)}
        .builder-panel{display:grid;gap:16px;min-height:100%}.builder-head p{margin-top:8px;color:var(--muted)}.slot-list{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:14px;align-content:start}.slot-card{padding:16px;display:grid;gap:12px;height:100%}.slot-card[data-action]{cursor:pointer}.slot-card[data-action]:hover{transform:translateY(-1px)}.slot-card.empty{background:linear-gradient(180deg,rgba(61,130,255,.08),rgba(255,255,255,.02)),var(--panel)}.slot-card.add{place-items:center;text-align:center;border-style:dashed}.slot-number{display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:50%;background:var(--accent-soft);color:#d5e4ff;font-weight:700}.slot-actions{flex-wrap:wrap}.icon-btn{min-height:36px;padding:8px 12px;border-radius:12px;border:1px solid var(--line);background:var(--panel-2);color:var(--text)}.slot-main h3{font-size:28px;line-height:1}.slot-meta{display:flex;flex-wrap:wrap;gap:8px}.tag{display:inline-flex;align-items:center;min-height:32px;max-width:100%;padding:6px 10px;border-radius:999px;border:1px solid var(--line);background:var(--panel-2);color:var(--muted);font-size:12px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.stats-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px}.stat-box{padding:12px;border-radius:16px;border:1px solid var(--line);background:var(--panel-2)}.stat-box strong{display:block;font-size:22px}.stat-box span{display:block;margin-top:5px;font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:var(--muted)}
        .matrix-panel,.insight-card{display:grid;gap:16px;min-height:100%}.table{overflow-x:auto;border:1px solid var(--line);border-radius:18px;background:var(--panel-2)}table{width:100%;min-width:520px;border-collapse:collapse}th,td{padding:12px 14px;text-align:left;border-bottom:1px solid var(--line)}thead th{font-size:12px;letter-spacing:.08em;text-transform:uppercase;color:var(--muted)}.leader{background:rgba(61,130,255,.12);color:#dbe9ff;font-weight:700}.kv{display:grid;gap:10px}.kv article{padding:14px;border-radius:18px;border:1px solid var(--line);background:var(--panel-2)}.kv strong{display:block;font-size:20px}.status-ok{color:var(--success)}.status-warn{color:#ffd66b}
        .api-card{display:grid;gap:14px}details{width:100%}summary{list-style:none;display:flex;justify-content:space-between;align-items:center;cursor:pointer}summary::-webkit-details-marker{display:none}.api-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;margin-top:14px}.endpoint-list{display:grid;gap:10px}.endpoint{padding:14px;border-radius:18px;border:1px solid var(--line);background:var(--panel-2);display:grid;gap:6px}.method{display:inline-flex;align-items:center;justify-content:center;min-width:54px;min-height:28px;padding:4px 10px;border-radius:999px;background:#0d1424;color:#fff;font-size:12px;font-weight:700;letter-spacing:.08em}pre{margin:0;padding:16px;border-radius:18px;background:#0c1322;color:#f6f8fb;overflow:auto;font-size:13px;line-height:1.5}code{font-family:Consolas,"Courier New",monospace}
        .modal{position:fixed;inset:0;padding:16px;background:rgba(6,9,14,.72);backdrop-filter:blur(8px);display:grid;place-items:center;z-index:50;animation:fade-in .18s ease}.modal-card{width:min(1120px,100%);max-height:min(88vh,900px);overflow:auto;padding:22px;animation:modal-in .22s ease}.modal-grid{display:grid;grid-template-columns:minmax(300px,.8fr) minmax(0,1.2fr);gap:16px}.replace-list{display:grid;gap:10px}.replace-btn{width:100%;justify-content:space-between;text-align:left}.replace-btn strong,.replace-btn span{display:block}.replace-btn span{color:var(--muted);font-size:13px}.accessory-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;align-items:start}
        @keyframes fade-in{from{opacity:0}to{opacity:1}}@keyframes modal-in{from{opacity:0;transform:translateY(12px) scale(.98)}to{opacity:1;transform:translateY(0) scale(1)}}
        @media (max-width:1600px){.catalog-grid{grid-template-columns:repeat(4,minmax(0,1fr))}}@media (max-width:1260px){.hero-main,.setup-shell,.workspace-top,.catalog-toolbar,.api-grid,.modal-grid,.build-form-grid{grid-template-columns:1fr}.catalog-grid{grid-template-columns:repeat(3,minmax(0,1fr))}.slot-list{grid-template-columns:repeat(2,minmax(0,1fr))}.hero-stats{grid-template-columns:repeat(2,minmax(0,1fr))}}@media (max-width:820px){.page{width:min(calc(100vw - 12px),1800px);margin:6px auto 20px}.hero,.top-controls,.catalog-panel,.builder-panel,.api-card{padding:16px}.catalog-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.setup-grid,.advanced-grid,.stats-grid,.accessory-grid,.slot-list{grid-template-columns:1fr}.hero-stats{grid-template-columns:repeat(2,minmax(0,1fr))}.hero-head,.catalog-head,.builder-head,.api-head,.hero-links,.toolbar,.pager,.modal-head,.modal-actions{flex-direction:column;align-items:stretch}.btn,.summary,.replace-btn{width:100%}}@media (max-width:560px){.catalog-grid,.chips,.slot-actions{display:grid;grid-template-columns:1fr}.chip,.btn,.icon-btn{width:100%}}@media (max-width:380px){.hero-stats{grid-template-columns:1fr}}
    </style>
</head>
<body>
    <div class="page">
        <header class="hero panel">
            <div class="hero-head">
                <div class="hero-links">
                    <a class="btn primary" href="/api/v1">Root API</a>
                    <a class="btn" href="/api/v1/games">Jeux</a>
                </div>
            </div>
            <div class="hero-main">
                <div class="hero-copy">
                    <h1>Wiki Builder API</h1>
                    <p id="hero-copy">Clique sur une arme pour la charger. Si un slot visible est libre, elle s'y place toute seule. Si tout est plein, tu choisis simplement quoi remplacer.</p>
                </div>
                <div id="hero-stats" class="hero-stats"></div>
            </div>
        </header>

        <section class="top-controls panel">
            <div class="section-head">
                <span class="pill">Configuration</span>
                <h2>Commencer</h2>
                <p class="muted">Choisis d'abord un jeu, un archétype et un build. L'objectif ici est de comprendre vite et naviguer simplement.</p>
            </div>

            <div class="setup-shell">
                <div class="setup-main">
                    <div class="setup-card">
                        <div class="setup-grid">
                            <div class="field">
                                <label for="game-select">Jeu</label>
                                <select id="game-select"></select>
                            </div>
                            <div class="field">
                                <label for="profile">Archétype</label>
                                <select id="profile"></select>
                            </div>
                            <div class="field full">
                                <label for="build-select">Build public</label>
                                <div class="field-stack">
                                    <select id="build-select"></select>
                                    <p id="build-note" class="helper-inline"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <aside class="setup-side">
                    <div class="status-card">
                        <span class="mini">Compte</span>
                        @if ($authUser)
                            <strong>Session web active</strong>
                            <p class="muted">Tu es connecte en tant que {{ $authUser->display_name ?: $authUser->username }}.</p>
                            <div class="hero-links">
                                <a class="btn primary" href="{{ route('dashboard') }}">Dashboard</a>
                                <a class="btn" href="{{ route('account') }}">Mon compte</a>
                            </div>
                        @else
                            <strong>Aucun compte connecte</strong>
                            <p class="muted">La connexion web sert pour ton espace personnel, tes brouillons et tes demandes RGPD.</p>
                            <div class="hero-links">
                                <a class="btn" href="{{ route('login') }}">Connexion</a>
                                <a class="btn primary" href="{{ route('register') }}">Creer un compte</a>
                            </div>
                        @endif
                    </div>
                </aside>
            </div>
        </section>

        <main class="workspace">
            <section class="workspace-top">
                <section class="builder-panel panel">
                    <div class="builder-head">
                        <div>
                            <span class="pill">Builder</span>
                            <h2 id="builder-title" style="margin-top:10px;font-size:clamp(30px,4vw,42px);line-height:.95">1 slot actif</h2>
                            <p id="builder-copy">Un slot visible au départ. Clique sur + si tu veux ouvrir une comparaison.</p>
                        </div>
                        <div class="toolbar">
                            <button id="reset-builder" type="button" class="btn ghost">Réinitialiser</button>
                        </div>
                    </div>

                    <div id="slot-list" class="slot-list"></div>
                </section>

                <section id="matrix-panel" class="builder-panel panel matrix-panel">
                    <div class="builder-head">
                        <div>
                            <span class="pill">Comparaison</span>
                            <h3 style="margin-top:10px;font-size:26px">Matrice</h3>
                        </div>
                    </div>
                    <div id="matrix"></div>
                </section>
            </section>

            <section class="catalog-panel panel">
                <div class="catalog-head">
                    <div>
                        <span class="pill">Catalogue</span>
                        <h2 style="margin-top:10px;font-size:clamp(30px,4vw,42px);line-height:.95">Choisir une arme</h2>
                    </div>
                    <div id="catalog-status" class="summary"></div>
                </div>

                <div class="catalog-toolbar">
                    <input id="search" type="search" placeholder="Rechercher une arme, une catégorie, un style">
                    <select id="slot-filter"></select>
                    <select id="tier-filter"></select>
                    <select id="sort-filter"></select>
                </div>

                <div class="build-form-grid">
                    <div class="field">
                        <label for="score-filter">Score minimum</label>
                        <input id="score-filter" type="number" min="0" max="100" placeholder="70">
                    </div>
                    <div class="field">
                        <label for="modifier-filter">Type de mod</label>
                        <select id="modifier-filter"></select>
                    </div>
                    <div class="field">
                        <label>État</label>
                        <div id="filter-note" class="summary">Catalogue complet</div>
                    </div>
                </div>

                <div class="field">
                        <label>Catégories</label>
                    <div id="category-list" class="chips"></div>
                </div>

                <div id="catalog" class="catalog-grid"></div>

                <div class="pager">
                    <button id="page-prev" type="button" class="btn ghost" data-action="page-prev">Page précédente</button>
                    <span id="page-status" class="summary"></span>
                    <button id="page-next" type="button" class="btn ghost" data-action="page-next">Page suivante</button>
                </div>
            </section>
        </main>

        <section class="api-card panel">
            <details>
                <summary class="api-head">
                    <div>
                        <span class="pill">API rapide</span>
                        <h2 style="margin-top:10px;font-size:clamp(28px,4vw,36px);line-height:1">/api/v1 et exemples utiles</h2>
                    </div>
                    <span class="summary">Ouvrir</span>
                </summary>
                <div class="api-grid">
                    <div id="api-endpoints" class="endpoint-list"></div>
                    <div class="endpoint-list">
                        <div class="endpoint">
                            <strong>Lecture publique</strong>
                            <pre id="api-public-sample"><code>Chargement de la documentation live...</code></pre>
                        </div>
                        <div class="endpoint">
                            <strong>Écriture protégée</strong>
                            <pre id="api-write-sample"><code>Chargement de la documentation live...</code></pre>
                        </div>
                    </div>
                </div>
            </details>
        </section>
    </div>

    <div id="modal-root"></div>

    <script>
        const S={root:null,games:[],items:[],profiles:[],builds:[],modifiers:[],game:null,profile:null,selectedBuild:null,search:'',slot:'all',tier:'all',sort:'score-desc',scoreMin:'',modifierType:'all',category:'all',page:1,perPage:12,visibleSlots:1,compare:[{item:null,mods:{}},{item:null,mods:{}},{item:null,mods:{}}],cache:{},editing:null,pending:null},K=['score','attack','defense','utility','accuracy','mobility'],L={score:'Score',attack:'Attaque',defense:'Défense',utility:'Utilité',accuracy:'Précision',mobility:'Mobilité'},SORT_OPTIONS=[{value:'score-desc',label:'Score décroissant'},{value:'score-asc',label:'Score croissant'},{value:'name-asc',label:'Nom A-Z'},{value:'name-desc',label:'Nom Z-A'}],$=id=>document.getElementById(id),N={heroCopy:$('hero-copy'),heroStats:$('hero-stats'),game:$('game-select'),profile:$('profile'),buildSelect:$('build-select'),buildNote:$('build-note'),search:$('search'),slot:$('slot-filter'),tier:$('tier-filter'),sort:$('sort-filter'),score:$('score-filter'),modifier:$('modifier-filter'),filterNote:$('filter-note'),category:$('category-list'),catalogStatus:$('catalog-status'),catalog:$('catalog'),pageStatus:$('page-status'),pagePrev:$('page-prev'),pageNext:$('page-next'),builderTitle:$('builder-title'),builderCopy:$('builder-copy'),slots:$('slot-list'),matrixPanel:$('matrix-panel'),matrix:$('matrix'),apiEndpoints:$('api-endpoints'),apiPublicSample:$('api-public-sample'),apiWriteSample:$('api-write-sample'),modal:$('modal-root'),reset:$('reset-builder')},E=v=>String(v??'').replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;').replaceAll('"','&quot;').replaceAll("'","&#39;"),M=v=>Number(v??0),GAME=()=>S.games.find(game=>game.id===S.game)||null,PROFILE=()=>S.profiles.find(profile=>profile.id===S.profile)||null,ITEM=(item,mods={})=>({item,mods});
        const itemLabel=item=>item?.metadata?.bo3?.weapon_class??item?.metadata?.bo7?.category??item?.slot??'weapon';
        const itemImageUrl=item=>item?.metadata?.source?.image_url??null;
        function renderItemMedia(item,className='item-media'){const imageUrl=itemImageUrl(item);if(!imageUrl)return `<div class="${className}"><div class="item-media-fallback">${E(item?.name??'Arme')}</div></div>`;return `<div class="${className}"><img src="${E(imageUrl)}" alt="${E(item?.name??'Arme')}" loading="lazy" decoding="async" referrerpolicy="no-referrer" onerror="this.hidden=true;this.nextElementSibling.hidden=false"><div class="item-media-fallback" hidden>${E(item?.name??'Arme')}</div></div>`}
        async function api(path,options={}){const headers={Accept:'application/json',...(options.headers??{})};if(options.json)headers['Content-Type']='application/json';const response=await fetch(path,{method:options.method??'GET',headers,body:options.body?JSON.stringify(options.body):undefined}),payload=await response.json();if(!response.ok||payload.success===false)throw new Error(payload.message??`Request failed for ${path}`);return payload}
        const visibleEntries=()=>S.compare.slice(0,S.visibleSlots),loadedEntries=()=>visibleEntries().filter(entry=>entry.item),pageCount=rows=>Math.max(1,Math.ceil(rows.length/S.perPage));
        function cats(){const map=new Map();S.items.forEach(item=>(item.categories??[]).forEach(category=>{const row=map.get(category.id)??{...category,count:0};row.count+=1;map.set(category.id,row)}));return [...map.values()].sort((a,b)=>b.count-a.count||a.label.localeCompare(b.label))}
        function filteredItems(){const q=S.search.trim().toLowerCase(),minimumScore=S.scoreMin===''?null:Number(S.scoreMin);return S.items.filter(item=>{const bag=[item.name,item.slot,itemLabel(item),...(item.categories??[]).map(category=>category.label)].filter(Boolean).join(' ').toLowerCase(),matchQuery=q===''||bag.includes(q),matchSlot=S.slot==='all'||item.slot===S.slot,matchTier=S.tier==='all'||item.tier_level===S.tier,matchCategory=S.category==='all'||(item.categories??[]).some(category=>category.slug===S.category||String(category.id)===S.category),matchScore=minimumScore===null||M(item.score)>=minimumScore,matchModifier=S.modifierType==='all'||(item.item_slots??[]).some(slot=>slot.accepted_modifier_type===S.modifierType);return matchQuery&&matchSlot&&matchTier&&matchCategory&&matchScore&&matchModifier}).sort((left,right)=>{if(S.sort==='score-asc')return M(left.score)-M(right.score)||left.name.localeCompare(right.name);if(S.sort==='name-asc')return left.name.localeCompare(right.name);if(S.sort==='name-desc')return right.name.localeCompare(left.name);return M(right.score)-M(left.score)||left.name.localeCompare(right.name)})}
        const findMod=(item,id)=>(item.compatible_modifiers??[]).find(modifier=>modifier.id===id)||null,options=(entry,slot)=>(entry.item?.compatible_modifiers??[]).filter(modifier=>!slot.accepted_modifier_type||modifier.modifier_type===slot.accepted_modifier_type).sort((a,b)=>a.name.localeCompare(b.name));
        function totals(entry){if(!entry.item)return null;const total={score:M(entry.item.score),attack:M(entry.item.attack),defense:M(entry.item.defense),utility:M(entry.item.utility),accuracy:M(entry.item.accuracy),mobility:M(entry.item.mobility)};Object.values(entry.mods).forEach(modifierId=>{const modifier=findMod(entry.item,Number(modifierId));if(!modifier)return;total.score+=M(modifier.delta_score);total.attack+=M(modifier.delta_attack);total.defense+=M(modifier.delta_defense);total.utility+=M(modifier.delta_utility);total.accuracy+=M(modifier.delta_accuracy);total.mobility+=M(modifier.delta_mobility)});return total}
        function setPage(value){S.page=Math.min(Math.max(1,value),pageCount(filteredItems()))}
        function resetBuilder(){S.visibleSlots=1;S.compare=[{item:null,mods:{}},{item:null,mods:{}},{item:null,mods:{}}];S.editing=null;S.pending=null;S.selectedBuild=null}
        async function fetchItem(id){if(S.cache[id])return S.cache[id];const payload=await api(`/api/v1/items/${id}`);S.cache[id]=payload.data;return payload.data}
        async function placeItem(id,index){S.compare[index]=ITEM(await fetchItem(id));S.editing=null;S.pending=null}
        async function handlePick(id){const freeIndex=visibleEntries().findIndex(entry=>!entry.item);if(freeIndex!==-1){await placeItem(id,freeIndex);render();return}S.pending=Number(id);render()}
        async function replacePending(index){if(S.pending===null)return;await placeItem(S.pending,index);render()}
        async function loadBuild(id){const payload=await api(`/api/v1/builds/${id}`),grouped=(payload.data.slots??[]).reduce((carry,row)=>{const bucket=carry[row.item_id]??{};bucket[row.slot_key]=row.modifier.id;carry[row.item_id]=bucket;return carry},{}),next=[{item:null,mods:{}},{item:null,mods:{}},{item:null,mods:{}}],buildItems=payload.data.items??[];for(let index=0;index<Math.min(3,buildItems.length);index+=1){const buildItem=buildItems[index],item=await fetchItem(buildItem.item_id);next[index]=ITEM(item,{...(grouped[buildItem.item_id]??{})})}S.compare=next;S.visibleSlots=Math.max(1,Math.min(3,buildItems.length||1));S.editing=null;S.pending=null;S.selectedBuild=Number(id)||null;render()}
        async function loadGame(){const game=GAME();if(!game)return;const [itemsPayload,profilesPayload,buildsPayload,modifiersPayload]=await Promise.all([api(`/api/v1/games/${game.id}/items?per_page=100&sort_by=score&sort_dir=desc`),api(`/api/v1/games/${game.id}/archetypes?per_page=100`),api(`/api/v1/builds?game_id=${game.id}&is_public=true&per_page=100`),api(`/api/v1/games/${game.id}/modifiers?per_page=100&sort_by=name&sort_dir=asc`)]);S.items=itemsPayload.data.data??[];S.profiles=profilesPayload.data.data??[];S.builds=buildsPayload.data.data??[];S.modifiers=modifiersPayload.data.data??[];S.profile=S.profiles[0]?.id??null;S.search='';S.slot=S.items.some(item=>item.slot==='weapon')?'weapon':'all';S.tier='all';S.sort='score-desc';S.scoreMin='';S.modifierType='all';S.category='all';S.page=1;S.cache={};resetBuilder();if(S.builds[0]?.id)await loadBuild(S.builds[0].id)}
        function renderHero(){const game=GAME(),loaded=loadedEntries().length;N.heroCopy.textContent=game?`${game.name} : charge des armes, configure les accessoires et compare les stats directement dans le builder.`:'Choisis un jeu puis charge des armes depuis le catalogue.';N.heroStats.innerHTML=[['Jeux',S.games.length],['Items',S.items.length],['Builds publics',S.builds.length],['Armes chargées',loaded]].map(([label,value])=>`<article class="hero-stat"><strong>${E(value)}</strong><span>${E(label)}</span></article>`).join('')}
        function renderControls(){
            N.game.innerHTML=S.games.length
                ?S.games.map(game=>`<option value="${game.id}" ${game.id===S.game?'selected':''}>${E(game.name)}</option>`).join('')
                :'<option value="">Aucun jeu</option>';
            N.profile.innerHTML=S.profiles.length
                ?S.profiles.map(profile=>`<option value="${profile.id}" ${profile.id===S.profile?'selected':''}>${E(profile.name)}</option>`).join('')
                :'<option value="">Aucun archétype</option>';
            N.buildSelect.disabled=!S.builds.length;
            N.buildSelect.innerHTML=S.builds.length
                ?[`<option value="">Choisir un build public</option>`,...S.builds.map(build=>`<option value="${build.id}" ${build.id===S.selectedBuild?'selected':''}>${E(build.title)}</option>`)].join('')
                :'<option value="">Aucun build public</option>';
            N.buildNote.textContent=S.builds.length?`${S.builds.length} build(s) publics pour ce jeu.`:'Aucun build public pour ce jeu.';
            N.slot.innerHTML=['all',...new Set(S.items.map(item=>item.slot).filter(Boolean))].map(slot=>`<option value="${E(slot)}" ${slot===S.slot?'selected':''}>${E(slot==='all'?'Tous les slots':slot)}</option>`).join('');
            N.tier.innerHTML=['all',...new Set(S.items.map(item=>item.tier_level).filter(Boolean))].map(tier=>`<option value="${E(tier)}" ${tier===S.tier?'selected':''}>${E(tier==='all'?'Tous les tiers':tier)}</option>`).join('');
            N.sort.innerHTML=SORT_OPTIONS.map(option=>`<option value="${option.value}" ${option.value===S.sort?'selected':''}>${E(option.label)}</option>`).join('');
            N.modifier.innerHTML=['all',...new Set(S.modifiers.map(modifier=>modifier.modifier_type).filter(Boolean))].map(type=>`<option value="${E(type)}" ${type===S.modifierType?'selected':''}>${E(type==='all'?'Tous les types':type)}</option>`).join('');
            N.score.value=S.scoreMin;
            N.category.innerHTML=[`<button type="button" class="chip" data-action="category" data-id="all" aria-pressed="${S.category==='all'}">Toutes</button>`,...cats().map(category=>{const id=category.slug??String(category.id);return `<button type="button" class="chip" data-action="category" data-id="${E(id)}" aria-pressed="${S.category===id}">${E(category.label)} - ${E(category.count)}</button>`})].join('');
        }
        function renderBuilder(){const loaded=loadedEntries().length;N.builderTitle.textContent=`${S.visibleSlots} slot${S.visibleSlots>1?'s':''} actif${S.visibleSlots>1?'s':''}`;N.builderCopy.textContent=loaded>1?'La matrice apparaît car au moins deux slots sont chargés. Clique sur une arme du builder pour ouvrir les accessoires.':loaded===1?'Clique sur l’arme chargée pour ouvrir les accessoires dans une popup.':"Un slot visible au départ. Clique sur + si tu veux ouvrir une comparaison.";N.slots.innerHTML=visibleEntries().map((entry,index)=>{if(!entry.item){const canRemove=S.visibleSlots>1&&index===S.visibleSlots-1;return `<article class="slot-card empty"><div class="slot-top"><span class="slot-number">${index+1}</span>${canRemove?`<div class="slot-actions"><button type="button" class="icon-btn" data-action="remove-slot">Retirer le slot</button></div>`:''}</div><h3>Sélection vide</h3><p class="muted">Choisis une arme dans la liste à gauche. Le premier slot vide visible sera utilisé automatiquement.</p></article>`}const total=totals(entry),mods=Object.keys(entry.mods).length;return `<article class="slot-card" data-action="edit-slot" data-index="${index}"><div class="slot-top"><span class="slot-number">${index+1}</span><div class="slot-actions"><button type="button" class="icon-btn" data-action="edit-slot" data-index="${index}">Configurer</button><button type="button" class="icon-btn" data-action="clear-slot" data-index="${index}">Retirer</button></div></div>${renderItemMedia(entry.item,'slot-media')}<div class="slot-main"><h3>${E(entry.item.name)}</h3><p class="muted" style="margin-top:6px">${E(itemLabel(entry.item))}</p></div><div class="slot-meta"><span class="tag">${E(entry.item.slot)}</span><span class="tag">${mods} accessoire(s)</span>${(entry.item.categories??[]).slice(0,2).map(category=>`<span class="tag">${E(category.label)}</span>`).join('')}</div><div class="stats-grid"><div class="stat-box"><strong>${E(total.score)}</strong><span>Score</span></div><div class="stat-box"><strong>${E(total.attack)}</strong><span>Attaque</span></div><div class="stat-box"><strong>${E(total.mobility)}</strong><span>Mobilité</span></div></div></article>`}).join('')+(S.visibleSlots<3?`<article class="slot-card add"><span class="slot-number">+</span><h3>Ajouter un slot</h3><p class="muted">Ouvre un slot de plus uniquement quand tu veux comparer.</p><button type="button" class="btn neon" data-action="add-slot">Ajouter</button></article>`:'')}
        function renderMatrix(){const rows=loadedEntries().map(totals).filter(Boolean);N.matrixPanel.classList.add('show');if(rows.length<2){N.matrix.innerHTML=`<article class="slot-card empty"><h3>Comparaison en attente</h3><p class="muted">Ajoute au moins deux armes dans le builder pour afficher une vraie matrice côte à côte.</p></article>`;return}const leaders=Object.fromEntries(K.map(key=>[key,Math.max(...rows.map(row=>row[key]))]));N.matrix.innerHTML=`<div class="table"><table><thead><tr><th>Stat</th>${visibleEntries().map((entry,index)=>`<th>${E(entry.item?.name??`Slot ${index+1}`)}</th>`).join('')}</tr></thead><tbody>${K.map(key=>`<tr><th>${E(L[key])}</th>${visibleEntries().map(entry=>{const row=totals(entry);if(!row)return'<td>-</td>';return `<td class="${row[key]===leaders[key]?'leader':''}">${E(row[key])}</td>`}).join('')}</tr>`).join('')}</tbody></table></div>`}
        function renderCatalog(){const rows=filteredItems(),pages=pageCount(rows),start=(S.page-1)*S.perPage,end=start+S.perPage,chunk=rows.slice(start,end);N.catalogStatus.textContent=`${rows.length} arme(s) - filtres actifs`;N.filterNote.textContent=`${S.modifierType==='all'?'Tous les mods':S.modifierType} | ${S.tier==='all'?'Tous les tiers':S.tier}`;N.pageStatus.textContent=`Page ${S.page} / ${pages}`;N.pagePrev.disabled=S.page<=1;N.pageNext.disabled=S.page>=pages;N.catalog.innerHTML=chunk.length?chunk.map(item=>`<button type="button" class="item-card" data-action="load" data-id="${item.id}">${renderItemMedia(item)}<div class="item-top"><div class="item-title"><strong>${E(item.name)}</strong><span>${E(itemLabel(item))}</span></div><span class="item-hint">Ajouter</span></div><div class="tags"><span class="tag">${E(item.slot)}</span><span class="tag">${E(item.tier_level??'n/a')}</span>${(item.categories??[]).slice(0,2).map(category=>`<span class="tag">${E(category.label)}</span>`).join('')}</div><div class="bars"><div class="bar"><span>Score</span><div class="track"><div class="fill" style="width:${Math.max(0,Math.min(100,M(item.score)))}%"></div></div><strong>${E(item.score??0)}</strong></div><div class="bar"><span>Attaque</span><div class="track"><div class="fill" style="width:${Math.max(0,Math.min(100,M(item.attack)))}%"></div></div><strong>${E(item.attack??0)}</strong></div><div class="bar"><span>Mobilité</span><div class="track"><div class="fill" style="width:${Math.max(0,Math.min(100,M(item.mobility)))}%"></div></div><strong>${E(item.mobility??0)}</strong></div></div></button>`).join(''):`<article class="slot-card empty"><h3>Aucun résultat</h3><p class="muted">Ajuste la recherche ou les filtres pour élargir la liste.</p></article>`}
        function renderApiDocs(){if(!S.root){N.apiEndpoints.innerHTML='<div class="endpoint"><p class="muted">Documentation en cours de chargement...</p></div>';return}N.apiEndpoints.innerHTML=(S.root.public_routes??[]).map(route=>`<div class="endpoint"><span class="method">${E(route.method)}</span><strong>${E(route.path)}</strong><p class="muted">${E(route.usage)}</p></div>`).join('');N.apiPublicSample.innerHTML=`<code>curl http://localhost:8000/api/v1\ncurl "http://localhost:8000${E(S.root.examples?.[1]?.path??'/api/v1/games')}"</code>`;N.apiWriteSample.innerHTML=`<code>curl -X POST http://localhost:8000/api/v1/builds ^\n  -H "Content-Type: application/json" ^\n  -H "API-KEY: votre-cle-api" ^\n  -d "{\\"game_id\\":${E(S.game??1)},\\"user_id\\":1,\\"title\\":\\"Build front\\",\\"items\\":[{\\"item_id\\":1,\\"position\\":1}]}"</code>`}
        function renderModals(){const entry=S.editing!==null?S.compare[S.editing]:null,pending=S.pending!==null?S.items.find(item=>item.id===S.pending)||null:null;if(!entry?.item&&!pending){N.modal.innerHTML='';return}if(pending){N.modal.innerHTML=`<div class="modal"><div class="modal-card"><div class="modal-head"><div><span class="pill">Remplacer une arme</span><h2 style="margin-top:10px;font-size:34px;line-height:.95">${E(pending.name)}</h2><p class="muted" style="margin-top:8px">Tous les slots visibles sont occupés. Choisis simplement lequel remplacer.</p></div><div class="modal-actions"><button type="button" class="btn ghost" data-action="close-modal">Annuler</button></div></div><div class="replace-list" style="margin-top:16px">${visibleEntries().map((slot,index)=>`<button type="button" class="replace-btn" data-action="replace-slot" data-index="${index}"><span><strong>Slot ${index+1}</strong><span>${E(slot.item?.name??'Vide')}</span></span><span>Remplacer</span></button>`).join('')}</div></div></div>`;return}const total=totals(entry),itemSlots=entry.item.item_slots??[];N.modal.innerHTML=`<div class="modal"><div class="modal-card"><div class="modal-head"><div><span class="pill">Configuration</span><h2 style="margin-top:10px;font-size:34px;line-height:.95">${E(entry.item.name)}</h2><p class="muted" style="margin-top:8px">${E(itemLabel(entry.item))}</p></div><div class="modal-actions"><button type="button" class="btn ghost" data-action="close-modal">Fermer</button></div></div><div class="modal-grid" style="margin-top:16px"><div class="slot-card">${renderItemMedia(entry.item,'slot-media')}<div class="stats-grid"><div class="stat-box"><strong>${E(total.score)}</strong><span>Score</span></div><div class="stat-box"><strong>${E(total.attack)}</strong><span>Attaque</span></div><div class="stat-box"><strong>${E(total.accuracy)}</strong><span>Précision</span></div><div class="stat-box"><strong>${E(total.mobility)}</strong><span>Mobilité</span></div><div class="stat-box"><strong>${E(total.utility)}</strong><span>Utilité</span></div><div class="stat-box"><strong>${E(itemSlots.length)}</strong><span>Slots</span></div></div><div class="slot-meta" style="margin-top:12px"><span class="tag">${E(entry.item.slot)}</span>${(entry.item.categories??[]).slice(0,3).map(category=>`<span class="tag">${E(category.label)}</span>`).join('')}</div></div><div class="accessory-grid">${itemSlots.length?itemSlots.map(slot=>{const selected=String(entry.mods[slot.slot_key]??'');return `<div class="field"><label>${E(slot.slot_label)}</label><select data-action="mod" data-index="${S.editing}" data-slot-key="${E(slot.slot_key)}"><option value="">Aucun accessoire</option>${options(entry,slot).map(modifier=>`<option value="${modifier.id}" ${selected===String(modifier.id)?'selected':''}>${E(modifier.name)}</option>`).join('')}</select></div>`}).join(''):`<div class="slot-card empty"><h3>Aucun slot exploitable</h3><p class="muted">Cet item ne déclare pas encore de slots internes utilisables dans le builder.</p></div>`}</div></div></div></div>`}
        function render(){renderHero();renderControls();renderBuilder();renderMatrix();renderCatalog();renderApiDocs();renderModals()}
        document.addEventListener('click',async event=>{const target=event.target.closest('[data-action]');if(!target)return;try{if(target.dataset.action==='game'){if(Number(target.dataset.id)!==S.game){S.game=Number(target.dataset.id);await loadGame()}render();return}if(target.dataset.action==='category'){S.category=target.dataset.id??'all';S.page=1;render();return}if(target.dataset.action==='load'){await handlePick(Number(target.dataset.id));return}if(target.dataset.action==='add-slot'){S.visibleSlots=Math.min(3,S.visibleSlots+1);render();return}if(target.dataset.action==='remove-slot'){if(S.visibleSlots>1){S.compare[S.visibleSlots-1]={item:null,mods:{}};S.visibleSlots-=1;render()}return}if(target.dataset.action==='clear-slot'){const index=Number(target.dataset.index);S.compare[index]={item:null,mods:{}};if(S.editing===index)S.editing=null;render();return}if(target.dataset.action==='edit-slot'){S.editing=Number(target.dataset.index);render();return}if(target.dataset.action==='replace-slot'){await replacePending(Number(target.dataset.index));return}if(target.dataset.action==='close-modal'){S.editing=null;S.pending=null;render();return}if(target.dataset.action==='page-prev'){setPage(S.page-1);render();return}if(target.dataset.action==='page-next'){setPage(S.page+1);render()}}catch(error){N.builderCopy.textContent=error.message}});
        document.addEventListener('change',async event=>{const target=event.target;try{if(target===N.game){const nextGame=Number(target.value)||null;if(nextGame!==S.game){S.game=nextGame;await loadGame()}render();return}if(target===N.profile){S.profile=Number(target.value)||null;render();return}if(target===N.slot){S.slot=target.value;S.page=1;render();return}if(target===N.tier){S.tier=target.value;S.page=1;render();return}if(target===N.sort){S.sort=target.value;render();return}if(target===N.score){S.scoreMin=target.value;S.page=1;render();return}if(target===N.modifier){S.modifierType=target.value;S.page=1;render();return}if(target.matches('[data-action="mod"]')){const index=Number(target.dataset.index),key=target.dataset.slotKey;if(!key||!S.compare[index])return;if(target.value==='')delete S.compare[index].mods[key];else S.compare[index].mods[key]=Number(target.value);render()}}catch(error){N.builderCopy.textContent=error.message}});
        document.addEventListener('keydown',event=>{if(event.key==='Escape'&&(S.editing!==null||S.pending!==null)){S.editing=null;S.pending=null;render()}});
        N.search.addEventListener('input',event=>{S.search=event.target.value;S.page=1;render()});N.reset.addEventListener('click',()=>{resetBuilder();render()});N.buildSelect.addEventListener('change',async event=>{const id=Number(event.target.value)||null;if(!id){S.selectedBuild=null;render();return}try{await loadBuild(id)}catch(error){N.builderCopy.textContent=error.message}});(async function boot(){try{const [rootPayload,payload]=await Promise.all([api('/api/v1'),api('/api/v1/games?per_page=20')]);S.root=rootPayload.data;S.games=payload.data.data??[];S.game=S.games.find(game=>game.slug==='call-of-duty-black-ops-7')?.id??S.games.find(game=>game.slug==='call-of-duty-black-ops-iii')?.id??S.games[0]?.id??null;await loadGame();render()}catch(error){N.heroCopy.textContent='Chargement impossible';N.catalog.innerHTML=`<article class="slot-card empty"><h3>Interface indisponible</h3><p class="muted">${E(error.message)}</p></article>`;N.slots.innerHTML=`<article class="slot-card empty"><h3>API inaccessible</h3><p class="muted">Vérifie que l'application tourne et que la base a bien été migrée puis alimentée.</p></article>`}})();
    </script>
</body>
</html>
