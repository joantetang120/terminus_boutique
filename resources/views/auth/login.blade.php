<x-auth-layout title="Connexion">
    <div class="login-page">
        {{-- LEFT — Decorative Panel --}}
        <div class="login-decoration">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>

            <div class="deco-content">
                <div class="deco-logo">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.15c0 .415.336.75.75.75z" />
                    </svg>
                </div>
                <h1>Terminus-Boutique</h1>
                <p class="deco-subtitle">Gestion de boutique simplifiée</p>

                <div class="deco-features">
                    <div class="deco-feature">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                        Facturation intelligente
                    </div>
                    <div class="deco-feature">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M6 7.5V12h12M6 7.5H3.75m16.5 0H18" /></svg>
                        Suivi du stock en temps réel
                    </div>
                    <div class="deco-feature">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" /></svg>
                        Comptabilité intégrée
                    </div>
                    <div class="deco-feature">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
                        Permissions granulaires & audit
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT — Form Panel --}}
        <div class="login-form-panel">
            <div class="login-form-card">
                <div class="form-header">
                    <h2>Bon retour !</h2>
                    <p>Connectez-vous à votre espace de gestion</p>
                </div>

                <form action="{{ route('login') }}" method="POST">
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

                    <div class="form-group">
                        <label class="form-label" for="password">Mot de passe</label>
                        <div style="position:relative;">
                            <input class="form-input @error('password') is-invalid @enderror"
                                   type="password"
                                   id="password"
                                   name="password"
                                   placeholder="••••••••"
                                   required>
                            <button type="button"
                                    onclick="togglePassword()"
                                    style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94A3B8;">
                                <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="m1.306 8.185a.5.5 0 0 1 .586-.053c2.078 1.264 4.274 1.264 6.216 0l.19-.125a.5.5 0 0 1 .518.854l-.19.125c-1.978 1.285-4.386 1.376-6.466.274l-.799-.428a.5.5 0 0 1-.055-.647z"/>
                                    <path d="m2.42 11.372A4.513 4.513 0 0 0 6.5 13.5c2.76 0 5.2-1.64 6.42-3.986a12.083 12.083 0 0 1-2.38 1.474l-.468.216a.5.5 0 0 1-.42-.908l.468-.216a11.09 11.09 0 0 0 2.758-1.722 11.09 11.09 0 0 0-2.758-1.722l-.468-.216a.5.5 0 1 1 .42-.908l.468.216c.84.387 1.642.864 2.38 1.474C11.7 5.14 9.26 3.5 6.5 3.5a4.513 4.513 0 0 0-4.08 2.128l-.314.662a.5.5 0 0 1-.91-.404l.314-.662A5.513 5.513 0 0 1 6.5 2.5c3.14 0 5.87 1.81 7.18 4.5.14.29.14.62 0 .91-1.31 2.69-4.04 4.5-7.18 4.5a5.513 5.513 0 0 1-4.78-2.872.5.5 0 0 1 .7-.666z"/>
                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary" style="width:100%;height:48px;margin-top:8px;font-size:0.9375rem;">
                        Se connecter
                    </button>
                </form>

                <div style="text-align:center;margin-top:16px;">
                    <a href="{{ route('password.forgot') }}" style="color:#2E75B6;text-decoration:none;font-size:0.875rem;font-weight:500;">
                        Mot de passe oublié ?
                    </a>
                </div>

                <p style="text-align:center;margin-top:24px;font-size:0.75rem;color:#94A3B8;">
                    Terminus-Boutique v1.0.0
                </p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const pwd = document.getElementById('password');
            const icon = document.getElementById('eye-icon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.innerHTML = '<path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l.708.709z"/><path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/><path d="M3.35 5.47c-.18.164-.353.327-.518.492A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12-.708.708z"/>';
            } else {
                pwd.type = 'password';
                icon.innerHTML = '<path d="m1.306 8.185a.5.5 0 0 1 .586-.053c2.078 1.264 4.274 1.264 6.216 0l.19-.125a.5.5 0 0 1 .518.854l-.19.125c-1.978 1.285-4.386 1.376-6.466.274l-.799-.428a.5.5 0 0 1-.055-.647z"/><path d="m2.42 11.372A4.513 4.513 0 0 0 6.5 13.5c2.76 0 5.2-1.64 6.42-3.986a12.083 12.083 0 0 1-2.38 1.474l-.468.216a.5.5 0 0 1-.42-.908l.468-.216a11.09 11.09 0 0 0 2.758-1.722 11.09 11.09 0 0 0-2.758-1.722l-.468-.216a.5.5 0 1 1 .42-.908l.468.216c.84.387 1.642.864 2.38 1.474C11.7 5.14 9.26 3.5 6.5 3.5a4.513 4.513 0 0 0-4.08 2.128l-.314.662a.5.5 0 0 1-.91-.404l.314-.662A5.513 5.513 0 0 1 6.5 2.5c3.14 0 5.87 1.81 7.18 4.5.14.29.14.62 0 .91-1.31 2.69-4.04 4.5-7.18 4.5a5.513 5.513 0 0 1-4.78-2.872.5.5 0 0 1 .7-.666z"/><path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>';
            }
        }
    </script>
</x-auth-layout>
