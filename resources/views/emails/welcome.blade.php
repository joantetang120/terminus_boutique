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
        .body p { color: #64748B; font-size: 14px; line-height: 1.6; margin: 0 0 16px; }
        .info-box { background: #F4F6F9; padding: 20px; border-radius: 12px; margin: 24px 0; }
        .info-box p { margin: 8px 0; color: #1B3A6B; }
        .info-box strong { color: #1B3A6B; }
        .footer { padding: 20px 32px; background: #F8FAFC; text-align: center; font-size: 12px; color: #94A3B8; border-top: 1px solid #E2E8F0; }
        .button { display: inline-block; background: linear-gradient(135deg, #1B3A6B, #2E75B6); color: white; text-decoration: none; padding: 14px 32px; border-radius: 8px; font-weight: 600; font-size: 14px; margin-top: 16px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Terminus-Boutique</h1>
            <p>Gestion de boutique</p>
        </div>
        <div class="body">
            <h2>Bienvenue, {{ $userName }} !</h2>
            <p>Votre compte a été créé avec succès sur l'application <strong>Terminus-Boutique</strong>.</p>
            
            <div class="info-box">
                @if($createdByName)
                <p><strong>Créé par :</strong> {{ $createdByName }}</p>
                @endif
                <p><strong>Email :</strong> {{ $userName }}</p>
            </div>

            <p>Vous pouvez dès à présent vous connecter à votre espace pour gérer vos activités.</p>
            <p style="font-size:12px;">Si vous n'avez pas demandé la création de ce compte, veuillez contacter l'administrateur.</p>
        </div>
        <div class="footer">
            Terminus-Boutique — Gestion de boutique
        </div>
    </div>
</body>
</html>
