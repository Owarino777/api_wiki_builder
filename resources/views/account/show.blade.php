<x-layouts.app :title="__('Mon compte - Wiki Builder API')">
    @php
        $displayName = $user->display_name ?: $user->username;
        $accountStatus = $user->anonymized_at ? __('Anonymisé') : __('Actif');
    @endphp

    <section class="page-head">
        <div class="page-head-user">
            <div class="avatar-frame">
                <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <div>
                <span class="eyebrow">{{ __('Compte') }}</span>
                <h1>{{ __('Gestion du compte') }}</h1>
                <span class="subtitle">{{ __('Espace personnel') }}</span>
                <hr class="neon-line">
                <p class="helper">{{ __('Informations, export RGPD, anonymisation, suppression.') }}</p>
                <div class="page-meta">
                    <span>{{ $displayName }}</span>
                    <span>{{ $user->email }}</span>
                    <span>{{ $accountStatus }}</span>
                </div>
            </div>
        </div>
        <div class="page-head-actions">
            <a class="btn primary" href="{{ route('account.export') }}">{{ __('Exporter') }}</a>
            <a class="btn ghost" href="{{ route('privacy') }}">{{ __('Politique') }}</a>
            <a class="btn subtle" href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
        </div>
    </section>

    <section class="info-grid">
        <article class="info-card">
            <div class="info-icon">
                <svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            </div>
            <div class="info-content">
                <span>{{ __('Email') }}</span>
                <strong>{{ $user->email }}</strong>
            </div>
        </article>
        <article class="info-card">
            <div class="info-icon">
                <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <div class="info-content">
                <span>{{ __('Username') }}</span>
                <strong>{{ $user->username }}</strong>
            </div>
        </article>
        <article class="info-card">
            <div class="info-icon">
                <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </div>
            <div class="info-content">
                <span>{{ __('Rôle') }}</span>
                <strong>{{ $user->role->value ?? $user->role }}</strong>
            </div>
        </article>
        <article class="info-card">
            <div class="info-icon">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div class="info-content">
                <span>{{ __('Dernière connexion') }}</span>
                <strong>{{ $user->last_login_at?->format('d/m/Y H:i') ?? __('Première session') }}</strong>
            </div>
        </article>
    </section>

    <section class="section-shell">
        <article class="panel section-card account-anonymize">
            <div class="section-head">
                <div class="section-head-row">
                    <div class="section-icon">
                        <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <div class="section-copy">
                        <span class="eyebrow">{{ __('Statut') }}</span>
                        <h2>{{ __('Compte') }}</h2>
                    </div>
                </div>
            </div>

            <div class="feature-list">
                <article class="feature-item">
                    <div class="feature-item-icon">
                        <svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </div>
                    <div>
                        <strong>{{ __('Statut') }}</strong>
                        <p class="helper compact">{{ $accountStatus }}</p>
                    </div>
                </article>
                <article class="feature-item">
                    <div class="feature-item-icon">
                        <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    </div>
                    <div>
                        <strong>{{ __('Politique acceptée') }}</strong>
                        <p class="helper compact">{{ $user->privacy_policy_accepted_at?->format('d/m/Y') ?? __('Non renseigné') }}</p>
                    </div>
                </article>
                <article class="feature-item">
                    <div class="feature-item-icon">
                        <svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </div>
                    <div>
                        <strong>{{ __('Marketing') }}</strong>
                        <p class="helper compact">{{ $user->marketing_consent_at ? __('Accordé') : __('Non accordé') }}</p>
                    </div>
                </article>
                <article class="feature-item">
                    <div class="feature-item-icon">
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    </div>
                    <div>
                        <strong>{{ __('Version de politique') }}</strong>
                        <p class="helper compact">{{ $policyVersion }}</p>
                    </div>
                </article>
            </div>
        </article>

        <article class="panel section-card danger-zone">
            <div class="section-head">
                <div class="section-head-row">
                    <div class="section-icon" style="background: linear-gradient(135deg, rgba(255,109,134,.18), rgba(255,60,80,.1)); border-color: rgba(255,109,134,.35); color: var(--danger);">
                        <svg viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    </div>
                    <div class="section-copy">
                        <span class="eyebrow">{{ __('Données') }}</span>
                        <h2>{{ __('Actions') }}</h2>
                    </div>
                </div>
            </div>

            <div class="action-list">
                <article class="feature-item">
                    <div class="feature-item-icon">
                        <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    </div>
                    <div>
                        <strong>{{ __('Export RGPD') }}</strong>
                        <p class="helper compact">{{ __('Télécharger tes données en JSON.') }}</p>
                        <div class="section-actions">
                            <a class="btn ghost" href="{{ route('account.export') }}">{{ __('Télécharger') }}</a>
                        </div>
                    </div>
                </article>
                <article class="feature-item">
                    <div class="feature-item-icon">
                        <svg viewBox="0 0 24 24"><path d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2z"/><path d="M22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z"/></svg>
                    </div>
                    <div>
                        <strong>{{ __('Politique') }}</strong>
                        <p class="helper compact">{{ __('Consulter la version') }} {{ $policyVersion }}.</p>
                        <div class="section-actions">
                            <a class="btn ghost" href="{{ route('privacy') }}">{{ __('Ouvrir') }}</a>
                        </div>
                    </div>
                </article>
            </div>
        </article>
    </section>

    <section class="duo-grid">
        <article class="panel section-card">
            <div class="section-head">
                <div class="section-copy">
                    <span class="eyebrow">{{ __('Anonymisation') }}</span>
                    <h2>{{ __('Retirer les données nominatives') }}</h2>
                    <p class="helper compact">{{ __('Action irréversible. La session sera fermée.') }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('account.anonymize') }}">
                @csrf
                <div class="field">
                    <label for="anonymize-password">{{ __('Mot de passe actuel') }}</label>
                    <input id="anonymize-password" name="anonymize_password" type="password" required autocomplete="current-password">
                    @error('anonymize_password')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>
                <button class="btn ghost" type="submit">{{ __('Anonymiser') }}</button>
            </form>
        </article>

        <article class="panel section-card">
            <div class="section-head">
                <div class="section-copy">
                    <span class="eyebrow">{{ __('Suppression') }}</span>
                    <h2>{{ __('Supprimer le compte') }}</h2>
                    <p class="helper compact">{{ __('Action irréversible. Le compte et la session seront supprimés.') }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('account.destroy') }}">
                @csrf
                @method('DELETE')
                <div class="field">
                    <label for="delete-password">{{ __('Mot de passe actuel') }}</label>
                    <input id="delete-password" name="delete_password" type="password" required autocomplete="current-password">
                    @error('delete_password')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>
                <label class="checkbox">
                    <input name="confirm_delete" type="checkbox" value="1" {{ old('confirm_delete') ? 'checked' : '' }}>
                    <span>{{ __('Je confirme la suppression définitive.') }}</span>
                </label>
                @error('confirm_delete')
                    <span class="error">{{ $message }}</span>
                @enderror
                <button class="btn danger" type="submit">{{ __('Supprimer') }}</button>
            </form>
        </article>
    </section>
</x-layouts.app>
