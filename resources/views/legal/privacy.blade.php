<x-layouts.app :title="'Confidentialité - Wiki Builder API'">
    <section class="panel hero-panel">
        <div class="hero-main">
            <div class="hero-copy">
                <span class="eyebrow">Confidentialité</span>
                <h1>Politique transparente</h1>
                <hr class="neon-line">
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

            <article class="spotlight-card corner-brackets">
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
                <div class="section-head-row">
                    <div class="section-icon">
                        <svg viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 002 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                    </div>
                    <div class="section-copy">
                        <span class="eyebrow">Traitements</span>
                        <h2>Données collectées</h2>
                    </div>
                </div>
            </div>
            <div class="feature-list">
                <article class="feature-item">
                    <div class="feature-item-icon">
                        <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                    <div>
                        <strong>Compte utilisateur</strong>
                        <p class="helper compact">Email, username, mot de passe chiffré, nom affiché et métadonnées utiles à la session.</p>
                    </div>
                </article>
                <article class="feature-item">
                    <div class="feature-item-icon">
                        <svg viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                    </div>
                    <div>
                        <strong>Contenus créés</strong>
                        <p class="helper compact">Jeux, builds, brouillons et paramètres rattachés à ton compte.</p>
                    </div>
                </article>
                <article class="feature-item">
                    <div class="feature-item-icon">
                        <svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </div>
                    <div>
                        <strong>Consentements</strong>
                        <p class="helper compact">Acceptation de la politique et consentement marketing lorsqu'il est donné.</p>
                    </div>
                </article>
            </div>
        </article>

        <article class="panel section-card">
            <div class="section-head">
                <div class="section-head-row">
                    <div class="section-icon">
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                    </div>
                    <div class="section-copy">
                        <span class="eyebrow">Finalités</span>
                        <h2>Pourquoi ces données existent</h2>
                    </div>
                </div>
            </div>
            <div class="feature-list">
                <article class="feature-item">
                    <div class="feature-item-icon">
                        <svg viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                    </div>
                    <div>
                        <strong>Fournir le service</strong>
                        <p class="helper compact">Créer un compte, maintenir une session et rattacher les contenus à la bonne personne.</p>
                    </div>
                </article>
                <article class="feature-item">
                    <div class="feature-item-icon">
                        <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <div>
                        <strong>Sécuriser l'application</strong>
                        <p class="helper compact">Protéger l'accès, limiter les abus et encadrer les actions sensibles.</p>
                    </div>
                </article>
                <article class="feature-item">
                    <div class="feature-item-icon">
                        <svg viewBox="0 0 24 24"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                    </div>
                    <div>
                        <strong>Respecter tes droits</strong>
                        <p class="helper compact">Permettre l'export, l'anonymisation et la suppression du compte.</p>
                    </div>
                </article>
            </div>
        </article>
    </section>

    <section class="duo-grid">
        <article class="panel action-card">
            <div class="section-head-row">
                <div class="section-icon">
                    <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                </div>
                <div>
                    <span class="eyebrow">Tes droits</span>
                    <h3>Ce que tu peux faire</h3>
                </div>
            </div>
            <div class="action-list">
                <article class="feature-item">
                    <div class="feature-item-icon">
                        <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    </div>
                    <div>
                        <strong>Accéder</strong>
                        <p class="helper compact">Exporter un JSON lisible depuis la page Mon compte.</p>
                    </div>
                </article>
                <article class="feature-item">
                    <div class="feature-item-icon">
                        <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </div>
                    <div>
                        <strong>Modifier ou retirer</strong>
                        <p class="helper compact">Piloter ton compte puis demander anonymisation ou suppression.</p>
                    </div>
                </article>
                <article class="feature-item">
                    <div class="feature-item-icon">
                        <svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </div>
                    <div>
                        <strong>Contacter</strong>
                        <p class="helper compact">{{ $contactEmail }}</p>
                    </div>
                </article>
            </div>
        </article>

        <article class="panel action-card">
            <div class="section-head-row">
                <div class="section-icon">
                    <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <div>
                    <span class="eyebrow">Garanties</span>
                    <h3>Principes appliqués</h3>
                </div>
            </div>
            <div class="feature-list">
                <article class="feature-item">
                    <div class="feature-item-icon">
                        <svg viewBox="0 0 24 24"><line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/></svg>
                    </div>
                    <div>
                        <strong>Minimisation</strong>
                        <p class="helper compact">Seules les données utiles au service sont demandées.</p>
                    </div>
                </article>
                <article class="feature-item">
                    <div class="feature-item-icon">
                        <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                    </div>
                    <div>
                        <strong>Protection par défaut</strong>
                        <p class="helper compact">CSRF, validation serveur, session web et vérification du mot de passe.</p>
                    </div>
                </article>
                <article class="feature-item">
                    <div class="feature-item-icon">
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>
                    </div>
                    <div>
                        <strong>Transparence</strong>
                        <p class="helper compact">Les droits et informations restent accessibles depuis l'espace compte.</p>
                    </div>
                </article>
            </div>
        </article>
    </section>
</x-layouts.app>
