<x-layouts.app :title="'Confidentialité - Wiki Builder API'">
    <section class="panel hero-panel">
        <div class="hero-main">
            <div class="hero-copy">
                <span class="eyebrow">Confidentialité</span>
                <h1>Une politique lisible et utile.</h1>
                <p class="lead">
                    Cette page résume ce que Wiki Builder traite, pourquoi ces données existent
                    et comment tu gardes la main sur tes droits.
                </p>
                <div class="hero-actions">
                    @auth
                        <a class="btn primary" href="{{ route('account') }}">Mon compte</a>
                        <a class="btn ghost" href="{{ route('account.export') }}">Exporter mes données</a>
                    @else
                        <a class="btn primary" href="{{ route('register') }}">Créer un compte</a>
                        <a class="btn ghost" href="{{ route('login') }}">Se connecter</a>
                    @endauth
                </div>
                <div class="hero-note">
                    <span>Version {{ $policyVersion }}</span>
                    <span>•</span>
                    <span>Contact {{ $contactEmail }}</span>
                </div>
            </div>

            <article class="spotlight-card">
                <span class="eyebrow">Principes</span>
                <h2>Protection par défaut</h2>
                <div class="keyline">
                    <span>Collecte</span>
                    <strong>Minimisée</strong>
                </div>
                <div class="keyline">
                    <span>Actions sensibles</span>
                    <strong>Mot de passe + session</strong>
                </div>
                <div class="keyline">
                    <span>Droits</span>
                    <strong>Export, anonymisation, suppression</strong>
                </div>
                <div class="keyline">
                    <span>Référence</span>
                    <strong>{{ $policyVersion }}</strong>
                </div>
            </article>
        </div>
    </section>

    <section class="policy-grid">
        <article class="panel section-card">
            <div class="section-head">
                <div class="section-copy">
                    <span class="eyebrow">Traitements</span>
                    <h2>Données collectées</h2>
                </div>
            </div>
            <div class="feature-list">
                <article>
                    <strong>Compte utilisateur</strong>
                    <p class="helper compact">Email, username, mot de passe chiffré, nom affiché et métadonnées utiles à la session.</p>
                </article>
                <article>
                    <strong>Contenus créés</strong>
                    <p class="helper compact">Jeux, builds, brouillons et paramètres rattachés à ton compte.</p>
                </article>
                <article>
                    <strong>Consentements</strong>
                    <p class="helper compact">Acceptation de la politique et consentement marketing lorsqu'il est donné.</p>
                </article>
            </div>
        </article>

        <article class="panel section-card">
            <div class="section-head">
                <div class="section-copy">
                    <span class="eyebrow">Finalités</span>
                    <h2>Pourquoi ces données existent</h2>
                </div>
            </div>
            <div class="feature-list">
                <article>
                    <strong>Fournir le service</strong>
                    <p class="helper compact">Créer un compte, maintenir une session et rattacher les contenus à la bonne personne.</p>
                </article>
                <article>
                    <strong>Sécuriser l'application</strong>
                    <p class="helper compact">Protéger l'accès, limiter les abus et encadrer les actions sensibles.</p>
                </article>
                <article>
                    <strong>Respecter tes droits</strong>
                    <p class="helper compact">Permettre l'export, l'anonymisation et la suppression du compte.</p>
                </article>
            </div>
        </article>
    </section>

    <section class="duo-grid">
        <article class="panel action-card">
            <span class="eyebrow">Tes droits</span>
            <h3>Ce que tu peux faire</h3>
            <div class="action-list">
                <article>
                    <strong>Accéder</strong>
                    <p class="helper compact">Exporter un JSON lisible depuis la page Mon compte.</p>
                </article>
                <article>
                    <strong>Modifier ou retirer</strong>
                    <p class="helper compact">Piloter ton compte puis demander anonymisation ou suppression.</p>
                </article>
                <article>
                    <strong>Contacter</strong>
                    <p class="helper compact">{{ $contactEmail }}</p>
                </article>
            </div>
        </article>

        <article class="panel action-card">
            <span class="eyebrow">Garanties</span>
            <h3>Principes appliqués</h3>
            <div class="feature-list">
                <article>
                    <strong>Minimisation</strong>
                    <p class="helper compact">Seules les données utiles au service sont demandées.</p>
                </article>
                <article>
                    <strong>Protection par défaut</strong>
                    <p class="helper compact">CSRF, validation serveur, session web et vérification du mot de passe.</p>
                </article>
                <article>
                    <strong>Transparence</strong>
                    <p class="helper compact">Les droits et informations restent accessibles depuis l'espace compte.</p>
                </article>
            </div>
        </article>
    </section>
</x-layouts.app>
