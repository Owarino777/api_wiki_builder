<x-layouts.app :title="'Dashboard - Wiki Builder API'">
    <section class="panel card">
        <span class="pill">Espace utilisateur</span>
        <div>
            <h1 style="font-size:40px;line-height:1;">{{ auth()->user()->display_name ?: auth()->user()->username }}</h1>
            <p class="helper" style="margin-top:10px;">La session web sert maintenant de point d entree pour creer un jeu en brouillon, le completer puis le publier sans passer par les widgets de demo du builder public.</p>
        </div>

        <div class="actions">
            <a class="btn primary" href="{{ route('studio.games.create') }}">Creer un jeu</a>
            <a class="btn" href="{{ route('home') }}">Retour a l accueil</a>
            <a class="btn" href="/api/v1">Voir l API</a>
        </div>

        <div class="stats">
            <article class="metric">
                <strong>{{ $drafts->count() }}</strong>
                <span>Brouillons recents</span>
            </article>
            <article class="metric">
                <strong>{{ $games->count() }}</strong>
                <span>Jeux publies</span>
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
            <p class="helper">Le parcours conseille par la spec reste bien separe : foundation, structure, archetypes, categories, items, modifiers, puis publication.</p>
            <div class="list">
                @forelse ($drafts as $draft)
                    <article>
                        <strong>{{ $draft->name }}</strong>
                        <div class="meta">
                            <span class="pill">{{ $draft->category_key }}</span>
                            <span class="pill">{{ $draft->status }}</span>
                            <span class="pill">{{ $draft->current_step }}</span>
                        </div>
                        <p class="helper">Completion : {{ $draft->completion_percent ?? 0 }}%</p>
                        <div class="actions">
                            <a class="btn" href="{{ route('studio.games.create', ['draft' => $draft->id]) }}">Ouvrir le draft</a>
                        </div>
                    </article>
                @empty
                    <article>
                        <strong>Aucun draft pour le moment</strong>
                        <p class="helper">Commence par un brouillon FPS ou RPG cadre, puis complete les equipements dans la page dediee.</p>
                    </article>
                @endforelse
            </div>
        </article>

        <article class="panel card">
            <h2>Mes jeux publies</h2>
            <p class="helper">Les jeux publies par l utilisateur restent prives par defaut en V1 et sont visibles depuis l espace owner.</p>
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
                        <strong>Aucun jeu utilisateur publie</strong>
                        <p class="helper">Quand un draft passe la revue finale, il apparait ici et dans ton endpoint owner.</p>
                    </article>
                @endforelse
            </div>
        </article>
    </section>
</x-layouts.app>
