<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Wiki Builder API') }}</title>
    <style>
        :root{--bg:#0f151c;--ink:#171b21;--muted:#626b77;--paper:rgba(250,244,234,.9);--paper2:rgba(255,250,245,.78);--night:rgba(14,20,28,.94);--accent:#e26a37;--accentSoft:rgba(226,106,55,.16);--cyan:#6bd1cf;--line:rgba(20,25,32,.1);--lineDark:rgba(255,255,255,.1);--xl:30px;--lg:22px;--md:18px;--sm:14px;--shadow:0 28px 72px rgba(6,10,16,.22);--ease:220ms cubic-bezier(.2,.8,.2,1)}
        *{box-sizing:border-box}body{margin:0;min-height:100vh;font-family:"Aptos","Segoe UI",sans-serif;color:var(--ink);background:radial-gradient(circle at top left,rgba(226,106,55,.26),transparent 20%),radial-gradient(circle at 80% 14%,rgba(107,209,207,.15),transparent 18%),linear-gradient(180deg,#eef1f4,#dbe0e3)}
        button,a{transition:transform var(--ease),border-color var(--ease),background var(--ease),box-shadow var(--ease)}button:focus-visible,a:focus-visible{outline:3px solid rgba(226,106,55,.35);outline-offset:3px}
        .page{width:min(1380px,calc(100vw - 26px));margin:14px auto 24px;display:grid;gap:18px}.hero,.panel{border-radius:var(--xl);box-shadow:var(--shadow);overflow:hidden;border:1px solid rgba(255,255,255,.28)}
        .hero{position:relative;padding:30px;color:#f8f1e8;background:linear-gradient(145deg,rgba(8,11,17,.96),rgba(37,29,24,.92)),linear-gradient(120deg,rgba(226,106,55,.32),transparent 55%)}.hero:before{content:"";position:absolute;inset:0;background:linear-gradient(90deg,rgba(255,255,255,.03) 1px,transparent 1px),linear-gradient(rgba(255,255,255,.03) 1px,transparent 1px);background-size:34px 34px;mask-image:linear-gradient(180deg,rgba(0,0,0,.86),transparent 94%);pointer-events:none}
        .heroGrid,.layout{position:relative;z-index:1;display:grid;gap:18px}.heroGrid{grid-template-columns:1.1fr .9fr;align-items:start}.layout{grid-template-columns:minmax(0,1.45fr) minmax(320px,.85fr)}.stack{display:grid;gap:18px;align-content:start}
        .hero h1{margin:16px 0 14px;max-width:10ch;font-family:"Arial Narrow","Bahnschrift",sans-serif;font-size:clamp(44px,7vw,82px);line-height:.9;letter-spacing:-.06em}.hero p,.muted,.source{color:rgba(248,241,232,.78);line-height:1.6}
        .row,.switcher,.tabs,.metrics,.chips,.mods,.actions{display:flex;flex-wrap:wrap;gap:10px}.badge,.chip,.tab,.pill,.mod,.pick,.button{border-radius:999px;border:1px solid transparent;padding:9px 14px}.badge{display:inline-flex;align-items:center;gap:8px;font-size:11px;letter-spacing:.12em;text-transform:uppercase;background:rgba(255,255,255,.08)}
        .metric{flex:1 1 150px;border-radius:20px;padding:16px;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08)}.metric strong{display:block;font-family:"Arial Narrow","Bahnschrift",sans-serif;font-size:31px;letter-spacing:-.05em}
        .heroCard{padding:18px;border-radius:var(--lg);background:rgba(255,255,255,.06);border:1px solid var(--lineDark)}.heroCard h3,.panel h2,.panel h3,.panel h4,.panel p{margin:0}.heroCard p{margin-top:6px}
        .panel{padding:24px;background:var(--paper);color:var(--ink)}.panel.dark{background:var(--night);color:#f8f1e8;border-color:rgba(255,255,255,.06)}.panel.dark .muted,.panel.dark .source{color:rgba(248,241,232,.7)}
        .head{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;margin-bottom:18px}.head h2{font-size:30px;letter-spacing:-.05em}.head h3{font-size:22px;letter-spacing:-.04em}
        .chip,.tab,.pill,.mod,.pick,.button{background:rgba(255,255,255,.76);color:var(--ink);border-color:var(--line);cursor:pointer}.chip:hover,.tab:hover,.pill:hover,.mod:hover,.pick:hover,.button:hover{transform:translateY(-1px);border-color:rgba(226,106,55,.36);box-shadow:0 10px 24px rgba(7,10,16,.08)}
        .chip.active,.tab.active,.pill.active,.mod.active,.pick.active{background:var(--accentSoft);border-color:rgba(226,106,55,.4);color:#5e2b1b}.button.primary{background:linear-gradient(135deg,#d85d2e,#f08a59);color:#fff7f1;border-color:transparent;text-decoration:none}.button.ghost{background:rgba(255,255,255,.08);color:#f8f1e8;border-color:rgba(255,255,255,.12)}
        .section{display:grid;gap:14px;padding-top:18px;border-top:1px solid rgba(20,25,32,.08)}.section:first-child{padding-top:0;border-top:0}.grid2,.grid3,.tagGrid{display:grid;gap:14px}.grid2{grid-template-columns:repeat(2,minmax(0,1fr))}.grid3{grid-template-columns:repeat(3,minmax(0,1fr))}.tagGrid{grid-template-columns:repeat(2,minmax(0,1fr))}
        .card,.summary,.empty{border-radius:var(--md);border:1px solid var(--line);background:var(--paper2);overflow:hidden;padding:14px}.media{position:relative;overflow:hidden;aspect-ratio:16/10;border-radius:18px;margin-bottom:14px;background:radial-gradient(circle at 18% 18%,rgba(107,209,207,.25),transparent 34%),linear-gradient(135deg,rgba(8,12,18,.98),rgba(38,31,27,.94));border:1px solid rgba(255,255,255,.08)}
        .media img{position:absolute;inset:0;width:100%;height:100%;object-fit:contain;padding:12px}.fallback{position:absolute;inset:0;display:grid;place-items:center;text-align:center;gap:10px;color:rgba(255,247,241,.86)}.mark{width:84px;height:84px;display:grid;place-items:center;border-radius:24px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);font-family:"Arial Narrow","Bahnschrift",sans-serif;font-size:30px}
        .card h4,.summary h4{font-size:22px;letter-spacing:-.04em}.mini{color:var(--muted);line-height:1.5}.stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;margin-top:12px}.stat{border-radius:14px;padding:10px;background:rgba(18,24,32,.05)}.stat strong{display:block;font-size:22px;letter-spacing:-.05em}.stat span{color:var(--muted);font-size:12px;text-transform:uppercase;letter-spacing:.08em}
        .empty{min-height:180px;display:grid;place-content:center;text-align:center;gap:10px}.empty strong{font-size:22px;letter-spacing:-.04em}.pick{width:100%;display:flex;justify-content:space-between;align-items:center;text-align:left}.summaryMain{border-radius:22px;padding:18px;background:linear-gradient(160deg,rgba(255,255,255,.06),rgba(255,255,255,.02)),radial-gradient(circle at top right,rgba(226,106,55,.22),transparent 34%);border:1px solid rgba(255,255,255,.08)}.summaryMain strong{display:block;font-family:"Arial Narrow","Bahnschrift",sans-serif;font-size:42px;letter-spacing:-.06em}
        [hidden]{display:none!important}@media (max-width:1120px){.heroGrid,.layout{grid-template-columns:1fr}}@media (max-width:760px){.page{width:min(100vw - 12px,1380px);margin:6px auto 16px;gap:12px}.hero,.panel{padding:18px;border-radius:24px}.grid2,.grid3,.tagGrid,.stats{grid-template-columns:1fr}.head{flex-direction:column}}
    </style>
</head>
<body>
    <div class="page">
        <header class="hero">
            <div class="heroGrid">
                <div>
                    <div class="row"><span class="badge">Wiki Builder API</span><span class="badge">Builder preview enabled</span></div>
                    <h1>Multi-game catalog, usable builder, cleaner front.</h1>
                    <p>This version is no longer just a catalog viewer. It adds a builder preview, public seeded builds, and resilient media cards that still look correct when wiki images fail to load.</p>
                    <div id="hero-metrics" class="metrics"></div>
                </div>
                <div class="stack">
                    <section class="heroCard">
                        <div class="head"><div><h3>Catalog switcher</h3><p>Select a game and reload its items, profiles, modifiers, and public builds.</p></div></div>
                        <div id="game-switcher" class="switcher"></div>
                    </section>
                    <section class="heroCard">
                        <div class="head"><div><h3>Workspace tabs</h3><p>Builder first, then catalog review, then tag coverage.</p></div></div>
                        <div id="tabs" class="tabs" role="tablist" aria-label="Workspace tabs"></div>
                    </section>
                </div>
            </div>
        </header>

        <main class="layout">
            <section class="stack">
                <section class="panel">
                    <div class="head">
                        <div><h2 id="title">Loading workspace</h2><p id="copy" class="mini">Fetching selected game data from the API.</p></div>
                        <div class="actions"><button id="reset" type="button" class="button">Reset preview</button><a href="/api/v1/games" target="_blank" rel="noreferrer" class="button primary">Open API</a></div>
                    </div>
                    <div id="panel-builder"></div>
                    <div id="panel-catalog" hidden></div>
                    <div id="panel-tags" hidden></div>
                </section>
            </section>

            <aside class="stack">
                <section class="panel dark">
                    <div class="head"><div><h3>Preview summary</h3><p class="muted">Local composition only. Persisting builds still requires the protected API routes.</p></div></div>
                    <div id="summary"></div>
                </section>
                <section class="panel">
                    <div class="head"><div><h3>Reference builds</h3><p class="mini">Use seeded public builds as comparison anchors.</p></div></div>
                    <div id="builds" class="stack"></div>
                </section>
                <section class="panel dark">
                    <div class="head"><div><h3>What changed</h3><p class="muted">The three concrete issues you raised are addressed here.</p></div></div>
                    <div class="stack">
                        <div class="summary"><h4>Builder added</h4><p class="muted">The page can now assemble a preview loadout with compatible modifiers.</p></div>
                        <div class="summary"><h4>Images hardened</h4><p class="muted">Broken remote assets now degrade to generated marks instead of empty boxes.</p></div>
                        <div class="summary"><h4>Public build listing</h4><p class="muted">The front no longer depends on hardcoded build ids.</p></div>
                    </div>
                </section>
            </aside>
        </main>
    </div>

    <script>
        const state = {activeTab:'builder',games:[],tags:[],items:[],modifiers:[],profiles:[],builds:[],buildSummaries:{},selectedGameId:null,selectedProfileId:null,selectedItems:[],selectedModifiers:{},selectedBuildId:null};
        const nodes = {heroMetrics:document.getElementById('hero-metrics'),gameSwitcher:document.getElementById('game-switcher'),tabs:document.getElementById('tabs'),title:document.getElementById('title'),copy:document.getElementById('copy'),builder:document.getElementById('panel-builder'),catalog:document.getElementById('panel-catalog'),tags:document.getElementById('panel-tags'),summary:document.getElementById('summary'),builds:document.getElementById('builds'),reset:document.getElementById('reset')};
        const tabOrder = [{id:'builder',label:'Builder'},{id:'catalog',label:'Catalog'},{id:'tags',label:'Tags'}];
        const statKeys = ['score','attack','defense','utility','accuracy','mobility'];
        const escapeHtml = (value) => String(value ?? '').replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;').replaceAll('"','&quot;').replaceAll("'","&#39;");
        const asNumber = (value) => Number(value ?? 0);
        const getJson = async (path, options = {}) => { const response = await fetch(path, {headers:{Accept:'application/json'}, ...options}); const payload = await response.json(); if (!response.ok || payload.success === false) { throw new Error(payload.message ?? `Request failed for ${path}`); } return payload; };
        const currentGame = () => state.games.find((game) => game.id === state.selectedGameId) ?? null;
        const initials = (label) => label.split(/[\s-]+/).filter(Boolean).slice(0,2).map((part) => part[0]?.toUpperCase() ?? '').join('');
        const metricCard = (value, label) => `<div class="metric"><strong>${escapeHtml(value)}</strong><span>${escapeHtml(label)}</span></div>`;
        const statCard = (label, value) => `<div class="stat"><strong>${escapeHtml(value)}</strong><span>${escapeHtml(label)}</span></div>`;
        const mediaMarkup = (item) => { const imageUrl = item.metadata?.source?.image_url; const slotLabel = item.metadata?.bo3?.weapon_class ?? item.slot; return `<div class="media">${imageUrl ? `<img src="${escapeHtml(imageUrl)}" alt="${escapeHtml(item.name)}" loading="lazy" referrerpolicy="no-referrer" onerror="this.remove()">` : ''}<div class="fallback"><div class="mark">${escapeHtml(initials(item.name))}</div><div>${escapeHtml(slotLabel)}</div></div></div>`; };
        const itemCard = (item, selected = false) => { const sourceUrl = item.metadata?.source?.page_url; const tags = Array.isArray(item.tags) ? item.tags.slice(0,3) : []; return `<article class="card">${mediaMarkup(item)}<div class="head"><div><h4>${escapeHtml(item.name)}</h4><p class="mini">${escapeHtml(item.metadata?.bo3?.handling_profile ?? 'Catalog item')}</p></div><button type="button" class="button ${selected ? '' : 'primary'}" data-action="${selected ? 'remove-item' : 'add-item'}" data-item-id="${item.id}">${selected ? 'Remove' : 'Add'}</button></div><div class="chips"><span class="pill">${escapeHtml(item.slot)}</span><span class="pill">${escapeHtml(item.rarity)}</span><span class="pill">${escapeHtml(item.tier_level)}</span>${tags.map((tag) => `<span class="pill">${escapeHtml(tag.label)}</span>`).join('')}</div><div class="stats">${statCard('Score', item.score)}${statCard('Attack', asNumber(item.attack))}${statCard('Accuracy', asNumber(item.accuracy))}${statCard('Utility', asNumber(item.utility))}${statCard('Mobility', asNumber(item.mobility))}${statCard('Defense', asNumber(item.defense))}</div>${sourceUrl ? `<p class="source">Source: <a href="${escapeHtml(sourceUrl)}" target="_blank" rel="noreferrer">weapon page</a></p>` : ''}</article>`; };
        const modifierCard = (modifier) => `<article class="card"><div class="head"><div><h4>${escapeHtml(modifier.name)}</h4><p class="mini">${escapeHtml(modifier.modifier_type)}</p></div></div><p>${escapeHtml(modifier.description ?? 'No description available.')}</p><div class="stats">${statCard('Score', asNumber(modifier.delta_score))}${statCard('Attack', asNumber(modifier.delta_attack))}${statCard('Accuracy', asNumber(modifier.delta_accuracy))}${statCard('Utility', asNumber(modifier.delta_utility))}${statCard('Mobility', asNumber(modifier.delta_mobility))}${statCard('Defense', asNumber(modifier.delta_defense))}</div></article>`;
        const previewTotals = () => { const totals = {score:0,attack:0,defense:0,utility:0,accuracy:0,mobility:0}; state.selectedItems.forEach((item) => { totals.score += asNumber(item.score); totals.attack += asNumber(item.attack); totals.defense += asNumber(item.defense); totals.utility += asNumber(item.utility); totals.accuracy += asNumber(item.accuracy); totals.mobility += asNumber(item.mobility); }); Object.values(state.selectedModifiers).forEach((ids) => { ids.forEach((id) => { const modifier = state.modifiers.find((entry) => entry.id === id); if (!modifier) { return; } totals.score += asNumber(modifier.delta_score); totals.attack += asNumber(modifier.delta_attack); totals.defense += asNumber(modifier.delta_defense); totals.utility += asNumber(modifier.delta_utility); totals.accuracy += asNumber(modifier.delta_accuracy); totals.mobility += asNumber(modifier.delta_mobility); }); }); return totals; };
        const modifierCount = () => Object.values(state.selectedModifiers).reduce((count, ids) => count + ids.length, 0);
        const compatibleModifiers = (itemId) => state.modifiers.filter((modifier) => Array.isArray(modifier.compatible_equipment_items) && modifier.compatible_equipment_items.some((entry) => entry.id === itemId));
        const renderHero = () => { const game = currentGame(); nodes.heroMetrics.innerHTML = [metricCard(state.games.length,'Games'),metricCard(state.items.length,'Items'),metricCard(state.modifiers.length,'Modifiers'),metricCard(state.builds.length,'Public builds')].join(''); nodes.title.textContent = game ? `${game.name} workspace` : 'Loading workspace'; nodes.copy.textContent = game ? `${game.genre ?? 'Game catalog'} · local builder preview, public build references, and resilient media cards.` : 'Fetching selected game data from the API.'; };
        const renderGames = () => { nodes.gameSwitcher.innerHTML = state.games.map((game) => `<button type="button" class="chip ${game.id === state.selectedGameId ? 'active' : ''}" data-action="select-game" data-game-id="${game.id}">${escapeHtml(game.name)}</button>`).join(''); };
        const renderTabs = () => { nodes.tabs.innerHTML = tabOrder.map((tab) => `<button type="button" id="tab-${tab.id}" role="tab" class="tab ${state.activeTab === tab.id ? 'active' : ''}" data-action="select-tab" data-tab-id="${tab.id}">${escapeHtml(tab.label)}</button>`).join(''); nodes.builder.hidden = state.activeTab !== 'builder'; nodes.catalog.hidden = state.activeTab !== 'catalog'; nodes.tags.hidden = state.activeTab !== 'tags'; };
        const renderBuilder = () => {
            const profileMarkup = state.profiles.length ? state.profiles.map((profile) => `<button type="button" class="pill ${profile.id === state.selectedProfileId ? 'active' : ''}" data-action="select-profile" data-profile-id="${profile.id}">${escapeHtml(profile.name)}</button>`).join('') : '<span class="mini">No build profiles returned by the API.</span>';
            const selectedMarkup = state.selectedItems.length ? state.selectedItems.map((item) => `<article class="card"><div class="head"><div><h4>${escapeHtml(item.name)}</h4><p class="mini">${escapeHtml(item.metadata?.bo3?.weapon_class ?? item.slot)} · ${escapeHtml(item.metadata?.bo3?.fire_mode ?? 'standard')}</p></div><button type="button" class="button" data-action="remove-item" data-item-id="${item.id}">Remove</button></div><div class="mods">${compatibleModifiers(item.id).map((modifier) => `<button type="button" class="mod ${(state.selectedModifiers[item.id] ?? []).includes(modifier.id) ? 'active' : ''}" data-action="toggle-modifier" data-item-id="${item.id}" data-modifier-id="${modifier.id}">${escapeHtml(modifier.name)}</button>`).join('')}</div></article>`).join('') : '<article class="empty"><strong>No preview build yet</strong><p>Add up to three items, then toggle compatible modifiers to inspect the totals.</p></article>';
            nodes.builder.innerHTML = `<section class="section"><div class="head"><div><h3>Build profile</h3><p class="mini">Profiles remain game-scoped and drive the builder context.</p></div></div><div class="chips">${profileMarkup}</div></section><section class="section"><div class="head"><div><h3>Assembly bench</h3><p class="mini">Public preview only. Persistence stays behind the API key middleware.</p></div></div><div class="stack">${selectedMarkup}</div></section><section class="section"><div class="head"><div><h3>Available items</h3><p class="mini">The preview is capped at three items to keep the demo readable.</p></div></div><div class="grid2">${state.items.length ? state.items.map((item) => itemCard(item, state.selectedItems.some((entry) => entry.id === item.id))).join('') : '<article class="empty"><strong>No items</strong><p>The API returned an empty item list.</p></article>'}</div></section>`;
        };
        const renderCatalog = () => { nodes.catalog.innerHTML = `<section class="section"><div class="head"><div><h3>Top catalog items</h3><p class="mini">Sorted by score from the public game-scoped endpoint.</p></div></div><div class="grid2">${state.items.slice(0,4).length ? state.items.slice(0,4).map((item) => itemCard(item, state.selectedItems.some((entry) => entry.id === item.id))).join('') : '<article class="empty"><strong>No items</strong><p>The API returned an empty item list.</p></article>'}</div></section><section class="section"><div class="head"><div><h3>Top modifiers</h3><p class="mini">Compatibility remains driven by the API relation, not a hardcoded matrix in the front.</p></div></div><div class="stack">${state.modifiers.slice(0,6).length ? state.modifiers.slice(0,6).map((modifier) => modifierCard(modifier)).join('') : '<article class="empty"><strong>No modifiers</strong><p>The API returned an empty modifier list.</p></article>'}</div></section>`; };
        const renderTags = () => {
            const visibleItemIds = new Set(state.items.map((item) => item.id));
            const visibleTags = state.tags.map((tag) => ({...tag,matches:Array.isArray(tag.equipment_items) ? tag.equipment_items.filter((item) => visibleItemIds.has(item.id)) : []})).filter((tag) => tag.matches.length > 0);
            nodes.tags.innerHTML = `<section class="section"><div class="head"><div><h3>Visible tag coverage</h3><p class="mini">Tags remain global in storage but this panel narrows them to the selected game slice.</p></div></div><div class="tagGrid">${visibleTags.length ? visibleTags.map((tag) => `<article class="card"><div class="head"><div><h4>${escapeHtml(tag.label)}</h4><p class="mini">${escapeHtml(tag.matches.length)} linked items</p></div></div><p>${escapeHtml(tag.matches.map((item) => item.name).slice(0,4).join(', '))}</p></article>`).join('') : '<article class="empty"><strong>No tag overlap</strong><p>No visible tag currently matches the selected game slice.</p></article>'}</div></section>`;
        };
        const renderSummary = () => {
            const totals = previewTotals();
            const profile = state.profiles.find((entry) => entry.id === state.selectedProfileId) ?? null;
            const reference = state.selectedBuildId ? state.buildSummaries[state.selectedBuildId] ?? null : null;
            const diff = reference ? Object.fromEntries(statKeys.map((key) => [key, totals[key] - asNumber(reference.totals?.[key])])) : null;
            nodes.summary.innerHTML = `<div class="summaryMain"><span class="badge">Local preview</span><strong>${escapeHtml(totals.score)}</strong><span>Total score</span><p class="muted">${escapeHtml(profile?.name ?? 'No profile selected')}</p></div><div class="stats">${statCard('Items', state.selectedItems.length)}${statCard('Mods', modifierCount())}${statCard('Score', totals.score)}${statCard('Attack', totals.attack)}${statCard('Accuracy', totals.accuracy)}${statCard('Mobility', totals.mobility)}</div>${reference ? `<div class="summary"><h4>Vs reference build</h4><p class="muted">${escapeHtml(reference.title ?? 'Selected reference')}</p><div class="stats">${statKeys.map((key) => statCard(`${key} diff`, diff[key] > 0 ? `+${diff[key]}` : diff[key])).join('')}</div></div>` : '<div class="summary"><h4>No comparison selected</h4><p class="muted">Choose a public build on the right to benchmark the current preview.</p></div>'}`;
        };
        const renderBuilds = () => { nodes.builds.innerHTML = state.builds.length ? state.builds.map((build) => `<button type="button" class="pick ${build.id === state.selectedBuildId ? 'active' : ''}" data-action="select-build" data-build-id="${build.id}"><span><strong>${escapeHtml(build.title)}</strong><small>${escapeHtml(build.description ?? 'Seeded build')}</small></span><span><strong>${escapeHtml(state.buildSummaries[build.id]?.totals?.score ?? 0)}</strong><small>score</small></span></button>`).join('') : '<article class="empty"><strong>No public builds</strong><p>The selected game does not expose a public build yet.</p></article>'; };
        const bindEvents = () => {
            document.querySelectorAll('[data-action="select-game"]').forEach((button) => button.addEventListener('click', async () => { const gameId = Number(button.dataset.gameId); if (gameId === state.selectedGameId) { return; } state.selectedGameId = gameId; await loadSelectedGame(); render(); }));
            document.querySelectorAll('[data-action="select-tab"]').forEach((button) => button.addEventListener('click', () => { state.activeTab = button.dataset.tabId; render(); }));
            document.querySelectorAll('[data-action="select-profile"]').forEach((button) => button.addEventListener('click', () => { state.selectedProfileId = Number(button.dataset.profileId); render(); }));
            document.querySelectorAll('[data-action="add-item"],[data-action="remove-item"]').forEach((button) => button.addEventListener('click', () => { const itemId = Number(button.dataset.itemId); const existing = state.selectedItems.find((item) => item.id === itemId); if (existing) { state.selectedItems = state.selectedItems.filter((item) => item.id !== itemId); delete state.selectedModifiers[itemId]; render(); return; } if (state.selectedItems.length >= 3) { return; } const item = state.items.find((entry) => entry.id === itemId); if (!item) { return; } state.selectedItems = [...state.selectedItems, item]; render(); }));
            document.querySelectorAll('[data-action="toggle-modifier"]').forEach((button) => button.addEventListener('click', () => { const itemId = Number(button.dataset.itemId); const modifierId = Number(button.dataset.modifierId); const selected = new Set(state.selectedModifiers[itemId] ?? []); if (selected.has(modifierId)) { selected.delete(modifierId); } else { selected.add(modifierId); } state.selectedModifiers[itemId] = Array.from(selected); render(); }));
            document.querySelectorAll('[data-action="select-build"]').forEach((button) => button.addEventListener('click', () => { state.selectedBuildId = Number(button.dataset.buildId); render(); }));
        };
        const render = () => { renderHero(); renderGames(); renderTabs(); renderBuilder(); renderCatalog(); renderTags(); renderSummary(); renderBuilds(); bindEvents(); };
        const loadSelectedGame = async () => {
            const game = currentGame(); if (!game) { return; }
            const [itemsResponse, modifiersResponse, profilesResponse, buildsResponse] = await Promise.all([getJson(`/api/v1/games/${game.id}/equipment-items?per_page=12&sort_by=score&sort_dir=desc`),getJson(`/api/v1/games/${game.id}/equipment-modifiers?per_page=12&sort_by=delta_score&sort_dir=desc`),getJson(`/api/v1/games/${game.id}/build-profiles?per_page=12`),getJson(`/api/v1/builds?game_id=${game.id}&per_page=12`)]);
            state.items = itemsResponse.data.data ?? []; state.modifiers = modifiersResponse.data.data ?? []; state.profiles = profilesResponse.data.data ?? []; state.builds = buildsResponse.data.data ?? []; state.buildSummaries = {}; state.selectedItems = []; state.selectedModifiers = {}; state.selectedProfileId = state.profiles[0]?.id ?? null; state.selectedBuildId = state.builds[0]?.id ?? null;
            const summaries = await Promise.all(state.builds.map(async (build) => { const response = await getJson(`/api/v1/builds/${build.id}/summary`); return [build.id, {...response.data,title:build.title}]; })); state.buildSummaries = Object.fromEntries(summaries);
        };
        const boot = async () => {
            try { const [gamesResponse, tagsResponse] = await Promise.all([getJson('/api/v1/games?per_page=12'),getJson('/api/v1/tags?per_page=24')]); state.games = gamesResponse.data.data ?? []; state.tags = tagsResponse.data.data ?? []; state.selectedGameId = state.games.find((game) => game.slug === 'call-of-duty-black-ops-iii')?.id ?? state.games[0]?.id ?? null; await loadSelectedGame(); render(); } catch (error) { nodes.title.textContent = 'Front unavailable'; nodes.copy.textContent = error.message; nodes.builder.innerHTML = '<article class="empty"><strong>API fetch failed</strong><p>Start Laravel, run the migrations with seeds, and reload this page.</p></article>'; }
        };
        nodes.reset.addEventListener('click', () => { state.selectedItems = []; state.selectedModifiers = {}; render(); });
        boot();
    </script>
</body>
</html>
