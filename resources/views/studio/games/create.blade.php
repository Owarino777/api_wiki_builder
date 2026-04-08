<x-layouts.app :title="'Créer un jeu - Wiki Builder API'">
    <style>
        .studio-grid,.entity-grid,.summary-grid{display:grid;gap:16px}
        .studio-grid{grid-template-columns:minmax(300px,.85fr) minmax(0,1.15fr);align-items:start}
        .entity-grid{grid-template-columns:repeat(auto-fit,minmax(220px,1fr))}
        .summary-grid{grid-template-columns:repeat(auto-fit,minmax(180px,1fr))}
        .stack{display:grid;gap:10px}
        .section-block{padding:18px;border-radius:20px;border:1px solid var(--line);background:var(--panel-2);display:grid;gap:14px}
        .section-block h2,.section-block h3{font-size:24px;line-height:1}
        .section-head{display:flex;justify-content:space-between;gap:12px;align-items:flex-start}
        .note{padding:14px 16px;border-radius:16px;border:1px solid rgba(47,143,255,.25);background:rgba(47,143,255,.08);line-height:1.55}
        .note.danger{border-color:rgba(255,127,127,.35);background:rgba(255,127,127,.08)}
        .draft-card,.entity-card,.step-card{padding:14px;border-radius:18px;border:1px solid var(--line);background:var(--panel);display:grid;gap:8px}
        .draft-card.active{border-color:rgba(47,143,255,.45);background:rgba(47,143,255,.1)}
        .inline-actions,.check-grid{display:flex;flex-wrap:wrap;gap:10px}
        .check-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr))}
        .check-option{display:flex;align-items:flex-start;gap:10px;padding:12px;border-radius:16px;border:1px solid var(--line);background:var(--panel)}
        .check-option input{width:auto;min-height:auto;margin-top:2px}
        textarea,select{width:100%;min-height:48px;padding:12px 14px;border-radius:14px;border:1px solid var(--line);background:var(--panel);color:var(--text);outline:none;font:inherit}
        textarea{min-height:120px;resize:vertical}
        textarea:focus-visible,select:focus-visible{border-color:var(--accent);box-shadow:0 0 0 4px rgba(47,143,255,.14)}
        .field.two{grid-column:span 2}
        .pill-row{display:flex;flex-wrap:wrap;gap:8px}
        .meta-box{padding:12px;border-radius:16px;border:1px solid var(--line);background:var(--panel)}
        .meta-box strong{display:block;font-size:22px}
        .meta-box span{display:block;margin-top:6px;color:var(--muted);font-size:13px}
        .mini-form{display:grid;gap:12px}
        .empty-state{padding:18px;border-radius:18px;border:1px dashed var(--line);background:rgba(255,255,255,.02);color:var(--muted);line-height:1.6}
        .entity-card strong{font-size:18px}
        .entity-card p{color:var(--muted);line-height:1.5}
        .step-card.good{border-color:rgba(111,223,141,.35)}
        .step-card.warn{border-color:rgba(255,189,89,.35)}
        @media (max-width:980px){.studio-grid{grid-template-columns:1fr}.field.two{grid-column:auto}}
    </style>

    <section class="panel card">
        <span class="pill">Studio de création</span>
        <div>
            <h1 style="font-size:40px;line-height:1;">Créer un jeu et ses équipements</h1>
            <p class="helper" style="margin-top:10px;">Cette vue remplace les widgets de démo qui brouillaient le besoin. Ici, on suit le workflow de la spec : création d'un draft, complétude structurelle, ajout des archétypes, catégories, items, modifiers, puis publication.</p>
        </div>
        <div class="actions">
            <a class="btn" href="{{ route('dashboard') }}">Retour au dashboard</a>
            <a class="btn" href="{{ route('home') }}">Retour à l'accueil</a>
            <a class="btn ghost" href="/api/v1">Voir l'API</a>
        </div>
    </section>

    <div class="studio-grid">
        <aside class="panel card">
            <div class="section-head">
                <div>
                    <h2>Mes brouillons</h2>
                    <p class="helper" style="margin-top:8px;">Un brouillon par parcours, sélectionnable à tout moment.</p>
                </div>
            </div>

            @if (! $writeApiKey)
                <div class="note danger">
                    Aucune `API-KEY` active liee a ton compte n'a ete trouvee. La page reste visible, mais la creation de draft ne pourra pas envoyer les ecritures tant qu'une cle active n'aura pas ete seedee pour cet utilisateur.
                </div>
            @endif

            <form id="create-draft-form" class="mini-form">
                <div class="field">
                    <label for="create-category-key">Template</label>
                    <select id="create-category-key" required>
                        @foreach ($templates as $template)
                            <option value="{{ $template['key'] }}">{{ $template['label'] }} - {{ $template['version'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="create-name">Nom du jeu</label>
                    <input id="create-name" type="text" placeholder="Mon arena shooter" required>
                </div>
                <div class="field">
                    <label for="create-slug">Slug</label>
                    <input id="create-slug" type="text" placeholder="mon-arena-shooter">
                </div>
                <button class="btn primary" type="submit">Créer le brouillon</button>
            </form>

            <div id="draft-list" class="stack"></div>
        </aside>

        <section class="panel card">
            <div id="studio-status" class="note">Sélectionne un brouillon ou crée un nouveau draft pour commencer.</div>

            <div id="editor-empty" class="empty-state">
                Aucun draft n'est encore chargé. Une fois un brouillon sélectionné, la page affiche son état global puis les formulaires pour compléter foundation, structure, items et modifiers.
            </div>

            <div id="editor-root" class="stack" style="display:none;">
                <section class="section-block">
                    <div class="section-head">
                        <div>
                            <span class="pill">Draft actif</span>
                            <h2 id="draft-title" style="margin-top:10px;">Brouillon</h2>
                            <p id="draft-subtitle" class="helper" style="margin-top:8px;"></p>
                        </div>
                        <div class="inline-actions">
                            <button id="delete-draft" class="btn danger" type="button">Supprimer le draft</button>
                        </div>
                    </div>
                    <div id="draft-pill-row" class="pill-row"></div>
                    <div class="summary-grid">
                        <article class="meta-box">
                            <strong id="draft-progress">0%</strong>
                            <span>Complétion</span>
                        </article>
                        <article class="meta-box">
                            <strong id="draft-step">foundation</strong>
                            <span>Étape courante</span>
                        </article>
                        <article class="meta-box">
                            <strong id="draft-status">draft</strong>
                            <span>Statut</span>
                        </article>
                    </div>
                </section>

                <section class="section-block">
                    <div class="section-head">
                        <div>
                            <span class="pill">1. Foundation</span>
                            <h3 style="margin-top:10px;">Base du jeu</h3>
                        </div>
                    </div>
                    <form id="foundation-form" class="entity-grid">
                        <div class="field">
                            <label for="foundation-name">Nom final</label>
                            <input id="foundation-name" type="text" required>
                        </div>
                        <div class="field">
                            <label for="foundation-slug">Slug</label>
                            <input id="foundation-slug" type="text" required>
                        </div>
                        <div class="field two">
                            <label for="foundation-description">Description</label>
                            <textarea id="foundation-description" placeholder="Positionnement, fantasy, règles principales..."></textarea>
                        </div>
                        <div class="field">
                            <label for="foundation-visibility">Visibilité cible</label>
                            <select id="foundation-visibility">
                                <option value="private">Privé</option>
                                <option value="public">Public</option>
                            </select>
                        </div>
                        <div class="field" style="align-self:end;">
                            <button class="btn primary" type="submit">Enregistrer la foundation</button>
                        </div>
                    </form>
                </section>

                <section class="section-block">
                    <div class="section-head">
                        <div>
                            <span class="pill">2. Structure</span>
                            <h3 style="margin-top:10px;">Cadre du template</h3>
                            <p class="helper" style="margin-top:8px;">Une fois des items ou modifiers ajoutés, le backend verrouille les changements cassants.</p>
                        </div>
                    </div>
                    <form id="structure-form" class="stack">
                        <div class="check-grid">
                            <label class="check-option">
                                <input id="supports-modifiers" type="checkbox">
                                <span>
                                    <strong>Supports modifiers</strong>
                                    <small class="helper">Autorise les accessoires ou runes du draft.</small>
                                </span>
                            </label>
                            <label class="check-option">
                                <input id="supports-archetypes" type="checkbox">
                                <span>
                                    <strong>Supporte les archétypes</strong>
                                    <small class="helper">Active les profils ou classes du jeu.</small>
                                </span>
                            </label>
                        </div>
                        <div>
                            <label>Slots d'items actifs</label>
                            <div id="structure-item-slots" class="check-grid" style="margin-top:10px;"></div>
                        </div>
                        <div>
                            <label>Types de modifiers actifs</label>
                            <div id="structure-modifier-types" class="check-grid" style="margin-top:10px;"></div>
                        </div>
                        <div class="inline-actions">
                            <button class="btn primary" type="submit">Enregistrer la structure</button>
                        </div>
                    </form>
                </section>

                <section class="section-block">
                    <div class="section-head">
                        <div>
                            <span class="pill">3. Archetypes</span>
                            <h3 style="margin-top:10px;">Profils du jeu</h3>
                        </div>
                    </div>
                    <form id="archetype-form" class="entity-grid">
                        <div class="field">
                            <label for="archetype-name">Nom</label>
                            <input id="archetype-name" type="text" placeholder="Assaulter" required>
                        </div>
                        <div class="field">
                            <label for="archetype-slug">Slug</label>
                            <input id="archetype-slug" type="text" placeholder="assaulter">
                        </div>
                        <div class="field two">
                            <label for="archetype-description">Description</label>
                            <textarea id="archetype-description" placeholder="Rôle, positionnement, identité..."></textarea>
                        </div>
                        <div class="field" style="align-self:end;">
                            <button class="btn primary" type="submit">Ajouter un archétype</button>
                        </div>
                    </form>
                    <div id="archetype-list" class="stack"></div>
                </section>

                <section class="section-block">
                    <div class="section-head">
                        <div>
                            <span class="pill">4. Categories</span>
                            <h3 style="margin-top:10px;">Classification locale</h3>
                        </div>
                    </div>
                    <form id="category-form" class="entity-grid">
                        <div class="field">
                            <label for="category-label">Label</label>
                            <input id="category-label" type="text" placeholder="Assault Rifle" required>
                        </div>
                        <div class="field">
                            <label for="category-slug">Slug</label>
                            <input id="category-slug" type="text" placeholder="assault-rifle">
                        </div>
                        <div class="field two">
                            <label for="category-description">Description</label>
                            <textarea id="category-description" placeholder="Critère de regroupement des items..."></textarea>
                        </div>
                        <div class="field" style="align-self:end;">
                            <button class="btn primary" type="submit">Ajouter une catégorie</button>
                        </div>
                    </form>
                    <div id="category-list" class="stack"></div>
                </section>

                <section class="section-block">
                    <div class="section-head">
                        <div>
                            <span class="pill">5. Items</span>
                            <h3 style="margin-top:10px;">Équipements du jeu</h3>
                            <p class="helper" style="margin-top:8px;">On crée ici les armes, pièces d'armure ou objets équipables du draft.</p>
                        </div>
                    </div>
                    <form id="item-form" class="entity-grid">
                        <div class="field">
                            <label for="item-name">Nom</label>
                            <input id="item-name" type="text" placeholder="VX-7" required>
                        </div>
                        <div class="field">
                            <label for="item-slug">Slug</label>
                            <input id="item-slug" type="text" placeholder="vx-7">
                        </div>
                        <div class="field">
                            <label for="item-slot">Slot global</label>
                            <select id="item-slot"></select>
                        </div>
                        <div class="field">
                            <label for="item-rarity">Rareté</label>
                            <select id="item-rarity">
                                <option value="standard">standard</option>
                                <option value="rare">rare</option>
                                <option value="epic">epic</option>
                                <option value="legendary">legendary</option>
                            </select>
                        </div>
                        <div class="field">
                            <label for="item-tier">Tier</label>
                            <input id="item-tier" type="text" placeholder="T1">
                        </div>
                        <div class="field">
                            <label for="item-score">Score</label>
                            <input id="item-score" type="number" value="0">
                        </div>
                        <div class="field">
                            <label for="item-attack">Attack</label>
                            <input id="item-attack" type="number" value="0">
                        </div>
                        <div class="field">
                            <label for="item-defense">Defense</label>
                            <input id="item-defense" type="number" value="0">
                        </div>
                        <div class="field">
                            <label for="item-utility">Utility</label>
                            <input id="item-utility" type="number" value="0">
                        </div>
                        <div class="field">
                            <label for="item-accuracy">Accuracy</label>
                            <input id="item-accuracy" type="number" value="0">
                        </div>
                        <div class="field">
                            <label for="item-mobility">Mobility</label>
                            <input id="item-mobility" type="number" value="0">
                        </div>
                        <div class="field two">
                            <label>Categories</label>
                            <div id="item-category-options" class="check-grid"></div>
                        </div>
                        <div class="field" style="align-self:end;">
                            <button class="btn primary" type="submit">Ajouter un item</button>
                        </div>
                    </form>
                    <div id="item-list" class="stack"></div>
                </section>

                <section class="section-block">
                    <div class="section-head">
                        <div>
                            <span class="pill">6. Modifiers</span>
                            <h3 style="margin-top:10px;">Accessoires et effets</h3>
                            <p class="helper" style="margin-top:8px;">Si le template supporte les modifiers, on rattache ici les accessoires aux items compatibles.</p>
                        </div>
                    </div>
                    <form id="modifier-form" class="entity-grid">
                        <div class="field">
                            <label for="modifier-name">Nom</label>
                            <input id="modifier-name" type="text" placeholder="Red Dot X" required>
                        </div>
                        <div class="field">
                            <label for="modifier-slug">Slug</label>
                            <input id="modifier-slug" type="text" placeholder="red-dot-x">
                        </div>
                        <div class="field">
                            <label for="modifier-type">Type</label>
                            <select id="modifier-type"></select>
                        </div>
                        <div class="field">
                            <label for="modifier-score">Delta score</label>
                            <input id="modifier-score" type="number" value="0">
                        </div>
                        <div class="field">
                            <label for="modifier-attack">Delta attack</label>
                            <input id="modifier-attack" type="number" value="0">
                        </div>
                        <div class="field">
                            <label for="modifier-defense">Delta defense</label>
                            <input id="modifier-defense" type="number" value="0">
                        </div>
                        <div class="field">
                            <label for="modifier-utility">Delta utility</label>
                            <input id="modifier-utility" type="number" value="0">
                        </div>
                        <div class="field">
                            <label for="modifier-accuracy">Delta accuracy</label>
                            <input id="modifier-accuracy" type="number" value="0">
                        </div>
                        <div class="field">
                            <label for="modifier-mobility">Delta mobility</label>
                            <input id="modifier-mobility" type="number" value="0">
                        </div>
                        <div class="field two">
                            <label for="modifier-description">Description</label>
                            <textarea id="modifier-description" placeholder="Effet, compromis, usage..."></textarea>
                        </div>
                        <div class="field two">
                            <label>Items compatibles</label>
                            <div id="modifier-item-options" class="check-grid"></div>
                        </div>
                        <div class="field" style="align-self:end;">
                            <button class="btn primary" type="submit">Ajouter un modifier</button>
                        </div>
                    </form>
                    <div id="modifier-disabled" class="note" style="display:none;">Les modifiers sont actuellement désactivés dans la structure du draft.</div>
                    <div id="modifier-list" class="stack"></div>
                </section>

                <section class="section-block">
                    <div class="section-head">
                        <div>
                            <span class="pill">7. Revue</span>
                            <h3 style="margin-top:10px;">État des étapes et publication</h3>
                        </div>
                        <div class="inline-actions">
                            <button id="publish-draft" class="btn primary" type="button">Publier le jeu</button>
                        </div>
                    </div>
                    <div id="review-list" class="stack"></div>
                </section>
            </div>
        </section>
    </div>

    <section class="panel card">
        <h2>Mes jeux publiés récents</h2>
        <p class="helper">Ils apparaissent ici une fois la publication finalisée. L'endpoint owner équivalent reste `GET /api/v1/my/games`.</p>
        <div class="stack">
            @forelse ($publishedGames as $game)
                <article class="draft-card">
                    <strong>{{ $game->name }}</strong>
                    <div class="pill-row">
                        <span class="pill">{{ $game->visibility }}</span>
                        <span class="pill">{{ $game->creation_category ?: 'system' }}</span>
                        <span class="pill">{{ $game->published_at?->format('Y-m-d H:i') ?: 'non date' }}</span>
                    </div>
                </article>
            @empty
                <div class="empty-state">Aucun jeu utilisateur publié pour le moment.</div>
            @endforelse
        </div>
    </section>

    <script>
        const BOOT = {
            apiKey: @json($writeApiKey),
            actorId: @json($authUser->id),
            templates: @json($templates),
            initialDrafts: @json($drafts),
            selectedDraftId: @json($selectedDraftId),
        };

        const S = {
            apiKey: BOOT.apiKey,
            actorId: BOOT.actorId,
            templates: Array.isArray(BOOT.templates) ? BOOT.templates : [],
            drafts: Array.isArray(BOOT.initialDrafts) ? BOOT.initialDrafts : [],
            draft: null,
            selectedDraftId: BOOT.selectedDraftId ?? null,
            publishResult: null,
        };

        const N = {
            status: document.getElementById('studio-status'),
            createDraftForm: document.getElementById('create-draft-form'),
            createCategoryKey: document.getElementById('create-category-key'),
            createName: document.getElementById('create-name'),
            createSlug: document.getElementById('create-slug'),
            draftList: document.getElementById('draft-list'),
            editorEmpty: document.getElementById('editor-empty'),
            editorRoot: document.getElementById('editor-root'),
            draftTitle: document.getElementById('draft-title'),
            draftSubtitle: document.getElementById('draft-subtitle'),
            draftPillRow: document.getElementById('draft-pill-row'),
            draftProgress: document.getElementById('draft-progress'),
            draftStep: document.getElementById('draft-step'),
            draftStatus: document.getElementById('draft-status'),
            deleteDraft: document.getElementById('delete-draft'),
            foundationForm: document.getElementById('foundation-form'),
            foundationName: document.getElementById('foundation-name'),
            foundationSlug: document.getElementById('foundation-slug'),
            foundationDescription: document.getElementById('foundation-description'),
            foundationVisibility: document.getElementById('foundation-visibility'),
            structureForm: document.getElementById('structure-form'),
            supportsModifiers: document.getElementById('supports-modifiers'),
            supportsArchetypes: document.getElementById('supports-archetypes'),
            structureItemSlots: document.getElementById('structure-item-slots'),
            structureModifierTypes: document.getElementById('structure-modifier-types'),
            archetypeForm: document.getElementById('archetype-form'),
            archetypeName: document.getElementById('archetype-name'),
            archetypeSlug: document.getElementById('archetype-slug'),
            archetypeDescription: document.getElementById('archetype-description'),
            archetypeList: document.getElementById('archetype-list'),
            categoryForm: document.getElementById('category-form'),
            categoryLabel: document.getElementById('category-label'),
            categorySlug: document.getElementById('category-slug'),
            categoryDescription: document.getElementById('category-description'),
            categoryList: document.getElementById('category-list'),
            itemForm: document.getElementById('item-form'),
            itemName: document.getElementById('item-name'),
            itemSlug: document.getElementById('item-slug'),
            itemSlot: document.getElementById('item-slot'),
            itemRarity: document.getElementById('item-rarity'),
            itemTier: document.getElementById('item-tier'),
            itemScore: document.getElementById('item-score'),
            itemAttack: document.getElementById('item-attack'),
            itemDefense: document.getElementById('item-defense'),
            itemUtility: document.getElementById('item-utility'),
            itemAccuracy: document.getElementById('item-accuracy'),
            itemMobility: document.getElementById('item-mobility'),
            itemCategoryOptions: document.getElementById('item-category-options'),
            itemList: document.getElementById('item-list'),
            modifierForm: document.getElementById('modifier-form'),
            modifierName: document.getElementById('modifier-name'),
            modifierSlug: document.getElementById('modifier-slug'),
            modifierType: document.getElementById('modifier-type'),
            modifierDescription: document.getElementById('modifier-description'),
            modifierScore: document.getElementById('modifier-score'),
            modifierAttack: document.getElementById('modifier-attack'),
            modifierDefense: document.getElementById('modifier-defense'),
            modifierUtility: document.getElementById('modifier-utility'),
            modifierAccuracy: document.getElementById('modifier-accuracy'),
            modifierMobility: document.getElementById('modifier-mobility'),
            modifierItemOptions: document.getElementById('modifier-item-options'),
            modifierDisabled: document.getElementById('modifier-disabled'),
            modifierList: document.getElementById('modifier-list'),
            reviewList: document.getElementById('review-list'),
            publishDraft: document.getElementById('publish-draft'),
        };

        function E(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#39;');
        }

        function slugify(value) {
            return String(value ?? '')
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
        }

        function intOrNull(value) {
            if (value === '' || value === null || value === undefined) {
                return null;
            }

            const parsed = Number(value);
            return Number.isFinite(parsed) ? parsed : null;
        }

        function templateFor(key) {
            return S.templates.find((template) => template.key === key) ?? null;
        }

        function currentSettings() {
            const template = S.draft ? templateFor(S.draft.category_key) : null;
            const settings = S.draft?.settings ?? {};

            return {
                supports_modifiers: settings.supports_modifiers ?? template?.supports_modifiers ?? true,
                supports_archetypes: settings.supports_archetypes ?? template?.supports_archetypes ?? true,
                allowed_item_slots: Array.isArray(settings.allowed_item_slots) ? settings.allowed_item_slots : (template?.allowed_item_slots ?? []),
                allowed_modifier_types: Array.isArray(settings.allowed_modifier_types) ? settings.allowed_modifier_types : (template?.allowed_modifier_types ?? []),
            };
        }

        function selectedIds(selector) {
            return Array.from(document.querySelectorAll(selector))
                .filter((input) => input.checked)
                .map((input) => Number(input.value))
                .filter((value) => Number.isFinite(value));
        }

        function status(message, kind = 'info') {
            N.status.className = kind === 'danger' ? 'note danger' : 'note';
            N.status.textContent = message;
        }

        function updateDraftUrl(id) {
            const url = new URL(window.location.href);
            if (id) {
                url.searchParams.set('draft', String(id));
            } else {
                url.searchParams.delete('draft');
            }
            window.history.replaceState({}, '', url);
        }

        async function api(path, options = {}) {
            if (!S.apiKey) {
                throw new Error('Aucune clé API active n’est disponible pour les écritures.');
            }

            const headers = {
                Accept: 'application/json',
                'API-KEY': S.apiKey,
                'X-WIKI-ACTOR-ID': String(S.actorId),
                'X-BUILDER-USER-ID': String(S.actorId),
                'X-USER-ID': String(S.actorId),
            };

            if (options.body !== undefined) {
                headers['Content-Type'] = 'application/json';
            }

            const response = await fetch(path, {
                method: options.method ?? 'GET',
                headers,
                body: options.body !== undefined ? JSON.stringify(options.body) : undefined,
            });

            const payload = await response.json().catch(() => ({}));
            if (!response.ok || payload.success === false) {
                const reason = payload?.errors?.reason ?? payload?.message ?? 'Erreur API.';
                throw new Error(reason);
            }

            return payload;
        }

        async function loadDrafts() {
            const payload = await api('/api/v1/game-drafts?per_page=50');
            S.drafts = Array.isArray(payload?.data?.data) ? payload.data.data : [];
            renderDrafts();
        }

        async function loadDraft(id, message = 'Brouillon chargé.', preservePublishResult = false) {
            const payload = await api(`/api/v1/game-drafts/${id}`);
            S.draft = payload.data ?? null;
            S.selectedDraftId = S.draft?.id ?? null;
            if (!preservePublishResult) {
                S.publishResult = null;
            }
            updateDraftUrl(S.selectedDraftId);
            renderEditor();
            status(message);
        }

        async function refresh(id = S.selectedDraftId, message = 'Brouillon mis à jour.') {
            await loadDrafts();

            if (id) {
                await loadDraft(id, message);
                return;
            }

            S.draft = null;
            S.selectedDraftId = null;
            updateDraftUrl(null);
            renderEditor();
            status(message);
        }

        function renderDrafts() {
            if (!S.drafts.length) {
                N.draftList.innerHTML = '<div class="empty-state">Aucun brouillon en cours. Cree le premier draft depuis le formulaire ci-dessus.</div>';
                return;
            }

            N.draftList.innerHTML = S.drafts.map((draft) => {
                const active = Number(draft.id) === Number(S.selectedDraftId);
                return `<article class="draft-card ${active ? 'active' : ''}">
                    <strong>${E(draft.name)}</strong>
                    <div class="pill-row">
                        <span class="pill">${E(draft.category_key)}</span>
                        <span class="pill">${E(draft.status)}</span>
                        <span class="pill">${E(draft.current_step ?? 'foundation')}</span>
                    </div>
                    <p>Complétion : ${E(draft.completion_percent ?? 0)}%</p>
                    <button class="btn" type="button" data-action="select-draft" data-id="${draft.id}">Ouvrir ce draft</button>
                </article>`;
            }).join('');
        }

        function renderCheckboxOptions(container, values, selected, name) {
            container.innerHTML = values.length
                ? values.map((value) => `<label class="check-option">
                    <input type="checkbox" name="${E(name)}" value="${E(value)}" ${selected.includes(value) ? 'checked' : ''}>
                    <span><strong>${E(value)}</strong><small class="helper">Option active pour ce draft.</small></span>
                </label>`).join('')
                : '<div class="empty-state">Aucune option disponible pour ce template.</div>';
        }

        function renderEntityList(container, entities, type) {
            if (!entities.length) {
                container.innerHTML = `<div class="empty-state">Aucun ${E(type)} ajouté pour le moment.</div>`;
                return;
            }

            container.innerHTML = entities.map((entity) => {
                const label = entity.name ?? entity.label;
                const detail = type === 'item'
                    ? `${entity.slot} | score ${entity.score ?? 0}`
                    : type === 'modifier'
                        ? `${entity.modifier_type} | delta score ${entity.delta_score ?? 0}`
                        : entity.description ?? 'Aucune description.';

                const extra = type === 'item'
                    ? (entity.categories ?? []).map((category) => `<span class="pill">${E(category.label)}</span>`).join('')
                    : type === 'modifier'
                        ? (entity.compatible_item_ids ?? []).map((id) => {
                            const item = (S.draft?.items ?? []).find((entry) => Number(entry.id) === Number(id));
                            return item ? `<span class="pill">${E(item.name)}</span>` : '';
                        }).join('')
                        : '';

                return `<article class="entity-card">
                    <strong>${E(label)}</strong>
                    <p>${E(detail)}</p>
                    <div class="pill-row">${extra}</div>
                    <button class="btn danger" type="button" data-action="delete-entity" data-type="${E(type)}" data-id="${entity.id}">Supprimer</button>
                </article>`;
            }).join('');
        }

        function renderReview() {
            const steps = Array.isArray(S.draft?.steps) ? S.draft.steps : [];

            if (!steps.length) {
                N.reviewList.innerHTML = '<div class="empty-state">Aucune information de progression n est disponible pour ce draft.</div>';
                return;
            }

            const cards = steps.map((step) => {
                const good = step.status === 'complete';
                const snapshot = step.payload_snapshot && typeof step.payload_snapshot === 'object'
                    ? Object.entries(step.payload_snapshot)
                        .slice(0, 4)
                        .map(([key, value]) => `<span class="pill">${E(key)}: ${E(Array.isArray(value) ? value.length : value)}</span>`)
                        .join('')
                    : '';

                return `<article class="step-card ${good ? 'good' : 'warn'}">
                    <strong>${E(step.step_key)}</strong>
                    <div class="pill-row">
                        <span class="pill">${E(step.status)}</span>
                        <span class="pill">${E(step.blocking_errors_count ?? 0)} erreur(s)</span>
                        <span class="pill">${E(step.warnings_count ?? 0)} warning(s)</span>
                    </div>
                    <div class="pill-row">${snapshot}</div>
                </article>`;
            }).join('');

            const publishCard = S.publishResult
                ? `<article class="note"><strong>${E(S.publishResult.game?.name ?? 'Publication réussie')}</strong><p>Le draft a été converti en runtime et rattaché au jeu ${E(S.publishResult.game?.slug ?? '')}.</p></article>`
                : '';

            N.reviewList.innerHTML = cards + publishCard;
        }

        function setEditorDisabled(disabled) {
            const elements = N.editorRoot.querySelectorAll('input, textarea, select, button');
            elements.forEach((element) => {
                element.disabled = disabled;
            });
        }

        function renderEditor() {
            const draft = S.draft;
            const hasDraft = !!draft;

            N.editorEmpty.style.display = hasDraft ? 'none' : 'block';
            N.editorRoot.style.display = hasDraft ? 'grid' : 'none';

            if (!hasDraft) {
                return;
            }

            const template = templateFor(draft.category_key);
            const settings = currentSettings();
            const isPublished = draft.status === 'published' || draft.published_game_id !== null;

            N.draftTitle.textContent = draft.name ?? 'Brouillon';
            N.draftSubtitle.textContent = template ? `${template.label} v${template.version} - ${template.description}` : 'Template indisponible.';
            N.draftProgress.textContent = `${draft.completion_percent ?? 0}%`;
            N.draftStep.textContent = draft.current_step ?? 'foundation';
            N.draftStatus.textContent = draft.status ?? 'draft';
            N.draftPillRow.innerHTML = [
                `<span class="pill">${E(draft.category_key)}</span>`,
                `<span class="pill">${E(draft.visibility ?? 'private')}</span>`,
                `<span class="pill">${E(draft.slug)}</span>`,
                draft.published_game_id ? `<span class="pill">published game #${E(draft.published_game_id)}</span>` : '',
            ].join('');

            N.foundationName.value = draft.name ?? '';
            N.foundationSlug.value = draft.slug ?? '';
            N.foundationDescription.value = draft.description ?? '';
            N.foundationVisibility.value = draft.visibility ?? 'private';

            N.supportsModifiers.checked = Boolean(settings.supports_modifiers);
            N.supportsArchetypes.checked = Boolean(settings.supports_archetypes);

            renderCheckboxOptions(N.structureItemSlots, Array.isArray(template?.allowed_item_slots) ? template.allowed_item_slots : [], settings.allowed_item_slots ?? [], 'structure-item-slot');
            renderCheckboxOptions(N.structureModifierTypes, Array.isArray(template?.allowed_modifier_types) ? template.allowed_modifier_types : [], settings.allowed_modifier_types ?? [], 'structure-modifier-type');

            N.itemSlot.innerHTML = (settings.allowed_item_slots ?? []).length
                ? settings.allowed_item_slots.map((slot) => `<option value="${E(slot)}">${E(slot)}</option>`).join('')
                : '<option value="">Aucun slot actif</option>';

            N.modifierType.innerHTML = (settings.allowed_modifier_types ?? []).length
                ? settings.allowed_modifier_types.map((type) => `<option value="${E(type)}">${E(type)}</option>`).join('')
                : '<option value="">Aucun type actif</option>';

            N.itemCategoryOptions.innerHTML = (draft.categories ?? []).length
                ? draft.categories.map((category) => `<label class="check-option"><input type="checkbox" name="item-category" value="${category.id}"><span><strong>${E(category.label)}</strong><small class="helper">${E(category.slug)}</small></span></label>`).join('')
                : '<div class="empty-state">Ajoute d’abord des catégories pour pouvoir classer les items.</div>';

            N.modifierItemOptions.innerHTML = (draft.items ?? []).length
                ? draft.items.map((item) => `<label class="check-option"><input type="checkbox" name="modifier-item" value="${item.id}"><span><strong>${E(item.name)}</strong><small class="helper">${E(item.slot)}</small></span></label>`).join('')
                : '<div class="empty-state">Ajoute d’abord des items pour déclarer leurs compatibilités.</div>';

            N.modifierDisabled.style.display = settings.supports_modifiers ? 'none' : 'block';
            N.modifierForm.style.display = settings.supports_modifiers ? 'grid' : 'none';

            renderEntityList(N.archetypeList, draft.archetypes ?? [], 'archetype');
            renderEntityList(N.categoryList, draft.categories ?? [], 'category');
            renderEntityList(N.itemList, draft.items ?? [], 'item');
            renderEntityList(N.modifierList, draft.modifiers ?? [], 'modifier');
            renderReview();

            setEditorDisabled(isPublished);
            N.deleteDraft.disabled = isPublished;
            N.publishDraft.disabled = isPublished;

            if (isPublished) {
                status('Ce draft est déjà publié. Les formulaires sont figés pour éviter les modifications destructives.');
            }
        }

        function resetForm(form) {
            form.reset();
        }

        async function createDraft(event) {
            event.preventDefault();
            const name = N.createName.value.trim();
            const slug = N.createSlug.value.trim() || slugify(name);

            const payload = await api('/api/v1/game-drafts', {
                method: 'POST',
                body: {
                    category_key: N.createCategoryKey.value,
                    name,
                    slug,
                },
            });

            resetForm(N.createDraftForm);
            await refresh(payload?.data?.id ?? null, 'Brouillon créé.');
        }

        async function saveFoundation(event) {
            event.preventDefault();
            if (!S.selectedDraftId) {
                return;
            }

            await api(`/api/v1/game-drafts/${S.selectedDraftId}/foundation`, {
                method: 'PATCH',
                body: {
                    name: N.foundationName.value.trim(),
                    slug: N.foundationSlug.value.trim(),
                    description: N.foundationDescription.value.trim(),
                    visibility: N.foundationVisibility.value,
                },
            });

            await refresh(S.selectedDraftId, 'Foundation enregistrée.');
        }

        async function saveStructure(event) {
            event.preventDefault();
            if (!S.selectedDraftId) {
                return;
            }

            await api(`/api/v1/game-drafts/${S.selectedDraftId}/structure`, {
                method: 'PATCH',
                body: {
                    supports_modifiers: N.supportsModifiers.checked,
                    supports_archetypes: N.supportsArchetypes.checked,
                    allowed_item_slots: selectedIds('input[name="structure-item-slot"]'),
                    allowed_modifier_types: selectedIds('input[name="structure-modifier-type"]'),
                },
            });

            await refresh(S.selectedDraftId, 'Structure enregistrée.');
        }

        async function addArchetype(event) {
            event.preventDefault();
            if (!S.selectedDraftId) {
                return;
            }

            await api(`/api/v1/game-drafts/${S.selectedDraftId}/archetypes`, {
                method: 'POST',
                body: {
                    name: N.archetypeName.value.trim(),
                    slug: N.archetypeSlug.value.trim() || slugify(N.archetypeName.value),
                    description: N.archetypeDescription.value.trim(),
                },
            });

            resetForm(N.archetypeForm);
            await refresh(S.selectedDraftId, 'Archetype ajouté.');
        }

        async function addCategory(event) {
            event.preventDefault();
            if (!S.selectedDraftId) {
                return;
            }

            await api(`/api/v1/game-drafts/${S.selectedDraftId}/categories`, {
                method: 'POST',
                body: {
                    label: N.categoryLabel.value.trim(),
                    slug: N.categorySlug.value.trim() || slugify(N.categoryLabel.value),
                    description: N.categoryDescription.value.trim(),
                },
            });

            resetForm(N.categoryForm);
            await refresh(S.selectedDraftId, 'Catégorie ajoutée.');
        }

        async function addItem(event) {
            event.preventDefault();
            if (!S.selectedDraftId) {
                return;
            }

            await api(`/api/v1/game-drafts/${S.selectedDraftId}/items`, {
                method: 'POST',
                body: {
                    name: N.itemName.value.trim(),
                    slug: N.itemSlug.value.trim() || slugify(N.itemName.value),
                    slot: N.itemSlot.value,
                    rarity: N.itemRarity.value,
                    tier_level: N.itemTier.value.trim() || null,
                    score: intOrNull(N.itemScore.value) ?? 0,
                    attack: intOrNull(N.itemAttack.value),
                    defense: intOrNull(N.itemDefense.value),
                    utility: intOrNull(N.itemUtility.value),
                    accuracy: intOrNull(N.itemAccuracy.value),
                    mobility: intOrNull(N.itemMobility.value),
                    category_ids: selectedIds('input[name="item-category"]'),
                },
            });

            resetForm(N.itemForm);
            await refresh(S.selectedDraftId, 'Item ajouté.');
        }

        async function addModifier(event) {
            event.preventDefault();
            if (!S.selectedDraftId) {
                return;
            }

            await api(`/api/v1/game-drafts/${S.selectedDraftId}/modifiers`, {
                method: 'POST',
                body: {
                    name: N.modifierName.value.trim(),
                    slug: N.modifierSlug.value.trim() || slugify(N.modifierName.value),
                    modifier_type: N.modifierType.value,
                    description: N.modifierDescription.value.trim(),
                    delta_score: intOrNull(N.modifierScore.value) ?? 0,
                    delta_attack: intOrNull(N.modifierAttack.value),
                    delta_defense: intOrNull(N.modifierDefense.value),
                    delta_utility: intOrNull(N.modifierUtility.value),
                    delta_accuracy: intOrNull(N.modifierAccuracy.value),
                    delta_mobility: intOrNull(N.modifierMobility.value),
                    compatible_item_ids: selectedIds('input[name="modifier-item"]'),
                },
            });

            resetForm(N.modifierForm);
            await refresh(S.selectedDraftId, 'Modifier ajouté.');
        }

        async function publishDraft() {
            if (!S.selectedDraftId) {
                return;
            }

            const payload = await api(`/api/v1/game-drafts/${S.selectedDraftId}/publish`, {
                method: 'POST',
            });

            await refresh(S.selectedDraftId, 'Draft publié.');
            S.publishResult = payload.data ?? null;
            renderReview();
        }

        async function deleteDraft() {
            if (!S.selectedDraftId || !window.confirm('Supprimer ce draft ?')) {
                return;
            }

            await api(`/api/v1/game-drafts/${S.selectedDraftId}`, {
                method: 'DELETE',
            });

            const nextDraft = S.drafts.find((draft) => Number(draft.id) !== Number(S.selectedDraftId));
            await refresh(nextDraft?.id ?? null, 'Draft supprimé.');
        }

        async function deleteEntity(type, id) {
            if (!S.selectedDraftId || !window.confirm('Supprimer cet element ?')) {
                return;
            }

            const routes = {
                archetype: `/api/v1/game-drafts/${S.selectedDraftId}/archetypes/${id}`,
                category: `/api/v1/game-drafts/${S.selectedDraftId}/categories/${id}`,
                item: `/api/v1/game-drafts/${S.selectedDraftId}/items/${id}`,
                modifier: `/api/v1/game-drafts/${S.selectedDraftId}/modifiers/${id}`,
            };

            if (!routes[type]) {
                return;
            }

            await api(routes[type], {
                method: 'DELETE',
            });

            await refresh(S.selectedDraftId, `${type} supprimé.`);
        }

        document.addEventListener('click', async (event) => {
            const target = event.target.closest('[data-action]');
            if (!target) {
                return;
            }

            try {
                if (target.dataset.action === 'select-draft') {
                    await loadDraft(Number(target.dataset.id), 'Brouillon chargé.');
                }

                if (target.dataset.action === 'delete-entity') {
                    await deleteEntity(target.dataset.type, Number(target.dataset.id));
                }
            } catch (error) {
                status(error.message, 'danger');
            }
        });

        N.createDraftForm.addEventListener('submit', async (event) => {
            try {
                await createDraft(event);
            } catch (error) {
                status(error.message, 'danger');
            }
        });

        N.foundationForm.addEventListener('submit', async (event) => {
            try {
                await saveFoundation(event);
            } catch (error) {
                status(error.message, 'danger');
            }
        });

        N.structureForm.addEventListener('submit', async (event) => {
            try {
                await saveStructure(event);
            } catch (error) {
                status(error.message, 'danger');
            }
        });

        N.archetypeForm.addEventListener('submit', async (event) => {
            try {
                await addArchetype(event);
            } catch (error) {
                status(error.message, 'danger');
            }
        });

        N.categoryForm.addEventListener('submit', async (event) => {
            try {
                await addCategory(event);
            } catch (error) {
                status(error.message, 'danger');
            }
        });

        N.itemForm.addEventListener('submit', async (event) => {
            try {
                await addItem(event);
            } catch (error) {
                status(error.message, 'danger');
            }
        });

        N.modifierForm.addEventListener('submit', async (event) => {
            try {
                await addModifier(event);
            } catch (error) {
                status(error.message, 'danger');
            }
        });

        N.deleteDraft.addEventListener('click', async () => {
            try {
                await deleteDraft();
            } catch (error) {
                status(error.message, 'danger');
            }
        });

        N.publishDraft.addEventListener('click', async () => {
            try {
                await publishDraft();
            } catch (error) {
                status(error.message, 'danger');
            }
        });

        N.createName.addEventListener('input', () => {
            if (!N.createSlug.value.trim()) {
                N.createSlug.value = slugify(N.createName.value);
            }
        });
        N.archetypeName.addEventListener('input', () => {
            if (!N.archetypeSlug.value.trim()) {
                N.archetypeSlug.value = slugify(N.archetypeName.value);
            }
        });
        N.categoryLabel.addEventListener('input', () => {
            if (!N.categorySlug.value.trim()) {
                N.categorySlug.value = slugify(N.categoryLabel.value);
            }
        });
        N.itemName.addEventListener('input', () => {
            if (!N.itemSlug.value.trim()) {
                N.itemSlug.value = slugify(N.itemName.value);
            }
        });
        N.modifierName.addEventListener('input', () => {
            if (!N.modifierSlug.value.trim()) {
                N.modifierSlug.value = slugify(N.modifierName.value);
            }
        });

        (async function boot() {
            try {
                renderDrafts();

                if (S.selectedDraftId) {
                    await loadDraft(S.selectedDraftId, 'Brouillon chargé.');
                } else {
                    renderEditor();
                    status('Crée un draft ou ouvre un brouillon existant pour lancer le parcours de création.');
                }

                await loadDrafts();
            } catch (error) {
                status(error.message, 'danger');
            }
        }());
    </script>
</x-layouts.app>
