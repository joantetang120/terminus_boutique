<x-app-layout title="Mon Profil">
    <div class="profile-container">
        <div class="breadcrumb">
            Accueil > Administration > <span class="breadcrumb-current">Mon Profil</span>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="profile-grid">

            <div class="profile-card">
                <div class="profile-header">
                    <div class="profile-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <h2 class="profile-name">{{ Auth::user()->name }}</h2>
                    <p class="profile-email">{{ Auth::user()->email }}</p>
                </div>

                <form action="{{ route('profile.request_update') }}" method="POST" class="profile-form">
                    @csrf

                    <div class="form-section">
                        <h3 class="section-title">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            Informations personnelles
                        </h3>

                        <div class="form-group">
                            <label class="form-label">Nom complet</label>
                            <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" required
                                   class="form-input" placeholder="Votre nom">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Adresse Email</label>
                            <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" required
                                   class="form-input" placeholder="votre@email.com">
                            <span class="input-hint">Un code sera envoyé pour confirmer si vous changez d'email</span>
                        </div>
                    </div>

                    <div class="form-divider"></div>

                    <div class="form-section">
                        <h3 class="section-title">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                            Sécurité
                        </h3>

                        <p class="section-hint">Laissez vide si vous ne souhaitez pas changer de mot de passe.</p>

                        <div class="form-group">
                            <label class="form-label">Nouveau mot de passe</label>
                            <div class="password-wrapper">
                                <input type="password" name="password" id="password" class="form-input password-input" placeholder="••••••••">
                                <button type="button" class="toggle-password" onclick="togglePassword('password', this)" aria-label="Afficher le mot de passe">
                                    <svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    <svg class="eye-closed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: none;">
                                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                        <line x1="1" y1="1" x2="23" y2="23"></line>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Confirmer le mot de passe</label>
                            <div class="password-wrapper">
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-input password-input" placeholder="••••••••">
                                <button type="button" class="toggle-password" onclick="togglePassword('password_confirmation', this)" aria-label="Afficher le mot de passe">
                                    <svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    <svg class="eye-closed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: none;">
                                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                        <line x1="1" y1="1" x2="23" y2="23"></line>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="security-box">
                        <label class="security-label">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            </svg>
                            Vérification de sécurité requise
                        </label>
                        <div class="password-wrapper">
                            <input type="password" name="old_password" id="old_password" required placeholder="Entrez votre mot de passe actuel"
                                   class="security-input password-input">
                            <button type="button" class="toggle-password toggle-password-security" onclick="togglePassword('old_password', this)" aria-label="Afficher le mot de passe">
                                <svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <svg class="eye-closed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: none;">
                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                    <line x1="1" y1="1" x2="23" y2="23"></line>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="submit-btn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                        <span>Envoyer le code de vérification</span>
                    </button>
                </form>
            </div>

            <div class="permissions-card">
                <div class="permissions-header">
                    <h3>Mes Accès et Permissions</h3>
                    <div class="role-badge">
                        {{ Auth::user()->roles->pluck('name')->first() ?? 'Utilisateur' }}
                    </div>
                </div>

                <div class="permissions-body">
                    <div class="info-box">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                        <p><strong>Note de sécurité :</strong> Les permissions et le rôle sont en <strong>lecture seule</strong>. Seul un administrateur peut modifier ces paramètres système.</p>
                    </div>

                    <div class="permissions-grid">
                        @foreach(Auth::user()->getAllPermissions() as $permission)
                        <div class="permission-item">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            {{ $permission->name }}
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>

    <style>
        .profile-container {
            padding: 20px;
        }

        .breadcrumb {
            margin-bottom: 20px;
            color: #64748b;
            font-size: 0.9rem;
        }

        .breadcrumb-current {
            color: #1e293b;
            font-weight: 600;
        }

        .alert {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 0.95rem;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert-error ul {
            margin: 0;
            padding-left: 20px;
        }

        .profile-grid {
            display: grid;
            grid-template-columns: 400px 1fr;
            gap: 25px;
        }

        .profile-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            overflow: hidden;
            align-self: start;
        }

        .profile-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            padding: 30px;
            text-align: center;
            color: white;
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 700;
            margin: 0 auto 15px;
            backdrop-filter: blur(10px);
        }

        .profile-name {
            font-size: 1.3rem;
            margin: 0;
            font-weight: 600;
        }

        .profile-email {
            margin: 5px 0 0;
            opacity: 0.9;
            font-size: 0.9rem;
        }

        .profile-form {
            padding: 25px;
        }

        .form-section {
            margin-bottom: 25px;
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.95rem;
            font-weight: 600;
            color: #1e293b;
            margin: 0 0 15px 0;
        }

        .section-hint {
            font-size: 0.8rem;
            color: #64748b;
            font-style: italic;
            margin: -10px 0 15px 0;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 6px;
        }

        .form-input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.2s;
            box-sizing: border-box;
        }

        .form-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }

        .input-hint {
            display: block;
            font-size: 0.75rem;
            color: #64748b;
            margin-top: 5px;
        }

        .form-divider {
            height: 1px;
            background: #e2e8f0;
            margin: 20px 0;
        }

        .security-box {
            background: #fffbeb;
            border: 1px solid #fcd34d;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .security-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            font-weight: 700;
            color: #92400e;
            margin-bottom: 10px;
        }

        .security-input {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid #fbbf24;
            border-radius: 10px;
            font-size: 0.95rem;
            box-sizing: border-box;
        }

        .security-input:focus {
            border-color: #f59e0b;
            box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.2);
            outline: none;
        }

        .submit-btn {
            width: 100%;
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            color: #fff;
            padding: 14px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(30, 41, 59, 0.3);
        }

        .permissions-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .permissions-header {
            background: #1e293b;
            padding: 20px 25px;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .permissions-header h3 {
            margin: 0;
            font-size: 1.1rem;
        }

        .role-badge {
            font-size: 0.75rem;
            background: rgba(255,255,255,0.15);
            padding: 5px 12px;
            border-radius: 20px;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .permissions-body {
            padding: 25px;
        }

        .info-box {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            background: #f0f9ff;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            border-radius: 0 8px 8px 0;
            margin-bottom: 20px;
            color: #475569;
            font-size: 0.9rem;
        }

        .info-box p {
            margin: 0;
            line-height: 1.5;
        }

        .permissions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 10px;
        }

        .permission-item {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 0.85rem;
            color: #334155;
        }

        @media (max-width: 768px) {
            .profile-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Password toggle styles */
        .password-wrapper {
            position: relative;
        }

        .password-input {
            padding-right: 45px !important;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            padding: 5px;
            cursor: pointer;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .toggle-password:hover {
            color: #3b82f6;
            background: rgba(59, 130, 246, 0.1);
        }

        .toggle-password-security {
            color: #d97706;
        }

        .toggle-password-security:hover {
            color: #b45309;
            background: rgba(217, 119, 6, 0.1);
        }

        .toggle-password:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
        }
    </style>

    <script>
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const eyeOpen = button.querySelector('.eye-open');
            const eyeClosed = button.querySelector('.eye-closed');

            if (input.type === 'password') {
                input.type = 'text';
                eyeOpen.style.display = 'none';
                eyeClosed.style.display = 'block';
                button.setAttribute('aria-label', 'Masquer le mot de passe');
            } else {
                input.type = 'password';
                eyeOpen.style.display = 'block';
                eyeClosed.style.display = 'none';
                button.setAttribute('aria-label', 'Afficher le mot de passe');
            }
        }
    </script>
</x-app-layout>