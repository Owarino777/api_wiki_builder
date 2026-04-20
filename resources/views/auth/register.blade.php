<x-layouts.app :title="__('Inscription - Wiki Builder API')">
    <section class="panel card auth-card corner-brackets">
        <span class="pill">{{ __('Inscription') }}</span>
        <div class="stack-tight">
            <h1 class="auth-title">{{ __('Créer un compte utilisateur') }}</h1>
            <hr class="neon-line">
            <p class="helper">{{ __('Le compte créé te donne un espace persistant pour tes drafts de jeux, tes builds et tes futures publications.') }}</p>
        </div>

        <form method="POST" action="{{ route('register.store') }}">
            @csrf
            <div class="grid">
                <div class="field field-with-icon">
                    <label for="username">{{ __('Username') }}</label>
                    <span class="field-icon">
                        <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </span>
                    <input id="username" name="username" type="text" value="{{ old('username') }}" required autocomplete="username" placeholder="Choisis un username">
                    @error('username')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="field field-with-icon">
                    <label for="display_name">{{ __('Nom affiché') }}</label>
                    <span class="field-icon">
                        <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                    </span>
                    <input id="display_name" name="display_name" type="text" value="{{ old('display_name') }}" autocomplete="nickname" placeholder="Nom visible par les autres">
                    @error('display_name')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="field field-with-icon">
                <label for="email">{{ __('Email') }}</label>
                <span class="field-icon">
                    <svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                </span>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" placeholder="ton@email.com">
                @error('email')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="grid">
                <div class="field field-with-icon">
                    <label for="password">{{ __('Mot de passe') }}</label>
                    <span class="field-icon">
                        <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                    </span>
                    <input id="password" name="password" type="password" required autocomplete="new-password" placeholder="Crée un mot de passe sécurisé">
                    @error('password')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="field field-with-icon">
                    <label for="password_confirmation">{{ __('Confirmation') }}</label>
                    <span class="field-icon">
                        <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </span>
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" placeholder="Répète ton mot de passe">
                </div>
            </div>

            <label class="checkbox">
                <input name="privacy_policy_acknowledged" type="checkbox" value="1" {{ old('privacy_policy_acknowledged') ? 'checked' : '' }}>
                <span>{{ __("J'ai lu la") }} <a href="{{ route('privacy') }}"><u>{{ __('politique de confidentialité') }}</u></a> {{ __('et je comprends l\'usage de mes données pour fournir le service.') }}</span>
            </label>
            @error('privacy_policy_acknowledged')
                <span class="error">{{ $message }}</span>
            @enderror

            <label class="checkbox">
                <input name="marketing_opt_in" type="checkbox" value="1" {{ old('marketing_opt_in') ? 'checked' : '' }}>
                <span>{{ __("J'accepte de recevoir des informations produit. Ce consentement est optionnel.") }}</span>
            </label>

            <button class="btn primary" type="submit">{{ __('Créer mon compte') }}</button>
        </form>

        <div class="actions">
            <a class="btn ghost" href="{{ route('login') }}">{{ __("J'ai déjà un compte") }}</a>
            <a class="btn ghost" href="{{ route('privacy') }}">{{ __('Confidentialité') }}</a>
        </div>
    </section>
</x-layouts.app>
