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
        .body p { color: #64748B; font-size: 14px; line-height: 1.6; margin: 0 0 24px; }
        .code { display: inline-block; font-size: 36px; font-weight: 800; letter-spacing: 12px; color: #1B3A6B; background: #F4F6F9; padding: 16px 32px; border-radius: 12px; margin-bottom: 24px; }
        .expiry { font-size: 12px; color: #94A3B8; }
        .footer { padding: 20px 32px; background: #F8FAFC; text-align: center; font-size: 12px; color: #94A3B8; border-top: 1px solid #E2E8F0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Terminus-Boutique</h1>
            <p>Réinitialisation du mot de passe</p>
        </div>
        <div class="body">
            <p>Voici votre code de vérification à 4 chiffres. Entrez-le dans la page de réinitialisation pour continuer.</p>
            <div class="code">{{ $code }}</div>
            <p class="expiry">Ce code expire dans {{ $expiresInMinutes }} minutes.</p>
            <p style="font-size:12px;">Si vous n'avez pas demandé cette réinitialisation, ignorez cet email.</p>
        </div>
        <div class="footer">
            Terminus-Boutique — Gestion de boutique
        </div>
    </div>
</body>
</html>
