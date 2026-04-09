<x-auth-layout title="Mot de passe oublié">
    <div class="login-page">
        {{-- LEFT — Decorative Panel --}}
        <div class="login-decoration">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
            <div class="deco-content">
                <div class="deco-logo">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                </div>
                <h1>Terminus-Boutique</h1>
                <p class="deco-subtitle">Gestion de boutique simplifiée</p>
                <div class="deco-features">
                    <div class="deco-feature">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
                        Réinitialisation sécurisée
                    </div>
                    <div class="deco-feature">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                        Vérification par email
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT — Form Panel --}}
        <div class="login-form-panel">
            <div class="login-form-card">
                <div class="form-header">
                    <div style="width:48px;height:48px;border-radius:12px;background:#EBF4FF;display:flex;align-items:center;justify-content:center;margin-bottom:16px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2E75B6"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                    </div>
                    <h2>Mot de passe oublié ?</h2>
                    <p>Entrez votre email pour recevoir un code de vérification</p>
                </div>

                <form action="{{ route('password.send') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label" for="email">Adresse email</label>
                        <input class="form-input @error('email') is-invalid @enderror"
                               type="email"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               placeholder="admin@boutique.cm"
                               required
                               autofocus>
                        @error('email')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary" style="width:100%;height:48px;margin-top:8px;font-size:0.9375rem;">
                        Envoyer le code
                    </button>
                </form>

                <p style="text-align:center;margin-top:24px;font-size:0.875rem;">
                    <a href="{{ route('login') }}" style="color:#2E75B6;text-decoration:none;font-weight:500;">
                        ← Retour à la connexion
                    </a>
                </p>
            </div>
        </div>
    </div>
</x-auth-layout>
