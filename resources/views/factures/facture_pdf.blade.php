<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; font-size: 13px; line-height: 1.4; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #F8FAFC; border: 1px solid #E2E8F0; padding: 10px; text-align: left; color: #475569; }
        td { border: 1px solid #E2E8F0; padding: 10px; vertical-align: top; }
        .text-right { text-align: right; }
        
        /* En-tête */
        .header-table td { border: none; padding: 0; vertical-align: top; }
        .shop-name { font-size: 22px; font-weight: bold; color: #1E293B; text-transform: uppercase; }
        .invoice-details { color: #64748B; margin-top: 5px; }

        /* Filigrane */
        .watermark {
            position: fixed; top: 30%; left: 10%; font-size: 90px;
            color: rgba(220, 38, 38, 0.1); transform: rotate(-45deg); z-index: -1000;
            width: 100%; text-align: center; font-weight: bold;
        }

        /* Cadre Solde */
        .balance-due-box {
            border: 2px solid #DC2626; padding: 8px 12px; margin-top: 10px;
            display: inline-block; color: #DC2626; font-weight: bold; background: #FEF2F2;
        }

        /* Tampon SOLDÉE */
        .stamp-soldee {
            border: 5px double #16A34A; color: #16A34A; font-weight: bold;
            padding: 10px 20px; font-size: 24px; text-align: center;
            width: 280px; margin-top: 20px; float: right; transform: rotate(-5deg);
        }
        .logo-img {
            max-height: 80px; /* Ajuste la hauteur selon tes goûts */
            width: auto;
            margin-bottom: 10px;
        }


        body { font-family: 'Helvetica', sans-serif; font-size: 11px; color: #000; margin: 0; padding: 10px; }
    
    /* En-tête */
    .header-table td { border: none; padding: 0; vertical-align: middle; }
    .company-info { font-size: 10px; line-height: 1.3; }
    .company-name { font-size: 16px; font-weight: bold; }

    /* Titre Facture */
    .invoice-title { 
        font-weight: bold; 
        border-bottom: 1px dashed #000; 
        margin: 15px 0 10px 0; 
        padding-bottom: 5px; 
        font-size: 12px;
    }

    /* Tableau des produits */
    .items-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    .items-table th { border-top: 1px dashed #000; border-bottom: 1px dashed #000; padding: 6px 4px; text-align: left; }
    .items-table td { padding: 6px 4px; border: none; }
    .text-right { text-align: right; }

    /* Section Totaux */
    .total-section { 
        border-top: 1px dashed #000; 
        margin-top: 10px; 
        padding-top: 10px; 
        font-weight: bold; 
        font-size: 13px;
    }

    /* Zone signatures */
    .footer-signatures { margin-top: 60px; width: 100%; }
    .footer-signatures td { border: none; width: 50%; font-weight: bold; }
    </style>
</head>
<body>
    {{-- En-tête avec TON Logo et TES infos --}}
    <table class="header-table" style="width: 100%;">
        <tr>
            <td style="width: 20%;">
                @php
                    $path = public_path('img/logo-blue.png');
                    $base64 = 'data:image/png;base64,' . base64_encode(file_get_contents($path));
                @endphp
                <img src="{{ $base64 }}" style="width: 120px;">
            </td>
            <td style="width: 80%;" class="company-info">
                <!-- <div class="company-name">TERMINUS BOUTIQUE</div> -->
                {{ $company['address'] ?? 'Douala, Cameroun' }}<br>
                Tél: {{ $company['phone'] ?? '690 39 48 01' }}<br>
               
            </td>
        </tr>
    </table>

    <div class="invoice-title">FACTURE N° {{ $facture->number }}</div>

    {{-- Infos Client et Vendeur --}}
    <table style="width: 100%; margin-bottom: 10px;">
        <tr>
            <td><strong>DATE :</strong> {{ $facture->created_at->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td>
            <strong>Client :</strong> {{ $facture->client_name }} 
            {{-- On ajoute le téléphone ici s'il existe --}}
            @if($facture->client_phone)
                <span style="margin-left: 10px;"><strong>Tél :</strong> {{ $facture->client_phone }}</span>
            @endif
        </td>
            <td class="text-right"><strong>Vendeur :</strong> {{ Auth::user()->name ?? 'Admin' }}</td>
        </tr>
    </table>

    {{-- Tableau des articles --}}
    <table class="items-table">
        <thead>
            <tr>
                
                <th style="width: 45%;">Désignation</th>
                <th style="text-align: right;">P.U</th>
                <th style="text-align: right;">Qté</th>
                <th style="text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($facture->items as $item)
            <tr>
              
                <td>{{ $item->designation }}</td>
                <td class="text-right">{{ number_format($item->unit_price, 0, ',', ' ') }}</td>
                <td class="text-right">{{ (int)$item->quantity_sold }}</td>
                <td class="text-right"><strong>{{ number_format($item->total_price, 0, ',', ' ') }} FCFA</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Section Total --}}
    <div class="total-section">
        <div style="float: left;">TOTAL :</div>
        <div style="float: right;">{{ number_format($facture->total, 0, ',', ' ') }} FCFA</div>
        <div style="clear: both;"></div>
    </div>

    {{-- Pied de page --}}
    <div style="text-align: center; margin-top: 20px; font-size: 9px;">
        Les articles vendus ne sont ni repris ni échangés.<br>
        Merci pour votre confiance !
    </div>

    {{-- Signatures comme sur le modèle --}}
    <table class="footer-signatures">
        <tr>
            <td>VISA Vendeur</td>
            <td class="text-right">VISA Client</td>
        </tr>
    </table>
</body>
</html>