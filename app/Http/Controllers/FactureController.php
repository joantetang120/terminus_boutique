<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceRequest;
use App\Models\Invoice;
use App\Models\Product;
use App\Services\InvoicePdfService;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FactureController extends Controller
{
    /**
     * Liste des factures avec filtres et recherche
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['createdBy', 'cancelledBy']);

        // Filtre Admin : Voir uniquement les demandes d'annulation
        if ($request->has('filter_cancellation')) {
            $query->where('cancellation_requested', true)
                  ->where('status', '!=', 'annulee');
        }

        // Recherche par numéro ou nom client
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('number', 'like', '%' . $search . '%')
                  ->orWhere('client_name', 'like', '%' . $search . '%');
            });
        }

        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtre par plage de dates
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filtre par client exact ou partiel
        if ($request->filled('client')) {
            $query->where('client_name', 'like', '%' . $request->client . '%');
        }

        $invoices = $query->latest()->paginate(20);

        // Calculs pour le résumé (stats en haut de page)
        $unpaidCount = Invoice::whereIn('status', ['IMPAYEE', 'PARTIELLE', 'EN_RETARD'])->count();
        $unpaidTotal = Invoice::whereIn('status', ['IMPAYEE', 'PARTIELLE', 'EN_RETARD'])->sum('balance');

        return view('factures.index', compact('invoices', 'unpaidCount', 'unpaidTotal'));
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        $products = Product::where('is_active', true)
            ->with('unitConversions')
            ->select('id', 'name', 'unit', 'sale_unit', 'purchase_unit', 'sale_conversion_rate', 'purchase_conversion_rate', 'current_stock', 'alert_threshold', 'is_active')
            ->get()
            ->map(function ($product) {
                $product->available_units = $product->getAvailableUnits();
                return $product;
            });
            
        return view('factures.create', compact('products'));
    }

    /**
     * Enregistrement d'une nouvelle facture
     */
    public function store(InvoiceRequest $request)
    {
        $validated = $request->validated();

        try {
            $invoice = InvoiceService::create($validated, Auth::id());

            return redirect()
                ->route('factures.show', $invoice)
                ->with('success', 'Facture ' . $invoice->number . ' créée avec succès.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erreur lors de la création de la facture: ' . $e->getMessage());
        }
    }

    /**
     * Détails d'une facture
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['items.product', 'createdBy', 'cancelledBy', 'payments']);

        $paymentSummary = [
            'total_payments' => $invoice->payments->sum('amount'),
            'payment_count' => $invoice->payments->count(),
            'last_payment' => $invoice->payments->sortByDesc('payment_date')->first(),
        ];

        return view('factures.show', compact('invoice', 'paymentSummary'));
    }

    /**
     * VENDEUR : Demander l'annulation à l'admin
     */
    public function requestCancellation(Invoice $invoice)
    {
        if ($invoice->status === 'ANNULEE' || $invoice->status === 'annulee') {
            return back()->with('error', 'Cette facture est déjà annulée.');
        }

        $invoice->update(['cancellation_requested' => true]);

        return back()->with('success', 'La demande d\'annulation a été envoyée à l\'administrateur.');
    }

    /**
     * ADMIN : Confirmer l'annulation et remettre les stocks à jour
     */
    public function confirmCancellation(Invoice $invoice)
    {
        if (!Auth::user()->can('facture.admin')) {
            abort(403, 'Action non autorisée.');
        }
         try {
        InvoiceService::cancel($invoice, 'Annulation validée par l\'administrateur', Auth::user());
        $invoice->update(['cancellation_requested' => false]);

        return redirect()->route('factures.index')
            ->with('success', 'L\'annulation de la facture ' . $invoice->number . ' a été validée.');
    } catch (\Exception $e) {
        return back()->with('error', 'Erreur lors de la validation : ' . $e->getMessage());
    }
    }

    /**
     * Impression PDF
     */
    public function print(Invoice $invoice)
    {
        try {
            $invoice->load(['items.product', 'payments', 'createdBy']);
            $data = ['facture' => $invoice, 'company' => ['name' => 'TERMINUS BOUTIQUE']];
            $html = view('factures.facture_pdf', $data)->render();
            
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A5', 'portrait');
            set_time_limit(120); 
            $dompdf->render();

            if (ob_get_level() > 0) ob_end_clean();
            return response($dompdf->output())->header('Content-Type', 'application/pdf');

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Erreur PDF',
                'erreur' => $e->getMessage()
            ]);
        }
    }
}