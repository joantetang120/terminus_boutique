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
        .code-box { display: inline-block; font-size: 36px; font-weight: 800; letter-spacing: 8px; color: #1B3A6B; background: #F4F6F9; padding: 20px 40px; border-radius: 12px; margin-bottom: 24px; border: 1px dashed #2E75B6; }
        .footer { padding: 20px 32px; background: #F8FAFC; text-align: center; font-size: 12px; color: #94A3B8; border-top: 1px solid #E2E8F0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Terminus-Boutique</h1>
            <p>Sécurité du compte</p>
        </div>
        <div class="body">
            <h2>Validation de modification</h2>
            <p>Bonjour {{ Auth::user()->name }},<br>
            Vous avez demandé la mise à jour de vos informations personnelles. Pour valider cette action, veuillez utiliser le code de vérification suivant :</p>
            
            <div class="code-box">{{ $code }}</div>

            <p style="font-size: 12px; color: #94A3B8;">Ce code est confidentiel et expire à la fin de votre session. Si vous n'êtes pas à l'origine de cette demande, ignorez simplement cet e-mail.</p>
        </div>
        <div class="footer">
            Terminus-Boutique — Gestion de boutique
        </div>
    </div>
</body>
</html>