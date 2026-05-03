<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport des Dépenses</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 2px solid #1e3a8a; padding-bottom: 10px; }
        .header h1 { color: #1e3a8a; margin: 0; font-size: 18px; }
        table { width: 100%; border-collapse: collapse; }
        th { background-color: #1e3a8a; color: white; padding: 8px; text-align: left; }
        td { padding: 8px; border-bottom: 1px solid #eee; }
        .amount { text-align: right; font-weight: bold; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #777; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Journal des Dépenses</h1>
        <p>Généré le {{ date('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Catégorie</th>
                <th>Libellé / Note</th>
                <th>Montant</th>
                <th>Par</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach($items as $item)
            <tr>
                <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') }}</td>
                <td>{{ $item->category->name ?? 'N/A' }}</td>
                <td>
                    <strong>{{ $item->label }}</strong><br>
                    <small>{{ $item->note }}</small>
                </td>
                <td class="amount">{{ number_format($item->amount, 0, ',', ' ') }} FCFA</td>
                <td>{{ $item->user->name ?? 'Système' }}</td>
            </tr>
            @php $total += $item->amount; @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f9fafb;">
                <td colspan="3" style="text-align: right; font-weight: bold;">TOTAL CUMULÉ :</td>
                <td class="amount" style="color: #b91c1c;">{{ number_format($total, 0, ',', ' ') }} FCFA</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">Document généré par IUGET Finance</div>
</body>
</html>