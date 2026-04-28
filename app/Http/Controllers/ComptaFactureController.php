<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\DueDateAlertService;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComptaFactureController extends Controller
{
    public function __construct(
        private DueDateAlertService $dueDateAlertService,
        private InvoiceService $invoiceService
    ) {}

    /**
     * Display all invoices with accounting info (paid_amount, balance, status, due_date)
     * Filterable by status, date, client. Paginated.
     */
    public function index(Request $request)
    {
        // Check permission
        if (!Auth::user()->can('compta.view')) {
            abort(403, 'Accès refusé.');
        }

        // Build query with eager-loaded payments
        $query = Invoice::with(['payments', 'items', 'createdBy'])
            ->select('id', 'number', 'client_name', 'client_phone', 'total', 'paid_amount', 'balance', 'status', 'due_date', 'created_at', 'created_by', 'marked_for_cancellation')
            ->where('status', '!=', 'ANNULEE');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by client name
        if ($request->filled('client')) {
            $query->where('client_name', 'like', '%' . $request->client . '%');
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by due date status
        if ($request->filled('due_status')) {
            match ($request->due_status) {
                'overdue' => $query->where('due_date', '<', now()->format('Y-m-d'))
                    ->whereIn('status', ['IMPAYEE', 'PARTIELLE']),
                'due_today' => $query->whereDate('due_date', now()->format('Y-m-d')),
                'due_soon' => $query->whereBetween('due_date', [now()->format('Y-m-d'), now()->addDays(3)->format('Y-m-d')])
                    ->whereIn('status', ['IMPAYEE', 'PARTIELLE']),
                default => $query,
            };
        }

        // Order by creation date descending
        $query->orderBy('created_at', 'desc');

        // Clone for stats BEFORE paginate() executes the query
        $statsQuery = (clone $query);

        // Get paginated results
        $invoices = $query->paginate(20)->withQueryString();

        // Get alerts for banners
        $alerts = $this->dueDateAlertService->getAlerts();

        // Compute stats from the filtered query
        // Note: If user filtered by status, some stats may be 0 (which is correct)
        $stats = [
            'total_unpaid' => (clone $statsQuery)->whereIn('status', ['IMPAYEE', 'PARTIELLE', 'EN_RETARD'])->sum('balance'),
            'count_unpaid' => (clone $statsQuery)->whereIn('status', ['IMPAYEE', 'PARTIELLE', 'EN_RETARD'])->count(),
            'total_overdue' => (clone $statsQuery)
                ->where(function ($q) {
                    $q->where('due_date', '<', now()->format('Y-m-d'))
                      ->whereIn('status', ['IMPAYEE', 'PARTIELLE', 'EN_RETARD']);
                })
                ->sum('balance'),
            'count_overdue' => (clone $statsQuery)
                ->where(function ($q) {
                    $q->where('due_date', '<', now()->format('Y-m-d'))
                      ->whereIn('status', ['IMPAYEE', 'PARTIELLE', 'EN_RETARD']);
                })
                ->count(),
            'total_paid' => (clone $statsQuery)->where('status', 'SOLDEE')->sum('total'),
            'count_paid' => (clone $statsQuery)->where('status', 'SOLDEE')->count(),
            'total_ca' => (clone $statsQuery)->sum('total'),
            'count_ca' => (clone $statsQuery)->count(),
        ];

        return view('comptabilite.factures.index', compact('invoices', 'alerts', 'stats'));
    }

    /**
     * Record a payment for an invoice
     * Protected by facture.payment permission
     * Returns updated invoice data for Alpine.js refresh
     */
    public function recordPayment(Request $request)
    {
        // Check permission
        if (!Auth::user()->can('facture.payment')) {
            return response()->json([
                'success' => false,
                'message' => 'Permission refusée.',
            ], 403);
        }

        // Validate request
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string|in:cash,transfer,mobile_money,check,card',
            'note' => 'nullable|string|max:255',
        ]);

        try {
            // Get the invoice
            $invoice = Invoice::findOrFail($validated['invoice_id']);

            // Check if invoice is cancelled
            if ($invoice->isCancelled()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible d\'enregistrer un paiement pour une facture annulée.',
                ], 422);
            }

            // Check if invoice is already paid
            if ($invoice->isPaid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette facture est déjà soldée.',
                ], 422);
            }

            // Check payment amount doesn't exceed balance
            if ($validated['amount'] > $invoice->balance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le montant du paiement dépasse le solde restant (' . number_format($invoice->balance, 2) . ' FCFA).',
                ], 422);
            }

            // Record payment using InvoiceService
            $payment = InvoiceService::recordPayment(
                $invoice,
                [
                    'amount' => $validated['amount'],
                    'payment_method' => $validated['payment_method'],
                    'payment_date' => $validated['payment_date'],
                    'note' => $validated['note'] ?? null,
                ],
                Auth::user()
            );

            // Refresh invoice with updated data
            $invoice->refresh();
            $invoice->load(['payments']);

            // Get the last created payment record
            $lastPayment = $invoice->payments()->latest()->first();

            return response()->json([
                'success' => true,
                'message' => 'Paiement enregistré avec succès.',
                'invoice' => [
                    'id' => $invoice->id,
                    'number' => $invoice->number,
                    'status' => $invoice->status,
                    'status_label' => $invoice->getStatusLabel(),
                    'status_badge_class' => $invoice->getStatusBadgeClass(),
                    'total' => $invoice->total,
                    'paid_amount' => $invoice->paid_amount,
                    'balance' => $invoice->balance,
                    'payments' => $invoice->payments->map(fn ($p) => [
                        'id' => $p->id,
                        'amount' => $p->amount,
                        'payment_method' => $p->payment_method,
                        'payment_date' => $p->payment_date?->format('d/m/Y'),
                        'note' => $p->note,
                    ]),
                ],
                'payment' => $lastPayment ? [
                    'id' => $lastPayment->id,
                    'amount' => $lastPayment->amount,
                    'payment_method' => $lastPayment->payment_method,
                    'payment_date' => $lastPayment->payment_date?->format('d/m/Y'),
                ] : null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement du paiement: ' . $e->getMessage(),
            ], 500);
        }
    }
}
