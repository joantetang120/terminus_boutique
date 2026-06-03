<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Facture {{ $invoice->number }}</title>
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            line-height: 1.4;
            color: #000;
            width: auto;
            max-width: none;
            margin: 0;
            padding: 0;
        }

        .receipt {
            width: auto;
            margin: 0 3mm 0 0;
            padding: 0 8mm 0 3mm;
        }

        .company-header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 1px dashed #000;
            padding-bottom: 8px;
        }

        .company-name {
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .company-details {
            font-size: 11px;
            line-height: 1.4;
        }

        .invoice-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin: 8px 0;
            text-transform: uppercase;
        }

        .invoice-info {
            font-size: 12px;
            margin-bottom: 8px;
        }

        .invoice-info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }

        .client-section {
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 6px 0;
            margin-bottom: 8px;
            font-size: 12px;
        }

        .client-label {
            font-weight: bold;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin-bottom: 8px;
        }

        .items-table th {
            text-align: left;
            border-bottom: 1px solid #000;
            padding: 3px 2px;
            font-weight: bold;
        }

        .items-table td {
            padding: 3px 2px;
            vertical-align: top;
        }

        .items-table .text-right {
            text-align: right;
        }

        .items-table .text-center {
            text-align: center;
        }

        .totals-section {
            border-top: 1px solid #000;
            padding-top: 6px;
            margin-top: 8px;
            font-size: 12px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }

        .total-row.grand-total {
            font-size: 14px;
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: 4px;
            margin-top: 4px;
        }

        .payment-info {
            margin-top: 8px;
            padding-top: 6px;
            border-top: 1px dashed #000;
            font-size: 11px;
        }

        .footer {
            margin-top: 12px;
            padding-top: 8px;
            border-top: 1px dashed #000;
            text-align: center;
            font-size: 11px;
        }

        .signature-section {
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
            font-size: 11px;
        }

        .signature-box {
            width: 45%;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 15px;
            padding-top: 3px;
        }

        .thank-you {
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            margin-top: 10px;
        }

        @media print {
            body {
                width: auto;
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>

<body>
    <div class="receipt">
        {{-- Company Header --}}
        <div class="company-header">
            <div class="company-name">{{ config('app.name', 'TERMINUS BOUTIQUE') }}</div>
            <div class="company-details">
                {{ config('company.address', 'Terminus Bonamoussadi') }}<br>
                Tél: {{ config('company.phone', '690394801') }}<br>

                {{ config('company.registration', '') }}
            </div>
        </div>

        {{-- Invoice Title --}}
        <div class="invoice-title">FACTURE N° {{ $invoice->number }}</div>

        {{-- Invoice Info --}}
        <div class="invoice-info">
            <div class="invoice-info-row">
                <span>Date:</span>
                <span>{{ $invoice->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="invoice-info-row">
                <span>Vendeur:</span>
                <span>{{ $invoice->createdBy?->name ?? '—' }}</span>
            </div>
        </div>

        {{-- Client Info --}}
        <div class="client-section">
            <span class="client-label">Client:</span> {{ $invoice->client_name }}<br>
            @if ($invoice->client_phone)
                <span class="client-label">Tél:</span> {{ $invoice->client_phone }}
            @endif
        </div>

        {{-- Items Table --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 35%;">Désignation</th>
                    <th class="text-center" style="width: 12%;">Qté</th>
                    <th class="text-center" style="width: 12%;">Unité</th>
                    <th class="text-right" style="width: 18%;">Prix Unitaire</th>
                    <th class="text-right" style="width: 23%;">Prix Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->items as $item)
                    <tr>
                        <td>{{ $item->designation }}</td>
                        <td class="text-center">{{ number_format($item->quantity_sold, 0, ',', ' ') }}</td>
                        <td class="text-center">{{ $item->unit_sold }}</td>
                        <td class="text-right">{{ number_format($item->unit_price, 0, ',', ' ') }}</td>
                        <td class="text-right">{{ number_format($item->total_price, 0, ',', ' ') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Totals --}}
        <div class="totals-section">
            <div class="total-row grand-total">
                <span>TOTAL:</span>
                <span>{{ number_format($invoice->total, 0, ',', ' ') }} FCFA</span>
            </div>
            @if ($invoice->paid_amount > 0)
                <div class="total-row">
                    <span>Payé:</span>
                    <span>{{ number_format($invoice->paid_amount, 0, ',', ' ') }} FCFA</span>
                </div>
                @if ($invoice->balance > 0)
                    <div class="total-row" style="font-weight: bold;">
                        <span>Solde dû:</span>
                        <span>{{ number_format($invoice->balance, 0, ',', ' ') }} FCFA</span>
                    </div>
                @endif
            @endif
        </div>

        {{-- Thank You --}}
        <div class="thank-you">
            Merci pour votre confiance!
        </div>

        {{-- Signatures --}}
        <table style="width: 100%; margin-top: 20px; font-size: 11px;">
            <tr>
                <td style="width: 50%; text-align: left; vertical-align: bottom; padding-right: 10px;">
                    <div style="border-top: 1px solid #000; padding-top: 3px; display: inline-block; width: 80%;">
                        Visa Vendeur
                    </div>
                </td>
                <td style="width: 50%; text-align: right; vertical-align: bottom; padding-left: 10px;">
                    <div style="border-top: 1px solid #000; padding-top: 3px; display: inline-block; width: 80%;">
                        Visa Client
                    </div>
                </td>
            </tr>
        </table>

        {{-- Footer --}}
        <div class="footer">
            <small>Document généré le {{ now()->format('d/m/Y H:i') }}</small>
        </div>
    </div>
</body>

</html>
