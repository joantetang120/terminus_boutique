<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Expense;
use App\Models\AccountingEntry;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportExportService
{
    private function getQuery($source, $filters)
    {
        $query = match($source) {
            'factures' => Invoice::query()->with(['payments']),
            'depenses'  => Expense::query()->with(['category', 'user']),
            'ecritures' => AccountingEntry::query()->with('createdBy'),
        };

        if (!empty($filters['from'])) $query->whereDate('created_at', '>=', $filters['from']);
        if (!empty($filters['to']))   $query->whereDate('created_at', '<=', $filters['to']);

        if (!empty($filters['date'])) $query->whereDate('date', $filters['date']);

        if ($source === 'factures' && !empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if ($source === 'depenses' && !empty($filters['category'])) {
            $query->where('expense_category_id', $filters['category']);
        }

        if ($source === 'ecritures' && !empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return $query->orderBy('created_at', 'desc');
    }

    public function generateCsv($source, $filters)
    {
        $handle = fopen('php://output', 'w');
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

        $headers = match($source) {
            'factures'  => ['N° Facture', 'Client', 'Date', 'Total', 'Payé', 'Solde', 'Statut'],
            'depenses'  => ['Date', 'Catégorie', 'Libellé', 'Montant', 'Note', 'Par'],
            'ecritures' => ['Date', 'Type', 'Description', 'Montant', 'Référence', 'Effectué par'],
        };

        fputcsv($handle, $headers, ';');

        $this->getQuery($source, $filters)->chunk(200, function ($rows) use ($handle, $source) {
            foreach ($rows as $row) {
                fputcsv($handle, $this->mapRowData($row, $source), ';');
            }
        });

        fclose($handle);
    }

 public function generatePdf($source, $filters, $fileName)
{
    $data = $this->getQuery($source, $filters)->get();

    $pdf = Pdf::loadView("reports.pdf.{$source}", [
        'items'   => $data,
        'filters' => $filters,
        'date'    => now()->format('d/m/Y H:i'),
    ])->setPaper('a4', 'landscape');

    return response($pdf->output(), 200, [
    'Content-Type' => 'application/pdf',
    'Content-Disposition' => 'attachment; filename="'.$fileName.'.pdf"',
    ]);
}
    private function mapRowData($row, $source)
    {
        return match($source) {
            'factures' => [
    $row->number,
    $row->client_name,  // directement, pas $row->client->name
    $row->created_at->format('d/m/Y'),
    number_format($row->total ?? 0, 0, ',', ' ') . ' FCFA',
    number_format($row->paid_amount ?? 0, 0, ',', ' ') . ' FCFA',
    number_format($row->balance ?? 0, 0, ',', ' ') . ' FCFA',
    $row->status ?? 'N/A',
],
            'depenses' => [
                $row->created_at->format('d/m/Y'),
                $row->category->name ?? 'N/A',
                $row->label ?? $row->description ?? 'N/A',
                number_format($row->amount ?? 0, 0, ',', ' ') . ' FCFA',
                $row->note ?? '',
                $row->user->name ?? 'N/A',
            ],
            'ecritures' => [
                \Carbon\Carbon::parse($row->date ?? $row->created_at)->format('d/m/Y'),
                strtoupper($row->type ?? 'N/A'),
                $row->description ?? 'N/A',
                number_format($row->amount ?? 0, 0, ',', ' ') . ' FCFA',
                $row->reference_type ?? 'N/A',
                $row->createdBy->name ?? 'Admin',
            ],
        };
    }
}