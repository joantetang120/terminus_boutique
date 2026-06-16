<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Expense;
use App\Models\AccountingEntry;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportExportService
{
    private function getQuery($source, $filters)
    {
        $query = match($source) {
            'factures' => Invoice::query()->with(['payments']),
            'depenses'  => Expense::query()->with(['createdBy']),
            'ecritures' => AccountingEntry::query()->with('createdBy'),
            'produits' => Product::query(),
        };

        if ($source !== 'produits') {
            if (!empty($filters['from'])) $query->whereDate('created_at', '>=', $filters['from']);
            if (!empty($filters['to']))   $query->whereDate('created_at', '<=', $filters['to']);
            if (!empty($filters['date'])) $query->whereDate('date', $filters['date']);
        }

        if ($source === 'factures' && !empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if ($source === 'depenses' && !empty($filters['category'])) {
            $query->where('expense_category_id', $filters['category']);
        }

        if ($source === 'ecritures' && !empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if ($source === 'produits') {
            if (!empty($filters['search'])) {
                $query->where('name', 'like', '%' . $filters['search'] . '%');
            }
            if (!empty($filters['status'])) {
                if ($filters['status'] === 'active') {
                    $query->active();
                } elseif ($filters['status'] === 'inactive') {
                    $query->where('is_active', false);
                }
            }
            if (!empty($filters['low_stock'])) {
                $query->lowStock();
            }
        }

        return $query->when(
            $source !== 'produits',
            fn($q) => $q->orderBy('created_at', 'desc'),
            fn($q) => $q->orderBy('name')
        );
    }

    public function generateCsv($source, $filters)
    {
        $handle = fopen('php://output', 'w');
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

        $headers = match($source) {
            'factures'  => ['N° Facture', 'Client', 'Date', 'Total', 'Payé', 'Solde', 'Statut'],
            'depenses'  => ['Date', 'Catégorie', 'Libellé', 'Montant', 'Note', 'Par'],
            'ecritures' => ['Date', 'Type', 'Description', 'Montant', 'Référence', 'Effectué par'],
            'produits'  => ['Nom', 'Unité', 'Stock actuel', 'Seuil alerte', 'Statut', 'Prix d\'achat'],
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
    public function generateDocx($source, $filters, $fileName)
    {
        $data = $this->getQuery($source, $filters)->get();

        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Helvetica');
        $phpWord->setDefaultFontSize(11);

        $section = $phpWord->addSection([
            'marginLeft'  => 1000,
            'marginRight' => 1000,
            'marginTop'   => 1000,
            'marginBottom'=> 1000,
        ]);

        $section->addText(
            strtoupper('Rapport des ' . match($source) {
                'factures' => 'Factures',
                'depenses' => 'Dépenses',
                'ecritures' => 'Écritures Comptables',
                'produits' => 'Produits',
            }),
            ['bold' => true, 'size' => 16, 'color' => '1e3a8a'],
            ['align' => 'center']
        );
        $section->addText(
            'Généré le : ' . now()->format('d/m/Y H:i'),
            ['size' => 10, 'color' => '666666'],
            ['align' => 'center']
        );
        $section->addTextBreak(1);

        $headers = match($source) {
            'factures'  => ['N° Facture', 'Client', 'Date', 'Total', 'Statut'],
            'depenses'  => ['Date', 'Catégorie', 'Libellé', 'Montant', 'Par'],
            'ecritures' => ['Date', 'Type', 'Description', 'Montant', 'Référence'],
            'produits'  => ['Nom', 'Unité', 'Stock actuel', 'Seuil alerte', 'Statut', "Prix d'achat"],
        };

        $table = $section->addTable([
            'borderSize' => 1,
            'borderColor' => 'cccccc',
            'cellMargin' => 80,
        ]);

        $table->addRow();
        foreach ($headers as $header) {
            $table->addCell(1600, [
                'bgColor' => 'f3f4f6',
                'bold' => true,
                'size' => 10,
            ])->addText($header);
        }

        foreach ($data as $row) {
            $table->addRow();
            $cells = match($source) {
                'factures' => [
                    $row->number,
                    $row->client_name,
                    \Carbon\Carbon::parse($row->created_at)->format('d/m/Y'),
                    number_format($row->total, 0, ',', ' ') . ' FCFA',
                    ucfirst($row->status),
                ],
                'depenses' => [
                    $row->created_at->format('d/m/Y'),
                    $row->category->name ?? 'N/A',
                    $row->label ?? $row->description ?? 'N/A',
                    number_format($row->amount, 0, ',', ' ') . ' FCFA',
                    $row->user->name ?? 'N/A',
                ],
                'ecritures' => [
                    \Carbon\Carbon::parse($row->date ?? $row->created_at)->format('d/m/Y'),
                    strtoupper($row->type ?? 'N/A'),
                    $row->description ?? 'N/A',
                    number_format($row->amount, 0, ',', ' ') . ' FCFA',
                    $row->reference_type ?? 'N/A',
                ],
                'produits' => [
                    $row->name,
                    $row->unit,
                    number_format($row->current_stock, 0, ',', ' '),
                    number_format($row->alert_threshold, 0, ',', ' '),
                    $row->isLowStock() ? 'Alerte' : 'OK',
                    number_format($row->purchase_price, 0, ',', ' ') . ' FCFA',
                ],
            };
            foreach ($cells as $cell) {
                $table->addCell(1600, ['size' => 9])->addText($cell);
            }
        }

        $section->addTextBreak(1);
        $section->addText(
            'TERMINUS-BOUTIQUE — Export automatique',
            ['size' => 8, 'color' => '999999'],
            ['align' => 'right']
        );

        $tempFile = tempnam(sys_get_temp_dir(), 'docx_');
        $writer = IOFactory::createWriter($phpWord);
        $writer->save($tempFile);

        return (new BinaryFileResponse($tempFile, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ]))->setContentDisposition('attachment', $fileName . '.docx')
          ->deleteFileAfterSend(true);
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
            'produits' => [
                $row->name,
                $row->unit,
                number_format($row->current_stock ?? 0, 0, ',', ' '),
                number_format($row->alert_threshold ?? 0, 0, ',', ' '),
                $row->isLowStock() ? 'Alerte' : 'OK',
                number_format($row->purchase_price ?? 0, 0, ',', ' ') . ' FCFA',
            ],
        };
    }
}