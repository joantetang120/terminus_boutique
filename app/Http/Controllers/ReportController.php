<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Services\ReportExportService;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller implements HasMiddleware
{
    protected $exportService;

    public function __construct(ReportExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    // Laravel 13 : middleware déclaré ici au lieu du constructeur
    public static function middleware(): array
{
    return [
        new Middleware('can:compta.view'),  // ← ici
    ];
}

    public function export(Request $request)
{
    $request->validate([
        'source' => 'required|in:ecritures,factures,depenses',
        'format' => 'required|in:csv,pdf',
        'from'   => 'nullable|date',
        'to'     => 'nullable|date|after_or_equal:from',
        'status' => 'nullable|string',
        'type'   => 'nullable|string',
        'date'   => 'nullable|date',
    ]);

    $filters = $request->all();
    $fileName = "export_" . $request->source . "_" . now()->format('d-m-Y_H-i');

    if ($request->format === 'csv') {
        // Log après pour ne pas interférer
        activity()->causedBy(Auth::user())
            ->withProperties(['filters' => $filters])
            ->log("A exporté les données de {$request->source} au format csv");

        return response()->streamDownload(function () use ($filters) {
            $this->exportService->generateCsv($filters['source'], $filters);
        }, "{$fileName}.csv");
    }

    if ($request->format === 'pdf') {
        $response = $this->exportService->generatePdf($filters['source'], $filters, $fileName);

        // Log après génération PDF
        activity()->causedBy(Auth::user())
            ->withProperties(['filters' => $filters])
            ->log("A exporté les données de {$request->source} au format pdf");

        return $response;
    }


}


public function exportPdf(Request $request)
{
    $filters = $request->all();
    $source = $request->get('source', 'ecritures');
    $fileName = "export_" . $source . "_" . now()->format('d-m-Y_H-i');
    
    return $this->exportService->generatePdf($source, $filters, $fileName);
}
}