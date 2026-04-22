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
        $invoice->load(['items.product', 'payments', 'createdBy']);

        $data = [
            'invoice' => $invoice,
            'company' => [
                'name' => config('app.name', 'Terminus Boutique'),
                'address' => config('company.address', 'Adresse de l\'entreprise'),
                'phone' => config('company.phone', 'Téléphone'),
                'email' => config('company.email', 'Email'),
            ],
        ];

        // Logger facture.print dans activity_log
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

        $pdf = Pdf::loadView('pdf.invoice', $data);

        // Set paper size for 80mm thermal receipt printer format
        // Width: 80mm (226.77 points), Height: auto based on content
        $pdf->setPaper([0, 0, 226.77, 600], 'portrait');
        $pdf->setOptions(['defaultFont' => 'Courier']);

        return $pdf;
    }

    /**
     * Generate and download PDF
     *
     * @param Invoice $invoice
     * @return \Illuminate\Http\Response
     */
    public static function download(Invoice $invoice): \Illuminate\Http\Response
    {
        $pdf = self::generate($invoice);
        $filename = 'FACTURE_' . $invoice->number . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Generate and stream PDF
     *
     * @param Invoice $invoice
     * @return \Illuminate\Http\Response
     */
    public static function stream(Invoice $invoice): \Illuminate\Http\Response
    {
        $pdf = self::generate($invoice);

        return $pdf->stream('FACTURE_' . $invoice->number . '.pdf');
    }
}
