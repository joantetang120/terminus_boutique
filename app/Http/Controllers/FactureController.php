<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\GhostInvoice;
use App\Models\GhostInvoiceItem;
use App\Models\StockMovement;
use App\Models\AccountingEntry;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class FactureController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['createdBy', 'cancelledBy']);

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('number', 'like', '%' . $request->search . '%')
                  ->orWhere('client_name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $invoices = $query->latest()->paginate(20);

        return view('factures.index', compact('invoices'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->get();
        return view('factures.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_phone' => 'nullable|string|max:50',
            'type' => 'required|in:comptant,credit,avance',
            'advance_amount' => 'nullable|numeric|min:0',
            'note' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.designation' => 'required|string',
            'items.*.unit' => 'required|in:carton,boite,paquet,piece',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.original_price' => 'nullable|numeric|min:0',
            'items.*.product_id' => 'nullable|exists:products,id',
        ]);

        $total = collect($validated['items'])->sum(fn($item) => $item['quantity'] * $item['unit_price']);
        $advanceAmount = $validated['type'] === 'avance' ? ($validated['advance_amount'] ?? 0) : 0;
        $balance = $validated['type'] === 'comptant' ? 0 : ($total - $advanceAmount);
        $status = $validated['type'] === 'comptant' ? 'payee' : ($validated['type'] === 'credit' ? 'credit' : 'avance');

        DB::transaction(function () use ($validated, $total, $advanceAmount, $balance, $status) {
            $invoice = Invoice::create([
                'number' => Invoice::generateNumber(),
                'type' => $validated['type'],
                'status' => $status,
                'client_name' => $validated['client_name'],
                'client_phone' => $validated['client_phone'] ?? null,
                'total' => $total,
                'advance_amount' => $advanceAmount,
                'balance' => $balance,
                'note' => $validated['note'] ?? null,
                'created_by' => Auth::id(),
            ]);

            foreach ($validated['items'] as $itemData) {
                $itemPrice = $itemData['unit_price'] * $itemData['quantity'];
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'designation' => $itemData['designation'],
                    'unit' => $itemData['unit'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'original_price' => $itemData['original_price'] ?? $itemData['unit_price'],
                    'total_price' => $itemPrice,
                ]);

                // Update stock
                if (isset($itemData['product_id'])) {
                    StockMovement::create([
                        'product_id' => $itemData['product_id'],
                        'type' => 'exit',
                        'quantity' => $itemData['quantity'],
                        'reference_type' => 'invoice',
                        'reference_id' => $invoice->id,
                        'created_by' => Auth::id(),
                    ]);

                    Product::where('id', $itemData['product_id'])
                        ->decrement('current_stock', $itemData['quantity']);
                }
            }

            // Ghost invoice
            $ghostInvoice = GhostInvoice::create([
                'real_invoice_id' => $invoice->id,
                'number' => $invoice->number,
                'type' => $invoice->type,
                'status' => $invoice->status,
                'client_name' => $invoice->client_name,
                'client_phone' => $invoice->client_phone,
                'total' => $invoice->total,
                'advance_amount' => $invoice->advance_amount,
                'balance' => $invoice->balance,
                'created_by' => Auth::id(),
            ]);

            foreach ($invoice->items as $item) {
                GhostInvoiceItem::create([
                    'ghost_invoice_id' => $ghostInvoice->id,
                    'designation' => $item->designation,
                    'unit' => $item->unit,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->total_price,
                ]);
            }

            // Accounting entry for comptant or advance
            if ($validated['type'] === 'comptant') {
                AccountingEntry::create([
                    'date' => now(),
                    'type' => 'recette',
                    'amount' => $total,
                    'reference_type' => 'invoice',
                    'reference_id' => $invoice->id,
                    'description' => 'Facture ' . $invoice->number . ' - ' . $invoice->client_name,
                    'status' => 'active',
                    'created_by' => Auth::id(),
                ]);
            } elseif ($validated['type'] === 'avance' && $advanceAmount > 0) {
                AccountingEntry::create([
                    'date' => now(),
                    'type' => 'recette',
                    'amount' => $advanceAmount,
                    'reference_type' => 'invoice',
                    'reference_id' => $invoice->id,
                    'description' => 'Avance facture ' . $invoice->number . ' - ' . $invoice->client_name,
                    'status' => 'active',
                    'created_by' => Auth::id(),
                ]);
            }
        });

        return redirect()->route('factures.index')->with('success', 'Facture créée avec succès.');
    }

    public function show(Invoice $facture)
    {
        $facture->load(['items', 'createdBy', 'cancelledBy', 'payments']);
        return view('factures.show', compact('facture'));
    }

    public function annuler(Request $request, Invoice $facture)
    {
        $this->authorize('facture.cancel');

        $validated = $request->validate([
            'cancel_reason' => 'required|string|min:10',
        ]);

        DB::transaction(function () use ($facture, $validated) {
            $facture->update([
                'status' => 'annulee',
                'cancelled_by' => Auth::id(),
                'cancelled_at' => now(),
                'cancel_reason' => $validated['cancel_reason'],
            ]);

            // Restore stock - match products by designation
            foreach ($facture->items as $item) {
                $product = Product::where('name', $item->designation)->first();
                
                StockMovement::create([
                    'product_id' => $product?->id,
                    'type' => 'cancel',
                    'quantity' => $item->quantity,
                    'reference_type' => 'invoice',
                    'reference_id' => $facture->id,
                    'note' => 'Annulation facture ' . $facture->number,
                    'created_by' => Auth::id(),
                ]);

                if ($product) {
                    Product::where('id', $product->id)
                        ->increment('current_stock', $item->quantity);
                }
            }
        });

        return redirect()->route('factures.index')->with('success', 'Facture annulée avec succès.');
    }
}
