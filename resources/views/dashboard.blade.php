<x-layouts.app :title="'Dashboard - Wiki Builder API'">
    @php
        $user = auth()->user();
        $featuredDraft = $drafts->sortByDesc(fn ($draft) => $draft->updated_at ?? $draft->created_at)->first();
    @endphp

    <section class="panel hero-panel">
        <div class="hero-main">
            <div class="hero-copy">
                <span class="eyebrow">Dashboard</span>
                <h1>Ton espace de pilotage.</h1>
                <p class="lead">
                    Gère tes brouillons, suis tes publications et garde la main sur ton espace créateur
                    depuis une interface plus claire et plus directe.
                </p>
                <div class="hero-actions">
                    <a class="btn primary" href="{{ route('studio.games.create') }}">Créer un jeu</a>
                    <a class="btn ghost" href="{{ route('account') }}">Mon compte</a>
                    <a class="btn subtle" href="{{ route('home') }}">Retour à l'accueil</a>
                </div>
                <div class="hero-note">
                    <span>{{ $user->display_name ?: $user->username }}</span>
                    <span>•</span>
                    <span>{{ $drafts->count() }} brouillon(s)</span>
                    <span>•</span>
                    <span>{{ $games->count() }} publication(s)</span>
                </div>
            </div>

            <div class="hero-side">
                <div class="signal-grid">
                    <article class="signal accent">
                        <span class="label">Brouillons</span>
                        <strong class="value">{{ $drafts->count() }}</strong>
                    </article>
                    <article class="signal">
                        <span class="label">Jeux publiés</span>
                        <strong class="value">{{ $games->count() }}</strong>
                    </article>
                    <article class="signal">
                        <span class="label">Builds</span>
                        <strong class="value">{{ $builds->count() }}</strong>
                    </article>
                    <article class="signal success">
                        <span class="label">Brouillons finalisés</span>
                        <strong class="value">{{ $drafts->where('status', 'published')->count() }}</strong>
                    </article>
                </div>

                <article class="spotlight-card">
                    <span class="eyebrow">Focus</span>
                    @if ($featuredDraft)
                        <h2>{{ $featuredDraft->name }}</h2>
                        <div class="row-meta">
                            <span class="tag">{{ $featuredDraft->current_step }}</span>
                            <span class="tag">{{ $featuredDraft->status }}</span>
                            @if ($featuredDraft->category_key)
                                <span class="tag">{{ $featuredDraft->category_key }}</span>
                            @endif
                        </div>
                        <div class="progress">
                            <span style="width: {{ max(0, min(100, (int) ($featuredDraft->completion_percent ?? 0))) }}%"></span>
                        </div>
                        <p class="helper compact">
                            Progression {{ $featuredDraft->completion_percent ?? 0 }}%
                            @if ($featuredDraft->updated_at || $featuredDraft->created_at)
                                • Mis à jour le {{ ($featuredDraft->updated_at ?? $featuredDraft->created_at)?->format('d/m/Y') }}
                            @endif
                        </p>
                        <div class="section-actions">
                            <a class="btn ghost" href="{{ route('studio.games.create', ['draft' => $featuredDraft->id]) }}">Ouvrir le brouillon</a>
                        </div>
                    @else
                        <h2>Prêt à lancer un premier jeu</h2>
                        <p class="helper compact">
                            Commence un brouillon, structure-le, puis publie-le quand il est prêt.
                        </p>
                        <div class="section-actions">
                            <a class="btn ghost" href="{{ route('studio.games.create') }}">Créer maintenant</a>
                        </div>
                    @endif
                </article>
            </div>
        </div>
    </section>

    <section class="section-shell">
        <article class="panel section-card">
            <div class="section-head">
                <div class="section-copy">
                    <span class="eyebrow">Brouillons</span>
                    <h2>Récents</h2>
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
                                    {{ $draft->completion_percent ?? 0 }}% terminé
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
                    <strong>Aucun brouillon pour l'instant</strong>
                    <p class="helper compact">Le meilleur point de départ est un premier draft bien cadré.</p>
                    <div class="section-actions">
                        <a class="btn primary" href="{{ route('studio.games.create') }}">Créer un brouillon</a>
                    </div>
                </div>
            @endif
        </article>

        <div class="stack">
            <article class="panel section-card">
                <div class="section-head">
                    <div class="section-copy">
                        <span class="eyebrow">Publications</span>
                        <h2>En ligne</h2>
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
                        <p class="helper compact">Tes jeux validés apparaîtront ici dès leur mise en ligne.</p>
                    </div>
                @endif
            </article>

            <article class="panel action-card">
                <span class="eyebrow">Accès rapide</span>
                <h3>Raccourcis utiles</h3>
                <div class="action-list">
                    <article>
                        <strong>Compte</strong>
                        <p class="helper compact">Gérer tes données et tes paramètres.</p>
                        <div class="section-actions">
                            <a class="btn ghost" href="{{ route('account') }}">Ouvrir</a>
                        </div>
                    </article>
                    <article>
                        <strong>API</strong>
                        <p class="helper compact">Voir les routes et la documentation disponible.</p>
                        <div class="section-actions">
                            <a class="btn ghost" href="/api/v1">Explorer</a>
                        </div>
                    </article>
                </div>
            </article>
        </div>
    </section>
</x-layouts.app>
