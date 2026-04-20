<x-layouts.app :title="__('Connexion - Wiki Builder API')">
    <section class="panel card auth-card corner-brackets">
        <span class="pill">{{ __('Connexion') }}</span>
        <div class="stack-tight">
            <h1 class="auth-title">{{ __('Reprendre ton espace') }}</h1>
            <hr class="neon-line">
            <p class="helper">{{ __('Connecte-toi avec ton email ou ton username pour retrouver tes drafts, tes jeux et tes builds.') }}</p>
        </div>

        <form method="POST" action="{{ route('login.store') }}">
            @csrf
            <div class="field field-with-icon">
                <label for="login">{{ __('Email ou username') }}</label>
                <span class="field-icon">
                    <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </span>
                <input id="login" name="login" type="text" value="{{ old('login') }}" required autofocus autocomplete="username" placeholder="ton@email.com ou ton_username">
                @error('login')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="field field-with-icon">
                <label for="password">{{ __('Mot de passe') }}</label>
                <span class="field-icon">
                    <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                </span>
                <input id="password" name="password" type="password" required autocomplete="current-password" placeholder="Ton mot de passe">
                @error('password')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <label class="checkbox checkbox-inline">
                <input name="remember" type="checkbox" value="1">
                {{ __('Se souvenir de moi') }}
            </label>

            <button class="btn primary" type="submit">{{ __('Se connecter') }}</button>
        </form>

        <div class="actions">
            <a class="btn ghost" href="{{ route('register') }}">{{ __('Créer un compte') }}</a>
            <a class="btn ghost" href="{{ route('privacy') }}">{{ __('Confidentialité') }}</a>
        </div>

        <p class="helper">{{ __("Aucun compte de démonstration n'est créé par défaut. Utilise l'inscription pour créer un compte ou lance un seed de développement si tu veux des comptes déjà prêts.") }}</p>
    </section>
</x-layouts.app>
