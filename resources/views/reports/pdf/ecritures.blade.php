<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Grand Livre des Écritures</title>
    <style>
        @page { size: a4 landscape; margin: 1cm; }
        body { font-family: 'Helvetica', sans-serif; font-size: 10px; }
        .header { margin-bottom: 20px; }
        .title { color: #1e3a8a; font-size: 20px; font-weight: bold; text-transform: uppercase; }
        .meta { font-size: 9px; color: #666; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #f8fafc; border: 1px solid #e2e8f0; padding: 10px; color: #475569; text-align: left; }
        td { border: 1px solid #e2e8f0; padding: 8px; }
        tr:nth-child(even) { background-color: #f9fafb; }
        .type-recette { color: #16a34a; font-weight: bold; }
        .type-depense { color: #dc2626; font-weight: bold; }
        .empty { text-align: center; padding: 20px; color: #999; }
        .footer { margin-top: 20px; font-size: 9px; color: #aaa; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <span class="title">Comptabilité Générale</span><br>
        <span class="meta">Journal des écritures comptables — Généré le {{ $date ?? now()->format('d/m/Y H:i') }}</span>
        @if(!empty($filters['from']) || !empty($filters['to']))
            <span class="meta"> | Période : {{ $filters['from'] ?? 'Début' }} au {{ $filters['to'] ?? 'Fin' }}</span>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Description</th>
                <th>Référence</th>
                <th>Montant</th>
                <th>Effectué par</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
            <tr>
                <td>{{ \Carbon\Carbon::parse($item->date ?? $item->created_at)->format('d/m/Y') }}</td>
                <td class="type-{{ $item->type }}">
                    {{ strtoupper($item->type) }}
                </td>
                <td>{{ $item->description ?? 'N/A' }}</td>
                <td><code>{{ $item->reference_type ?? 'N/A' }}</code></td>
                <td style="font-weight: bold;">{{ number_format($item->amount, 0, ',', ' ') }} FCFA</td>
                <td>{{ $item->createdBy->name ?? 'Admin' }}</td>
            </tr>
            @empty
            <tr>
                <td class="empty" colspan="6">Aucune écriture trouvée.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">TERMINUS-BOUTIQUE — Export automatique</div>
</body>
</html>