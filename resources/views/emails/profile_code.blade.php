<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #F4F6F9; margin: 0; padding: 40px 20px; }
        .container { max-width: 480px; margin: 0 auto; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #1B3A6B, #2E75B6); padding: 32px; text-align: center; color: white; }
        .header h1 { margin: 0 0 4px; font-size: 22px; }
        .header p { margin: 0; font-size: 14px; opacity: 0.8; }
        .body { padding: 32px; text-align: center; }
        .body h2 { color: #1B3A6B; font-size: 20px; margin: 0 0 16px; }
        .body p { color: #64748B; font-size: 14px; line-height: 1.6; margin: 0 0 24px; }
        .code-box { display: inline-block; font-size: 36px; font-weight: 800; letter-spacing: 8px; color: #1B3A6B; background: #F4F6F9; padding: 20px 40px; border-radius: 12px; margin-bottom: 24px; border: 2px solid #2E75B6; }
        .footer { padding: 20px 32px; background: #F8FAFC; text-align: center; font-size: 12px; color: #94A3B8; border-top: 1px solid #E2E8F0; }
        .changes-box { background: #F0F9FF; border-left: 4px solid #2E75B6; padding: 16px; margin: 0 0 24px; text-align: left; border-radius: 0 8px 8px 0; }
        .changes-box h3 { margin: 0 0 12px; font-size: 14px; color: #1B3A6B; }
        .changes-box ul { margin: 0; padding-left: 20px; color: #475569; font-size: 13px; line-height: 1.8; }
        .changes-box li { margin: 0; }
        .highlight { color: #D97706; font-weight: 600; }
        .warning-box { background: #FFFBEB; border: 1px solid #FCD34D; padding: 12px 16px; margin: 0 0 20px; border-radius: 8px; text-align: left; }
        .warning-box p { margin: 0; font-size: 13px; color: #92400E; }
        .warning-box strong { color: #B45309; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Terminus-Boutique</h1>
            <p>Sécurité du compte</p>
        </div>
        <div class="body">
            <h2>{{ $emailChanged ? 'Changement d\'adresse email' : 'Validation de modification' }}</h2>

            @if($emailChanged)
                <div class="warning-box">
                    <p><strong>⚠️ Changement d'email détecté</strong></p>
                    <p>Ce code a été envoyé à votre <strong>nouvelle</strong> adresse email pour vérification. Veuillez confirmer que vous avez accès à cette adresse.</p>
                </div>
            @endif

            <p>Bonjour,<br>
            Vous avez demandé la mise à jour de vos informations personnelles. Voici les modifications demandées :</p>

            @if(!empty($changes))
                <div class="changes-box">
                    <h3>Modifications demandées :</h3>
                    <ul>
                        @if(isset($changes['name']))
                            <li><strong>Nom :</strong> {{ $changes['name']['old'] }} → {{ $changes['name']['new'] }}</li>
                        @endif
                        @if(isset($changes['email']))
                            <li class="highlight"><strong>Email :</strong> {{ $changes['email']['old'] }} → {{ $changes['email']['new'] }}</li>
                        @endif
                        @if(isset($changes['password']))
                            <li><strong>Mot de passe :</strong> ******** (modifié)</li>
                        @endif
                    </ul>
                </div>
            @endif

            <p>Pour valider ces modifications, veuillez utiliser le code de vérification suivant :</p>

            <div class="code-box">{{ $code }}</div>

            <p style="font-size: 12px; color: #94A3B8;">Ce code est confidentiel et expire à la fin de votre session. Si vous n'êtes pas à l'origine de cette demande, veuillez ignorer cet e-mail et changer votre mot de passe immédiatement.</p>
        </div>
        <div class="footer">
            Terminus-Boutique — Gestion de boutique<br>
            <small>Envoyé le {{ now()->format('d/m/Y à H:i') }}</small>
        </div>
    </div>
</body>
</html>