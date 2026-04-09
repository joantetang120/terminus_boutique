<x-auth-layout title="Nouveau mot de passe">
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
                        Identité vérifiée
                    </div>
                    <div class="deco-feature">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" /></svg>
                        Choisissez un mot de passe fort
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT — Form Panel --}}
        <div class="login-form-panel">
            <div class="login-form-card">
                <div class="form-header">
                    <div style="width:48px;height:48px;border-radius:12px;background:#E8F5EE;display:flex;align-items:center;justify-content:center;margin-bottom:16px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#1A7A4A"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
                    </div>
                    <h2>Nouveau mot de passe</h2>
                    <p>Votre identité est vérifiée. Choisissez un nouveau mot de passe.</p>
                </div>

                <form action="{{ route('password.update') }}" method="POST" x-data="{ showPwd: false, showPwdConfirm: false }">
                    @csrf

                    <div class="form-group">
                        <label class="form-label" for="password">Nouveau mot de passe</label>
                        <div style="position:relative;">
                            <input class="form-input @error('password') is-invalid @enderror"
                                   :type="showPwd ? 'text' : 'password'"
                                   id="password"
                                   name="password"
                                   placeholder="Minimum 6 caractères"
                                   required
                                   autofocus>
                            <button type="button"
                                    @click="showPwd = !showPwd"
                                    style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94A3B8;">
                                <svg x-show="!showPwd" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="m1.306 8.185a.5.5 0 0 1 .586-.053c2.078 1.264 4.274 1.264 6.216 0l.19-.125a.5.5 0 0 1 .518.854l-.19.125c-1.978 1.285-4.386 1.376-6.466.274l-.799-.428a.5.5 0 0 1-.055-.647z"/><path d="m2.42 11.372A4.513 4.513 0 0 0 6.5 13.5c2.76 0 5.2-1.64 6.42-3.986a12.083 12.083 0 0 1-2.38 1.474l-.468.216a.5.5 0 0 1-.42-.908l.468-.216a11.09 11.09 0 0 0 2.758-1.722 11.09 11.09 0 0 0-2.758-1.722l-.468-.216a.5.5 0 1 1 .42-.908l.468.216c.84.387 1.642.864 2.38 1.474C11.7 5.14 9.26 3.5 6.5 3.5a4.513 4.513 0 0 0-4.08 2.128l-.314.662a.5.5 0 0 1-.91-.404l.314-.662A5.513 5.513 0 0 1 6.5 2.5c3.14 0 5.87 1.81 7.18 4.5.14.29.14.62 0 .91-1.31 2.69-4.04 4.5-7.18 4.5a5.513 5.513 0 0 1-4.78-2.872.5.5 0 0 1 .7-.666z"/><path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/></svg>
                                <svg x-show="showPwd" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="m1.306 8.185a.5.5 0 0 1 .586-.053c2.078 1.264 4.274 1.264 6.216 0l.19-.125a.5.5 0 0 1 .518.854l-.19.125c-1.978 1.285-4.386 1.376-6.466.274l-.799-.428a.5.5 0 0 1-.055-.647z"/><path d="m2.42 11.372A4.513 4.513 0 0 0 6.5 13.5c2.76 0 5.2-1.64 6.42-3.986a12.083 12.083 0 0 1-2.38 1.474l-.468.216a.5.5 0 0 1-.42-.908l.468-.216a11.09 11.09 0 0 0 2.758-1.722 11.09 11.09 0 0 0-2.758-1.722l-.468-.216a.5.5 0 1 1 .42-.908l.468.216c.84.387 1.642.864 2.38 1.474C11.7 5.14 9.26 3.5 6.5 3.5a4.513 4.513 0 0 0-4.08 2.128l-.314.662a.5.5 0 0 1-.91-.404l.314-.662A5.513 5.513 0 0 1 6.5 2.5c3.14 0 5.87 1.81 7.18 4.5.14.29.14.62 0 .91-1.31 2.69-4.04 4.5-7.18 4.5a5.513 5.513 0 0 1-4.78-2.872.5.5 0 0 1 .7-.666z"/><path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/></svg>
                            </button>
                        </div>
                        @error('password')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password_confirmation">Confirmer le mot de passe</label>
                        <div style="position:relative;">
                            <input class="form-input"
                                   :type="showPwdConfirm ? 'text' : 'password'"
                                   id="password_confirmation"
                                   name="password_confirmation"
                                   placeholder="Confirmez votre mot de passe"
                                   required>
                            <button type="button"
                                    @click="showPwdConfirm = !showPwdConfirm"
                                    style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94A3B8;">
                                <svg x-show="!showPwdConfirm" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="m1.306 8.185a.5.5 0 0 1 .586-.053c2.078 1.264 4.274 1.264 6.216 0l.19-.125a.5.5 0 0 1 .518.854l-.19.125c-1.978 1.285-4.386 1.376-6.466.274l-.799-.428a.5.5 0 0 1-.055-.647z"/><path d="m2.42 11.372A4.513 4.513 0 0 0 6.5 13.5c2.76 0 5.2-1.64 6.42-3.986a12.083 12.083 0 0 1-2.38 1.474l-.468.216a.5.5 0 0 1-.42-.908l.468-.216a11.09 11.09 0 0 0 2.758-1.722 11.09 11.09 0 0 0-2.758-1.722l-.468-.216a.5.5 0 1 1 .42-.908l.468.216c.84.387 1.642.864 2.38 1.474C11.7 5.14 9.26 3.5 6.5 3.5a4.513 4.513 0 0 0-4.08 2.128l-.314.662a.5.5 0 0 1-.91-.404l.314-.662A5.513 5.513 0 0 1 6.5 2.5c3.14 0 5.87 1.81 7.18 4.5.14.29.14.62 0 .91-1.31 2.69-4.04 4.5-7.18 4.5a5.513 5.513 0 0 1-4.78-2.872.5.5 0 0 1 .7-.666z"/><path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/></svg>
                                <svg x-show="showPwdConfirm" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="m1.306 8.185a.5.5 0 0 1 .586-.053c2.078 1.264 4.274 1.264 6.216 0l.19-.125a.5.5 0 0 1 .518.854l-.19.125c-1.978 1.285-4.386 1.376-6.466.274l-.799-.428a.5.5 0 0 1-.055-.647z"/><path d="m2.42 11.372A4.513 4.513 0 0 0 6.5 13.5c2.76 0 5.2-1.64 6.42-3.986a12.083 12.083 0 0 1-2.38 1.474l-.468.216a.5.5 0 0 1-.42-.908l.468-.216a11.09 11.09 0 0 0 2.758-1.722 11.09 11.09 0 0 0-2.758-1.722l-.468-.216a.5.5 0 1 1 .42-.908l.468.216c.84.387 1.642.864 2.38 1.474C11.7 5.14 9.26 3.5 6.5 3.5a4.513 4.513 0 0 0-4.08 2.128l-.314.662a.5.5 0 0 1-.91-.404l.314-.662A5.513 5.513 0 0 1 6.5 2.5c3.14 0 5.87 1.81 7.18 4.5.14.29.14.62 0 .91-1.31 2.69-4.04 4.5-7.18 4.5a5.513 5.513 0 0 1-4.78-2.872.5.5 0 0 1 .7-.666z"/><path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/></svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width:100%;height:48px;margin-top:8px;font-size:0.9375rem;">
                        Réinitialiser le mot de passe
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-auth-layout>
