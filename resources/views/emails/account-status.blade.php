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
        .body h2 { font-size: 20px; margin: 0 0 16px; }
        .body p { color: #64748B; font-size: 14px; line-height: 1.6; margin: 0 0 16px; }
        .status-box { padding: 20px; border-radius: 12px; margin: 24px 0; }
        .status-box.activated { background: #D1FAE5; border: 2px solid #10B981; }
        .status-box.activated h3 { color: #065F46; margin: 0 0 8px; }
        .status-box.activated p { color: #047857; margin: 0; }
        .status-box.deactivated { background: #FEE2E2; border: 2px solid #EF4444; }
        .status-box.deactivated h3 { color: #991B1B; margin: 0 0 8px; }
        .status-box.deactivated p { color: #B91C1C; margin: 0; }
        .info-box { background: #F4F6F9; padding: 16px; border-radius: 8px; margin: 20px 0; }
        .info-box p { margin: 8px 0; color: #1B3A6B; font-size: 13px; }
        .info-box strong { color: #1B3A6B; }
        .footer { padding: 20px 32px; background: #F8FAFC; text-align: center; font-size: 12px; color: #94A3B8; border-top: 1px solid #E2E8F0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Terminus-Boutique</h1>
            <p>Gestion de boutique</p>
        </div>
        <div class="body">
            @if($isActive)
            <h2 style="color: #065F46;">✅ Compte activé</h2>
            <div class="status-box activated">
                <h3>Bonjour {{ $userName }} !</h3>
                <p>Votre compte a été <strong>réactivé</strong> avec succès.</p>
            </div>
            <p>Vous pouvez dès à présent vous connecter à nouveau à votre espace <strong>Terminus-Boutique</strong> et accéder à toutes vos fonctionnalités.</p>
            @else
            <h2 style="color: #991B1B;">🔒 Compte désactivé</h2>
            <div class="status-box deactivated">
                <h3>Bonjour {{ $userName }}</h3>
                <p>Votre compte a été <strong>désactivé</strong>.</p>
            </div>
            <p>Vous ne pouvez plus vous connecter à votre espace <strong>Terminus-Boutique</strong>. Si vous pensez qu'il s'agit d'une erreur, veuillez contacter l'administrateur.</p>
            @endif

            <div class="info-box">
                <p><strong>Modifié par :</strong> {{ $changedByName }}</p>
            </div>

            <p style="font-size:12px;">Si vous n'avez pas demandé ce changement, veuillez contacter l'administrateur immédiatement.</p>
        </div>
        <div class="footer">
            Terminus-Boutique — Gestion de boutique
        </div>
    </div>
</body>
</html>
