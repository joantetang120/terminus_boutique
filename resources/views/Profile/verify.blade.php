<x-app-layout title="Vérification du profil">
    <div style="display: flex; justify-content: center; align-items: center; min-height: 70vh; padding: 20px;">
        
        <div style="max-width: 450px; width: 100%; background: #fff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1); overflow: hidden;">
            
            <div style="background: linear-gradient(135deg, #1B3A6B, #2E75B6); padding: 30px; text-align: center; color: white;">
                <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                </div>
                <h2 style="margin: 0; font-size: 1.5rem; font-weight: 700;">Vérification de sécurité</h2>
                <p style="margin: 5px 0 0; opacity: 0.9; font-size: 0.9rem;">Un code a été envoyé à votre email</p>
            </div>

            <div style="padding: 40px;">
                @if($errors->any())
                    <div style="padding: 12px; background: #fee2e2; color: #991b1b; border-radius: 8px; margin-bottom: 20px; font-size: 0.85rem; border: 1px solid #fecaca; text-align: center;">
                        {{ $errors->first() }}
                    </div>
                @endif

                <p style="text-align: center; color: #64748b; font-size: 0.9rem; margin-bottom: 30px; line-height: 1.5;">
                    Veuillez saisir le code à <strong>4 chiffres</strong> pour confirmer les modifications de votre profil.
                </p>

                <form action="{{ route('profile.confirm_update') }}" method="POST">
                    @csrf
                    
                    <div style="margin-bottom: 30px; display: flex; justify-content: center;">
                        <input type="text" name="code" maxlength="4" placeholder="0 0 0 0" required autofocus
                               style="width: 180px; height: 60px; text-align: center; font-size: 2rem; font-weight: 800; letter-spacing: 10px; border: 2px solid #cbd5e1; border-radius: 12px; color: #1e293b; outline: none; transition: border-color 0.2s;"
                               oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                               onfocus="this.style.borderColor='#2E75B6'"
                               onblur="this.style.borderColor='#cbd5e1'">
                    </div>

                    <button type="submit" style="width: 100%; background: #1e293b; color: white; padding: 14px; border: none; border-radius: 10px; font-weight: 600; font-size: 1rem; cursor: pointer; transition: transform 0.1s;">
                        Confirmer la mise à jour
                    </button>
                </form>

                <div style="margin-top: 25px; text-align: center;">
                    <a href="{{ route('profile.show') }}" style="color: #64748b; font-size: 0.85rem; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 5px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                        Annuler et retourner au profil
                    </a>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>