<x-layouts.app :title="'Mon compte - Wiki Builder API'">
    @php
        $displayName = $user->display_name ?: $user->username;
        $accountStatus = $user->anonymized_at ? 'Anonymisé' : 'Actif';
    @endphp

    <section class="panel hero-panel">
        <div class="hero-main">
            <div class="hero-copy">
                <span class="eyebrow">Account center</span>
                <h1>Ton compte, sans friction.</h1>
                <p class="lead">
                    Retrouve ici tes informations essentielles, tes droits RGPD et les actions sensibles
                    dans un espace plus clair, plus sûr et plus lisible.
                </p>
                <div class="hero-actions">
                    <a class="btn primary" href="{{ route('account.export') }}">Exporter mes données</a>
                    <a class="btn ghost" href="{{ route('privacy') }}">Voir la politique</a>
                    <a class="btn subtle" href="{{ route('dashboard') }}">Retour au dashboard</a>
                </div>
                <div class="hero-note">
                    <span>{{ $displayName }}</span>
                    <span>•</span>
                    <span>{{ $user->email }}</span>
                </div>
            </div>

            <article class="spotlight-card">
                <span class="eyebrow">Aperçu</span>
                <h2>{{ $displayName }}</h2>
                <div class="keyline">
                    <span>Statut du compte</span>
                    <strong>{{ $accountStatus }}</strong>
                </div>
                <div class="keyline">
                    <span>Politique acceptée</span>
                    <strong>{{ $user->privacy_policy_accepted_at?->format('d/m/Y') ?? 'Non renseigné' }}</strong>
                </div>
                <div class="keyline">
                    <span>Dernière connexion</span>
                    <strong>{{ $user->last_login_at?->format('d/m/Y') ?? 'Première session' }}</strong>
                </div>
                <div class="keyline">
                    <span>Marketing</span>
                    <strong>{{ $user->marketing_consent_at ? 'Accordé' : 'Non accordé' }}</strong>
                </div>
                <div class="keyline">
                    <span>Version de politique</span>
                    <strong>{{ $policyVersion }}</strong>
                </div>
            </article>
        </div>
    </section>

    <section class="info-grid">
        <article class="info-card">
            <span>Email</span>
            <strong>{{ $user->email }}</strong>
        </article>
        <article class="info-card">
            <span>Username</span>
            <strong>{{ $user->username }}</strong>
        </article>
        <article class="info-card">
            <span>Rôle</span>
            <strong>{{ $user->role->value ?? $user->role }}</strong>
        </article>
        <article class="info-card">
            <span>Session</span>
            <strong>{{ $user->last_login_at?->format('d/m/Y H:i') ?? 'En cours' }}</strong>
        </article>
    </section>

    <section class="section-shell">
        <article class="panel section-card">
            <div class="section-head">
                <div class="section-copy">
                    <span class="eyebrow">Données</span>
                    <h2>Ce qui est conservé</h2>
                </div>
            </div>

            <div class="feature-list">
                <article>
                    <strong>Identité de compte</strong>
                    <p class="helper compact">Email, username, rôle, dates techniques et mot de passe chiffré.</p>
                </article>
                <article>
                    <strong>Contenus liés</strong>
                    <p class="helper compact">Jeux, builds et brouillons associés à ton espace utilisateur.</p>
                </article>
                <article>
                    <strong>Consentements</strong>
                    <p class="helper compact">Acceptation de la politique, dernière connexion et choix marketing optionnel.</p>
                </article>
            </div>
        </article>

        <article class="panel action-card">
            <span class="eyebrow">Actions rapides</span>
            <h3>Confidentialité</h3>
            <div class="action-list">
                <article>
                    <strong>Export RGPD</strong>
                    <p class="helper compact">Téléchargement immédiat de tes données au format JSON.</p>
                    <div class="section-actions">
                        <a class="btn ghost" href="{{ route('account.export') }}">Télécharger</a>
                    </div>
                </article>
                <article>
                    <strong>Politique de confidentialité</strong>
                    <p class="helper compact">Version {{ $policyVersion }} avec les principes appliqués.</p>
                    <div class="section-actions">
                        <a class="btn ghost" href="{{ route('privacy') }}">Consulter</a>
                    </div>
                </article>
            </div>
        </article>
    </section>

    <section class="duo-grid">
        <article class="panel action-card">
            <span class="eyebrow">Anonymisation</span>
            <h3>Retirer mes données nominatives</h3>
            <p class="helper compact">
                Cette action retire les données personnelles du compte et ferme la session.
            </p>
            <form method="POST" action="{{ route('account.anonymize') }}">
                @csrf
                <div class="field">
                    <label for="anonymize-password">Mot de passe actuel</label>
                    <input id="anonymize-password" name="anonymize_password" type="password" required autocomplete="current-password">
                    @error('anonymize_password')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>
                <button class="btn ghost" type="submit">Anonymiser mon compte</button>
            </form>
        </article>

        <article class="panel action-card danger">
            <span class="eyebrow">Suppression</span>
            <h3>Supprimer définitivement mon compte</h3>
            <p class="helper compact">
                Cette zone est volontairement isolée. La suppression est irréversible et met fin à la session.
            </p>
            <form method="POST" action="{{ route('account.destroy') }}">
                @csrf
                @method('DELETE')
                <div class="field">
                    <label for="delete-password">Mot de passe actuel</label>
                    <input id="delete-password" name="delete_password" type="password" required autocomplete="current-password">
                    @error('delete_password')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>
                <label class="checkbox">
                    <input name="confirm_delete" type="checkbox" value="1" {{ old('confirm_delete') ? 'checked' : '' }}>
                    <span>Je confirme la suppression définitive de mon compte.</span>
                </label>
                @error('confirm_delete')
                    <span class="error">{{ $message }}</span>
                @enderror
                <button class="btn danger" type="submit">Supprimer mon compte</button>
            </form>
        </article>
    </section>
</x-layouts.app>
