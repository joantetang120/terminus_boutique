<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture {{ $invoice->number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .company-info {
            text-align: left;
            margin-bottom: 20px;
        }
        .invoice-info {
            text-align: right;
            margin-bottom: 20px;
        }
        .client-info {
            margin-bottom: 20px;
            padding: 10px;
            background: #f5f5f5;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background: #f5f5f5;
            font-weight: bold;
        }
        .totals {
            text-align: right;
            margin-top: 20px;
        }
        .totals table {
            width: 300px;
            margin-left: auto;
        }
        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-weight: bold;
        }
        .status-IMPAYEE { background: #ffc107; color: #000; }
        .status-PARTIELLE { background: #17a2b8; color: #fff; }
        .status-SOLDEE { background: #28a745; color: #fff; }
        .status-ANNULEE { background: #dc3545; color: #fff; }
        .status-EN_RETARD { background: #dc3545; color: #fff; }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>FACTURE</h1>
        <span class="status status-{{ $invoice->status }}">{{ $invoice->status }}</span>
    </div>

    <table style="width: 100%; border: none;">
        <tr style="border: none;">
            <td style="border: none; width: 50%; vertical-align: top;">
                <div class="company-info">
                    <strong>{{ $company['name'] }}</strong><br>
                    {{ $company['address'] }}<br>
                    Tél: {{ $company['phone'] }}<br>
                    Email: {{ $company['email'] }}
                </div>
            </td>
            <td style="border: none; width: 50%; vertical-align: top;">
                <div class="invoice-info">
                    <strong>Facture N°:</strong> {{ $invoice->number }}<br>
                    <strong>Date:</strong> {{ $invoice->created_at->format('d/m/Y') }}<br>
                    <strong>Échéance:</strong> {{ $invoice->due_date?->format('d/m/Y') ?? 'N/A' }}<br>
                    <strong>Créée par:</strong> {{ $invoice->createdBy?->name ?? 'N/A' }}
                </div>
            </td>
        </tr>
    </table>

    <div class="client-info">
        <strong>Client:</strong> {{ $invoice->client_name }}<br>
        @if($invoice->client_phone)
            <strong>Téléphone:</strong> {{ $invoice->client_phone }}
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Désignation</th>
                <th>Unité</th>
                <th>Qté</th>
                <th>Prix unit.</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->designation }}</td>
                    <td>{{ $item->unit_sold }}</td>
                    <td>{{ number_format($item->quantity_sold, 2) }}</td>
                    <td>{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                    <td>{{ number_format($item->total_price, 0, ',', ' ') }} FCFA</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td><strong>Total:</strong></td>
                <td>{{ number_format($invoice->total, 0, ',', ' ') }} FCFA</td>
            </tr>
            <tr>
                <td><strong>Montant payé:</strong></td>
                <td>{{ number_format($invoice->paid_amount, 0, ',', ' ') }} FCFA</td>
            </tr>
            <tr style="background: #f5f5f5;">
                <td><strong>Solde restant:</strong></td>
                <td>{{ number_format($invoice->balance, 0, ',', ' ') }} FCFA</td>
            </tr>
        </table>
    </div>

    @if($invoice->payments->count() > 0)
        <h3>Historique des paiements</h3>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Montant</th>
                    <th>Méthode</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->payments as $payment)
                    <tr>
                        <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                        <td>{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</td>
                        <td>{{ $payment->payment_method ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($invoice->cancelled_at)
        <div style="color: #dc3545; margin-top: 20px; padding: 10px; border: 2px solid #dc3545;">
            <strong>FACTURE ANNULÉE</strong><br>
            Date d'annulation: {{ $invoice->cancelled_at->format('d/m/Y H:i') }}<br>
            Motif: {{ $invoice->cancel_reason }}
        </div>
    @endif

    <div class="footer">
        <p>Document généré le {{ now()->format('d/m/Y H:i') }} | {{ $company['name'] }}</p>
    </div>
</body>
</html>
