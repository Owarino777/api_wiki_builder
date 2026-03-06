<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Wiki Builder API') }}</title>
    <style>
        :root{--bg:#ede7df;--paper:rgba(255,250,244,.9);--ink:#181b20;--muted:#626975;--line:rgba(24,27,32,.12);--accent:#d86a35;--accent-soft:rgba(216,106,53,.14);--teal:#3f8b8f;--dark:#15191f;--dark-2:#1d232c;--ivory:#f8f3eb;--shadow:0 28px 56px rgba(14,18,24,.12);--ease:220ms cubic-bezier(.2,.8,.2,1)}
        *{box-sizing:border-box}body{margin:0;min-height:100vh;font-family:"Aptos","Segoe UI",sans-serif;color:var(--ink);background:radial-gradient(circle at top left,rgba(216,106,53,.16),transparent 22%),radial-gradient(circle at top right,rgba(63,139,143,.12),transparent 18%),linear-gradient(180deg,#f4f0ea,#ddd8cf)}button,input,select,a{font:inherit;transition:transform var(--ease),border-color var(--ease),background-color var(--ease),box-shadow var(--ease)}button:focus-visible,input:focus-visible,select:focus-visible,a:focus-visible{outline:3px solid rgba(216,106,53,.32);outline-offset:3px}
        .page{width:min(1480px,calc(100vw - 24px));margin:12px auto 24px;display:grid;gap:18px}.hero,.panel{border-radius:32px;box-shadow:var(--shadow);overflow:hidden}.hero{position:relative;padding:28px;color:var(--ivory);background:linear-gradient(135deg,rgba(11,15,20,.98),rgba(36,28,24,.95));border:1px solid rgba(255,255,255,.08)}.hero:before{content:"";position:absolute;inset:0;background:linear-gradient(90deg,rgba(255,255,255,.04) 1px,transparent 1px),linear-gradient(rgba(255,255,255,.04) 1px,transparent 1px);background-size:28px 28px;mask-image:linear-gradient(180deg,rgba(0,0,0,.9),transparent 96%)}
        .heroGrid,.layout,.compareGrid,.stack,.sidebar,.filters,.compareCard,.stats,.slotList,.catalogCard,.presetCard,.infoCard{position:relative;z-index:1;display:grid;gap:14px}.heroGrid{grid-template-columns:minmax(0,1.15fr) minmax(320px,.85fr);gap:18px}.layout{grid-template-columns:minmax(280px,340px) minmax(0,1fr);gap:18px}.compareGrid{grid-template-columns:repeat(3,minmax(0,1fr));gap:18px}
        .eyebrows,.metrics,.switcher,.targets,.chips,.actions{display:flex;flex-wrap:wrap;gap:10px}.eyebrow,.metric,.chip,.target,.chip2,.ghost,.primary,.slotTag,.meta span,.catalogMeta span,.pill{border-radius:999px;border:1px solid transparent}.eyebrow{padding:8px 12px;background:rgba(255,255,255,.08);font-size:11px;letter-spacing:.12em;text-transform:uppercase}.hero h1{margin:14px 0 10px;max-width:11ch;font-family:"Bahnschrift SemiCondensed","Trebuchet MS",sans-serif;font-size:clamp(44px,7vw,82px);line-height:.92;letter-spacing:-.06em}.hero p,.dark p,.dark .mini{color:rgba(248,243,235,.76);line-height:1.55}
        .metric{min-width:138px;padding:16px;background:rgba(255,255,255,.05);border-color:rgba(255,255,255,.08)}.metric strong{display:block;font-family:"Bahnschrift SemiCondensed","Trebuchet MS",sans-serif;font-size:30px;letter-spacing:-.05em}.metric span{font-size:12px;color:rgba(248,243,235,.7);text-transform:uppercase;letter-spacing:.08em}
        .panel{padding:22px;background:var(--paper);border:1px solid rgba(255,255,255,.24)}.dark{background:linear-gradient(160deg,var(--dark),var(--dark-2));color:var(--ivory);border-color:rgba(255,255,255,.06)}.panelHeader{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;margin-bottom:16px}.panelHeader h2,.panelHeader h3,.weaponName,.catalogTitle{margin:0;font-family:"Bahnschrift SemiCondensed","Trebuchet MS",sans-serif;letter-spacing:-.04em}.panelHeader h2{font-size:34px}.panelHeader h3,.weaponName{font-size:24px}.mini{margin:0;color:var(--muted);line-height:1.55}
        .chip,.target,.chip2,.ghost,.primary{padding:10px 14px;border-color:var(--line);cursor:pointer;background:rgba(255,255,255,.8);color:var(--ink)}.primary{background:linear-gradient(135deg,#b84c20,var(--accent));color:#fff7f1;border-color:transparent;text-decoration:none}.chip:hover,.target:hover,.chip2:hover,.ghost:hover,.primary:hover{transform:translateY(-1px);border-color:rgba(216,106,53,.34);box-shadow:0 12px 28px rgba(16,20,28,.08)}.chip[aria-pressed=true],.target[aria-pressed=true],.chip2[aria-pressed=true]{background:var(--accent-soft);border-color:rgba(216,106,53,.42);color:#5f2b18}
        .field{display:grid;gap:8px}.field label{font-size:13px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.08em}.field input,.field select,.slotSelect{width:100%;min-height:46px;padding:11px 13px;border-radius:16px;border:1px solid var(--line);background:#fffdfa;color:var(--ink)}
        .compareCard{padding:18px;border-radius:24px;background:linear-gradient(180deg,rgba(255,255,255,.92),rgba(243,235,226,.94));border:1px solid var(--line)}.compareCard.active{border-color:rgba(216,106,53,.42);box-shadow:inset 0 0 0 1px rgba(216,106,53,.12)}.compareCard.empty{min-height:520px;place-content:center;text-align:center;background:linear-gradient(180deg,rgba(255,255,255,.72),rgba(238,230,220,.92))}.cardTop{display:flex;justify-content:space-between;align-items:flex-start;gap:12px}.slotTag,.meta span,.catalogMeta span,.pill{display:inline-flex;align-items:center;padding:7px 10px;background:rgba(24,27,32,.05);font-size:12px;color:var(--muted)}
        .media{position:relative;overflow:hidden;aspect-ratio:16/10;border-radius:22px;background:radial-gradient(circle at top left,rgba(63,139,143,.28),transparent 30%),linear-gradient(135deg,rgba(12,16,22,.98),rgba(36,28,24,.94))}.media img{position:absolute;inset:0;width:100%;height:100%;object-fit:contain;padding:16px}.fallback{position:absolute;inset:0;display:grid;place-items:center;gap:10px;text-align:center;color:rgba(248,243,235,.88)}.mark{width:84px;height:84px;display:grid;place-items:center;border-radius:24px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);font-family:"Bahnschrift SemiCondensed","Trebuchet MS",sans-serif;font-size:30px}
        .stats{grid-template-columns:repeat(2,minmax(0,1fr))}.summary{grid-template-columns:repeat(3,minmax(0,1fr))}.stat{padding:12px;border:1px solid var(--line);border-radius:18px;background:rgba(255,255,255,.62)}.stat strong{display:block;font-size:24px;letter-spacing:-.05em}.stat span{display:block;margin-top:3px;font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:.08em}.slotItem{padding:12px;border-radius:18px;border:1px solid rgba(24,27,32,.08);background:rgba(255,255,255,.62)}.slotHead{display:flex;justify-content:space-between;align-items:baseline;gap:10px;margin-bottom:8px}.slotHead strong{font-size:15px}.slotHead span{font-size:12px;color:var(--muted)}
        .matrixWrap{overflow:auto;border-radius:24px;border:1px solid var(--line);background:rgba(255,255,255,.76)}table{width:100%;border-collapse:collapse}th,td{padding:14px 16px;text-align:left;border-bottom:1px solid rgba(24,27,32,.08)}thead th{font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;background:rgba(248,243,235,.92)}tbody th{min-width:120px;font-size:14px}.leader{font-weight:700;color:#5f2b18;background:#fff0e4}
        .catalogGrid{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:12px}.catalogCard,.presetCard,.infoCard,.note{padding:14px;border:1px solid var(--line);border-radius:20px;background:var(--paper)}.catalogTop{display:flex;justify-content:space-between;align-items:flex-start;gap:12px}.catalogTitle{font-size:22px}.bars{display:grid;gap:8px}.bar{display:grid;grid-template-columns:74px 1fr 34px;gap:10px;align-items:center;font-size:13px;color:var(--muted)}.track{height:10px;border-radius:999px;overflow:hidden;background:rgba(24,27,32,.08)}.fill{height:100%;border-radius:inherit;background:linear-gradient(90deg,var(--teal),var(--accent))}.activeLabel{color:#31784d;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.08em}
        @media (max-width:1220px){.heroGrid,.layout,.compareGrid{grid-template-columns:1fr}.hero h1{max-width:14ch}}@media (max-width:760px){.page{width:min(100vw - 12px,1480px);margin:6px auto 16px;gap:12px}.hero,.panel{padding:18px;border-radius:24px}.panelHeader,.cardTop{flex-direction:column}.stats,.summary{grid-template-columns:1fr 1fr}.catalogGrid{grid-template-columns:1fr}}
    </style>
</head>
<body>
    <div class="page">
        <header class="hero">
            <div class="heroGrid">
                <section class="stack">
                    <div class="eyebrows"><span class="eyebrow">Wiki Builder API</span><span class="eyebrow">Comparateur 1-3 armes</span></div>
                    <div><h1>Comparer des armes, pas juste les lister.</h1><p id="hero-copy">Charge un jeu, choisis jusqu’à trois armes, configure leurs slots puis lis immédiatement les écarts de stats.</p></div>
                    <div id="hero-metrics" class="metrics" aria-live="polite"></div>
                </section>
                <section class="stack">
                    <article class="panel dark"><div class="panelHeader"><div><h3>Jeu actif</h3><p class="mini">Les données viennent directement des endpoints publics.</p></div></div><div id="game-switcher" class="switcher" role="toolbar" aria-label="Sélection du jeu"></div></article>
                    <article class="panel dark"><div class="panelHeader"><div><h3>Cible active</h3><p class="mini">Le catalogue charge l’arme dans la colonne sélectionnée.</p></div></div><div id="slot-targets" class="targets" role="toolbar" aria-label="Sélection de la colonne active"></div></article>
                </section>
            </div>
        </header>
        <main class="layout">
            <aside class="sidebar stack">
                <section class="panel">
                    <div class="panelHeader"><div><h3>Contexte builder</h3><p class="mini">Un profil, des filtres et un catalogue orienté comparaison.</p></div></div>
                    <div class="filters">
                        <div class="field"><label for="profile">Archétype</label><select id="profile" data-field="profile"></select></div>
                        <div class="field"><label for="search">Recherche d’arme</label><input id="search" type="search" placeholder="Ex: Man-O-War, KN-44, VMP" data-field="search"></div>
                        <div class="field"><label for="slot-filter">Type d’emplacement</label><select id="slot-filter" data-field="slot-filter"></select></div>
                        <div class="field"><label>Catégories</label><div id="category-filter" class="chips" role="toolbar" aria-label="Filtre par catégorie"></div></div>
                    </div>
                </section>
                <section class="panel dark"><div class="panelHeader"><div><h3>Builds publics</h3><p class="mini">Charge un preset pour remplir les colonnes.</p></div></div><div id="preset-list" class="stack"></div></section>
                <section class="panel"><div class="panelHeader"><div><h3>Repères UX</h3><p class="mini">Le flux est construit pour le vrai besoin.</p></div></div><div class="note"><p class="mini">Tu peux charger la même arme sur plusieurs colonnes avec des accessoires différents. Chaque colonne gère ses propres slots, et la matrice met en avant la meilleure valeur par statistique.</p></div></section>
            </aside>
            <section class="stack">
                <section class="panel"><div class="panelHeader"><div><h2 id="workspace-title">Comparateur d’armes</h2><p id="workspace-copy" class="mini">Choisis une à trois armes puis compare leurs stats finales avec leurs accessoires.</p></div><div class="actions"><button id="reset" type="button" class="ghost">Réinitialiser</button><a href="/api/v1/games" target="_blank" rel="noreferrer" class="primary">Ouvrir l’API</a></div></div><div id="compare-board"></div></section>
                <section class="panel"><div class="panelHeader"><div><h3>Matrice de comparaison</h3><p class="mini">Les valeurs incluent l’arme de base et les modifiers sélectionnés dans sa colonne.</p></div></div><div id="matrix"></div></section>
                <section class="panel"><div class="panelHeader"><div><h3>Catalogue d’armes</h3><p class="mini">Le bouton principal charge l’arme dans la colonne active du comparateur.</p></div><div id="catalog-status" class="eyebrow" aria-live="polite"></div></div><div id="catalog" class="catalogGrid"></div></section>
            </section>
        </main>
    </div>
    <script>
        const statKeys = ['score','attack','defense','utility','accuracy','mobility'];
        const labels = {score:'Score',attack:'Attack',defense:'Defense',utility:'Utility',accuracy:'Accuracy',mobility:'Mobility'};
        const makeEntry = () => ({item:null,mods:{}});
        const state = {games:[],items:[],profiles:[],builds:[],selectedGameId:null,selectedProfileId:null,search:'',slotFilter:'all',categoryFilter:'all',activeSlot:0,compare:[makeEntry(),makeEntry(),makeEntry()],cache:{}};
        const $ = (id) => document.getElementById(id);
        const nodes = {heroMetrics:$('hero-metrics'),heroCopy:$('hero-copy'),gameSwitcher:$('game-switcher'),slotTargets:$('slot-targets'),profile:$('profile'),search:$('search'),slotFilter:$('slot-filter'),categoryFilter:$('category-filter'),presetList:$('preset-list'),workspaceTitle:$('workspace-title'),workspaceCopy:$('workspace-copy'),compareBoard:$('compare-board'),matrix:$('matrix'),catalogStatus:$('catalog-status'),catalog:$('catalog'),reset:$('reset')};
        const esc = (value) => String(value ?? '').replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;').replaceAll('"','&quot;').replaceAll("'","&#39;");
        const num = (value) => Number(value ?? 0);
        const api = async (path) => { const response = await fetch(path,{headers:{Accept:'application/json'}}); const payload = await response.json(); if (!response.ok || payload.success === false) { throw new Error(payload.message ?? `Request failed for ${path}`); } return payload; };
        const currentGame = () => state.games.find((game) => game.id === state.selectedGameId) ?? null;
        const currentProfile = () => state.profiles.find((profile) => profile.id === state.selectedProfileId) ?? null;
        const initials = (label) => label.split(/[\s-]+/).filter(Boolean).slice(0,2).map((part) => part[0]?.toUpperCase() ?? '').join('');
        const image = (item) => { const url = item.metadata?.source?.image_url; const slotLabel = item.metadata?.bo3?.weapon_class ?? item.slot; return `<div class="media">${url ? `<img src="${esc(url)}" alt="${esc(item.name)}" loading="lazy" referrerpolicy="no-referrer" onerror="this.remove()">` : ''}<div class="fallback"><div class="mark">${esc(initials(item.name))}</div><div>${esc(slotLabel)}</div></div></div>`; };
        const availableSlots = () => ['all', ...new Set(state.items.map((item) => item.slot))];
        const categories = () => { const map = new Map(); state.items.forEach((item) => (item.categories ?? []).forEach((category) => { const row = map.get(category.id) ?? {...category,count:0}; row.count += 1; map.set(category.id,row); })); return [...map.values()].sort((a,b) => b.count - a.count || a.label.localeCompare(b.label)); };
        const filteredItems = () => { const q = state.search.trim().toLowerCase(); return state.items.filter((item) => { const searchOk = q === '' || item.name.toLowerCase().includes(q) || (item.metadata?.bo3?.weapon_class ?? '').toLowerCase().includes(q); const slotOk = state.slotFilter === 'all' || item.slot === state.slotFilter; const catOk = state.categoryFilter === 'all' || (item.categories ?? []).some((category) => category.slug === state.categoryFilter || String(category.id) === state.categoryFilter); return searchOk && slotOk && catOk; }); };
        const findMod = (item, id) => (item.compatible_modifiers ?? []).find((modifier) => modifier.id === id) ?? null;
        const totals = (entry) => { if (!entry.item) { return null; } const base = {score:num(entry.item.score),attack:num(entry.item.attack),defense:num(entry.item.defense),utility:num(entry.item.utility),accuracy:num(entry.item.accuracy),mobility:num(entry.item.mobility)}; Object.values(entry.mods).forEach((id) => { const mod = findMod(entry.item, Number(id)); if (!mod) { return; } base.score += num(mod.delta_score); base.attack += num(mod.delta_attack); base.defense += num(mod.delta_defense); base.utility += num(mod.delta_utility); base.accuracy += num(mod.delta_accuracy); base.mobility += num(mod.delta_mobility); }); return base; };
        const optionsForSlot = (entry, slot) => (entry.item?.compatible_modifiers ?? []).filter((modifier) => !slot.accepted_modifier_type || modifier.modifier_type === slot.accepted_modifier_type).sort((a,b) => a.name.localeCompare(b.name));
        const metric = (value, label) => `<div class="metric"><strong>${esc(value)}</strong><span>${esc(label)}</span></div>`;
        const stat = (label, value) => `<div class="stat"><strong>${esc(value)}</strong><span>${esc(label)}</span></div>`;

        function renderHero() {
            const game = currentGame();
            const active = state.compare[state.activeSlot]?.item?.name ?? `colonne ${state.activeSlot + 1}`;
            nodes.heroMetrics.innerHTML = [metric(state.games.length,'Jeux'),metric(state.items.length,'Armes catalogue'),metric(state.compare.filter((entry) => entry.item).length,'Armes comparées'),metric(active,'Cible active')].join('');
            nodes.heroCopy.textContent = game ? `${game.name} · charge une, deux ou trois armes, applique leurs accessoires par slot puis compare les écarts de stats sans sortir de la page.` : 'Charge un jeu, puis compose ton comparatif arme par arme.';
            nodes.workspaceTitle.textContent = game ? `${game.name} · Builder comparateur` : 'Comparateur d’armes';
            nodes.workspaceCopy.textContent = currentProfile() ? `Archétype actif: ${currentProfile().name}. La colonne active reçoit la prochaine arme chargée depuis le catalogue.` : 'Choisis un archétype puis configure les colonnes de comparaison.';
        }

        function renderFilters() {
            nodes.gameSwitcher.innerHTML = state.games.map((game) => `<button type="button" class="chip" data-action="game" data-id="${game.id}" aria-pressed="${game.id === state.selectedGameId}">${esc(game.name)}</button>`).join('');
            nodes.slotTargets.innerHTML = state.compare.map((entry, index) => `<button type="button" class="target" data-action="target" data-index="${index}" aria-pressed="${index === state.activeSlot}"><strong>Arme ${index + 1}</strong><span>${esc(entry.item?.name ?? 'Vide')}</span></button>`).join('');
            nodes.profile.innerHTML = state.profiles.length ? state.profiles.map((profile) => `<option value="${profile.id}" ${profile.id === state.selectedProfileId ? 'selected' : ''}>${esc(profile.name)}</option>`).join('') : '<option value="">Aucun archétype</option>';
            nodes.search.value = state.search;
            nodes.slotFilter.innerHTML = availableSlots().map((slot) => `<option value="${esc(slot)}" ${slot === state.slotFilter ? 'selected' : ''}>${esc(slot === 'all' ? 'Tous les emplacements' : slot)}</option>`).join('');
            nodes.categoryFilter.innerHTML = [`<button type="button" class="chip2" data-action="category" data-id="all" aria-pressed="${state.categoryFilter === 'all'}">Toutes</button>`, ...categories().map((category) => `<button type="button" class="chip2" data-action="category" data-id="${esc(category.slug ?? String(category.id))}" aria-pressed="${state.categoryFilter === (category.slug ?? String(category.id))}">${esc(category.label)} · ${esc(category.count)}</button>`)].join('');
            nodes.presetList.innerHTML = state.builds.length ? state.builds.map((build) => `<article class="presetCard"><strong>${esc(build.title)}</strong><p class="mini">${esc(build.description ?? 'Build public')}</p><button type="button" class="ghost" data-action="build" data-id="${build.id}">Charger ce build</button></article>`).join('') : `<article class="presetCard"><strong>Aucun build public</strong><p class="mini">Le jeu actif n’expose pas encore de preset public.</p></article>`;
        }

        function renderCompare() {
            nodes.compareBoard.innerHTML = `<div class="compareGrid">${state.compare.map((entry, index) => {
                if (!entry.item) {
                    return `<article class="compareCard empty ${index === state.activeSlot ? 'active' : ''}"><div class="stack"><span class="slotTag">Arme ${index + 1}</span><strong>Colonne prête</strong><p class="mini">Active cette colonne puis charge une arme depuis le catalogue.</p><button type="button" class="primary" data-action="target" data-index="${index}">Utiliser cette colonne</button></div></article>`;
                }
                const sum = totals(entry);
                const itemSlots = entry.item.item_slots ?? [];
                return `<article class="compareCard ${index === state.activeSlot ? 'active' : ''}"><div class="cardTop"><div><span class="slotTag">Arme ${index + 1}</span>${index === state.activeSlot ? `<div class="activeLabel">Cible active</div>` : ''}</div><div class="actions"><button type="button" class="ghost" data-action="target" data-index="${index}">Cibler</button><button type="button" class="ghost" data-action="clear" data-index="${index}">Retirer</button></div></div>${image(entry.item)}<div><h3 class="weaponName">${esc(entry.item.name)}</h3><p class="mini">${esc(entry.item.metadata?.bo3?.handling_profile ?? entry.item.metadata?.bo3?.weapon_class ?? entry.item.slot)}</p></div><div class="meta"><span>${esc(entry.item.slot)}</span><span>${esc(entry.item.rarity)}</span><span>${esc(entry.item.tier_level ?? 'N/A')}</span></div><div class="summary stats">${stat('Score total',sum.score)}${stat('Mods actifs',Object.keys(entry.mods).length)}${stat('Slots',itemSlots.length)}</div><div class="stats">${stat('Attack',sum.attack)}${stat('Accuracy',sum.accuracy)}${stat('Utility',sum.utility)}${stat('Mobility',sum.mobility)}</div><section class="slotList">${itemSlots.length ? itemSlots.map((slot) => { const selected = String(entry.mods[slot.slot_key] ?? ''); return `<div class="slotItem"><div class="slotHead"><strong>${esc(slot.slot_label)}</strong><span>${esc(slot.accepted_modifier_type ?? 'Tous')}</span></div><select class="slotSelect" data-action="mod" data-index="${index}" data-slot-key="${esc(slot.slot_key)}"><option value="">Aucun accessoire</option>${optionsForSlot(entry, slot).map((modifier) => `<option value="${modifier.id}" ${selected === String(modifier.id) ? 'selected' : ''}>${esc(modifier.name)}</option>`).join('')}</select></div>`; }).join('') : `<div class="note"><p class="mini">Cette arme n’expose pas encore de slots d’attachments exploitables par l’API.</p></div>`}</section></article>`;
            }).join('')}</div>`;
        }

        function renderMatrix() {
            const filled = state.compare.map((entry) => totals(entry)).filter(Boolean);
            if (!filled.length) {
                nodes.matrix.innerHTML = `<div class="note"><strong>Aucune arme sélectionnée</strong><p class="mini">Ajoute une arme dans le catalogue pour afficher une vraie matrice de comparaison.</p></div>`;
                return;
            }
            const leaders = Object.fromEntries(statKeys.map((key) => [key, Math.max(...filled.map((row) => row[key]))]));
            nodes.matrix.innerHTML = `<div class="matrixWrap"><table><thead><tr><th>Stat</th>${state.compare.map((entry, index) => `<th>${esc(entry.item?.name ?? `Arme ${index + 1}`)}</th>`).join('')}</tr></thead><tbody>${statKeys.map((key) => `<tr><th>${esc(labels[key])}</th>${state.compare.map((entry) => { const row = totals(entry); if (!row) { return '<td>—</td>'; } return `<td class="${row[key] === leaders[key] ? 'leader' : ''}">${esc(row[key])}</td>`; }).join('')}</tr>`).join('')}<tr><th>Attachments</th>${state.compare.map((entry) => `<td>${esc(Object.keys(entry.mods).length)}</td>`).join('')}</tr></tbody></table></div>`;
        }

        function renderCatalog() {
            const items = filteredItems();
            const active = state.compare[state.activeSlot]?.item?.name ?? `arme ${state.activeSlot + 1}`;
            nodes.catalogStatus.textContent = `${items.length} résultat(s) · cible: ${active}`;
            if (!items.length) {
                nodes.catalog.innerHTML = `<div class="note"><strong>Aucune arme trouvée</strong><p class="mini">Modifie la recherche ou les filtres pour afficher plus de résultats.</p></div>`;
                return;
            }
            const bar = (label, value) => `<div class="bar"><span>${esc(label)}</span><div class="track"><div class="fill" style="width:${Math.max(0,Math.min(100,num(value)))}%"></div></div><strong>${esc(value)}</strong></div>`;
            nodes.catalog.innerHTML = items.map((item) => `<article class="catalogCard"><div class="catalogTop"><div><h4 class="catalogTitle">${esc(item.name)}</h4><p class="mini">${esc(item.metadata?.bo3?.weapon_class ?? item.slot)}</p></div><span class="pill">${esc(item.tier_level ?? 'N/A')}</span></div>${image(item)}<div class="catalogMeta"><span>${esc(item.slot)}</span><span>${esc(item.rarity)}</span>${(item.categories ?? []).slice(0,3).map((category) => `<span>${esc(category.label)}</span>`).join('')}</div><div class="bars">${bar('Score',item.score)}${bar('Attack',item.attack)}${bar('Accuracy',item.accuracy)}${bar('Mobility',item.mobility)}</div><button type="button" class="primary" data-action="load" data-id="${item.id}">Charger dans arme ${state.activeSlot + 1}</button></article>`).join('');
        }

        function render() { renderHero(); renderFilters(); renderCompare(); renderMatrix(); renderCatalog(); }
        async function fetchItem(id) { if (state.cache[id]) { return state.cache[id]; } const payload = await api(`/api/v1/items/${id}`); state.cache[id] = payload.data; return payload.data; }
        function resetBoard() { state.compare = [makeEntry(),makeEntry(),makeEntry()]; state.activeSlot = 0; }
        async function loadItemIntoSlot(itemId, slotIndex) { const item = await fetchItem(itemId); state.compare[slotIndex] = {item,mods:{}}; state.activeSlot = slotIndex; }
        async function loadBuild(buildId) { const payload = await api(`/api/v1/builds/${buildId}`); const grouped = (payload.data.slots ?? []).reduce((acc,row) => { const bucket = acc[row.item_id] ?? {}; bucket[row.slot_key] = row.modifier.id; acc[row.item_id] = bucket; return acc; }, {}); const next = [makeEntry(),makeEntry(),makeEntry()]; for (let i = 0; i < Math.min(3,(payload.data.items ?? []).length); i += 1) { const buildItem = payload.data.items[i]; const item = await fetchItem(buildItem.item_id); next[i] = {item,mods:{...(grouped[buildItem.item_id] ?? {})}}; } state.compare = next; state.activeSlot = 0; }
        async function loadGame() { const game = currentGame(); if (!game) { return; } const [items,profiles,builds] = await Promise.all([api(`/api/v1/games/${game.id}/items?per_page=100&sort_by=score&sort_dir=desc`),api(`/api/v1/games/${game.id}/archetypes?per_page=100`),api(`/api/v1/builds?game_id=${game.id}&is_public=true&per_page=20`)]); state.items = items.data.data ?? []; state.profiles = profiles.data.data ?? []; state.builds = builds.data.data ?? []; state.selectedProfileId = state.profiles[0]?.id ?? null; state.search = ''; state.categoryFilter = 'all'; state.slotFilter = state.items.some((item) => item.slot === 'weapon') ? 'weapon' : 'all'; state.cache = {}; resetBoard(); }

        document.addEventListener('click', async (event) => {
            const trigger = event.target.closest('[data-action]');
            if (!trigger) { return; }
            try {
                if (trigger.dataset.action === 'game') { const id = Number(trigger.dataset.id); if (id !== state.selectedGameId) { state.selectedGameId = id; await loadGame(); } render(); return; }
                if (trigger.dataset.action === 'target') { state.activeSlot = Number(trigger.dataset.index); render(); return; }
                if (trigger.dataset.action === 'clear') { const index = Number(trigger.dataset.index); state.compare[index] = makeEntry(); if (state.activeSlot === index) { state.activeSlot = Math.max(0,index - 1); } render(); return; }
                if (trigger.dataset.action === 'category') { state.categoryFilter = trigger.dataset.id ?? 'all'; render(); return; }
                if (trigger.dataset.action === 'load') { await loadItemIntoSlot(Number(trigger.dataset.id), state.activeSlot); render(); return; }
                if (trigger.dataset.action === 'build') { await loadBuild(Number(trigger.dataset.id)); render(); }
            } catch (error) { nodes.workspaceCopy.textContent = error.message; }
        });

        document.addEventListener('change', (event) => {
            const target = event.target;
            if (target.matches('[data-field="profile"]')) { state.selectedProfileId = Number(target.value) || null; render(); return; }
            if (target.matches('[data-field="slot-filter"]')) { state.slotFilter = target.value; render(); return; }
            if (target.matches('[data-action="mod"]')) { const index = Number(target.dataset.index); const slotKey = target.dataset.slotKey; if (!slotKey || !state.compare[index]) { return; } if (target.value === '') { delete state.compare[index].mods[slotKey]; } else { state.compare[index].mods[slotKey] = Number(target.value); } render(); }
        });

        document.addEventListener('input', (event) => { const target = event.target; if (target.matches('[data-field="search"]')) { state.search = target.value; render(); } });
        nodes.reset.addEventListener('click', () => { resetBoard(); render(); });

        (async function boot() {
            try {
                const payload = await api('/api/v1/games?per_page=20');
                state.games = payload.data.data ?? [];
                state.selectedGameId = state.games.find((game) => game.slug === 'call-of-duty-black-ops-iii')?.id ?? state.games[0]?.id ?? null;
                await loadGame();
                render();
            } catch (error) {
                nodes.workspaceTitle.textContent = 'Interface indisponible';
                nodes.workspaceCopy.textContent = error.message;
                nodes.compareBoard.innerHTML = `<div class="note"><strong>Chargement échoué</strong><p class="mini">Vérifie que Laravel tourne, que la base est seedée, puis recharge la page.</p></div>`;
            }
        }());
    </script>
</body>
</html>
