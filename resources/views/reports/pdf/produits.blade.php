<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport des Produits</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #1e3a8a; padding-bottom: 10px; }
        .header h1 { color: #1e3a8a; margin: 0; text-transform: uppercase; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 10px; border-top: 1px solid #ddd; padding-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #f3f4f6; color: #1e3a8a; padding: 10px; text-align: left; border: 1px solid #ddd; }
        td { padding: 8px; border: 1px solid #ddd; }
        .alert { color: #dc2626; font-weight: bold; }
        .ok { color: #16a34a; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Rapport des Produits</h1>
        <p>Généré le : {{ $date ?? now()->format('d/m/Y H:i') }}</p>
        @if(!empty($filters['low_stock']))
            <p>Filtré : Stock faible uniquement</p>
        @endif
        @if(!empty($filters['search']))
            <p>Recherche : {{ $filters['search'] }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Unité</th>
                <th>Stock actuel</th>
                <th>Seuil alerte</th>
                <th>Statut</th>
                <th>Prix d'achat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td>{{ $item->unit }}</td>
                <td>{{ number_format($item->current_stock, 0, ',', ' ') }}</td>
                <td>{{ number_format($item->alert_threshold, 0, ',', ' ') }}</td>
                <td class="{{ $item->isLowStock() ? 'alert' : 'ok' }}">{{ $item->isLowStock() ? 'Alerte' : 'OK' }}</td>
                <td>{{ number_format($item->purchase_price, 0, ',', ' ') }} FCFA</td>
            </tr>
            @endforeach
        </tbody>
    </table>

  <div class="footer">
    TERMINUS-BOUTIQUE — Export automatique
</div>
</body>
</html>
