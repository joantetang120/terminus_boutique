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
     * Display a paginated list of invoices with filters
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['createdBy', 'cancelledBy', 'markedBy']);

        // Filter by search (number or client name)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('number', 'like', '%' . $search . '%')
                  ->orWhere('client_name', 'like', '%' . $search . '%');
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by client
        if ($request->filled('client')) {
            $query->where('client_name', 'like', '%' . $request->client . '%');
        }

        // Filter by marked for cancellation
        if ($request->filled('marked') && $request->marked === '1') {
            $query->where('marked_for_cancellation', true);
        }

        $invoices = $query->latest()->paginate(20);

        // Summary: unpaid invoices count and total balance
        $unpaidCount = Invoice::whereIn('status', ['IMPAYEE', 'PARTIELLE', 'EN_RETARD'])->count();
        $unpaidTotal = Invoice::whereIn('status', ['IMPAYEE', 'PARTIELLE', 'EN_RETARD'])->sum('balance');

        return view('factures.index', compact('invoices', 'unpaidCount', 'unpaidTotal'));
    }

    /**
     * Show the form for creating a new invoice
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
     * Store a newly created invoice using InvoiceService
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
     * Display the specified invoice with items and payment summary
     */
    public function show(Invoice $facture)
    {
        $facture->load(['items.product', 'createdBy', 'cancelledBy', 'payments']);

        // Calculate payment summary
        $paymentSummary = [
            'total_payments' => $facture->payments->sum('amount'),
            'payment_count' => $facture->payments->count(),
            'last_payment' => $facture->payments->sortByDesc('payment_date')->first(),
        ];

        return view('factures.show', compact('facture', 'paymentSummary'));
    }

    /**
     * Mark an invoice for cancellation (for users without facture.cancel permission)
     */
    public function markForCancellation(Invoice $facture)
    {
        // Only users without cancel permission can mark
        if (Auth::user()->hasPermissionTo('facture.cancel')) {
            return redirect()
                ->back()
                ->with('error', 'Les administrateurs n\'ont pas besoin de marquer les factures. Annulez directement.');
        }

        // Can't mark already cancelled invoices
        if ($facture->isCancelled()) {
            return redirect()
                ->back()
                ->with('error', 'Cette facture est déjà annulée.');
        }

        // Toggle the mark
        $isMarked = $facture->marked_for_cancellation;

        $facture->update([
            'marked_for_cancellation' => !$isMarked,
            'marked_by' => !$isMarked ? Auth::id() : null,
            'marked_at' => !$isMarked ? now() : null,
        ]);

        $message = !$isMarked
            ? 'Facture ' . $facture->number . ' marquée pour annulation.'
            : 'Marque d\'annulation retirée pour la facture ' . $facture->number . '.';

        return redirect()
            ->back()
            ->with('success', $message);
    }

    /**
     * Cancel an invoice using InvoiceService (requires facture.cancel permission)
     */
    public function cancel(Request $request, Invoice $facture)
    {
        $this->authorize('facture.cancel');

        $validated = $request->validate([
            'cancel_reason' => 'required|string|min:10|max:500',
        ]);

        try {
            InvoiceService::cancel($facture, $validated['cancel_reason'], Auth::user());

            return redirect()
                ->route('factures.index')
                ->with('success', 'Facture ' . $facture->number . ' annulée avec succès.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Erreur lors de l\'annulation: ' . $e->getMessage());
        }
    }

    /**
     * Generate and download PDF of the invoice (requires facture.print permission)
     */
    public function print(Invoice $facture)
    {
        $this->authorize('facture.print');

        try {
            return InvoicePdfService::download($facture);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Erreur lors de la génération du PDF: ' . $e->getMessage());
        }
    }

    /**
     * Preview PDF of the invoice in browser (requires facture.print permission)
     */
    public function preview(Invoice $facture)
    {
        $this->authorize('facture.print');

        try {
            return InvoicePdfService::stream($facture);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Erreur lors de la génération du PDF: ' . $e->getMessage());
        }
    }
}
