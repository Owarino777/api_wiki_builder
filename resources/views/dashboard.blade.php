<x-layouts.app :title="'Dashboard - Wiki Builder API'">
    <section class="panel card">
        <span class="pill">Espace utilisateur</span>
        <div>
            <h1 style="font-size:40px;line-height:1;">{{ auth()->user()->display_name ?: auth()->user()->username }}</h1>
            <p class="helper" style="margin-top:10px;">La session web sert maintenant de point d'entrée pour créer un jeu en brouillon, le compléter puis le publier sans passer par les widgets de démo du builder public.</p>
        </div>

        <div class="actions">
            <a class="btn primary" href="{{ route('studio.games.create') }}">Créer un jeu</a>
            <a class="btn" href="{{ route('account') }}">Mon compte</a>
            <a class="btn" href="{{ route('home') }}">Retour à l'accueil</a>
            <a class="btn" href="/api/v1">Voir l'API</a>
        </div>

        <div class="stats">
            <article class="metric">
                <strong>{{ $drafts->count() }}</strong>
                <span>Brouillons récents</span>
            </article>
            <article class="metric">
                <strong>{{ $games->count() }}</strong>
                <span>Jeux publiés</span>
            </article>
            <article class="metric">
                <strong>{{ $builds->count() }}</strong>
                <span>Builds personnels</span>
            </article>
        </div>
    </section>

    <section class="grid">
        <article class="panel card">
            <h2>Mes brouillons de jeux</h2>
            <p class="helper">Le parcours conseillé par la spec reste bien séparé : foundation, structure, archetypes, categories, items, modifiers, puis publication.</p>
            <div class="list">
                @forelse ($drafts as $draft)
                    <article>
                        <strong>{{ $draft->name }}</strong>
                        <div class="meta">
                            <span class="pill">{{ $draft->category_key }}</span>
                            <span class="pill">{{ $draft->status }}</span>
                            <span class="pill">{{ $draft->current_step }}</span>
                        </div>
                        <p class="helper">Complétion : {{ $draft->completion_percent ?? 0 }}%</p>
                        <div class="actions">
                            <a class="btn" href="{{ route('studio.games.create', ['draft' => $draft->id]) }}">Ouvrir le draft</a>
                        </div>
                    </article>
                @empty
                    <article>
                        <strong>Aucun draft pour le moment</strong>
                        <p class="helper">Commence par un brouillon FPS ou RPG cadré, puis complète les équipements dans la page dédiée.</p>
                    </article>
                @endforelse
            </div>
        </article>

        <article class="panel card">
            <h2>Mes jeux publiés</h2>
            <p class="helper">Les jeux publiés par l'utilisateur restent privés par défaut en V1 et sont visibles depuis l'espace owner.</p>
            <div class="list">
                @forelse ($games as $game)
                    <article>
                        <strong>{{ $game->name }}</strong>
                        <div class="meta">
                            <span class="pill">{{ $game->creation_category ?: 'system' }}</span>
                            <span class="pill">{{ $game->visibility }}</span>
                        </div>
                    </article>
                @empty
                    <article>
                        <strong>Aucun jeu utilisateur publié</strong>
                        <p class="helper">Quand un draft passe la revue finale, il apparaît ici et dans ton endpoint owner.</p>
                    </article>
                @endforelse
            </div>
        </article>
    </section>
</x-layouts.app>
