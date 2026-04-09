<x-layouts.app :title="'Dashboard - Wiki Builder API'" bodyClass="page-dashboard">
    @php
        $user = auth()->user();
        $featuredDraft = $drafts->sortByDesc(fn ($draft) => $draft->updated_at ?? $draft->created_at)->first();
    @endphp

    <section class="page-head">
        <div>
            <span class="eyebrow">Dashboard</span>
            <h1>Pilotage</h1>
            <span class="subtitle">Control Center</span>
            <hr class="neon-line">
            <p class="helper">Reprends un draft, crée un nouveau jeu ou consulte tes publications.</p>
            <div class="page-meta">
                <span>{{ $user->display_name ?: $user->username }}</span>
                <span>{{ $drafts->count() }} brouillon(s)</span>
                <span>{{ $games->count() }} publication(s)</span>
            </div>
        </div>
        <div class="page-head-actions">
            @if ($featuredDraft)
                <a class="btn primary" href="{{ route('studio.games.create', ['draft' => $featuredDraft->id]) }}">Reprendre</a>
            @endif
            <a class="btn ghost" href="{{ route('studio.games.create') }}">Nouveau draft</a>
            <a class="btn subtle" href="{{ route('home') }}">Builder</a>
        </div>
    </section>

    <section class="page-kpis">
        <article class="kpi-card corner-brackets">
            <div class="kpi-icon">
                <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            </div>
            <div class="kpi-content">
                <strong>{{ $drafts->count() }}</strong>
                <span class="kpi-label">Brouillons</span>
            </div>
        </article>
        <article class="kpi-card corner-brackets">
            <div class="kpi-icon">
                <svg viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            </div>
            <div class="kpi-content">
                <strong>{{ $games->count() }}</strong>
                <span class="kpi-label">Jeux publiés</span>
            </div>
        </article>
        <article class="kpi-card corner-brackets">
            <div class="kpi-icon">
                <svg viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
            </div>
            <div class="kpi-content">
                <strong>{{ $builds->count() }}</strong>
                <span class="kpi-label">Builds</span>
            </div>
        </article>
        <article class="kpi-card corner-brackets">
            <div class="kpi-icon">
                <svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div class="kpi-content">
                <strong>{{ $drafts->where('status', 'published')->count() }}</strong>
                <span class="kpi-label">Drafts finalisés</span>
            </div>
        </article>
    </section>

    <section class="section-shell">
        <article class="panel section-card">
            <div class="section-head">
                <div class="section-copy">
                    <span class="eyebrow">Brouillons</span>
                    <h2>Récents</h2>
                    <p class="helper compact">Nom, étape, statut, progression.</p>
                </div>
                <a class="btn ghost" href="{{ route('studio.games.create') }}">Nouveau</a>
            </div>

            @if ($drafts->isNotEmpty())
                <div class="list-rows">
                    @foreach ($drafts as $draft)
                        <article class="list-row">
                            <div class="row-main">
                                <strong>{{ $draft->name }}</strong>
                                <div class="row-meta">
                                    <span class="tag">{{ $draft->current_step }}</span>
                                    <span class="tag">{{ $draft->status }}</span>
                                    @if ($draft->category_key)
                                        <span class="tag">{{ $draft->category_key }}</span>
                                    @endif
                                </div>
                                <small>
                                    {{ $draft->completion_percent ?? 0 }}%
                                    @if ($draft->updated_at || $draft->created_at)
                                        • {{ ($draft->updated_at ?? $draft->created_at)?->format('d/m/Y') }}
                                    @endif
                                </small>
                            </div>
                            <div class="row-actions">
                                <a class="btn ghost" href="{{ route('studio.games.create', ['draft' => $draft->id]) }}">Ouvrir</a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="empty-block">
                    <strong>Aucun brouillon</strong>
                    <p class="helper compact">Crée un draft pour démarrer l’édition.</p>
                    <div class="section-actions">
                        <a class="btn primary" href="{{ route('studio.games.create') }}">Créer un draft</a>
                    </div>
                </div>
            @endif
        </article>

        <div class="stack">
            <article class="panel section-card">
                <div class="section-head">
                    <div class="section-copy">
                        <span class="eyebrow">Publications</span>
                        <h2>Jeux en ligne</h2>
                        <p class="helper compact">Vue rapide des jeux publiés.</p>
                    </div>
                </div>

                @if ($games->isNotEmpty())
                    <div class="list-rows">
                        @foreach ($games as $game)
                            <article class="list-row">
                                <div class="row-main">
                                    <strong>{{ $game->name }}</strong>
                                    <div class="row-meta">
                                        <span class="tag">{{ $game->creation_category ?: 'system' }}</span>
                                        <span class="tag">{{ $game->visibility }}</span>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="empty-block">
                        <strong>Aucune publication</strong>
                        <p class="helper compact">Tes jeux publiés apparaîtront ici.</p>
                    </div>
                @endif
            </article>

            <article class="panel section-card">
                <div class="section-head">
                    <div class="section-copy">
                        <span class="eyebrow">Compte</span>
                        <h2>Accès rapides</h2>
                    </div>
                </div>
                <div class="actions">
                    <a class="btn ghost" href="{{ route('account') }}">Mon compte</a>
                    <a class="btn ghost" href="{{ route('privacy') }}">Confidentialité</a>
                </div>
            </article>
        </div>
    </section>
</x-layouts.app>
