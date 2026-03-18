<x-layouts.app :title="'Inscription - Wiki Builder API'">
    <section class="panel card" style="max-width:620px;margin:0 auto;">
        <span class="pill">Inscription</span>
        <div>
            <h1 style="font-size:36px;line-height:1;">Creer un compte utilisateur</h1>
            <p class="helper" style="margin-top:10px;">Le compte cree te donne un espace persistant pour tes drafts de jeux, tes builds et tes futures publications.</p>
        </div>

        <form method="POST" action="{{ route('register.store') }}">
            @csrf
            <div class="grid">
                <div class="field">
                    <label for="username">Username</label>
                    <input id="username" name="username" type="text" value="{{ old('username') }}" required autocomplete="username">
                    @error('username')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="field">
                    <label for="display_name">Nom affiche</label>
                    <input id="display_name" name="display_name" type="text" value="{{ old('display_name') }}" autocomplete="nickname">
                    @error('display_name')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="field">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email">
                @error('email')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="grid">
                <div class="field">
                    <label for="password">Mot de passe</label>
                    <input id="password" name="password" type="password" required autocomplete="new-password">
                    @error('password')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="field">
                    <label for="password_confirmation">Confirmation</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password">
                </div>
            </div>

            <button class="btn primary" type="submit">Creer mon compte</button>
        </form>
    </section>
</x-layouts.app>
