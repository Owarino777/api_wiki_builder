<x-layouts.app :title="'Connexion - Wiki Builder API'">
    <section class="panel card" style="max-width:560px;margin:0 auto;">
        <span class="pill">Connexion</span>
        <div>
            <h1 style="font-size:36px;line-height:1;">Reprendre ton espace</h1>
            <p class="helper" style="margin-top:10px;">Connecte-toi avec ton email ou ton username pour retrouver tes drafts, tes jeux et tes builds.</p>
        </div>

        <form method="POST" action="{{ route('login.store') }}">
            @csrf
            <div class="field">
                <label for="login">Email ou username</label>
                <input id="login" name="login" type="text" value="{{ old('login') }}" required autofocus autocomplete="username">
                @error('login')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="field">
                <label for="password">Mot de passe</label>
                <input id="password" name="password" type="password" required autocomplete="current-password">
                @error('password')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <label class="helper" style="display:flex;gap:8px;align-items:center;">
                <input name="remember" type="checkbox" value="1" style="width:auto;min-height:auto;">
                Se souvenir de moi
            </label>

            <button class="btn primary" type="submit">Se connecter</button>
        </form>

        <p class="helper">Comptes seed de demo : <strong>admin</strong> / <strong>player1</strong> avec le mot de passe <code>builder123</code>.</p>
    </section>
</x-layouts.app>
