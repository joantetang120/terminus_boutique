<?php

namespace App\Services;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class InvoicePdfService
{
    /**
     * Generate PDF for an invoice
     *
     * @param Invoice $invoice
     * @return \Barryvdh\DomPDF\PDF
     */
    public static function generate(Invoice $invoice): \Barryvdh\DomPDF\PDF
    {
        // On charge les relations nécessaires
        $invoice->load(['items.product', 'payments', 'createdBy']);

        $data = [
            'facture' => $invoice, 
            'company' => [
                'name' => config('app.name', 'Terminus Boutique'),
                'address' => config('company.address', 'Adresse de l\'entreprise'),
                'phone' => config('company.phone', 'Téléphone'),
                'email' => config('company.email', 'Email'),
            ],
        ];

        // Activity log
        activity('facture.print')
            ->performedOn($invoice)
            ->causedBy(Auth::user())
            ->withProperties([
                'invoice_number' => $invoice->number,
                'client_name' => $invoice->client_name,
                'total' => $invoice->total,
                'status' => $invoice->status,
                'items_count' => $invoice->items->count(),
            ])
            ->log('Impression PDF facture: ' . $invoice->number . ' (' . $invoice->client_name . ') - ' . number_format($invoice->total, 2) . ' FCFA');

        // AJOUT D'OPTIONS DE RENDU POUR ÉVITER LA PAGE BLANCHE
        return Pdf::setOption([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false, // Désactivé pour éviter les délais réseau sur Windows
            'defaultFont' => 'sans-serif',
            'logOutputFile' => storage_path('logs/dompdf.log.html'), // Log les erreurs PDF ici
        ])
        ->setPaper('a4')
        ->loadView('factures.facture_pdf', $data);
    }

    /**
     * Generate and download PDF
     */
    public static function download(Invoice $invoice): \Illuminate\Http\Response
    {
        $pdf = self::generate($invoice);
        $filename = 'FACTURE_' . $invoice->number . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Generate and stream PDF
     */
    public static function stream(Invoice $invoice): \Illuminate\Http\Response
    {
        // Appel de la méthode generate avec les nouvelles options
        $pdf = self::generate($invoice);

        return $pdf->stream('FACTURE_' . $invoice->number . '.pdf');
    }
}