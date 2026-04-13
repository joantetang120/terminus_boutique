<x-app-layout title="Mon Profil">
    <div style="padding: 20px;">
        <div style="margin-bottom: 20px; color: #64748b; font-size: 0.9rem;">
            Accueil > Administration > <span style="color: #1e293b; font-weight: 600;">Mon Profil</span>
        </div>

        @if(session('success'))
            <div style="padding: 15px; background: #dcfce7; color: #166534; border-radius: 8px; margin-bottom: 20px; border: 1px solid #bbf7d0;">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div style="padding: 15px; background: #fee2e2; color: #991b1b; border-radius: 8px; margin-bottom: 20px; border: 1px solid #fecaca;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div style="display: grid; grid-template-columns: 380px 1fr; gap: 25px;">
            
            <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); overflow: hidden; align-self: start;">
                <div style="background: #f8fafc; padding: 20px; border-bottom: 1px solid #e2e8f0; text-align: center;">
                    <div style="width: 70px; height: 70px; background: #fbbf24; color: #1e293b; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; font-weight: 800; margin: 0 auto 10px;">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <h2 style="font-size: 1.1rem; color: #1e293b; margin: 0;">{{ Auth::user()->name }}</h2>
                    <p style="color: #64748b; font-size: 0.85rem;">{{ Auth::user()->email }}</p>
                </div>

                <form action="{{ route('profile.request_update') }}" method="POST" style="padding: 25px;">
                    @csrf
                    
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 5px;">Nom complet</label>
                        <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" required
                               style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem;">
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 5px;">Adresse Email</label>
                        <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" required
                               style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem;">
                    </div>

                    <div style="background: #f1f5f9; height: 1px; margin: 25px 0;"></div>

                    <p style="font-size: 0.8rem; color: #64748b; margin-bottom: 15px; font-style: italic;">
                        Laissez vide si vous ne souhaitez pas changer de mot de passe.
                    </p>

                    <div style="margin-bottom: 15px;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 5px;">Nouveau mot de passe</label>
                        <input type="password" name="password" 
                               style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem;">
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 5px;">Confirmer le mot de passe</label>
                        <input type="password" name="password_confirmation" 
                               style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem;">
                    </div>

                    <div style="background: #fffbeb; border: 1px solid #fef3c7; padding: 10px; border-radius: 8px; margin-bottom: 20px;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #92400e; margin-bottom: 5px;">Ancien mot de passe requis</label>
                        <input type="password" name="old_password" required placeholder="Vérification de sécurité"
                               style="width: 100%; padding: 10px; border: 1px solid #fbbf24; border-radius: 8px; font-size: 0.9rem;">
                    </div>

                    <button type="submit" style="width: 100%; background: #1e293b; color: #fff; padding: 12px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.2s;">
                        Envoyer le code de vérification
                    </button>
                </form>
            </div>

            <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); overflow: hidden;">
                <div style="background: #1e293b; padding: 15px 25px; color: #fff; display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="margin: 0; font-size: 1.1rem;">Mes Accès et Permissions</h3>
                    <div style="font-size: 0.75rem; background: rgba(255,255,255,0.1); padding: 4px 10px; border-radius: 4px; text-transform: uppercase;">
                        Rôle : {{ Auth::user()->roles->pluck('name')->first() ?? 'Utilisateur' }}
                    </div>
                </div>
                
                <div style="padding: 25px;">
                    <div style="background: #f8fafc; border-left: 4px solid #fbbf24; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
                        <p style="margin: 0; color: #475569; font-size: 0.85rem; line-height: 1.5;">
                            <strong>Note de sécurité :</strong> Les permissions et le rôle sont en <strong>lecture seule</strong>. Seul un administrateur peut modifier ces paramètres système.
                        </p>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 10px;">
                        @foreach(Auth::user()->getAllPermissions() as $permission)
                        <div style="background: #f8fafc; border: 1px solid #f1f5f9; padding: 10px 15px; border-radius: 6px; font-size: 0.85rem; color: #334155; display: flex; align-items: center;">
                            <span style="color: #10b981; margin-right: 8px;">✔</span> {{ $permission->name }}
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>