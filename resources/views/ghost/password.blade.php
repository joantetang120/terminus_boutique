<x-app-layout title="Accès Archive - Authentification">
    <div style="min-height: calc(100vh - 200px); display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 24px;">
        <div style="width: 100%; max-width: 420px; background: white; border-radius: 12px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); overflow: hidden;">
            {{-- Header --}}
            <div style="background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%); padding: 32px 24px; text-align: center; color: white;">
                <div style="width: 64px; height: 64px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                </div>
                <h1 style="margin: 0 0 8px; font-size: 20px; font-weight: 600;">Archive des Factures</h1>
                <p style="margin: 0; opacity: 0.8; font-size: 14px;">Accès protégé • Consultation seule</p>
            </div>

            {{-- Form --}}
            <div style="padding: 32px 24px;">
                @if(session('error'))
                    <div style="background: #fed7d7; color: #c53030; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; font-size: 14px;">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('ghost.verify') }}">
                    @csrf
                    <div style="margin-bottom: 20px;">
                        <label for="password" style="display: block; margin-bottom: 8px; color: #4a5568; font-weight: 500; font-size: 14px;">
                            Mot de passe d'accès
                        </label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-input"
                            placeholder="Entrez le mot de passe..."
                            required
                            autofocus
                            style="width: 100%; padding: 12px 16px; border: 2px solid #e2e8f0; border-radius: 6px; font-size: 14px; transition: border-color 0.2s;"
                            onfocus="this.style.borderColor='#667eea'"
                            onblur="this.style.borderColor='#e2e8f0'"
                        >
                        @error('password')
                            <span style="color: #e53e3e; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" style="width: 100%; padding: 14px 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 6px; font-size: 16px; font-weight: 600; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(102,126,234,0.4)';" onmouseout="this.style.transform=''; this.style.boxShadow='';">
                        Accéder à l'archive
                    </button>
                </form>

                <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid #e2e8f0; text-align: center;">
                    <p style="margin: 0 0 8px; color: #718096; font-size: 12px;">
                        Cet accès est réservé aux utilisateurs autorisés.
                    </p>
                    <p style="margin: 0; color: #a0aec0; font-size: 11px;">
                        Mot de passe fourni par l'administrateur uniquement.
                    </p>
                </div>

                <div style="margin-top: 20px; text-align: center;">
                    <a href="{{ route('dashboard') }}" style="color: #667eea; font-size: 14px; text-decoration: none;">
                        ← Retour au tableau de bord
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
