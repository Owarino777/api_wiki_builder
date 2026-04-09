<x-layouts.app :title="'Accueil - Wiki Builder API'" bodyClass="page-home" shellClass="shell-home" mainClass="content-home">
    <section class="page-head">
        <div>
            <span class="eyebrow">Builder</span>
            <h1>Créer un build</h1>
            <span class="subtitle">Weapon Loadout</span>
            <hr class="neon-line">
            <p id="hero-copy" class="helper">Choisis un jeu, charge un build ou pars vide, ajoute des items puis compare.</p>
        </div>
        @auth
        <div class="page-head-actions">
            <a class="btn ghost" href="{{ route('dashboard') }}">Dashboard</a>
            <a class="btn primary" href="{{ route('studio.games.create') }}">Nouveau draft</a>
        </div>
        @endauth
    </section>

    <section class="panel builder-setup">
        <div class="section-head">
            <div class="section-copy">
                <span class="eyebrow">Configuration</span>
                <h2>Point de départ</h2>
                <p class="helper compact setup-helper">Jeu, archétype, build public.</p>
            </div>
            <div id="hero-stats" class="page-kpis"></div>
        </div>

        <div class="home-config-grid">
            <div class="field">
                <label for="game-select">Jeu</label>
                <select id="game-select"></select>
            </div>
            <div class="field">
                <label for="profile">Archétype</label>
                <select id="profile"></select>
            </div>
            <div class="field span-2">
                <label for="build-select">Build public</label>
                <select id="build-select"></select>
            </div>
            <div class="field span-3">
                <label>État</label>
                <p id="build-note" class="helper-inline">Chargement...</p>
            </div>
        </div>
    </section>

    <section class="home-workspace">
        <section class="panel builder-panel">
            <div class="builder-head">
                <div>
                    <span class="eyebrow">Build</span>
                    <h2 id="builder-title" class="builder-title">1 slot actif</h2>
                    <p id="builder-copy" class="helper compact">Ajoute un item puis configure ses accessoires si besoin.</p>
                </div>
                <div class="toolbar">
                    <button id="reset-builder" type="button" class="btn ghost">Réinitialiser</button>
                </div>
            </div>

            <div id="slot-list" class="slot-list"></div>
        </section>

        <section id="matrix-panel" class="panel builder-panel matrix-panel" data-state="empty">
            <div class="builder-head">
                <div>
                    <span class="eyebrow">Comparaison</span>
                    <h3 class="matrix-title">Stats</h3>
                    <p class="helper compact">Affichée à partir de deux slots chargés.</p>
                </div>
            </div>
            <div id="matrix"></div>
        </section>
    </section>

    <section class="panel catalog-panel">
        <div class="catalog-head">
            <div>
                <span class="eyebrow">Catalogue</span>
                <h2 class="catalog-title">Ajouter un item</h2>
                <p class="helper compact">Recherche, filtres, chargement direct dans le build.</p>
            </div>
            <div id="catalog-status" class="summary">Chargement...</div>
        </div>

        <x-home.catalog-filters />

        <div id="catalog" class="catalog-grid"></div>

        <div class="pager">
            <button id="page-prev" type="button" class="btn ghost" data-action="page-prev">Page précédente</button>
            <span id="page-status" class="summary"></span>
            <button id="page-next" type="button" class="btn ghost" data-action="page-next">Page suivante</button>
        </div>
    </section>

    <section class="panel api-card">
        <details>
            <summary class="api-head">
                <div>
                    <span class="eyebrow">API</span>
                    <h2 class="api-title">Routes</h2>
                    <p class="helper compact">Lecture publique et exemple d’écriture.</p>
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

    <div id="modal-root"></div>

    <script>
        const S = {root: null, games: [], items: [], profiles: [], builds: [], modifiers: [], game: null, profile: null, selectedBuild: null, search: '', slot: 'all', tier: 'all', sort: 'score-desc', scoreMin: '', modifierType: 'all', category: 'all', page: 1, perPage: 12, visibleSlots: 1, compare: [{item: null, mods: {}}, {item: null, mods: {}}, {item: null, mods: {}}], cache: {}, editing: null, pending: null},
            K = ['score', 'attack', 'defense', 'utility', 'accuracy', 'mobility'],
            L = {score: 'Score', attack: 'Attaque', defense: 'Défense', utility: 'Utilité', accuracy: 'Précision', mobility: 'Mobilité'},
            SORT_OPTIONS = [{value: 'score-desc', label: 'Score décroissant'}, {value: 'score-asc', label: 'Score croissant'}, {value: 'name-asc', label: 'Nom A-Z'}, {value: 'name-desc', label: 'Nom Z-A'}],
            $ = (id) => document.getElementById(id),
            N = {heroCopy: $('hero-copy'), heroStats: $('hero-stats'), game: $('game-select'), profile: $('profile'), buildSelect: $('build-select'), buildNote: $('build-note'), search: $('search'), slot: $('slot-filter'), tier: $('tier-filter'), sort: $('sort-filter'), score: $('score-filter'), modifier: $('modifier-filter'), filterNote: $('filter-note'), category: $('category-list'), catalogStatus: $('catalog-status'), catalog: $('catalog'), pageStatus: $('page-status'), pagePrev: $('page-prev'), pageNext: $('page-next'), builderTitle: $('builder-title'), builderCopy: $('builder-copy'), slots: $('slot-list'), matrixPanel: $('matrix-panel'), matrix: $('matrix'), apiEndpoints: $('api-endpoints'), apiPublicSample: $('api-public-sample'), apiWriteSample: $('api-write-sample'), modal: $('modal-root'), reset: $('reset-builder')},
            E = (v) => String(v ?? '').replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;').replaceAll("'", '&#39;'),
            M = (v) => Number(v ?? 0),
            GAME = () => S.games.find((game) => game.id === S.game) || null,
            ITEM = (item, mods = {}) => ({item, mods});

        const itemLabel = (item) => item?.metadata?.bo3?.weapon_class ?? item?.metadata?.bo7?.category ?? item?.slot ?? 'weapon';
        const itemImageUrl = (item) => item?.metadata?.source?.image_url ?? null;

        function renderItemMedia(item, className = 'item-media') {
            const imageUrl = itemImageUrl(item);
            if (!imageUrl) return `<div class="${className}"><div class="item-media-fallback">${E(item?.name ?? 'Item')}</div></div>`;
            return `<div class="${className}"><img src="${E(imageUrl)}" alt="${E(item?.name ?? 'Item')}" loading="lazy" decoding="async" referrerpolicy="no-referrer" onerror="this.hidden=true;this.nextElementSibling.hidden=false"><div class="item-media-fallback" hidden>${E(item?.name ?? 'Item')}</div></div>`;
        }

        async function api(path, options = {}) {
            const headers = {Accept: 'application/json', ...(options.headers ?? {})};
            if (options.json) headers['Content-Type'] = 'application/json';
            const response = await fetch(path, {method: options.method ?? 'GET', headers, body: options.body ? JSON.stringify(options.body) : undefined});
            const payload = await response.json();
            if (!response.ok || payload.success === false) throw new Error(payload.message ?? `Request failed for ${path}`);
            return payload;
        }

        const visibleEntries = () => S.compare.slice(0, S.visibleSlots);
        const loadedEntries = () => visibleEntries().filter((entry) => entry.item);
        const pageCount = (rows) => Math.max(1, Math.ceil(rows.length / S.perPage));

        function cats() {
            const map = new Map();
            S.items.forEach((item) => (item.categories ?? []).forEach((category) => {
                const row = map.get(category.id) ?? {...category, count: 0};
                row.count += 1;
                map.set(category.id, row);
            }));
            return [...map.values()].sort((a, b) => b.count - a.count || a.label.localeCompare(b.label));
        }

        function filteredItems() {
            const q = S.search.trim().toLowerCase();
            const minimumScore = S.scoreMin === '' ? null : Number(S.scoreMin);
            return S.items
                .filter((item) => {
                    const bag = [item.name, item.slot, itemLabel(item), ...(item.categories ?? []).map((category) => category.label)].filter(Boolean).join(' ').toLowerCase();
                    const matchQuery = q === '' || bag.includes(q);
                    const matchSlot = S.slot === 'all' || item.slot === S.slot;
                    const matchTier = S.tier === 'all' || item.tier_level === S.tier;
                    const matchCategory = S.category === 'all' || (item.categories ?? []).some((category) => category.slug === S.category || String(category.id) === S.category);
                    const matchScore = minimumScore === null || M(item.score) >= minimumScore;
                    const matchModifier = S.modifierType === 'all' || (item.item_slots ?? []).some((slot) => slot.accepted_modifier_type === S.modifierType);
                    return matchQuery && matchSlot && matchTier && matchCategory && matchScore && matchModifier;
                })
                .sort((left, right) => {
                    if (S.sort === 'score-asc') return M(left.score) - M(right.score) || left.name.localeCompare(right.name);
                    if (S.sort === 'name-asc') return left.name.localeCompare(right.name);
                    if (S.sort === 'name-desc') return right.name.localeCompare(left.name);
                    return M(right.score) - M(left.score) || left.name.localeCompare(right.name);
                });
        }

        const findMod = (item, id) => (item?.compatible_modifiers ?? []).find((modifier) => modifier.id === id) || null;
        const options = (entry, slot) => (entry.item?.compatible_modifiers ?? []).filter((modifier) => !slot.accepted_modifier_type || modifier.modifier_type === slot.accepted_modifier_type).sort((a, b) => a.name.localeCompare(b.name));

        function totals(entry) {
            if (!entry.item) return null;
            const total = {score: M(entry.item.score), attack: M(entry.item.attack), defense: M(entry.item.defense), utility: M(entry.item.utility), accuracy: M(entry.item.accuracy), mobility: M(entry.item.mobility)};
            Object.values(entry.mods).forEach((modifierId) => {
                const modifier = findMod(entry.item, Number(modifierId));
                if (!modifier) return;
                total.score += M(modifier.delta_score);
                total.attack += M(modifier.delta_attack);
                total.defense += M(modifier.delta_defense);
                total.utility += M(modifier.delta_utility);
                total.accuracy += M(modifier.delta_accuracy);
                total.mobility += M(modifier.delta_mobility);
            });
            return total;
        }

        function setPage(value) {
            S.page = Math.min(Math.max(1, value), pageCount(filteredItems()));
        }

        function resetBuilder() {
            S.visibleSlots = 1;
            S.compare = [{item: null, mods: {}}, {item: null, mods: {}}, {item: null, mods: {}}];
            S.editing = null;
            S.pending = null;
            S.selectedBuild = null;
        }

        async function fetchItem(id) {
            if (S.cache[id]) return S.cache[id];
            const payload = await api(`/api/v1/items/${id}`);
            S.cache[id] = payload.data;
            return payload.data;
        }

        async function placeItem(id, index) {
            S.compare[index] = ITEM(await fetchItem(id));
            S.editing = null;
            S.pending = null;
        }

        async function handlePick(id) {
            const freeIndex = visibleEntries().findIndex((entry) => !entry.item);
            if (freeIndex !== -1) {
                await placeItem(id, freeIndex);
                render();
                return;
            }
            S.pending = Number(id);
            render();
        }

        async function replacePending(index) {
            if (S.pending === null) return;
            await placeItem(S.pending, index);
            render();
        }

        async function loadBuild(id) {
            const payload = await api(`/api/v1/builds/${id}`);
            const grouped = (payload.data.slots ?? []).reduce((carry, row) => {
                const bucket = carry[row.item_id] ?? {};
                bucket[row.slot_key] = row.modifier.id;
                carry[row.item_id] = bucket;
                return carry;
            }, {});
            const next = [{item: null, mods: {}}, {item: null, mods: {}}, {item: null, mods: {}}];
            const buildItems = payload.data.items ?? [];
            for (let index = 0; index < Math.min(3, buildItems.length); index += 1) {
                const buildItem = buildItems[index];
                const item = await fetchItem(buildItem.item_id);
                next[index] = ITEM(item, {...(grouped[buildItem.item_id] ?? {})});
            }
            S.compare = next;
            S.visibleSlots = Math.max(1, Math.min(3, buildItems.length || 1));
            S.editing = null;
            S.pending = null;
            S.selectedBuild = Number(id) || null;
            render();
        }

        async function loadGame() {
            const game = GAME();
            if (!game) return;
            const [itemsPayload, profilesPayload, buildsPayload, modifiersPayload] = await Promise.all([
                api(`/api/v1/games/${game.id}/items?per_page=100&sort_by=score&sort_dir=desc`),
                api(`/api/v1/games/${game.id}/archetypes?per_page=100`),
                api(`/api/v1/builds?game_id=${game.id}&is_public=true&per_page=100`),
                api(`/api/v1/games/${game.id}/modifiers?per_page=100&sort_by=name&sort_dir=asc`),
            ]);
            S.items = itemsPayload.data.data ?? [];
            S.profiles = profilesPayload.data.data ?? [];
            S.builds = buildsPayload.data.data ?? [];
            S.modifiers = modifiersPayload.data.data ?? [];
            S.profile = S.profiles[0]?.id ?? null;
            S.search = '';
            S.slot = S.items.some((item) => item.slot === 'weapon') ? 'weapon' : 'all';
            S.tier = 'all';
            S.sort = 'score-desc';
            S.scoreMin = '';
            S.modifierType = 'all';
            S.category = 'all';
            S.page = 1;
            S.cache = {};
            resetBuilder();
            if (S.builds[0]?.id) await loadBuild(S.builds[0].id);
        }

        function renderHero() {
            const game = GAME();
            const loaded = loadedEntries().length;
            N.heroCopy.textContent = game
                ? `${game.name} : choisis un build de départ ou ajoute tes propres items.`
                : 'Choisis un jeu puis ajoute des items.';
            N.heroStats.innerHTML = [['Jeux', S.games.length], ['Items', S.items.length], ['Builds publics', S.builds.length], ['Slots chargés', loaded]]
                .map(([label, value]) => `<article class="kpi-card"><strong>${E(value)}</strong><span>${E(label)}</span></article>`).join('');
        }

        function renderControls() {
            N.game.innerHTML = S.games.length ? S.games.map((game) => `<option value="${game.id}" ${game.id === S.game ? 'selected' : ''}>${E(game.name)}</option>`).join('') : '<option value="">Aucun jeu</option>';
            N.profile.innerHTML = S.profiles.length ? S.profiles.map((profile) => `<option value="${profile.id}" ${profile.id === S.profile ? 'selected' : ''}>${E(profile.name)}</option>`).join('') : '<option value="">Aucun archétype</option>';
            N.buildSelect.disabled = !S.builds.length;
            N.buildSelect.innerHTML = S.builds.length ? [`<option value="">Choisir un build public</option>`, ...S.builds.map((build) => `<option value="${build.id}" ${build.id === S.selectedBuild ? 'selected' : ''}>${E(build.title)}</option>`)].join('') : '<option value="">Aucun build public</option>';
            N.buildNote.textContent = S.builds.length ? `${S.builds.length} build(s) publics disponibles pour ce jeu.` : 'Aucun build public pour ce jeu.';
            N.slot.innerHTML = ['all', ...new Set(S.items.map((item) => item.slot).filter(Boolean))].map((slot) => `<option value="${E(slot)}" ${slot === S.slot ? 'selected' : ''}>${E(slot === 'all' ? 'Tous les slots' : slot)}</option>`).join('');
            N.tier.innerHTML = ['all', ...new Set(S.items.map((item) => item.tier_level).filter(Boolean))].map((tier) => `<option value="${E(tier)}" ${tier === S.tier ? 'selected' : ''}>${E(tier === 'all' ? 'Tous les tiers' : tier)}</option>`).join('');
            N.sort.innerHTML = SORT_OPTIONS.map((option) => `<option value="${option.value}" ${option.value === S.sort ? 'selected' : ''}>${E(option.label)}</option>`).join('');
            N.modifier.innerHTML = ['all', ...new Set(S.modifiers.map((modifier) => modifier.modifier_type).filter(Boolean))].map((type) => `<option value="${E(type)}" ${type === S.modifierType ? 'selected' : ''}>${E(type === 'all' ? 'Tous les types' : type)}</option>`).join('');
            N.score.value = S.scoreMin;
            N.category.innerHTML = [`<button type="button" class="chip" data-action="category" data-id="all" aria-pressed="${S.category === 'all'}">Toutes</button>`, ...cats().map((category) => {
                const id = category.slug ?? String(category.id);
                return `<button type="button" class="chip" data-action="category" data-id="${E(id)}" aria-pressed="${S.category === id}">${E(category.label)} · ${E(category.count)}</button>`;
            })].join('');
        }

        function renderBuilder() {
            const loaded = loadedEntries().length;
            N.builderTitle.textContent = `${S.visibleSlots} slot${S.visibleSlots > 1 ? 's' : ''} actif${S.visibleSlots > 1 ? 's' : ''}`;
            N.builderCopy.textContent = loaded > 1 ? 'Deux slots ou plus : la comparaison est disponible.' : loaded === 1 ? 'Clique sur l’item chargé pour ouvrir ses accessoires.' : 'Charge un premier item.';
            N.slots.innerHTML = visibleEntries().map((entry, index) => {
                if (!entry.item) {
                    const canRemove = S.visibleSlots > 1 && index === S.visibleSlots - 1;
                    return `<article class="slot-card empty"><div class="slot-top"><span class="slot-number">${index + 1}</span>${canRemove ? `<div class="slot-actions"><button type="button" class="icon-btn" data-action="remove-slot">Retirer le slot</button></div>` : ''}</div><h3>Slot vide</h3><p class="muted">Charge un item depuis le catalogue.</p></article>`;
                }
                const total = totals(entry);
                const mods = Object.keys(entry.mods).length;
                return `<article class="slot-card" data-action="edit-slot" data-index="${index}"><div class="slot-top"><span class="slot-number">${index + 1}</span><div class="slot-actions"><button type="button" class="icon-btn" data-action="edit-slot" data-index="${index}">Configurer</button><button type="button" class="icon-btn" data-action="clear-slot" data-index="${index}">Retirer</button></div></div>${renderItemMedia(entry.item, 'slot-media')}<div class="slot-main"><h3>${E(entry.item.name)}</h3><p class="muted">${E(itemLabel(entry.item))}</p></div><div class="slot-meta"><span class="tag">${E(entry.item.slot)}</span><span class="tag">${mods} accessoire(s)</span>${(entry.item.categories ?? []).slice(0, 2).map((category) => `<span class="tag">${E(category.label)}</span>`).join('')}</div><div class="stats-grid"><div class="stat-box"><strong>${E(total.score)}</strong><span>Score</span></div><div class="stat-box"><strong>${E(total.attack)}</strong><span>Attaque</span></div><div class="stat-box"><strong>${E(total.mobility)}</strong><span>Mobilité</span></div></div></article>`;
            }).join('') + (S.visibleSlots < 3 ? `<article class="slot-card add"><span class="slot-number">+</span><h3>Ajouter un slot</h3><p class="muted">Utile pour comparer.</p><button type="button" class="btn neon" data-action="add-slot">Ajouter</button></article>` : '');
        }

        function renderMatrix() {
            const rows = loadedEntries().map(totals).filter(Boolean);
            N.matrixPanel.dataset.state = rows.length < 2 ? 'empty' : 'ready';
            if (rows.length < 2) {
                N.matrix.innerHTML = `<article class="slot-card empty"><h3>Comparaison indisponible</h3><p class="muted">Ajoute au moins deux items.</p></article>`;
                return;
            }
            const leaders = Object.fromEntries(K.map((key) => [key, Math.max(...rows.map((row) => row[key]))]));
            N.matrix.innerHTML = `<div class="table"><table><thead><tr><th>Stat</th>${visibleEntries().map((entry, index) => `<th>${E(entry.item?.name ?? `Slot ${index + 1}`)}</th>`).join('')}</tr></thead><tbody>${K.map((key) => `<tr><th>${E(L[key])}</th>${visibleEntries().map((entry) => { const row = totals(entry); if (!row) return '<td>-</td>'; return `<td class="${row[key] === leaders[key] ? 'leader' : ''}">${E(row[key])}</td>`; }).join('')}</tr>`).join('')}</tbody></table></div>`;
        }

        function renderCatalog() {
            const rows = filteredItems();
            const pages = pageCount(rows);
            const start = (S.page - 1) * S.perPage;
            const chunk = rows.slice(start, start + S.perPage);
            N.catalogStatus.textContent = `${rows.length} item(s) visibles`;
            N.filterNote.textContent = `${S.modifierType === 'all' ? 'Tous les mods' : S.modifierType} · ${S.tier === 'all' ? 'Tous les tiers' : S.tier}`;
            N.pageStatus.textContent = `Page ${S.page} / ${pages}`;
            N.pagePrev.disabled = S.page <= 1;
            N.pageNext.disabled = S.page >= pages;
            N.catalog.innerHTML = chunk.length ? chunk.map((item) => `<button type="button" class="item-card" data-action="load" data-id="${item.id}">${renderItemMedia(item)}<div class="item-top"><div class="item-title"><strong>${E(item.name)}</strong><span>${E(itemLabel(item))}</span></div><span class="item-hint">Ajouter</span></div><div class="tags"><span class="tag">${E(item.slot)}</span><span class="tag">${E(item.tier_level ?? 'n/a')}</span>${(item.categories ?? []).slice(0, 2).map((category) => `<span class="tag">${E(category.label)}</span>`).join('')}</div><div class="bars"><div class="bar"><span>Score</span><div class="track"><div class="fill" style="width:${Math.max(0, Math.min(100, M(item.score)))}%"></div></div><strong>${E(item.score ?? 0)}</strong></div><div class="bar"><span>Attaque</span><div class="track"><div class="fill" style="width:${Math.max(0, Math.min(100, M(item.attack)))}%"></div></div><strong>${E(item.attack ?? 0)}</strong></div><div class="bar"><span>Mobilité</span><div class="track"><div class="fill" style="width:${Math.max(0, Math.min(100, M(item.mobility)))}%"></div></div><strong>${E(item.mobility ?? 0)}</strong></div></div></button>`).join('') : `<article class="slot-card empty"><h3>Aucun résultat</h3><p class="muted">Ajuste les filtres.</p></article>`;
        }

        function renderApiDocs() {
            if (!S.root) {
                N.apiEndpoints.innerHTML = '<div class="endpoint"><p class="muted">Chargement...</p></div>';
                return;
            }
            N.apiEndpoints.innerHTML = (S.root.public_routes ?? []).map((route) => `<div class="endpoint"><span class="method">${E(route.method)}</span><strong>${E(route.path)}</strong><p class="muted">${E(route.usage)}</p></div>`).join('');
            N.apiPublicSample.innerHTML = `<code>curl http://localhost:8000/api/v1\ncurl "http://localhost:8000${E(S.root.examples?.[1]?.path ?? '/api/v1/games')}"</code>`;
            N.apiWriteSample.innerHTML = `<code>curl -X POST http://localhost:8000/api/v1/builds ^\n  -H "Content-Type: application/json" ^\n  -H "API-KEY: votre-cle-api" ^\n  -d "{\\"game_id\\":${E(S.game ?? 1)},\\"user_id\\":1,\\"title\\":\\"Build demo\\",\\"items\\":[{\\"item_id\\":1,\\"position\\":1}]}"</code>`;
        }

        function renderModals() {
            const entry = S.editing !== null ? S.compare[S.editing] : null;
            const pending = S.pending !== null ? S.items.find((item) => item.id === S.pending) || null : null;
            if (!entry?.item && !pending) {
                N.modal.innerHTML = '';
                return;
            }
            if (pending) {
                N.modal.innerHTML = `<div class="modal"><div class="modal-card"><div class="modal-head"><div><span class="eyebrow">Remplacement</span><h2 class="builder-title">${E(pending.name)}</h2><p class="muted">Choisis le slot à remplacer.</p></div><div class="modal-actions"><button type="button" class="btn ghost" data-action="close-modal">Annuler</button></div></div><div class="replace-list">${visibleEntries().map((slot, index) => `<button type="button" class="replace-btn" data-action="replace-slot" data-index="${index}"><span><strong>Slot ${index + 1}</strong><span>${E(slot.item?.name ?? 'Vide')}</span></span><span>Remplacer</span></button>`).join('')}</div></div></div>`;
                return;
            }
            const total = totals(entry);
            const itemSlots = entry.item.item_slots ?? [];
            N.modal.innerHTML = `<div class="modal"><div class="modal-card"><div class="modal-head"><div><span class="eyebrow">Configuration</span><h2 class="builder-title">${E(entry.item.name)}</h2><p class="muted">${E(itemLabel(entry.item))}</p></div><div class="modal-actions"><button type="button" class="btn ghost" data-action="close-modal">Fermer</button></div></div><div class="modal-grid"><div class="slot-card">${renderItemMedia(entry.item, 'slot-media')}<div class="stats-grid"><div class="stat-box"><strong>${E(total.score)}</strong><span>Score</span></div><div class="stat-box"><strong>${E(total.attack)}</strong><span>Attaque</span></div><div class="stat-box"><strong>${E(total.accuracy)}</strong><span>Précision</span></div><div class="stat-box"><strong>${E(total.mobility)}</strong><span>Mobilité</span></div><div class="stat-box"><strong>${E(total.utility)}</strong><span>Utilité</span></div><div class="stat-box"><strong>${E(itemSlots.length)}</strong><span>Slots</span></div></div><div class="slot-meta"><span class="tag">${E(entry.item.slot)}</span>${(entry.item.categories ?? []).slice(0, 3).map((category) => `<span class="tag">${E(category.label)}</span>`).join('')}</div></div><div class="accessory-grid">${itemSlots.length ? itemSlots.map((slot) => { const selected = String(entry.mods[slot.slot_key] ?? ''); return `<div class="field"><label>${E(slot.slot_label)}</label><select data-action="mod" data-index="${S.editing}" data-slot-key="${E(slot.slot_key)}"><option value="">Aucun accessoire</option>${options(entry, slot).map((modifier) => `<option value="${modifier.id}" ${selected === String(modifier.id) ? 'selected' : ''}>${E(modifier.name)}</option>`).join('')}</select></div>`; }).join('') : `<div class="slot-card empty"><h3>Aucun slot</h3><p class="muted">Cet item n'accepte pas encore d'accessoires.</p></div>`}</div></div></div></div>`;
        }

        function render() {
            renderHero();
            renderControls();
            renderBuilder();
            renderMatrix();
            renderCatalog();
            renderApiDocs();
            renderModals();
        }

        document.addEventListener('click', async (event) => {
            const target = event.target.closest('[data-action]');
            if (!target) return;
            try {
                if (target.dataset.action === 'category') { S.category = target.dataset.id ?? 'all'; S.page = 1; render(); return; }
                if (target.dataset.action === 'load') { await handlePick(Number(target.dataset.id)); return; }
                if (target.dataset.action === 'add-slot') { S.visibleSlots = Math.min(3, S.visibleSlots + 1); render(); return; }
                if (target.dataset.action === 'remove-slot') { if (S.visibleSlots > 1) { S.compare[S.visibleSlots - 1] = {item: null, mods: {}}; S.visibleSlots -= 1; render(); } return; }
                if (target.dataset.action === 'clear-slot') { const index = Number(target.dataset.index); S.compare[index] = {item: null, mods: {}}; if (S.editing === index) S.editing = null; render(); return; }
                if (target.dataset.action === 'edit-slot') { S.editing = Number(target.dataset.index); render(); return; }
                if (target.dataset.action === 'replace-slot') { await replacePending(Number(target.dataset.index)); return; }
                if (target.dataset.action === 'close-modal') { S.editing = null; S.pending = null; render(); return; }
                if (target.dataset.action === 'page-prev') { setPage(S.page - 1); render(); return; }
                if (target.dataset.action === 'page-next') { setPage(S.page + 1); render(); }
            } catch (error) {
                N.builderCopy.textContent = error.message;
            }
        });

        document.addEventListener('change', async (event) => {
            const target = event.target;
            try {
                if (target === N.game) { const nextGame = Number(target.value) || null; if (nextGame !== S.game) { S.game = nextGame; await loadGame(); } render(); return; }
                if (target === N.profile) { S.profile = Number(target.value) || null; render(); return; }
                if (target === N.slot) { S.slot = target.value; S.page = 1; render(); return; }
                if (target === N.tier) { S.tier = target.value; S.page = 1; render(); return; }
                if (target === N.sort) { S.sort = target.value; render(); return; }
                if (target === N.score) { S.scoreMin = target.value; S.page = 1; render(); return; }
                if (target === N.modifier) { S.modifierType = target.value; S.page = 1; render(); return; }
                if (target === N.buildSelect) { const id = Number(target.value) || null; if (!id) { S.selectedBuild = null; render(); return; } await loadBuild(id); return; }
                if (target.matches('[data-action="mod"]')) {
                    const index = Number(target.dataset.index);
                    const key = target.dataset.slotKey;
                    if (!key || !S.compare[index]) return;
                    if (target.value === '') delete S.compare[index].mods[key];
                    else S.compare[index].mods[key] = Number(target.value);
                    render();
                }
            } catch (error) {
                N.builderCopy.textContent = error.message;
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && (S.editing !== null || S.pending !== null)) {
                S.editing = null;
                S.pending = null;
                render();
            }
        });

        N.search.addEventListener('input', (event) => {
            S.search = event.target.value;
            S.page = 1;
            render();
        });

        N.reset.addEventListener('click', () => {
            resetBuilder();
            render();
        });

        (async function boot() {
            try {
                const [rootPayload, payload] = await Promise.all([api('/api/v1'), api('/api/v1/games?per_page=20')]);
                S.root = rootPayload.data;
                S.games = payload.data.data ?? [];
                S.game = S.games.find((game) => game.slug === 'call-of-duty-black-ops-7')?.id ?? S.games.find((game) => game.slug === 'call-of-duty-black-ops-iii')?.id ?? S.games[0]?.id ?? null;
                await loadGame();
                render();
            } catch (error) {
                N.heroCopy.textContent = 'Chargement impossible.';
                N.catalog.innerHTML = `<article class="slot-card empty"><h3>Interface indisponible</h3><p class="muted">${E(error.message)}</p></article>`;
                N.slots.innerHTML = `<article class="slot-card empty"><h3>API inaccessible</h3><p class="muted">Vérifie que l'application tourne et que la base a bien été migrée puis alimentée.</p></article>`;
            }
        })();
    </script>
</x-layouts.app>
