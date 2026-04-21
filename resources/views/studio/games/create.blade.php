<x-layouts.app :title="__('Créer un jeu - Wiki Builder API')" bodyClass="page-studio" shellClass="shell-studio" mainClass="content-studio">

    {{-- ═══════════════ MODAL DE CRÉATION ═══════════════ --}}
    <div id="creation-modal" class="creation-modal-overlay" hidden>
        <div class="creation-modal-dialog">
            <div class="creation-modal-head">
                <h2 id="modal-title"></h2>
                <div class="creation-modal-dots" id="modal-dots"></div>
                <button class="btn ghost" type="button" data-action="close-modal">&times;</button>
            </div>
            <div id="modal-body" class="creation-modal-body"></div>
            <div class="creation-modal-foot">
                <button id="modal-back" class="btn ghost" type="button" hidden>{{ __('Retour') }}</button>
                <span style="flex:1"></span>
                <button id="modal-next" class="btn primary" type="button">{{ __('Continuer') }}</button>
            </div>
        </div>
    </div>
    <style>
        .creation-modal-overlay{position:fixed;inset:0;z-index:900;background:rgba(5,12,25,.78);display:flex;align-items:center;justify-content:center;padding:24px;backdrop-filter:blur(4px)}
        .creation-modal-overlay[hidden]{display:none}
        .creation-modal-dialog{background:var(--bg-card,#0f1b30);border:1px solid var(--border);border-radius:18px;width:100%;max-width:620px;max-height:85vh;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 24px 64px rgba(0,0,0,.5)}
        .creation-modal-head{display:flex;align-items:center;gap:12px;padding:20px 24px 14px;border-bottom:1px solid var(--border)}
        .creation-modal-head h2{margin:0;font-size:20px;flex:1}
        .creation-modal-dots{display:flex;gap:6px}
        .creation-modal-dots .dot{width:8px;height:8px;border-radius:50%;background:var(--border);transition:background .2s}
        .creation-modal-dots .dot.active{background:var(--accent)}
        .creation-modal-dots .dot.done{background:var(--success,#22c55e)}
        .creation-modal-body{padding:24px;overflow-y:auto;flex:1}
        .creation-modal-body .template-grid{margin-bottom:20px}
        .creation-modal-body .field{margin-bottom:14px}
        .creation-modal-body .option-grid{margin-bottom:18px}
        .creation-modal-foot{display:flex;align-items:center;gap:12px;padding:14px 24px 20px;border-top:1px solid var(--border)}
        .modal-step-copy{margin:0 0 18px}
        .modal-pill-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px}
        .modal-check-list{display:grid;gap:10px}
        .modal-muted{opacity:.72}
        .modal-review-list{display:grid;gap:10px;margin-top:16px}
        .studio-stage>.wizard-stack{display:none}
        .studio-stage{min-height:220px;display:flex;align-items:center}
        .studio-stage .studio-status{width:100%;font-size:16px}
    </style>

    <section class="page-head">
        <div>
            <span class="eyebrow">{{ __('Studio') }}</span>
            <h1>{{ __('Créateur de jeux') }}</h1>
            <span class="subtitle">{{ __('Tactical Game Editor') }}</span>
            <hr class="neon-line">
            <p class="helper">{{ __('Crée ton jeu étape par étape, ajoute du contenu, puis publie-le.') }}</p>
        </div>
        <div class="page-head-actions">
            <a class="btn ghost" href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
            <a class="btn subtle" href="{{ route('home') }}">{{ __('Builder') }}</a>
        </div>
    </section>

    <div class="studio-layout">
        <aside class="panel studio-sidebar">
            <section class="sidebar-block">
                <span class="eyebrow">{{ __('Jeu en cours') }}</span>
                <div id="draft-summary" class="draft-summary empty">{{ __('Crée un nouveau jeu pour commencer.') }}</div>
            </section>

            <section class="sidebar-block">
                <div class="sidebar-head">
                    <h3>{{ __('Progression') }}</h3>
                    <button id="start-new-draft" class="btn ghost" type="button">{{ __('Nouveau jeu') }}</button>
                </div>
                <div id="step-nav" class="step-nav"></div>
            </section>

            <section class="sidebar-block">
                <div class="sidebar-head">
                    <h3>{{ __('Mes jeux en cours') }}</h3>
                </div>
                <div id="draft-list" class="draft-list"></div>
            </section>
        </aside>

        <section class="panel studio-stage">
            <div id="studio-status" class="flash studio-status" role="status" aria-live="polite">{{ __('Crée un nouveau jeu ou sélectionne un jeu existant.') }}</div>

            <div class="wizard-stack">
                <section class="stage-card" data-step="template">
                    <div class="stage-intro">
                        <span class="eyebrow">{{ __('Template') }}</span>
                        <h2>{{ __('Choisir le cadre du jeu') }}</h2>
                        <p class="helper">{{ __("Choisis un template de jeu.") }}</p>
                    </div>
                    <div id="template-grid" class="template-grid"></div>
                    <div id="template-preview" class="template-preview"></div>
                    <form id="create-draft-form" class="item-form-grid">
                        <div class="field">
                            <label for="create-name">{{ __('Nom du jeu') }}</label>
                            <input id="create-name" type="text" placeholder="{{ __('Mon arena shooter') }}" required>
                        </div>
                        <div class="field">
                            <label for="create-slug">Slug</label>
                            <input id="create-slug" type="text" placeholder="mon-arena-shooter">
                        </div>
                        <div class="field span-2">
                            <div class="wizard-actions">
                                <p class="helper compact">{{ __('Le jeu démarre en privé.') }}</p>
                                <button class="btn primary" type="submit">{{ __('Démarrer le parcours') }}</button>
                            </div>
                        </div>
                    </form>
                </section>

                <section class="stage-card" data-step="foundation" hidden>
                    <div class="stage-intro">
                        <span class="eyebrow">{{ __('Base') }}</span>
                        <h2>{{ __('Informations du jeu') }}</h2>
                        <p class="helper">{{ __('Nom, slug, description, visibilité.') }}</p>
                    </div>
                    <form id="foundation-form" class="item-form-grid">
                        <div class="field"><label for="foundation-name">{{ __('Nom du jeu') }}</label><input id="foundation-name" type="text" required></div>
                        <div class="field"><label for="foundation-slug">Slug</label><input id="foundation-slug" type="text" required></div>
                        <div class="field span-2"><label for="foundation-description">Description</label><textarea id="foundation-description" placeholder="{{ __('Description du jeu') }}"></textarea></div>
                        <div class="field"><label for="foundation-visibility">{{ __('Visibilité') }}</label><select id="foundation-visibility"><option value="private">{{ __('Privé') }}</option><option value="public">{{ __('Public') }}</option></select></div>
                        <div class="field span-2"><div class="wizard-actions"><div class="autosave-row"><span id="foundation-save-state">{{ __('Autosave inactif') }}</span></div><div class="inline-tools"><button id="save-foundation" class="btn ghost" type="submit">{{ __('Enregistrer maintenant') }}</button><button class="btn primary" type="button" data-go-step="structure">{{ __('Continuer') }}</button></div></div></div>
                    </form>
                </section>

                <section class="stage-card" data-step="structure" hidden>
                    <div class="stage-intro">
                        <span class="eyebrow">{{ __('Structure') }}</span>
                        <h2>{{ __('Options du jeu') }}</h2>
                        <p class="helper">{{ __('Configure les slots et types de modificateurs autorisés.') }}</p>
                    </div>
                    <div id="structure-lock-note" class="compact-note warn" hidden>{{ __('Certains choix sont verrouillés après ajout d\'items ou de modificateurs.') }}</div>
                    <form id="structure-form" class="wizard-stack">
                        <div class="option-grid">
                            <label class="option-card"><input id="supports-modifiers" type="checkbox"><span><strong>{{ __('Modificateurs') }}</strong><small>{{ __('Accessoires, runes ou effets reliés aux items.') }}</small></span></label>
                            <label class="option-card"><input id="supports-archetypes" type="checkbox"><span><strong>{{ __('Archétypes / Profils') }}</strong><small>{{ __('Classes ou styles de jeu personnalisés.') }}</small></span></label>
                        </div>
                        <div class="structure-preview"><div class="card-head"><h2>{{ __("Slots d'items actifs") }}</h2></div><div id="structure-item-slots" class="checkbox-grid"></div></div>
                        <div class="structure-preview"><div class="card-head"><h2>{{ __('Types de modifiers actifs') }}</h2></div><div id="structure-modifier-types" class="checkbox-grid"></div></div>
                        <div class="wizard-actions"><div class="autosave-row"><span id="structure-save-state">{{ __('Autosave inactif') }}</span></div><div class="inline-tools"><button id="save-structure" class="btn ghost" type="submit">{{ __('Enregistrer maintenant') }}</button><button class="btn primary" type="button" data-go-step="taxonomy">{{ __('Continuer') }}</button></div></div>
                    </form>
                </section>

                <section class="stage-card" data-step="taxonomy" hidden>
                    <div class="stage-intro">
                        <span class="eyebrow">{{ __('Classification') }}</span>
                        <h2>{{ __('Profils et catégories') }}</h2>
                        <p class="helper">{{ __('Définis les profils et catégories de ton jeu.') }}</p>
                    </div>
                    <div class="taxonomy-grid">
                        <article class="stage-card">
                            <div class="card-head"><div><h2>{{ __('Archetypes') }}</h2><p class="helper compact">{{ __('Profils, classes ou styles de jeu.') }}</p></div><button id="apply-archetype-starters" class="btn ghost" type="button">{{ __('Suggestions') }}</button></div>
                            <div id="archetype-disabled-note" class="compact-note" hidden>{{ __('Les archetypes sont desactives dans la structure actuelle.') }}</div>
                            <form id="archetype-form" class="wizard-stack">
                                <div class="field"><label for="archetype-name">Nom</label><input id="archetype-name" type="text" placeholder="Assaillant"></div>
                                <div class="field"><label for="archetype-slug">Slug</label><input id="archetype-slug" type="text" placeholder="assaillant"></div>
                                <div class="field"><label for="archetype-description">Description</label><textarea id="archetype-description" placeholder="{{ __('Description du profil') }}"></textarea></div>
                                <div class="inline-tools"><button id="save-archetype" class="btn primary" type="submit">{{ __('Ajouter') }}</button><button id="cancel-archetype-edit" class="btn subtle" type="button" hidden>{{ __('Annuler') }}</button></div>
                            </form>
                            <div id="archetype-list" class="entity-list"></div>
                        </article>
                        <article class="stage-card">
                            <div class="card-head"><div><h2>{{ __('Catégories') }}</h2><p class="helper compact">{{ __('Regroupements visuels et fonctionnels des items.') }}</p></div><button id="apply-category-starters" class="btn ghost" type="button">{{ __('Suggestions') }}</button></div>
                            <form id="category-form" class="wizard-stack">
                                <div class="field"><label for="category-label">Label</label><input id="category-label" type="text" placeholder="Assault rifle"></div>
                                <div class="field"><label for="category-slug">Slug</label><input id="category-slug" type="text" placeholder="assault-rifle"></div>
                                <div class="field"><label for="category-description">Description</label><textarea id="category-description" placeholder="{{ __('A quoi sert cette categorie dans le jeu ?') }}"></textarea></div>
                                <div class="inline-tools"><button id="save-category" class="btn primary" type="submit">{{ __('Ajouter') }}</button><button id="cancel-category-edit" class="btn subtle" type="button" hidden>{{ __('Annuler') }}</button></div>
                            </form>
                            <div id="category-list" class="entity-list"></div>
                        </article>
                    </div>
                    <div class="wizard-actions"><button class="btn ghost" type="button" data-go-step="structure">{{ __('Retour') }}</button><button class="btn primary" type="button" data-go-step="items">{{ __('Continuer') }}</button></div>
                </section>

                <section class="stage-card" data-step="items" hidden>
                    <div class="stage-intro">
                        <span class="eyebrow">{{ __('Items') }}</span>
                        <h2>{{ __('Equipements du jeu') }}</h2>
                        <p class="helper">{{ __('Ajoute les items, leurs stats et leurs slots internes.') }}</p>
                    </div>
                    <form id="item-form" class="wizard-stack">
                        <div class="item-form-grid">
                            <div class="field"><label for="item-name">Nom</label><input id="item-name" type="text" placeholder="VX-7"></div>
                            <div class="field"><label for="item-slug">Slug</label><input id="item-slug" type="text" placeholder="vx-7"></div>
                            <div class="field"><label for="item-slot">Slot global</label><select id="item-slot"></select></div>
                            <div class="field"><label for="item-rarity">{{ __('Rarete') }}</label><select id="item-rarity"><option value="standard">standard</option><option value="rare">rare</option><option value="epic">epic</option><option value="legendary">legendary</option></select></div>
                            <div class="field"><label for="item-tier">Tier</label><input id="item-tier" type="text" placeholder="T1"></div>
                            <div class="field"><label for="item-score">Score</label><input id="item-score" type="number" value="0"></div>
                            <div class="field"><label for="item-attack">Attack</label><input id="item-attack" type="number" value="0"></div>
                            <div class="field"><label for="item-defense">Defense</label><input id="item-defense" type="number" value="0"></div>
                            <div class="field"><label for="item-utility">Utility</label><input id="item-utility" type="number" value="0"></div>
                            <div class="field"><label for="item-accuracy">Accuracy</label><input id="item-accuracy" type="number" value="0"></div>
                            <div class="field"><label for="item-mobility">Mobility</label><input id="item-mobility" type="number" value="0"></div>
                        </div>
                        <div class="structure-preview">
                            <div class="card-head"><h2>Categories</h2></div>
                            <div id="item-category-options" class="checkbox-grid"></div>
                        </div>
                        <div id="item-slots-card" class="structure-preview">
                            <div class="card-head">
                                <div>
                                    <h2>{{ __('Slots internes') }}</h2>
                                    <p class="helper compact">{{ __('Utiles si cet item doit recevoir des modifiers compatibles.') }}</p>
                                </div>
                                <div class="slot-tools">
                                    <button id="apply-item-slot-preset" class="btn ghost" type="button">{{ __('Appliquer un preset') }}</button>
                                    <button id="add-item-slot-row" class="btn ghost" type="button">{{ __('Ajouter un slot') }}</button>
                                </div>
                            </div>
                            <div id="item-slot-list" class="slot-list"></div>
                        </div>
                        <div class="inline-tools">
                            <button id="save-item" class="btn primary" type="submit">{{ __("Ajouter l'item") }}</button>
                            <button id="cancel-item-edit" class="btn subtle" type="button" hidden>{{ __('Annuler') }}</button>
                        </div>
                    </form>
                    <div id="item-list" class="entity-list"></div>
                    <div class="wizard-actions"><button class="btn ghost" type="button" data-go-step="taxonomy">{{ __('Retour') }}</button><button class="btn primary" type="button" data-go-step="modifiers">{{ __('Continuer') }}</button></div>
                </section>

                <section class="stage-card" data-step="modifiers" hidden>
                    <div class="stage-intro">
                        <span class="eyebrow">{{ __('Modifiers') }}</span>
                        <h2>{{ __('Accessoires et effets') }}</h2>
                        <p class="helper">{{ __('Relie les modifiers aux items compatibles.') }}</p>
                    </div>
                    <div id="modifier-disabled-note" class="compact-note" hidden>{{ __('Les modificateurs sont désactivés dans les options du jeu.') }}</div>
                    <form id="modifier-form" class="wizard-stack">
                        <div class="modifier-form-grid">
                            <div class="field"><label for="modifier-name">Nom</label><input id="modifier-name" type="text" placeholder="Red Dot X"></div>
                            <div class="field"><label for="modifier-slug">Slug</label><input id="modifier-slug" type="text" placeholder="red-dot-x"></div>
                            <div class="field"><label for="modifier-type">Type</label><select id="modifier-type"></select></div>
                            <div class="field"><label for="modifier-score">Delta score</label><input id="modifier-score" type="number" value="0"></div>
                            <div class="field"><label for="modifier-attack">Delta attack</label><input id="modifier-attack" type="number" value="0"></div>
                            <div class="field"><label for="modifier-defense">Delta defense</label><input id="modifier-defense" type="number" value="0"></div>
                            <div class="field"><label for="modifier-utility">Delta utility</label><input id="modifier-utility" type="number" value="0"></div>
                            <div class="field"><label for="modifier-accuracy">Delta accuracy</label><input id="modifier-accuracy" type="number" value="0"></div>
                            <div class="field"><label for="modifier-mobility">Delta mobility</label><input id="modifier-mobility" type="number" value="0"></div>
                            <div class="field span-2"><label for="modifier-description">Description</label><textarea id="modifier-description" placeholder="{{ __('Effet, compromis, role dans le build...') }}"></textarea></div>
                        </div>
                        <div class="structure-preview">
                            <div class="card-head"><h2>{{ __('Items compatibles') }}</h2></div>
                            <div id="modifier-item-options" class="checkbox-grid"></div>
                        </div>
                        <div class="inline-tools">
                            <button id="save-modifier" class="btn primary" type="submit">{{ __('Ajouter le modifier') }}</button>
                            <button id="cancel-modifier-edit" class="btn subtle" type="button" hidden>{{ __('Annuler') }}</button>
                        </div>
                    </form>
                    <div id="modifier-list" class="entity-list"></div>
                    <div class="wizard-actions"><button class="btn ghost" type="button" data-go-step="items">{{ __('Retour') }}</button><button class="btn primary" type="button" data-go-step="review">{{ __('Continuer') }}</button></div>
                </section>

                <section class="stage-card" data-step="review" hidden>
                    <div class="stage-intro">
                        <span class="eyebrow">{{ __('Publication') }}</span>
                        <h2>{{ __('Validation et publication') }}</h2>
                        <p class="helper">{{ __('Corrige les points bloquants puis publie ton jeu.') }}</p>
                    </div>
                    <div class="review-grid">
                        <article class="review-summary">
                            <span class="eyebrow">{{ __('Synthese') }}</span>
                            <strong id="review-completion">0%</strong>
                            <p id="review-subtitle" class="helper compact">{{ __('Aucune donnee pour le moment.') }}</p>
                            <div class="summary-metrics">
                                <article class="summary-metric"><strong id="review-items-count">0</strong><span>{{ __('Items') }}</span></article>
                                <article class="summary-metric"><strong id="review-modifiers-count">0</strong><span>{{ __('Modifiers') }}</span></article>
                                <article class="summary-metric"><strong id="review-errors-count">0</strong><span>{{ __('Bloquants') }}</span></article>
                            </div>
                        </article>
                        <article class="review-summary">
                            <span class="eyebrow">{{ __('Publication') }}</span>
                            <strong id="publish-state-label">{{ __('En attente') }}</strong>
                            <p id="publish-state-copy" class="helper compact">{{ __('Le jeu doit être complet avant publication.') }}</p>
                            <div class="inline-tools"><button id="publish-draft" class="btn primary" type="button">{{ __('Publier le jeu') }}</button></div>
                        </article>
                    </div>
                    <div id="review-list" class="review-list"></div>
                    <div class="wizard-actions"><button class="btn ghost" type="button" data-go-step="modifiers">{{ __('Retour') }}</button></div>
                </section>
            </div>
        </section>
    </div>

    <section class="panel section-card">
        <div class="section-head"><div class="section-copy"><span class="eyebrow">{{ __('Publies') }}</span><h2>{{ __('Mes jeux recents') }}</h2></div></div>
        <div class="published-grid">
            @forelse ($publishedGames as $game)
                <article class="published-tile">
                    <h4>{{ $game->name }}</h4>
                    <div class="entity-meta"><span class="tag">{{ $game->visibility }}</span><span class="tag">{{ $game->creation_category ?: 'system' }}</span></div>
                    <p>{{ __('Publie le') }} {{ $game->published_at?->format('d/m/Y H:i') ?: __('non date') }}</p>
                </article>
            @empty
                <div class="empty-soft">{{ __('Aucun jeu utilisateur publie pour le moment.') }}</div>
            @endforelse
        </div>
    </section>

    <script>
        const BOOT = {
            studioBase: @json(url('/studio/api/v1')),
            csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '',
            templates: @json($templates),
            drafts: @json($drafts),
            selectedDraftId: @json($selectedDraftId),
        };

        const WIZARD_STEPS = ['template', 'foundation', 'structure', 'taxonomy', 'items', 'modifiers', 'review'];
        const STEP_LABELS = {template: 'Template', foundation: 'Base', structure: 'Options', taxonomy: 'Profils et cat\u00e9gories', items: 'Items', modifiers: 'Modificateurs', review: 'Publication'};
        const STEP_DESCRIPTIONS = {template: 'Choisir le cadre initial', foundation: 'Nom, slug, description, visibilit\u00e9', structure: 'Slots et types de modificateurs', taxonomy: 'Profils et classification du jeu', items: '\u00c9quipements et slots internes', modifiers: 'Compatibilit\u00e9s et effets', review: 'Validation et publication'};
        const STEP_ORDER = {foundation: 1, structure: 2, archetypes: 3, categories: 4, items: 5, modifiers: 6, review: 7};

        const S = {
            templates: Array.isArray(BOOT.templates) ? BOOT.templates : [],
            drafts: Array.isArray(BOOT.drafts) ? BOOT.drafts : [],
            draft: null,
            selectedDraftId: BOOT.selectedDraftId ?? null,
            activeStep: BOOT.selectedDraftId ? 'foundation' : 'template',
            publishResult: null,
            foundationTimer: null,
            structureTimer: null,
            foundationState: 'Autosave inactif',
            structureState: 'Autosave inactif',
            itemSlotsDraft: [],
            createSlugTouched: false,
            foundationSlugTouched: false,
            archetypeSlugTouched: false,
            categorySlugTouched: false,
            itemSlugTouched: false,
            modifierSlugTouched: false,
            editing: {archetypeId: null, categoryId: null, itemId: null, modifierId: null},
            modal: {open: false, stepKey: 'name', busy: false, data: {}},
        };

        const N = {
            status: document.getElementById('studio-status'),
            draftSummary: document.getElementById('draft-summary'),
            stepNav: document.getElementById('step-nav'),
            draftList: document.getElementById('draft-list'),
            startNewDraft: document.getElementById('start-new-draft'),
            templateGrid: document.getElementById('template-grid'),
            templatePreview: document.getElementById('template-preview'),
            createDraftForm: document.getElementById('create-draft-form'),
            createName: document.getElementById('create-name'),
            createSlug: document.getElementById('create-slug'),
            foundationForm: document.getElementById('foundation-form'),
            foundationName: document.getElementById('foundation-name'),
            foundationSlug: document.getElementById('foundation-slug'),
            foundationDescription: document.getElementById('foundation-description'),
            foundationVisibility: document.getElementById('foundation-visibility'),
            foundationSaveState: document.getElementById('foundation-save-state'),
            structureForm: document.getElementById('structure-form'),
            structureLockNote: document.getElementById('structure-lock-note'),
            supportsModifiers: document.getElementById('supports-modifiers'),
            supportsArchetypes: document.getElementById('supports-archetypes'),
            structureItemSlots: document.getElementById('structure-item-slots'),
            structureModifierTypes: document.getElementById('structure-modifier-types'),
            structureSaveState: document.getElementById('structure-save-state'),
            archetypeDisabledNote: document.getElementById('archetype-disabled-note'),
            applyArchetypeStarters: document.getElementById('apply-archetype-starters'),
            archetypeForm: document.getElementById('archetype-form'),
            archetypeName: document.getElementById('archetype-name'),
            archetypeSlug: document.getElementById('archetype-slug'),
            archetypeDescription: document.getElementById('archetype-description'),
            saveArchetype: document.getElementById('save-archetype'),
            cancelArchetypeEdit: document.getElementById('cancel-archetype-edit'),
            archetypeList: document.getElementById('archetype-list'),
            applyCategoryStarters: document.getElementById('apply-category-starters'),
            categoryForm: document.getElementById('category-form'),
            categoryLabel: document.getElementById('category-label'),
            categorySlug: document.getElementById('category-slug'),
            categoryDescription: document.getElementById('category-description'),
            saveCategory: document.getElementById('save-category'),
            cancelCategoryEdit: document.getElementById('cancel-category-edit'),
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
            itemSlotsCard: document.getElementById('item-slots-card'),
            applyItemSlotPreset: document.getElementById('apply-item-slot-preset'),
            addItemSlotRow: document.getElementById('add-item-slot-row'),
            itemSlotList: document.getElementById('item-slot-list'),
            saveItem: document.getElementById('save-item'),
            cancelItemEdit: document.getElementById('cancel-item-edit'),
            itemList: document.getElementById('item-list'),
            modifierDisabledNote: document.getElementById('modifier-disabled-note'),
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
            saveModifier: document.getElementById('save-modifier'),
            cancelModifierEdit: document.getElementById('cancel-modifier-edit'),
            modifierList: document.getElementById('modifier-list'),
            reviewCompletion: document.getElementById('review-completion'),
            reviewSubtitle: document.getElementById('review-subtitle'),
            reviewItemsCount: document.getElementById('review-items-count'),
            reviewModifiersCount: document.getElementById('review-modifiers-count'),
            reviewErrorsCount: document.getElementById('review-errors-count'),
            publishStateLabel: document.getElementById('publish-state-label'),
            publishStateCopy: document.getElementById('publish-state-copy'),
            publishDraft: document.getElementById('publish-draft'),
            reviewList: document.getElementById('review-list'),
            stepPanels: Array.from(document.querySelectorAll('[data-step]')),
            creationModal: document.getElementById('creation-modal'),
            modalTitle: document.getElementById('modal-title'),
            modalDots: document.getElementById('modal-dots'),
            modalBody: document.getElementById('modal-body'),
            modalBack: document.getElementById('modal-back'),
            modalNext: document.getElementById('modal-next'),
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

        function slotKeyify(value) {
            return slugify(value).replaceAll('-', '_');
        }

        function numeric(value, fallback = null) {
            if (value === '' || value === null || value === undefined) return fallback;
            const parsed = Number(value);
            return Number.isFinite(parsed) ? parsed : fallback;
        }

        function checkedValues(selector) {
            return Array.from(document.querySelectorAll(selector))
                .filter((input) => input.checked)
                .map((input) => input.value)
                .filter((value) => value !== '');
        }

        function checkedNumbers(selector) {
            return checkedValues(selector).map((value) => Number(value)).filter((value) => Number.isFinite(value));
        }

        function templateFor(key) {
            return S.templates.find((template) => template.key === key) ?? null;
        }

        function selectedTemplateKey() {
            return document.querySelector('input[name="template-card"]:checked')?.value ?? S.templates[0]?.key ?? null;
        }

        function activeTemplate() {
            return S.draft ? templateFor(S.draft.category_key) : templateFor(selectedTemplateKey());
        }

        function draftStep(stepKey) {
            return (S.draft?.steps ?? []).find((step) => step.step_key === stepKey) ?? null;
        }

        function mapDraftStepToWizard(stepKey) {
            if (stepKey === 'archetypes' || stepKey === 'categories') return 'taxonomy';
            return stepKey || 'foundation';
        }

        function stepStateForWizard(step) {
            if (!S.draft) return step === 'template' ? 'in_progress' : 'not_started';
            if (step === 'template') return 'complete';
            if (step === 'taxonomy') {
                const states = ['archetypes', 'categories'].map((key) => draftStep(key)?.status ?? 'not_started');
                if (states.every((value) => value === 'complete')) return 'complete';
                if (states.some((value) => value === 'in_progress' || value === 'complete')) return 'in_progress';
                return 'not_started';
            }
            return draftStep(step)?.status ?? 'not_started';
        }

        function setStatus(message, kind = 'info') {
            N.status.className = kind === 'danger' ? 'flash compact-note danger studio-status' : 'flash studio-status';
            N.status.textContent = message;
        }

        async function studioApi(path, options = {}) {
            const headers = {Accept: 'application/json', 'X-CSRF-TOKEN': BOOT.csrfToken, ...(options.headers ?? {})};
            if (options.body !== undefined) headers['Content-Type'] = 'application/json';
            const response = await fetch(`${BOOT.studioBase}${path}`, {
                method: options.method ?? 'GET',
                headers,
                credentials: 'same-origin',
                body: options.body !== undefined ? JSON.stringify(options.body) : undefined,
            });
            const payload = await response.json().catch(() => ({}));
            if (!response.ok || payload.success === false) {
                const reason = payload?.errors?.reason ?? payload?.message ?? 'Erreur studio.';
                throw new Error(reason);
            }
            return payload;
        }

        function updateDraftUrl(id) {
            const url = new URL(window.location.href);
            if (id) url.searchParams.set('draft', String(id));
            else url.searchParams.delete('draft');
            window.history.replaceState({}, '', url);
        }

        function sortDrafts() {
            S.drafts = [...S.drafts].sort((a, b) => {
                const aTime = new Date(a.updated_at ?? a.created_at ?? 0).getTime();
                const bTime = new Date(b.updated_at ?? b.created_at ?? 0).getTime();
                return bTime - aTime;
            });
        }

        function upsertDraft(draft) {
            if (!draft?.id) return;
            const index = S.drafts.findIndex((entry) => Number(entry.id) === Number(draft.id));
            if (index === -1) S.drafts.push(draft);
            else S.drafts[index] = draft;
            sortDrafts();
        }

        function removeDraft(id) {
            S.drafts = S.drafts.filter((draft) => Number(draft.id) !== Number(id));
        }

        function draftCounts() {
            return {
                archetypes: Number(S.draft?.counts?.archetypes ?? S.draft?.archetypes?.length ?? 0),
                categories: Number(S.draft?.counts?.categories ?? S.draft?.categories?.length ?? 0),
                items: Number(S.draft?.counts?.items ?? S.draft?.items?.length ?? 0),
                modifiers: Number(S.draft?.counts?.modifiers ?? S.draft?.modifiers?.length ?? 0),
            };
        }

        function displayStatus(status) {
            if (status === 'draft') return 'en création';
            if (status === 'published') return 'publié';
            return status ?? 'en création';
        }

        function displayStep(step) {
            return STEP_LABELS[mapDraftStepToWizard(step)] ?? step ?? 'Base';
        }

        function currentSettings() {
            const template = templateFor(S.draft?.category_key ?? selectedTemplateKey());
            const settings = S.draft?.settings ?? {};
            return {
                supports_modifiers: settings.supports_modifiers ?? template?.supports_modifiers ?? true,
                supports_archetypes: settings.supports_archetypes ?? template?.supports_archetypes ?? true,
                allowed_item_slots: Array.isArray(settings.allowed_item_slots) ? settings.allowed_item_slots : (template?.allowed_item_slots ?? []),
                allowed_modifier_types: Array.isArray(settings.allowed_modifier_types) ? settings.allowed_modifier_types : (template?.allowed_modifier_types ?? []),
            };
        }

        function resetCreateForm() {
            N.createDraftForm.reset();
            S.createSlugTouched = false;
        }

        function resetArchetypeForm() {
            N.archetypeForm.reset();
            S.archetypeSlugTouched = false;
            S.editing.archetypeId = null;
            N.saveArchetype.textContent = 'Ajouter';
            N.cancelArchetypeEdit.hidden = true;
        }

        function resetCategoryForm() {
            N.categoryForm.reset();
            S.categorySlugTouched = false;
            S.editing.categoryId = null;
            N.saveCategory.textContent = 'Ajouter';
            N.cancelCategoryEdit.hidden = true;
        }

        function selectedDraftLocked() {
            return Boolean(S.draft?.published_game_id) || S.draft?.status === 'published';
        }

        function setActiveStep(step) {
            S.activeStep = !S.draft && step !== 'template' ? 'template' : step;
            render();
        }

        async function loadDraft(id, message = 'Jeu charg\u00e9.') {
            const previousId = S.selectedDraftId;
            const previousStep = S.activeStep;
            const payload = await studioApi(`/game-drafts/${id}`);
            S.draft = payload.data ?? null;
            S.selectedDraftId = S.draft?.id ?? null;
            S.activeStep = Number(previousId) === Number(S.selectedDraftId)
                ? previousStep
                : mapDraftStepToWizard(S.draft?.current_step);
            upsertDraft(S.draft);
            updateDraftUrl(S.selectedDraftId);
            S.foundationState = 'Autosave pret';
            S.structureState = 'Autosave pret';
            resetArchetypeForm();
            resetCategoryForm();
            resetItemForm();
            resetModifierForm();
            render();
            setStatus(message);
        }

        function renderDraftSummary() {
            if (!S.draft) {
                N.draftSummary.className = 'draft-summary empty';
                N.draftSummary.innerHTML = 'Cr\u00e9e un nouveau jeu pour commencer.';
                return;
            }
            const template = templateFor(S.draft.category_key);
            const counts = draftCounts();
            N.draftSummary.className = 'draft-summary';
            N.draftSummary.innerHTML = `<div><strong style="font-size:24px;line-height:1">${E(S.draft.name)}</strong><p class="helper compact" style="margin-top:8px">${E(template?.label ?? S.draft.category_key)} • ${E(S.draft.slug)}</p></div><div class="summary-metrics"><article class="summary-metric"><strong>${E(S.draft.completion_percent ?? 0)}%</strong><span>Progression</span></article><article class="summary-metric"><strong>${E(counts.items)}</strong><span>Items</span></article><article class="summary-metric"><strong>${E(counts.modifiers)}</strong><span>Modificateurs</span></article></div><div class="entity-meta"><span class="tag">${E(displayStatus(S.draft.status))}</span><span class="tag">${E(S.draft.visibility ?? 'private')}</span><span class="tag">${E(displayStep(S.draft.current_step))}</span></div><div class="inline-tools"><button class="btn subtle" type="button" data-action="delete-draft" ${selectedDraftLocked() ? 'disabled' : ''}>Supprimer ce jeu</button></div>`;
        }

        function renderStepNav() {
            N.stepNav.innerHTML = WIZARD_STEPS.map((step) => {
                const status = stepStateForWizard(step);
                const active = step === S.activeStep;
                const locked = !S.draft && step !== 'template';
                return `<button class="stepper-item ${active ? 'active' : ''} ${locked ? 'locked' : ''}" type="button" data-action="go-step" data-step="${E(step)}" ${locked ? 'disabled' : ''}><span class="stepper-copy"><strong>${E(STEP_LABELS[step])}</strong><span>${E(STEP_DESCRIPTIONS[step])}</span></span><span class="status-dot ${E(status)}"></span></button>`;
            }).join('');
        }

        function renderDraftList() {
            if (!S.drafts.length) {
                N.draftList.innerHTML = '<div class="empty-soft">Aucun jeu en cours.</div>';
                return;
            }
            N.draftList.innerHTML = S.drafts.map((draft) => {
                const active = Number(draft.id) === Number(S.selectedDraftId);
                return `<article class="draft-tile ${active ? 'active' : ''}"><h4>${E(draft.name)}</h4><p>${E(draft.category_key)} • ${E(draft.completion_percent ?? 0)}% • ${E(displayStatus(draft.status))}</p><div class="entity-meta"><span class="tag">${E(displayStep(draft.current_step))}</span><span class="tag">${E(draft.visibility ?? 'private')}</span></div><div class="entity-tools"><button class="btn ghost" type="button" data-action="select-draft" data-id="${draft.id}">Ouvrir</button></div></article>`;
            }).join('');
        }

        function renderTemplateCards() {
            const selectedKey = selectedTemplateKey();
            N.templateGrid.innerHTML = S.templates.map((template) => `<label class="template-card ${template.key === selectedKey ? 'active' : ''}"><input type="radio" name="template-card" value="${E(template.key)}" ${template.key === selectedKey ? 'checked' : ''} hidden><div><h4>${E(template.label)}</h4><p>${E(template.description)}</p></div><div class="entity-meta"><span class="tag">v${E(template.version)}</span><span class="tag">${E((template.allowed_item_slots ?? []).length)} slot(s)</span><span class="tag">${E((template.allowed_modifier_types ?? []).length)} modifier(s)</span></div></label>`).join('');
            renderTemplatePreview();
        }

        function renderTemplatePreview() {
            const template = activeTemplate();
            if (!template) {
                N.templatePreview.innerHTML = '<div class="empty-soft">Aucun template disponible.</div>';
                return;
            }
            const starterCategories = Array.isArray(template.starter_categories) ? template.starter_categories : [];
            const starterArchetypes = Array.isArray(template.starter_archetypes) ? template.starter_archetypes : [];
            N.templatePreview.innerHTML = `<div class="card-head"><div><h2>${E(template.label)}</h2><p class="helper compact" style="margin-top:8px">${E(template.description)}</p></div></div><div class="template-preview-grid"><article class="entity-box"><h4>Structure de base</h4><ul class="stage-list">${(template.allowed_item_slots ?? []).map((slot) => `<li>${E(slot)}</li>`).join('')}</ul></article><article class="entity-box"><h4>Types de modifiers</h4><ul class="stage-list">${(template.allowed_modifier_types ?? []).map((type) => `<li>${E(type)}</li>`).join('')}</ul></article><article class="entity-box"><h4>Suggestions d'archetypes</h4><ul class="stage-list">${starterArchetypes.map((entry) => `<li>${E(entry.name)}</li>`).join('') || '<li>Aucune suggestion</li>'}</ul></article><article class="entity-box"><h4>Suggestions de categories</h4><ul class="stage-list">${starterCategories.map((entry) => `<li>${E(entry.label)}</li>`).join('') || '<li>Aucune suggestion</li>'}</ul></article></div>`;
        }

        function renderFoundation() {
            if (!S.draft) return;
            const active = document.activeElement;
            if (active !== N.foundationName) N.foundationName.value = S.draft.name ?? '';
            if (active !== N.foundationSlug) N.foundationSlug.value = S.draft.slug ?? '';
            if (active !== N.foundationDescription) N.foundationDescription.value = S.draft.description ?? '';
            if (active !== N.foundationVisibility) N.foundationVisibility.value = S.draft.visibility ?? 'private';
            N.foundationSaveState.textContent = S.foundationState;
            N.foundationForm.querySelectorAll('input, textarea, select, button').forEach((element) => {
                element.disabled = selectedDraftLocked();
            });
        }

        function renderStructureCheckboxGrid(container, values, selected, inputName, disabled) {
            container.innerHTML = values.length
                ? values.map((value) => `<label class="option-card"><input type="checkbox" name="${E(inputName)}" value="${E(value)}" ${selected.includes(value) ? 'checked' : ''} ${disabled ? 'disabled' : ''}><span><strong>${E(value)}</strong><small>Option active pour ce jeu.</small></span></label>`).join('')
                : '<div class="empty-soft">Aucune option disponible.</div>';
        }

        function renderStructure() {
            if (!S.draft) return;
            const template = activeTemplate();
            const settings = currentSettings();
            const counts = draftCounts();
            const lockStructure = counts.items > 0 || counts.modifiers > 0 || selectedDraftLocked();
            const lockArchetypesToggle = counts.archetypes > 0 || selectedDraftLocked();
            N.structureLockNote.hidden = !(counts.items > 0 || counts.modifiers > 0);
            N.supportsModifiers.checked = Boolean(settings.supports_modifiers);
            N.supportsArchetypes.checked = Boolean(settings.supports_archetypes);
            N.supportsModifiers.disabled = lockStructure;
            N.supportsArchetypes.disabled = lockArchetypesToggle;
            N.structureSaveState.textContent = S.structureState;
            renderStructureCheckboxGrid(N.structureItemSlots, Array.isArray(template?.allowed_item_slots) ? template.allowed_item_slots : [], settings.allowed_item_slots ?? [], 'structure-item-slot', lockStructure);
            renderStructureCheckboxGrid(N.structureModifierTypes, Array.isArray(template?.allowed_modifier_types) ? template.allowed_modifier_types : [], settings.allowed_modifier_types ?? [], 'structure-modifier-type', lockStructure || !settings.supports_modifiers);
            N.structureForm.querySelectorAll('button[type="submit"]').forEach((button) => {
                button.disabled = selectedDraftLocked();
            });
        }

        function renderEntityRows(container, entities, type) {
            if (!entities.length) {
                container.innerHTML = '<div class="empty-soft">Rien pour le moment.</div>';
                return;
            }

            const locked = selectedDraftLocked();
            const categoriesById = new Map((S.draft?.categories ?? []).map((category) => [Number(category.id), category]));
            const itemsById = new Map((S.draft?.items ?? []).map((item) => [Number(item.id), item]));

            container.innerHTML = entities.map((entity) => {
                let subtitle = '';

                if (type === 'archetype') {
                    subtitle = entity.description || 'Aucune description pour le moment.';
                } else if (type === 'category') {
                    subtitle = entity.description || 'Categorie disponible pour les items du jeu.';
                } else if (type === 'item') {
                    const categoryLabels = Array.isArray(entity.category_ids)
                        ? entity.category_ids.map((id) => categoriesById.get(Number(id))?.label).filter(Boolean)
                        : [];
                    const slotCount = Array.isArray(entity.item_slots) ? entity.item_slots.length : 0;
                    subtitle = `${entity.slot || 'slot'} • ${entity.rarity || 'standard'}${entity.tier_level ? ` • ${entity.tier_level}` : ''}${categoryLabels.length ? ` • ${categoryLabels.join(', ')}` : ''}${slotCount ? ` • ${slotCount} slot(s) interne(s)` : ''}`;
                } else if (type === 'modifier') {
                    const compatible = Array.isArray(entity.compatible_item_ids)
                        ? entity.compatible_item_ids.map((id) => itemsById.get(Number(id))?.name).filter(Boolean)
                        : [];
                    subtitle = `${entity.modifier_type || 'type'}${compatible.length ? ` • ${compatible.join(', ')}` : ' • Aucun item compatible'}`;
                }

                return `<article class="entity-row"><div><strong>${E(entity.name ?? entity.label ?? 'Element')}</strong><small>${E(subtitle)}</small></div><div class="entity-tools"><button class="btn ghost" type="button" data-action="edit-entity" data-entity="${E(type)}" data-id="${entity.id}" ${locked ? 'disabled' : ''}>Editer</button><button class="btn subtle" type="button" data-action="delete-entity" data-entity="${E(type)}" data-id="${entity.id}" ${locked ? 'disabled' : ''}>Supprimer</button></div></article>`;
            }).join('');
        }

        function renderTaxonomy() {
            if (!S.draft) return;

            const settings = currentSettings();
            const archetypesEnabled = Boolean(settings.supports_archetypes);
            const locked = selectedDraftLocked();

            N.archetypeDisabledNote.hidden = archetypesEnabled;
            N.applyArchetypeStarters.disabled = !archetypesEnabled || locked;
            N.archetypeForm.querySelectorAll('input, textarea, button').forEach((element) => {
                element.disabled = !archetypesEnabled || locked;
            });

            N.categoryForm.querySelectorAll('input, textarea, button').forEach((element) => {
                element.disabled = locked;
            });
            N.applyCategoryStarters.disabled = locked;

            renderEntityRows(N.archetypeList, S.draft.archetypes ?? [], 'archetype');
            renderEntityRows(N.categoryList, S.draft.categories ?? [], 'category');
        }

        function currentItemSlotPreset() {
            const template = activeTemplate();
            const globalSlot = N.itemSlot.value;
            const presets = template?.suggested_item_slots ?? {};
            return Array.isArray(presets?.[globalSlot]) ? presets[globalSlot] : [];
        }

        function addItemSlotRow(data = {}) {
            S.itemSlotsDraft.push({
                slot_key: data.slot_key ?? '',
                slot_label: data.slot_label ?? '',
                accepted_modifier_type: data.accepted_modifier_type ?? '',
                sort_order: data.sort_order ?? (S.itemSlotsDraft.length + 1),
                is_required: Boolean(data.is_required ?? false),
            });
            renderItemSlotRows();
        }

        function applyCurrentItemSlotPreset() {
            const preset = currentItemSlotPreset();
            if (!preset.length) {
                setStatus('Aucun preset de slot interne pour ce slot global.', 'danger');
                return;
            }

            S.itemSlotsDraft = preset.map((entry, index) => ({
                slot_key: entry.slot_key ?? slotKeyify(entry.slot_label ?? `slot-${index + 1}`),
                slot_label: entry.slot_label ?? `Slot ${index + 1}`,
                accepted_modifier_type: entry.accepted_modifier_type ?? '',
                sort_order: index + 1,
                is_required: Boolean(entry.is_required ?? false),
            }));
            renderItemSlotRows();
            setStatus('Preset de slots appliqué.');
        }

        function updateItemSlotDraftFromDom() {
            S.itemSlotsDraft = Array.from(N.itemSlotList.querySelectorAll('[data-slot-index]')).map((row, index) => ({
                slot_key: row.querySelector('[data-field="slot_key"]')?.value?.trim() ?? '',
                slot_label: row.querySelector('[data-field="slot_label"]')?.value?.trim() ?? '',
                accepted_modifier_type: row.querySelector('[data-field="accepted_modifier_type"]')?.value ?? '',
                sort_order: index + 1,
                is_required: Boolean(row.querySelector('[data-field="is_required"]')?.checked ?? false),
            }));
        }

        function renderItemSlotRows() {
            const settings = currentSettings();
            const modifierTypes = Array.isArray(settings.allowed_modifier_types) ? settings.allowed_modifier_types : [];
            const disabled = selectedDraftLocked() || !settings.supports_modifiers;

            if (!settings.supports_modifiers) {
                N.itemSlotsCard.hidden = false;
                N.itemSlotList.innerHTML = '<div class="empty-soft">Les modifiers sont desactives dans la structure actuelle.</div>';
                N.applyItemSlotPreset.disabled = true;
                N.addItemSlotRow.disabled = true;
                return;
            }

            N.itemSlotsCard.hidden = false;
            N.applyItemSlotPreset.disabled = disabled;
            N.addItemSlotRow.disabled = disabled;

            if (!S.itemSlotsDraft.length) {
                N.itemSlotList.innerHTML = '<div class="empty-soft">Ajoute un slot interne seulement si l item doit recevoir des modifiers.</div>';
                return;
            }

            N.itemSlotList.innerHTML = S.itemSlotsDraft.map((slot, index) => `<article class="slot-row" data-slot-index="${index}"><div class="slot-row-main"><label>Cle technique<input type="text" value="${E(slot.slot_key)}" data-field="slot_key" ${disabled ? 'disabled' : ''}></label><label>Label<input type="text" value="${E(slot.slot_label)}" data-field="slot_label" ${disabled ? 'disabled' : ''}></label><label>Type accepte<select data-field="accepted_modifier_type" ${disabled ? 'disabled' : ''}><option value="">Tous</option>${modifierTypes.map((type) => `<option value="${E(type)}" ${slot.accepted_modifier_type === type ? 'selected' : ''}>${E(type)}</option>`).join('')}</select></label><label>Obligatoire<input type="checkbox" data-field="is_required" ${slot.is_required ? 'checked' : ''} ${disabled ? 'disabled' : ''}></label></div><div class="slot-tools"><button class="btn subtle" type="button" data-action="remove-slot-row" data-index="${index}" ${disabled ? 'disabled' : ''}>Retirer</button></div></article>`).join('');
        }

        function renderItemCategoryOptions(selectedIds = []) {
            const locked = selectedDraftLocked();
            const categories = S.draft?.categories ?? [];
            N.itemCategoryOptions.innerHTML = categories.length
                ? categories.map((category) => `<label class="option-card"><input type="checkbox" name="item-category" value="${category.id}" ${selectedIds.includes(Number(category.id)) ? 'checked' : ''} ${locked ? 'disabled' : ''}><span><strong>${E(category.label)}</strong><small>${E(category.description ?? 'Catégorie du jeu')}</small></span></label>`).join('')
                : '<div class="empty-soft">Ajoute d abord des categories pour mieux classer tes items.</div>';
        }

        function renderItems() {
            if (!S.draft) return;

            const settings = currentSettings();
            const locked = selectedDraftLocked();
            const slots = Array.isArray(settings.allowed_item_slots) ? settings.allowed_item_slots : [];
            N.itemSlot.innerHTML = slots.length
                ? slots.map((slot) => `<option value="${E(slot)}">${E(slot)}</option>`).join('')
                : '<option value="">Aucun slot disponible</option>';

            if (!slots.includes(N.itemSlot.value) && slots.length) {
                N.itemSlot.value = slots[0];
            }

            renderItemCategoryOptions(checkedNumbers('input[name="item-category"]'));

            N.itemForm.querySelectorAll('input, textarea, select, button').forEach((element) => {
                element.disabled = locked;
            });

            renderItemSlotRows();
            renderEntityRows(N.itemList, S.draft.items ?? [], 'item');
        }

        function renderModifierItemOptions(selectedIds = []) {
            const locked = selectedDraftLocked();
            const items = S.draft?.items ?? [];
            N.modifierItemOptions.innerHTML = items.length
                ? items.map((item) => `<label class="option-card"><input type="checkbox" name="modifier-item" value="${item.id}" ${selectedIds.includes(Number(item.id)) ? 'checked' : ''} ${locked ? 'disabled' : ''}><span><strong>${E(item.name)}</strong><small>${E(item.slot ?? 'slot')} • ${E(item.rarity ?? 'standard')}</small></span></label>`).join('')
                : '<div class="empty-soft">Ajoute d abord au moins un item compatible.</div>';
        }

        function renderModifiers() {
            if (!S.draft) return;

            const settings = currentSettings();
            const locked = selectedDraftLocked();
            const types = Array.isArray(settings.allowed_modifier_types) ? settings.allowed_modifier_types : [];
            N.modifierDisabledNote.hidden = Boolean(settings.supports_modifiers);
            N.modifierType.innerHTML = types.length
                ? types.map((type) => `<option value="${E(type)}">${E(type)}</option>`).join('')
                : '<option value="">Aucun type disponible</option>';

            if (!types.includes(N.modifierType.value) && types.length) {
                N.modifierType.value = types[0];
            }

            renderModifierItemOptions(checkedNumbers('input[name="modifier-item"]'));

            N.modifierForm.querySelectorAll('input, textarea, select, button').forEach((element) => {
                element.disabled = locked || !settings.supports_modifiers;
            });
            renderEntityRows(N.modifierList, S.draft.modifiers ?? [], 'modifier');
        }

        function renderReview() {
            if (!S.draft) return;

            const counts = draftCounts();
            const steps = S.draft.steps ?? [];
            const reviewStep = draftStep('review');
            const totalErrors = Number(reviewStep?.blocking_errors_count ?? 0);
            const totalWarnings = steps
                .filter((step) => step.step_key !== 'review')
                .reduce((sum, step) => sum + Number(step.warnings_count ?? 0), 0);
            const published = Boolean(S.draft.published_game_id) || S.draft.status === 'published';
            const ready = !published && Number(reviewStep?.blocking_errors_count ?? 0) === 0 && reviewStep?.status === 'complete';

            N.reviewCompletion.textContent = `${S.draft.completion_percent ?? 0}%`;
            N.reviewSubtitle.textContent = published
                ? 'Jeu d\u00e9j\u00e0 publi\u00e9 depuis ce projet.'
                : ready
                    ? 'Le jeu peut \u00eatre publi\u00e9.'
                    : `${totalErrors} blocage(s) et ${totalWarnings} warning(s) \u00e0 traiter avant publication.`;
            N.reviewItemsCount.textContent = String(counts.items);
            N.reviewModifiersCount.textContent = String(counts.modifiers);
            N.reviewErrorsCount.textContent = String(totalErrors);

            if (published) {
                N.publishStateLabel.textContent = 'Publi\u00e9';
                N.publishStateCopy.textContent = 'Ce jeu a d\u00e9j\u00e0 \u00e9t\u00e9 publi\u00e9.';
                N.publishDraft.disabled = true;
                N.publishDraft.textContent = 'D\u00e9j\u00e0 publi\u00e9';
            } else if (ready) {
                N.publishStateLabel.textContent = 'Pr\u00eat';
                N.publishStateCopy.textContent = 'Toutes les \u00e9tapes sont valid\u00e9es.';
                N.publishDraft.disabled = false;
                N.publishDraft.textContent = 'Publier le jeu';
            } else {
                N.publishStateLabel.textContent = 'Bloqu\u00e9';
                N.publishStateCopy.textContent = 'Corrige les points signal\u00e9s ci-dessous.';
                N.publishDraft.disabled = true;
                N.publishDraft.textContent = 'Publication indisponible';
            }

            const orderedSteps = ['foundation', 'structure', 'archetypes', 'categories', 'items', 'modifiers', 'review']
                .map((key) => draftStep(key))
                .filter(Boolean);
            const reviewTitles = {
                foundation: 'Base',
                structure: 'Structure',
                archetypes: 'Profils',
                categories: 'Categories',
                items: 'Items',
                modifiers: 'Modifiers',
                review: 'Validation finale',
            };

            N.reviewList.innerHTML = orderedSteps.map((step) => {
                const stepTarget = mapDraftStepToWizard(step.step_key);
                const isGood = Number(step.blocking_errors_count ?? 0) === 0;
                const errors = Array.isArray(step.errors) ? step.errors : [];
                const warnings = Array.isArray(step.warnings) ? step.warnings : [];
                const tone = isGood ? 'good' : 'warn';

                return `<article class="review-card ${tone}"><div class="card-head"><div><strong>${E(reviewTitles[step.step_key] ?? step.step_key)}</strong><p class="helper compact" style="margin-top:6px">${E(step.step_key === 'review' ? 'Validation globale avant publication.' : STEP_DESCRIPTIONS[stepTarget] ?? '')}</p></div><button class="btn ghost" type="button" data-action="go-step" data-step="${E(stepTarget)}">${stepTarget === 'review' ? 'Voir' : 'Corriger'}</button></div><div class="entity-meta"><span class="tag">${E(step.status)}</span><span class="tag">${E(step.blocking_errors_count ?? 0)} blocage(s)</span><span class="tag">${E(step.warnings_count ?? 0)} warning(s)</span></div>${errors.length ? `<div class="compact-note danger">${errors.map((error) => `<div>${E(error)}</div>`).join('')}</div>` : '<div class="compact-note">Aucun blocage detecte.</div>'}${warnings.length ? `<div class="compact-note warn">${warnings.map((warning) => `<div>${E(warning)}</div>`).join('')}</div>` : ''}</article>`;
            }).join('');
        }

        function renderStageVisibility() {
            const visibleStep = !S.draft && S.activeStep !== 'template' ? 'template' : S.activeStep;
            N.stepPanels.forEach((panel) => {
                panel.hidden = panel.dataset.step !== visibleStep;
            });
        }

        function render() {
            renderTemplateCards();
            renderDraftSummary();
            renderStepNav();
            renderDraftList();
            renderStageVisibility();

            if (!S.draft) return;

            renderFoundation();
            renderStructure();
            renderTaxonomy();
            renderItems();
            renderModifiers();
            renderReview();
        }

        function foundationPayload() {
            return {
                name: N.foundationName.value.trim(),
                slug: N.foundationSlug.value.trim(),
                description: N.foundationDescription.value.trim() || null,
                visibility: N.foundationVisibility.value,
            };
        }

        function structurePayload() {
            const supportsModifiers = Boolean(N.supportsModifiers.checked);
            return {
                supports_modifiers: supportsModifiers,
                supports_archetypes: Boolean(N.supportsArchetypes.checked),
                allowed_item_slots: checkedValues('input[name="structure-item-slot"]'),
                allowed_modifier_types: supportsModifiers ? checkedValues('input[name="structure-modifier-type"]') : [],
            };
        }

        function normalizedItemSlotsPayload() {
            updateItemSlotDraftFromDom();

            return S.itemSlotsDraft
                .map((slot, index) => ({
                    slot_key: slot.slot_key?.trim() ? slotKeyify(slot.slot_key) : slotKeyify(slot.slot_label),
                    slot_label: slot.slot_label?.trim() ?? '',
                    accepted_modifier_type: slot.accepted_modifier_type || null,
                    sort_order: index + 1,
                    is_required: Boolean(slot.is_required),
                }))
                .filter((slot) => slot.slot_key && slot.slot_label);
        }

        function syncDraftFromResponse(draft) {
            if (!draft?.id) return;

            const previousId = S.draft?.id ?? null;
            S.draft = draft;
            S.selectedDraftId = draft.id;
            upsertDraft(draft);
            updateDraftUrl(draft.id);

            if (!previousId) {
                S.activeStep = mapDraftStepToWizard(draft.current_step);
            } else if (S.activeStep === 'template') {
                S.activeStep = 'foundation';
            }

            S.foundationState = 'Autosave pret';
            S.structureState = 'Autosave pret';
        }

        async function persistFoundation(message = 'Informations enregistr\u00e9es.') {
            if (!S.draft || selectedDraftLocked()) return;
            S.foundationState = 'Enregistrement...';
            renderFoundation();

            try {
                const payload = await studioApi(`/game-drafts/${S.draft.id}/foundation`, {
                    method: 'PATCH',
                    body: foundationPayload(),
                });
                syncDraftFromResponse(payload.data);
                S.foundationState = 'Enregistre';
                render();
                setStatus(message);
            } catch (error) {
                S.foundationState = 'Erreur d autosave';
                renderFoundation();
                setStatus(error.message, 'danger');
            }
        }

        function scheduleFoundationSave() {
            if (!S.draft || selectedDraftLocked()) return;
            clearTimeout(S.foundationTimer);
            S.foundationState = 'Modifications detectees...';
            renderFoundation();
            S.foundationTimer = window.setTimeout(() => {
                persistFoundation('Fondation sauvegardee automatiquement.');
            }, 700);
        }

        async function persistStructure(message = 'Structure enregistree.') {
            if (!S.draft || selectedDraftLocked()) return;
            S.structureState = 'Enregistrement...';
            renderStructure();

            try {
                const payload = await studioApi(`/game-drafts/${S.draft.id}/structure`, {
                    method: 'PATCH',
                    body: structurePayload(),
                });
                syncDraftFromResponse(payload.data);
                S.structureState = 'Enregistre';
                render();
                setStatus(message);
            } catch (error) {
                S.structureState = 'Erreur d autosave';
                renderStructure();
                setStatus(error.message, 'danger');
            }
        }

        function scheduleStructureSave() {
            if (!S.draft || selectedDraftLocked()) return;
            clearTimeout(S.structureTimer);
            S.structureState = 'Modifications detectees...';
            renderStructure();
            S.structureTimer = window.setTimeout(() => {
                persistStructure('Structure sauvegardee automatiquement.');
            }, 500);
        }

        async function createDraft(event) {
            event.preventDefault();

            openCreationModal();
            setModalTemplate(selectedTemplateKey());
            S.modal.data.name = N.createName.value.trim();
            S.modal.data.slug = N.createSlug.value.trim();
            S.modal.data.slugTouched = S.modal.data.slug !== '';
            S.modal.stepKey = S.modal.data.name ? 'description' : 'name';
            renderModal();
        }

        async function saveArchetype(event) {
            event.preventDefault();
            if (!S.draft || !currentSettings().supports_archetypes || selectedDraftLocked()) return;

            const body = {
                name: N.archetypeName.value.trim(),
                slug: N.archetypeSlug.value.trim() || null,
                description: N.archetypeDescription.value.trim() || null,
            };

            try {
                if (S.editing.archetypeId) {
                    await studioApi(`/game-drafts/${S.draft.id}/archetypes/${S.editing.archetypeId}`, {method: 'PUT', body});
                } else {
                    await studioApi(`/game-drafts/${S.draft.id}/archetypes`, {method: 'POST', body});
                }
                resetArchetypeForm();
                await loadDraft(S.draft.id, 'Arch\u00e9type enregistr\u00e9.');
            } catch (error) {
                setStatus(error.message, 'danger');
            }
        }

        async function saveCategory(event) {
            event.preventDefault();
            if (!S.draft || selectedDraftLocked()) return;

            const body = {
                label: N.categoryLabel.value.trim(),
                slug: N.categorySlug.value.trim() || null,
                description: N.categoryDescription.value.trim() || null,
            };

            try {
                if (S.editing.categoryId) {
                    await studioApi(`/game-drafts/${S.draft.id}/categories/${S.editing.categoryId}`, {method: 'PUT', body});
                } else {
                    await studioApi(`/game-drafts/${S.draft.id}/categories`, {method: 'POST', body});
                }
                resetCategoryForm();
                await loadDraft(S.draft.id, 'Cat\u00e9gorie enregistr\u00e9e.');
            } catch (error) {
                setStatus(error.message, 'danger');
            }
        }

        function resetItemForm() {
            N.itemForm.reset();
            S.itemSlugTouched = false;
            S.itemSlotsDraft = [];
            S.editing.itemId = null;
            N.saveItem.textContent = 'Ajouter l item';
            N.cancelItemEdit.hidden = true;
            renderItemSlotRows();
        }

        function resetModifierForm() {
            N.modifierForm.reset();
            S.modifierSlugTouched = false;
            S.editing.modifierId = null;
            N.saveModifier.textContent = 'Ajouter le modifier';
            N.cancelModifierEdit.hidden = true;
        }

        async function saveItem(event) {
            event.preventDefault();
            if (!S.draft || selectedDraftLocked()) return;

            const body = {
                name: N.itemName.value.trim(),
                slug: N.itemSlug.value.trim() || null,
                slot: N.itemSlot.value,
                rarity: N.itemRarity.value,
                tier_level: N.itemTier.value.trim() || null,
                score: numeric(N.itemScore.value, 0),
                attack: numeric(N.itemAttack.value, null),
                defense: numeric(N.itemDefense.value, null),
                utility: numeric(N.itemUtility.value, null),
                accuracy: numeric(N.itemAccuracy.value, null),
                mobility: numeric(N.itemMobility.value, null),
                category_ids: checkedNumbers('input[name="item-category"]'),
                item_slots: normalizedItemSlotsPayload(),
            };

            try {
                if (S.editing.itemId) {
                    await studioApi(`/game-drafts/${S.draft.id}/items/${S.editing.itemId}`, {method: 'PUT', body});
                } else {
                    await studioApi(`/game-drafts/${S.draft.id}/items`, {method: 'POST', body});
                }
                resetItemForm();
                await loadDraft(S.draft.id, 'Item enregistr\u00e9.');
            } catch (error) {
                setStatus(error.message, 'danger');
            }
        }

        async function saveModifier(event) {
            event.preventDefault();
            if (!S.draft || selectedDraftLocked() || !currentSettings().supports_modifiers) return;

            const body = {
                name: N.modifierName.value.trim(),
                slug: N.modifierSlug.value.trim() || null,
                modifier_type: N.modifierType.value,
                description: N.modifierDescription.value.trim() || null,
                delta_score: numeric(N.modifierScore.value, 0),
                delta_attack: numeric(N.modifierAttack.value, null),
                delta_defense: numeric(N.modifierDefense.value, null),
                delta_utility: numeric(N.modifierUtility.value, null),
                delta_accuracy: numeric(N.modifierAccuracy.value, null),
                delta_mobility: numeric(N.modifierMobility.value, null),
                compatible_item_ids: checkedNumbers('input[name="modifier-item"]'),
            };

            try {
                if (S.editing.modifierId) {
                    await studioApi(`/game-drafts/${S.draft.id}/modifiers/${S.editing.modifierId}`, {method: 'PUT', body});
                } else {
                    await studioApi(`/game-drafts/${S.draft.id}/modifiers`, {method: 'POST', body});
                }
                resetModifierForm();
                await loadDraft(S.draft.id, 'Modificateur enregistr\u00e9.');
            } catch (error) {
                setStatus(error.message, 'danger');
            }
        }

        async function deleteDraft() {
            if (!S.draft) return;
            if (!window.confirm(`Supprimer le jeu "${S.draft.name}" ?`)) return;

            try {
                await studioApi(`/game-drafts/${S.draft.id}`, {method: 'DELETE'});
                removeDraft(S.draft.id);
                S.draft = null;
                S.selectedDraftId = null;
                updateDraftUrl(null);
                resetArchetypeForm();
                resetCategoryForm();
                resetItemForm();
                resetModifierForm();

                const nextDraft = S.drafts[0] ?? null;
                if (nextDraft?.id) {
                    await loadDraft(nextDraft.id, 'Jeu supprim\u00e9. Un autre jeu a \u00e9t\u00e9 charg\u00e9.');
                } else {
                    S.activeStep = 'template';
                    render();
                    setStatus('Jeu supprim\u00e9.');
                }
            } catch (error) {
                setStatus(error.message, 'danger');
            }
        }

        async function deleteEntity(type, id) {
            if (!S.draft || selectedDraftLocked()) return;
            if (!window.confirm('Supprimer cet \u00e9l\u00e9ment ?')) return;

            const paths = {
                archetype: `/game-drafts/${S.draft.id}/archetypes/${id}`,
                category: `/game-drafts/${S.draft.id}/categories/${id}`,
                item: `/game-drafts/${S.draft.id}/items/${id}`,
                modifier: `/game-drafts/${S.draft.id}/modifiers/${id}`,
            };

            try {
                await studioApi(paths[type], {method: 'DELETE'});
                await loadDraft(S.draft.id, '\u00c9l\u00e9ment supprim\u00e9.');
            } catch (error) {
                setStatus(error.message, 'danger');
            }
        }

        function editEntity(type, id) {
            if (!S.draft) return;

            if (type === 'archetype') {
                const entity = (S.draft.archetypes ?? []).find((entry) => Number(entry.id) === Number(id));
                if (!entity) return;
                N.archetypeName.value = entity.name ?? '';
                N.archetypeSlug.value = entity.slug ?? '';
                N.archetypeDescription.value = entity.description ?? '';
                S.archetypeSlugTouched = true;
                S.editing.archetypeId = entity.id;
                N.saveArchetype.textContent = 'Mettre a jour';
                N.cancelArchetypeEdit.hidden = false;
                setActiveStep('taxonomy');
                return;
            }

            if (type === 'category') {
                const entity = (S.draft.categories ?? []).find((entry) => Number(entry.id) === Number(id));
                if (!entity) return;
                N.categoryLabel.value = entity.label ?? '';
                N.categorySlug.value = entity.slug ?? '';
                N.categoryDescription.value = entity.description ?? '';
                S.categorySlugTouched = true;
                S.editing.categoryId = entity.id;
                N.saveCategory.textContent = 'Mettre a jour';
                N.cancelCategoryEdit.hidden = false;
                setActiveStep('taxonomy');
                return;
            }

            if (type === 'item') {
                const entity = (S.draft.items ?? []).find((entry) => Number(entry.id) === Number(id));
                if (!entity) return;
                N.itemName.value = entity.name ?? '';
                N.itemSlug.value = entity.slug ?? '';
                N.itemSlot.value = entity.slot ?? '';
                N.itemRarity.value = entity.rarity ?? 'standard';
                N.itemTier.value = entity.tier_level ?? '';
                N.itemScore.value = entity.score ?? 0;
                N.itemAttack.value = entity.attack ?? 0;
                N.itemDefense.value = entity.defense ?? 0;
                N.itemUtility.value = entity.utility ?? 0;
                N.itemAccuracy.value = entity.accuracy ?? 0;
                N.itemMobility.value = entity.mobility ?? 0;
                S.itemSlugTouched = true;
                S.editing.itemId = entity.id;
                S.itemSlotsDraft = Array.isArray(entity.item_slots)
                    ? entity.item_slots.map((slot, index) => ({
                        slot_key: slot.slot_key ?? '',
                        slot_label: slot.slot_label ?? '',
                        accepted_modifier_type: slot.accepted_modifier_type ?? '',
                        sort_order: slot.sort_order ?? (index + 1),
                        is_required: Boolean(slot.is_required ?? false),
                    }))
                    : [];
                N.saveItem.textContent = 'Mettre a jour l item';
                N.cancelItemEdit.hidden = false;
                renderItemCategoryOptions(Array.isArray(entity.category_ids) ? entity.category_ids.map((entry) => Number(entry)) : []);
                renderItemSlotRows();
                setActiveStep('items');
                return;
            }

            if (type === 'modifier') {
                const entity = (S.draft.modifiers ?? []).find((entry) => Number(entry.id) === Number(id));
                if (!entity) return;
                N.modifierName.value = entity.name ?? '';
                N.modifierSlug.value = entity.slug ?? '';
                N.modifierType.value = entity.modifier_type ?? '';
                N.modifierDescription.value = entity.description ?? '';
                N.modifierScore.value = entity.delta_score ?? 0;
                N.modifierAttack.value = entity.delta_attack ?? 0;
                N.modifierDefense.value = entity.delta_defense ?? 0;
                N.modifierUtility.value = entity.delta_utility ?? 0;
                N.modifierAccuracy.value = entity.delta_accuracy ?? 0;
                N.modifierMobility.value = entity.delta_mobility ?? 0;
                S.modifierSlugTouched = true;
                S.editing.modifierId = entity.id;
                N.saveModifier.textContent = 'Mettre a jour le modifier';
                N.cancelModifierEdit.hidden = false;
                renderModifierItemOptions(Array.isArray(entity.compatible_item_ids) ? entity.compatible_item_ids.map((entry) => Number(entry)) : []);
                setActiveStep('modifiers');
            }
        }

        async function publishDraft() {
            if (!S.draft || selectedDraftLocked()) return;

            try {
                const payload = await studioApi(`/game-drafts/${S.draft.id}/publish`, {method: 'POST'});
                S.publishResult = payload.data?.game ?? null;
                syncDraftFromResponse(payload.data?.draft);
                S.activeStep = 'review';
                render();
                setStatus('Jeu publi\u00e9 avec succ\u00e8s.');
            } catch (error) {
                setStatus(error.message, 'danger');
                await loadDraft(S.draft.id, 'Publication bloqu\u00e9e. Les erreurs ont \u00e9t\u00e9 recharg\u00e9es.');
            }
        }

        async function applyStarterPack(type) {
            if (!S.draft || selectedDraftLocked()) return;

            const template = activeTemplate();
            const source = type === 'archetype'
                ? (template?.starter_archetypes ?? [])
                : (template?.starter_categories ?? []);

            if (!source.length) {
                setStatus('Aucune suggestion disponible pour ce template.', 'danger');
                return;
            }

            if (type === 'archetype' && !currentSettings().supports_archetypes) {
                setStatus('Active d abord les archétypes dans les options du jeu.', 'danger');
                return;
            }

            const existing = new Set((type === 'archetype' ? (S.draft.archetypes ?? []) : (S.draft.categories ?? []))
                .map((entry) => String(entry.slug ?? '').toLowerCase()));

            try {
                for (const entry of source) {
                    const slug = entry.slug ?? slugify(entry.name ?? entry.label ?? '');
                    if (existing.has(slug)) continue;

                    if (type === 'archetype') {
                        await studioApi(`/game-drafts/${S.draft.id}/archetypes`, {
                            method: 'POST',
                            body: {
                                name: entry.name,
                                slug,
                                description: entry.description ?? null,
                            },
                        });
                    } else {
                        await studioApi(`/game-drafts/${S.draft.id}/categories`, {
                            method: 'POST',
                            body: {
                                label: entry.label,
                                slug,
                                description: entry.description ?? null,
                            },
                        });
                    }

                    existing.add(slug);
                }

                await loadDraft(S.draft.id, 'Suggestions appliqu\u00e9es.');
            } catch (error) {
                setStatus(error.message, 'danger');
            }
        }

        function autoSlug(sourceInput, slugInput, touchedFlag) {
            sourceInput?.addEventListener('input', () => {
                if (!S[touchedFlag]) {
                    slugInput.value = slugify(sourceInput.value);
                }
            });

            slugInput?.addEventListener('input', () => {
                S[touchedFlag] = slugInput.value.trim() !== '';
            });
        }

        function bootSelectedTemplate() {
            const first = S.templates[0]?.key ?? null;
            if (!document.querySelector('input[name="template-card"]:checked') && first) {
                const input = document.querySelector(`input[name="template-card"][value="${first}"]`);
                if (input) input.checked = true;
            }
            renderTemplateCards();
        }

        document.addEventListener('click', async (event) => {
            const actionTarget = event.target.closest('[data-action]');
            if (!actionTarget) return;

            const action = actionTarget.dataset.action;

            if (action === 'go-step') {
                setActiveStep(actionTarget.dataset.step);
                return;
            }

            if (action === 'select-draft') {
                await loadDraft(actionTarget.dataset.id, 'Jeu chargé.');
                return;
            }

            if (action === 'delete-draft') {
                await deleteDraft();
                return;
            }

            if (action === 'close-modal') {
                closeCreationModal();
                return;
            }

            if (action === 'edit-entity') {
                editEntity(actionTarget.dataset.entity, actionTarget.dataset.id);
                return;
            }

            if (action === 'delete-entity') {
                await deleteEntity(actionTarget.dataset.entity, actionTarget.dataset.id);
                return;
            }

            if (action === 'remove-slot-row') {
                const index = Number(actionTarget.dataset.index);
                S.itemSlotsDraft = S.itemSlotsDraft.filter((_, entryIndex) => entryIndex !== index);
                renderItemSlotRows();
            }
        });

        document.addEventListener('change', (event) => {
            const target = event.target;

            if (target.matches('input[name="template-card"]')) {
                renderTemplateCards();
                return;
            }

            if (target.matches('#foundation-visibility')) {
                scheduleFoundationSave();
                return;
            }

            if (target.matches('input[name="structure-item-slot"], input[name="structure-modifier-type"], #supports-modifiers, #supports-archetypes')) {
                scheduleStructureSave();
                renderStructure();
                return;
            }

            if (target.closest('[data-slot-index]')) {
                updateItemSlotDraftFromDom();
                return;
            }

            if (target.matches('#item-slot')) {
                renderItemSlotRows();
            }
        });

        autoSlug(N.createName, N.createSlug, 'createSlugTouched');
        autoSlug(N.foundationName, N.foundationSlug, 'foundationSlugTouched');
        autoSlug(N.archetypeName, N.archetypeSlug, 'archetypeSlugTouched');
        autoSlug(N.categoryLabel, N.categorySlug, 'categorySlugTouched');
        autoSlug(N.itemName, N.itemSlug, 'itemSlugTouched');
        autoSlug(N.modifierName, N.modifierSlug, 'modifierSlugTouched');

        N.createDraftForm.addEventListener('submit', createDraft);
        N.foundationForm.addEventListener('submit', (event) => {
            event.preventDefault();
            persistFoundation('Informations enregistr\u00e9es.');
        });
        N.structureForm.addEventListener('submit', (event) => {
            event.preventDefault();
            persistStructure('Structure enregistr\u00e9e.');
        });
        N.archetypeForm.addEventListener('submit', saveArchetype);
        N.categoryForm.addEventListener('submit', saveCategory);
        N.itemForm.addEventListener('submit', saveItem);
        N.modifierForm.addEventListener('submit', saveModifier);

        N.foundationForm.querySelectorAll('input, textarea').forEach((element) => {
            element.addEventListener('input', scheduleFoundationSave);
        });

        N.cancelArchetypeEdit.addEventListener('click', resetArchetypeForm);
        N.cancelCategoryEdit.addEventListener('click', resetCategoryForm);
        N.cancelItemEdit.addEventListener('click', resetItemForm);
        N.cancelModifierEdit.addEventListener('click', resetModifierForm);
        N.applyArchetypeStarters.addEventListener('click', () => applyStarterPack('archetype'));
        N.applyCategoryStarters.addEventListener('click', () => applyStarterPack('category'));
        N.applyItemSlotPreset.addEventListener('click', applyCurrentItemSlotPreset);
        N.addItemSlotRow.addEventListener('click', () => addItemSlotRow());
        N.publishDraft.addEventListener('click', publishDraft);
        N.startNewDraft.addEventListener('click', () => openCreationModal());

        /* ═══════════════ MODAL DE CRÉATION ═══════════════ */

        function templateDefaultsForModal(templateKey) {
            const template = templateFor(templateKey) ?? S.templates[0] ?? {};
            return {
                templateKey: template.key ?? null,
                supportsModifiers: template.supports_modifiers ?? true,
                supportsArchetypes: template.supports_archetypes ?? true,
                allowedItemSlots: [...(template.allowed_item_slots ?? [])],
                allowedModifierTypes: [...(template.allowed_modifier_types ?? [])],
                starterArchetypeSlugs: (template.starter_archetypes ?? []).map((entry) => entry.slug ?? slugify(entry.name ?? '')),
                starterCategorySlugs: (template.starter_categories ?? []).map((entry) => entry.slug ?? slugify(entry.label ?? '')),
            };
        }

        function freshModalState() {
            return {
                open: true,
                stepKey: 'name',
                busy: false,
                data: {
                    ...templateDefaultsForModal(S.templates[0]?.key ?? null),
                    name: '',
                    slug: '',
                    slugTouched: false,
                    description: '',
                    visibility: 'private',
                    customCategories: '',
                    itemsText: '',
                    modifiersText: '',
                    createdDraftId: null,
                    createdItems: [],
                    publishedGame: null,
                },
            };
        }

        function modalSteps() {
            const steps = ['name', 'description', 'options'];
            if (S.modal.data?.supportsArchetypes) steps.push('archetypes');
            steps.push('categories');
            steps.push('items');
            if (S.modal.data?.supportsModifiers) steps.push('modifiers');
            steps.push('publish');
            return steps;
        }

        function currentModalIndex() {
            return Math.max(0, modalSteps().indexOf(S.modal.stepKey));
        }

        function templateMinimum(key, fallback = 1) {
            const template = templateFor(S.modal.data?.templateKey) ?? {};
            return Number(template.minimums?.[key] ?? fallback);
        }

        function defaultItemNames() {
            const min = templateMinimum('items', 1);
            const slots = S.modal.data?.allowedItemSlots?.length ? S.modal.data.allowedItemSlots : ['item'];
            return Array.from({length: min}, (_, index) => {
                const slot = slots[index % slots.length] ?? 'item';
                return `${slot} ${index + 1}`;
            }).join('\n');
        }

        function defaultModifierNames() {
            const min = templateMinimum('modifiers', 1);
            const types = S.modal.data?.allowedModifierTypes?.length ? S.modal.data.allowedModifierTypes : ['modifier'];
            return Array.from({length: min}, (_, index) => {
                const type = types[index % types.length] ?? 'modifier';
                return `${type} ${index + 1}`;
            }).join('\n');
        }

        function modalLines(value) {
            return String(value ?? '')
                .split(/\r?\n/)
                .map((line) => line.trim())
                .filter(Boolean);
        }

        function openCreationModal() {
            S.modal = freshModalState();
            N.creationModal.hidden = false;
            renderModal();
        }

        function closeCreationModal() {
            S.modal.open = false;
            N.creationModal.hidden = true;
        }

        function renderModalDots() {
            const activeIndex = currentModalIndex();
            N.modalDots.innerHTML = modalSteps().map((_, index) => {
                const cls = index < activeIndex ? 'dot done' : index === activeIndex ? 'dot active' : 'dot';
                return `<span class="${cls}"></span>`;
            }).join('');
        }

        function setModalTemplate(templateKey) {
            const previous = S.modal.data ?? {};
            S.modal.data = {
                ...previous,
                ...templateDefaultsForModal(templateKey),
                name: previous.name ?? '',
                slug: previous.slug ?? '',
                slugTouched: Boolean(previous.slugTouched),
                description: previous.description ?? '',
                visibility: previous.visibility ?? 'private',
                customCategories: previous.customCategories ?? '',
                itemsText: previous.itemsText ?? '',
                modifiersText: previous.modifiersText ?? '',
                createdDraftId: previous.createdDraftId ?? null,
                createdItems: previous.createdItems ?? [],
                publishedGame: previous.publishedGame ?? null,
            };
        }

        function captureModalFields() {
            if (!S.modal.open) return;
            const selectedTemplate = document.querySelector('input[name="modal-template"]:checked')?.value;
            if (selectedTemplate && selectedTemplate !== S.modal.data.templateKey) setModalTemplate(selectedTemplate);
            const data = S.modal.data;

            const name = document.getElementById('modal-name');
            const slug = document.getElementById('modal-slug');
            const description = document.getElementById('modal-description');
            const visibility = document.getElementById('modal-visibility');
            const supportsModifiers = document.getElementById('modal-supports-modifiers');
            const supportsArchetypes = document.getElementById('modal-supports-archetypes');
            const customCategories = document.getElementById('modal-custom-categories');
            const itemsText = document.getElementById('modal-items-text');
            const modifiersText = document.getElementById('modal-modifiers-text');

            if (name) data.name = name.value;
            if (slug) data.slug = slug.value;
            if (description) data.description = description.value;
            if (visibility) data.visibility = visibility.value;
            if (supportsModifiers) data.supportsModifiers = supportsModifiers.checked;
            if (supportsArchetypes) data.supportsArchetypes = supportsArchetypes.checked;
            if (customCategories) data.customCategories = customCategories.value;
            if (itemsText) data.itemsText = itemsText.value;
            if (modifiersText) data.modifiersText = modifiersText.value;

            if (document.querySelector('input[name="modal-item-slot"]')) {
                data.allowedItemSlots = checkedValues('input[name="modal-item-slot"]');
            }
            if (document.querySelector('input[name="modal-modifier-type"]')) {
                data.allowedModifierTypes = data.supportsModifiers ? checkedValues('input[name="modal-modifier-type"]') : [];
            } else if (!data.supportsModifiers) {
                data.allowedModifierTypes = [];
            }
            if (document.querySelector('input[name="modal-archetype"]')) {
                data.starterArchetypeSlugs = checkedValues('input[name="modal-archetype"]');
            }
            if (document.querySelector('input[name="modal-category"]')) {
                data.starterCategorySlugs = checkedValues('input[name="modal-category"]');
            }
        }

        function wireModalPersistence() {
            N.modalBody.querySelectorAll('input, textarea, select').forEach((element) => {
                element.addEventListener('input', captureModalFields);
                element.addEventListener('change', captureModalFields);
            });
        }

        function renderModal() {
            const step = S.modal.stepKey;
            const data = S.modal.data ?? {};
            const template = templateFor(data.templateKey) ?? S.templates[0] ?? {};
            renderModalDots();
            N.modalBack.hidden = currentModalIndex() === 0 || Boolean(data.createdDraftId);
            N.modalNext.disabled = S.modal.busy;

            if (step === 'name') {
                N.modalTitle.textContent = 'Nom du jeu';
                N.modalNext.textContent = 'Continuer';
                N.modalBody.innerHTML = `
                    <p class="helper modal-step-copy">Commence par le nom public du jeu. Le slug est généré automatiquement.</p>
                    <div class="item-form-grid">
                        <div class="field"><label for="modal-name">Nom du jeu</label><input id="modal-name" type="text" value="${E(data.name)}" placeholder="Mon arena shooter" required></div>
                        <div class="field"><label for="modal-slug">Slug</label><input id="modal-slug" type="text" value="${E(data.slug)}" placeholder="mon-arena-shooter"></div>
                    </div>`;
                const nameInput = document.getElementById('modal-name');
                const slugInput = document.getElementById('modal-slug');
                nameInput?.addEventListener('input', () => {
                    S.modal.data.name = nameInput.value;
                    if (!S.modal.data.slugTouched) {
                        slugInput.value = slugify(nameInput.value);
                        S.modal.data.slug = slugInput.value;
                    }
                });
                slugInput?.addEventListener('input', () => {
                    S.modal.data.slug = slugInput.value;
                    S.modal.data.slugTouched = slugInput.value.trim() !== '';
                });
                nameInput?.focus();
            } else if (step === 'description') {
                N.modalTitle.textContent = 'Décris ton jeu';
                N.modalNext.textContent = 'Continuer';
                N.modalBody.innerHTML = `
                    <p class="helper modal-step-copy">Ajoute le contexte avant de choisir la structure du jeu.</p>
                    <div class="item-form-grid">
                        <div class="field span-2"><label for="modal-description">Description</label><textarea id="modal-description" rows="5" placeholder="Décris ton jeu en quelques lignes...">${E(data.description)}</textarea></div>
                        <div class="field"><label for="modal-visibility">Visibilité</label><select id="modal-visibility"><option value="private"${data.visibility === 'private' ? ' selected' : ''}>Privé</option><option value="public"${data.visibility === 'public' ? ' selected' : ''}>Public</option></select></div>
                    </div>`;
                wireModalPersistence();
                document.getElementById('modal-description')?.focus();
            } else if (step === 'options') {
                N.modalTitle.textContent = 'Options du jeu';
                N.modalNext.textContent = 'Continuer';
                const itemSlots = template.allowed_item_slots ?? [];
                const modTypes = template.allowed_modifier_types ?? [];
                const selSlots = Array.isArray(data.allowedItemSlots) ? data.allowedItemSlots : itemSlots;
                const selTypes = Array.isArray(data.allowedModifierTypes) ? data.allowedModifierTypes : modTypes;
                N.modalBody.innerHTML = `
                    <p class="helper modal-step-copy">Choisis le type de jeu, puis active uniquement les éléments dont ton jeu a besoin.</p>
                    <div class="template-grid">${S.templates.map((t) => `<label class="template-card ${t.key === data.templateKey ? 'active' : ''}"><input type="radio" name="modal-template" value="${E(t.key)}" ${t.key === data.templateKey ? 'checked' : ''} hidden><div><h4>${E(t.label)}</h4><p>${E(t.description)}</p></div><div class="entity-meta"><span class="tag">v${E(t.version)}</span><span class="tag">${(t.allowed_item_slots ?? []).length} slot(s)</span></div></label>`).join('')}</div>
                    <div class="option-grid">
                        <label class="option-card"><input id="modal-supports-archetypes" type="checkbox" ${data.supportsArchetypes ? 'checked' : ''}><span><strong>Archétypes / Profils</strong><small>Classes, styles de jeu...</small></span></label>
                        <label class="option-card"><input id="modal-supports-modifiers" type="checkbox" ${data.supportsModifiers ? 'checked' : ''}><span><strong>Modificateurs</strong><small>Accessoires, runes, effets...</small></span></label>
                    </div>
                    <div class="structure-preview" style="margin-top:16px"><div class="card-head"><h2>Slots d'items</h2></div><div class="checkbox-grid">${itemSlots.map((s) => `<label class="option-card"><input type="checkbox" name="modal-item-slot" value="${E(s)}" ${selSlots.includes(s) ? 'checked' : ''}><span><strong>${E(s)}</strong></span></label>`).join('')}</div></div>
                    <div class="structure-preview" style="margin-top:16px"><div class="card-head"><h2>Types de modificateurs</h2></div><div class="checkbox-grid" id="modal-mod-types">${modTypes.map((t) => `<label class="option-card"><input type="checkbox" name="modal-modifier-type" value="${E(t)}" ${selTypes.includes(t) ? 'checked' : ''} ${data.supportsModifiers ? '' : 'disabled'}><span><strong>${E(t)}</strong></span></label>`).join('')}</div></div>`;
                N.modalBody.querySelectorAll('input[name="modal-template"]').forEach((input) => {
                    input.addEventListener('change', () => {
                        setModalTemplate(input.value);
                        renderModal();
                    });
                });
                document.getElementById('modal-supports-modifiers')?.addEventListener('change', (event) => {
                    captureModalFields();
                    document.querySelectorAll('input[name="modal-modifier-type"]').forEach((input) => { input.disabled = !event.target.checked; });
                });
                document.getElementById('modal-supports-archetypes')?.addEventListener('change', captureModalFields);
                wireModalPersistence();
            } else if (step === 'archetypes') {
                N.modalTitle.textContent = 'Archétypes';
                N.modalNext.textContent = 'Continuer';
                const archetypes = template.starter_archetypes ?? [];
                const selected = data.starterArchetypeSlugs ?? [];
                N.modalBody.innerHTML = `
                    <p class="helper modal-step-copy">Sélectionne les profils à créer dès maintenant. Tu pourras en ajouter d'autres après.</p>
                    <div class="modal-check-list">${archetypes.length ? archetypes.map((entry) => {
                        const slug = entry.slug ?? slugify(entry.name ?? '');
                        return `<label class="option-card"><input type="checkbox" name="modal-archetype" value="${E(slug)}" ${selected.includes(slug) ? 'checked' : ''}><span><strong>${E(entry.name)}</strong><small>${E(entry.description ?? '')}</small></span></label>`;
                    }).join('') : '<div class="empty-soft">Aucune suggestion d archétype pour ce type de jeu.</div>'}</div>`;
                wireModalPersistence();
            } else if (step === 'categories') {
                N.modalTitle.textContent = 'Catégories';
                N.modalNext.textContent = 'Créer le jeu';
                const categories = template.starter_categories ?? [];
                const selected = data.starterCategorySlugs ?? [];
                N.modalBody.innerHTML = `
                    <p class="helper modal-step-copy">Ajoute les premières catégories qui serviront à classer les items du jeu.</p>
                    <div class="modal-check-list">${categories.length ? categories.map((entry) => {
                        const slug = entry.slug ?? slugify(entry.label ?? '');
                        return `<label class="option-card"><input type="checkbox" name="modal-category" value="${E(slug)}" ${selected.includes(slug) ? 'checked' : ''}><span><strong>${E(entry.label)}</strong><small>${E(entry.description ?? '')}</small></span></label>`;
                    }).join('') : '<div class="empty-soft">Aucune suggestion de catégorie pour ce type de jeu.</div>'}</div>
                    <div class="field" style="margin-top:18px"><label for="modal-custom-categories">Catégories en plus</label><textarea id="modal-custom-categories" rows="3" placeholder="Une catégorie par ligne">${E(data.customCategories)}</textarea><p class="helper compact modal-muted">Exemple: Fusils tactiques, Gadgets, Supports.</p></div>`;
                wireModalPersistence();
            } else if (step === 'items') {
                if (!data.itemsText) data.itemsText = defaultItemNames();
                const minItems = templateMinimum('items', 1);
                N.modalTitle.textContent = 'Équipements';
                N.modalNext.textContent = data.supportsModifiers ? 'Continuer' : 'Voir la publication';
                N.modalBody.innerHTML = `
                    <p class="helper modal-step-copy">Ajoute les équipements du jeu, un par ligne. Minimum demandé pour publier: ${E(minItems)}.</p>
                    <div class="field"><label for="modal-items-text">Items / équipements</label><textarea id="modal-items-text" rows="8" placeholder="KN-44\nVMP\nICR-1">${E(data.itemsText)}</textarea></div>
                    <div class="entity-meta"><span class="tag">${E(modalLines(data.itemsText).length)} item(s)</span><span class="tag">Slots: ${E((data.allowedItemSlots ?? []).join(', '))}</span></div>`;
                wireModalPersistence();
                document.getElementById('modal-items-text')?.focus();
            } else if (step === 'modifiers') {
                if (!data.modifiersText) data.modifiersText = defaultModifierNames();
                const minModifiers = templateMinimum('modifiers', 1);
                N.modalTitle.textContent = 'Modificateurs';
                N.modalNext.textContent = 'Voir la publication';
                N.modalBody.innerHTML = `
                    <p class="helper modal-step-copy">Ajoute les modificateurs, un par ligne. Ils seront rattachés aux équipements compatibles.</p>
                    <div class="field"><label for="modal-modifiers-text">Modificateurs</label><textarea id="modal-modifiers-text" rows="7" placeholder="Red Dot\nGrip\nExtended Mag">${E(data.modifiersText)}</textarea></div>
                    <div class="entity-meta"><span class="tag">${E(modalLines(data.modifiersText).length)} modificateur(s)</span><span class="tag">Minimum: ${E(minModifiers)}</span></div>`;
                wireModalPersistence();
                document.getElementById('modal-modifiers-text')?.focus();
            } else if (step === 'publish') {
                N.modalTitle.textContent = data.publishedGame ? 'Jeu publié' : 'Publication';
                N.modalNext.textContent = data.publishedGame ? 'Terminer' : 'Publier le jeu';
                const itemCount = modalLines(data.itemsText).length;
                const modifierCount = data.supportsModifiers ? modalLines(data.modifiersText).length : 0;
                N.modalBody.innerHTML = `
                    <p class="helper modal-step-copy">${data.publishedGame ? 'Ton jeu est publié.' : 'Vérifie le résumé puis publie le jeu.'}</p>
                    <div class="modal-review-list">
                        <article class="entity-row"><div><strong>${E(data.name)}</strong><small>${E(data.description || 'Sans description')}</small></div></article>
                        <article class="entity-row"><div><strong>${E(itemCount)} équipement(s)</strong><small>${E((data.allowedItemSlots ?? []).join(', '))}</small></div></article>
                        <article class="entity-row"><div><strong>${E(modifierCount)} modificateur(s)</strong><small>${data.supportsModifiers ? E((data.allowedModifierTypes ?? []).join(', ')) : 'Désactivés'}</small></div></article>
                    </div>`;
            }
        }

        function modalSelectedTemplate() {
            return templateFor(S.modal.data?.templateKey);
        }

        function selectedModalArchetypes(template, data) {
            const selected = new Set(data.supportsArchetypes ? data.starterArchetypeSlugs : []);
            return (template.starter_archetypes ?? [])
                .filter((entry) => selected.has(entry.slug ?? slugify(entry.name ?? '')));
        }

        function selectedModalCategories(template, data) {
            const selected = new Set(data.starterCategorySlugs ?? []);
            const starter = (template.starter_categories ?? [])
                .filter((entry) => selected.has(entry.slug ?? slugify(entry.label ?? '')));
            const custom = modalLines(data.customCategories)
                .map((label) => ({label, slug: slugify(label), description: null}));
            const seen = new Set();
            return [...starter, ...custom].filter((entry) => {
                if (!entry.slug || seen.has(entry.slug)) return false;
                seen.add(entry.slug);
                return true;
            });
        }

        async function createModalDraftShell() {
            const data = S.modal.data;
            const selectedTemplate = modalSelectedTemplate();
            if (!selectedTemplate) {
                throw new Error('Aucun type de jeu disponible.');
            }

            N.modalNext.textContent = 'Création...';

            const payload = await studioApi('/game-drafts', {
                method: 'POST',
                body: {
                    category_key: data.templateKey,
                    name: data.name.trim(),
                    slug: data.slug.trim() || null,
                },
            });
            syncDraftFromResponse(payload.data);
            resetCreateForm();

            await studioApi(`/game-drafts/${S.draft.id}/foundation`, {
                method: 'PATCH',
                body: {
                    name: data.name.trim(),
                    slug: S.draft.slug,
                    description: data.description.trim() || null,
                    visibility: data.visibility || 'private',
                },
            });

            await studioApi(`/game-drafts/${S.draft.id}/structure`, {
                method: 'PATCH',
                body: {
                    supports_modifiers: Boolean(data.supportsModifiers),
                    supports_archetypes: Boolean(data.supportsArchetypes),
                    allowed_item_slots: data.allowedItemSlots,
                    allowed_modifier_types: data.supportsModifiers ? data.allowedModifierTypes : [],
                },
            });

            if (data.supportsArchetypes) {
                for (const entry of selectedModalArchetypes(selectedTemplate, data)) {
                    await studioApi(`/game-drafts/${S.draft.id}/archetypes`, {
                        method: 'POST',
                        body: {
                            name: entry.name,
                            slug: entry.slug ?? slugify(entry.name ?? ''),
                            description: entry.description ?? null,
                        },
                    });
                }
            }

            data.createdCategories = [];
            for (const entry of selectedModalCategories(selectedTemplate, data)) {
                const created = await studioApi(`/game-drafts/${S.draft.id}/categories`, {
                    method: 'POST',
                    body: {
                        label: entry.label,
                        slug: entry.slug,
                        description: entry.description ?? null,
                    },
                });
                if (created.data?.id) data.createdCategories.push(created.data);
            }

            data.createdDraftId = S.draft.id;
            await loadDraft(S.draft.id, 'Jeu créé. Continue dans la popup.');
        }

        async function persistModalContentForCurrentStep() {
            const data = S.modal.data;
            if (!S.draft?.id) return;

            if (S.modal.stepKey === 'items' && !(data.createdItems ?? []).length) {
                N.modalNext.textContent = 'Ajout...';
                const slots = data.allowedItemSlots?.length ? data.allowedItemSlots : ['item'];
                const categories = (data.createdCategories?.length ? data.createdCategories : (S.draft.categories ?? []));
                const categoryIds = categories.map((category) => Number(category.id)).filter(Number.isFinite);
                data.createdItems = [];

                for (const [index, name] of modalLines(data.itemsText).entries()) {
                    const created = await studioApi(`/game-drafts/${S.draft.id}/items`, {
                        method: 'POST',
                        body: {
                            name,
                            slug: `${slugify(name) || 'item'}-${index + 1}`,
                            slot: slots[index % slots.length],
                            rarity: 'standard',
                            tier_level: `T${index + 1}`,
                            score: 10 + index,
                            attack: 0,
                            defense: 0,
                            utility: 0,
                            accuracy: 0,
                            mobility: 0,
                            category_ids: categoryIds,
                            item_slots: [],
                        },
                    });
                    if (created.data?.id) data.createdItems.push(created.data);
                }

                await loadDraft(S.draft.id, 'Équipements ajoutés.');
            }

            if (S.modal.stepKey === 'modifiers' && data.supportsModifiers && !(data.createdModifiers ?? []).length) {
                N.modalNext.textContent = 'Ajout...';
                const types = data.allowedModifierTypes?.length ? data.allowedModifierTypes : ['modifier'];
                const items = (data.createdItems?.length ? data.createdItems : (S.draft.items ?? []));
                const itemIds = items.map((item) => Number(item.id)).filter(Number.isFinite);
                data.createdModifiers = [];

                for (const [index, name] of modalLines(data.modifiersText).entries()) {
                    const created = await studioApi(`/game-drafts/${S.draft.id}/modifiers`, {
                        method: 'POST',
                        body: {
                            name,
                            slug: `${slugify(name) || 'modifier'}-${index + 1}`,
                            modifier_type: types[index % types.length],
                            description: `Modificateur ${name}`,
                            delta_score: 1,
                            delta_attack: 0,
                            delta_defense: 0,
                            delta_utility: 0,
                            delta_accuracy: 0,
                            delta_mobility: 0,
                            compatible_item_ids: itemIds,
                        },
                    });
                    if (created.data?.id) data.createdModifiers.push(created.data);
                }

                await loadDraft(S.draft.id, 'Modificateurs ajoutés.');
            }
        }

        async function publishModalDraft() {
            const data = S.modal.data;
            if (!S.draft?.id) {
                throw new Error('Crée le jeu avant de publier.');
            }

            N.modalNext.textContent = 'Publication...';
            const payload = await studioApi(`/game-drafts/${S.draft.id}/publish`, {method: 'POST'});
            data.publishedGame = payload.data?.game ?? null;
            syncDraftFromResponse(payload.data?.draft);
            S.activeStep = 'review';
            render();
            setStatus('Jeu publié avec succès.');
        }

        N.modalBack.addEventListener('click', () => {
            captureModalFields();
            const steps = modalSteps();
            const index = currentModalIndex();
            if (index > 0) {
                S.modal.stepKey = steps[index - 1];
                renderModal();
            }
        });

        N.modalNext.addEventListener('click', async () => {
            if (S.modal.busy) return;
            S.modal.busy = true;
            N.modalNext.disabled = true;
            captureModalFields();

            try {
                const data = S.modal.data;
                const steps = modalSteps();
                const index = currentModalIndex();

                if (S.modal.stepKey === 'name' && !data.name.trim()) {
                    setStatus('Le nom du jeu est requis.', 'danger');
                    return;
                }

                if (S.modal.stepKey === 'description' && !data.description.trim()) {
                    setStatus('La description est requise pour publier le jeu.', 'danger');
                    return;
                }

                if (S.modal.stepKey === 'options' && !data.allowedItemSlots.length) {
                    setStatus('Sélectionne au moins un slot d item.', 'danger');
                    return;
                }

                if (S.modal.stepKey === 'options' && data.supportsModifiers && !data.allowedModifierTypes.length) {
                    setStatus('Sélectionne au moins un type de modificateur ou désactive les modificateurs.', 'danger');
                    return;
                }

                if (S.modal.stepKey === 'categories') {
                    const categoryTotal = new Set([
                        ...(data.starterCategorySlugs ?? []),
                        ...modalLines(data.customCategories).map((label) => slugify(label)),
                    ].filter(Boolean)).size;
                    const minCategories = templateMinimum('categories', 2);
                    if (categoryTotal < minCategories) {
                        setStatus(`Ajoute au moins ${minCategories} catégorie(s) pour publier ce jeu.`, 'danger');
                        return;
                    }
                }

                if (S.modal.stepKey === 'items' && modalLines(data.itemsText).length < templateMinimum('items', 1)) {
                    setStatus(`Ajoute au moins ${templateMinimum('items', 1)} équipement(s) pour publier ce jeu.`, 'danger');
                    return;
                }

                if (S.modal.stepKey === 'modifiers' && data.supportsModifiers && modalLines(data.modifiersText).length < templateMinimum('modifiers', 1)) {
                    setStatus(`Ajoute au moins ${templateMinimum('modifiers', 1)} modificateur(s) ou désactive les modificateurs.`, 'danger');
                    return;
                }

                if (data.createdDraftId && S.modal.stepKey !== 'publish') {
                    await persistModalContentForCurrentStep();
                }

                if (index < steps.length - 1) {
                    if (S.modal.stepKey === 'categories' && !data.createdDraftId) {
                        await createModalDraftShell();
                    }
                    S.modal.stepKey = steps[index + 1];
                    renderModal();
                    return;
                }

                if (S.modal.stepKey === 'publish') {
                    if (data.publishedGame) {
                        closeCreationModal();
                        render();
                        return;
                    }
                    await publishModalDraft();
                    renderModal();
                }
            } catch (error) {
                setStatus(error.message, 'danger');
            } finally {
                S.modal.busy = false;
                if (S.modal.open) N.modalNext.disabled = false;
            }
        });

        N.creationModal.addEventListener('click', (event) => {
            if (event.target === N.creationModal) closeCreationModal();
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && S.modal.open) closeCreationModal();
        });

        (async () => {
            sortDrafts();
            bootSelectedTemplate();
            renderDraftList();

            if (S.selectedDraftId) {
                await loadDraft(S.selectedDraftId, 'Jeu chargé.');
                return;
            }

            render();
        })();
    </script>
</x-layouts.app>
