<x-layouts.app :title="'Mon compte - Wiki Builder API'">
    <section class="panel card">
        <span class="pill">Compte</span>
        <div>
            <h1 style="font-size:38px;line-height:1;">{{ $user->display_name ?: $user->username }}</h1>
            <p class="helper" style="margin-top:10px;">Cet espace centralise l'accès a tes donnees personnelles, a l'export RGPD et aux actions sensibles sur ton compte.</p>
        </div>

        <div class="actions">
            <a class="btn primary" href="{{ route('dashboard') }}">Retour au dashboard</a>
            <a class="btn" href="{{ route('privacy') }}">Lire la politique de confidentialite</a>
            <a class="btn" href="{{ route('account.export') }}">Exporter mes donnees</a>
        </div>
    </section>

    <section class="stats">
        <article class="metric">
            <strong>{{ $user->email }}</strong>
            <span>Email de connexion</span>
        </article>
        <article class="metric">
            <strong>{{ $user->username }}</strong>
            <span>Username</span>
        </article>
        <article class="metric">
            <strong>{{ $user->privacy_policy_accepted_at?->format('d/m/Y H:i') ?? 'Non renseigne' }}</strong>
            <span>Politique acceptee</span>
        </article>
        <article class="metric">
            <strong>{{ $user->last_login_at?->format('d/m/Y H:i') ?? 'Premiere session' }}</strong>
            <span>Derniere connexion</span>
        </article>
    </section>

    <section class="grid">
        <article class="panel card">
            <h2>Donnees conservees</h2>
            <p class="helper">Le compte conserve seulement les informations utiles au fonctionnement de l'espace utilisateur, des builds et des brouillons. Les champs facultatifs restent vides tant qu'ils ne sont pas renseignes.</p>
            <div class="list">
                <article>
                    <strong>Identifiants</strong>
                    <p class="helper">Username, email, mot de passe chiffre, role et dates techniques de compte.</p>
                </article>
                <article>
                    <strong>Contenus lies au compte</strong>
                    <p class="helper">Builds personnels, jeux possedes et brouillons en cours de creation.</p>
                </article>
                <article>
                    <strong>Consentements</strong>
                    <p class="helper">Version de la politique lue, consentement marketing optionnel et date de derniere connexion.</p>
                </article>
            </div>
        </article>

        <article class="panel card">
            <h2>Mes droits RGPD</h2>
            <p class="helper">Tu peux exporter tes donnees dans un JSON lisible, anonymiser le profil ou supprimer le compte. Les actions sensibles demandent toujours ton mot de passe actuel.</p>
            <div class="list">
                <article>
                    <strong>Version de la politique</strong>
                    <p class="helper">{{ $policyVersion }}</p>
                </article>
                <article>
                    <strong>Consentement marketing</strong>
                    <p class="helper">{{ $user->marketing_consent_at ? 'Accorde' : 'Non accorde' }}</p>
                </article>
            </div>
        </article>
    </section>

    <section class="grid">
        <article class="panel card">
            <span class="pill">Anonymisation</span>
            <h2>Retirer mes donnees nominatives</h2>
            <p class="helper">Cette action retire tes donnees personnelles, desactive tes cles API et ferme la session. Tes builds sont detaches du compte et tes brouillons sont supprimes.</p>
            <form method="POST" action="{{ route('account.anonymize') }}">
                @csrf
                <div class="field">
                    <label for="anonymize-password">Mot de passe actuel</label>
                    <input id="anonymize-password" name="anonymize_password" type="password" required autocomplete="current-password">
                    @error('anonymize_password')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>
                <button class="btn" type="submit">Anonymiser mon compte</button>
            </form>
        </article>

        <article class="panel card">
            <span class="pill">Suppression</span>
            <h2>Supprimer definitivement mon compte</h2>
            <p class="helper">Cette action supprime le compte. Les builds sont detaches du compte, les cles API sont supprimees et la session est fermee immediatement.</p>
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
                    <span>Je confirme vouloir supprimer definitivement ce compte et comprendre que cette action est irreversible.</span>
                </label>
                @error('confirm_delete')
                    <span class="error">{{ $message }}</span>
                @enderror
                <button class="btn danger" type="submit">Supprimer mon compte</button>
            </form>
        </article>
    </section>
</x-layouts.app>
