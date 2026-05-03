<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport des Factures</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #1e3a8a; padding-bottom: 10px; }
        .header h1 { color: #1e3a8a; margin: 0; text-transform: uppercase; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 10px; border-top: 1px solid #ddd; padding-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #f3f4f6; color: #1e3a8a; padding: 10px; text-align: left; border: 1px solid #ddd; }
        td { padding: 8px; border: 1px solid #ddd; }
        .status-paid { color: green; font-weight: bold; }
        .status-pending { color: orange; font-weight: bold; }
        .total-row { background-color: #eee; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Rapport des Factures</h1>
        <p>Généré le : {{ $date ?? now()->format('d/m/Y H:i') }}</p>
        @if(!empty($filters['from']) || !empty($filters['to']))
            <p>Période : {{ $filters['from'] ?? 'Début' }} au {{ $filters['to'] ?? 'Fin' }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>N° Facture</th>
                <th>Client</th>
                <th>Date</th>
                <th>Total</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item->number }}</td>
                <td>{{ $item->client_name }}</td>
                <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') }}</td>
                <td>{{ number_format($item->total, 2, ',', ' ') }} FCFA</td>
                <td>{{ ucfirst($item->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

  <div class="footer">
    TERMINUS-BOUTIQUE — Export automatique
</div>
</body>
</html>