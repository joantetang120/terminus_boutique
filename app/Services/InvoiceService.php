<?php

namespace App\Services;

use App\Models\AccountingEntry;
use App\Models\GhostInvoice;
use App\Models\GhostInvoiceItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoicePayment;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    /**
     * Create a new invoice with items - complete atomic transaction
     *
     * Steps:
     * (1) Generate number via InvoiceNumberService
     * (2) Calculate due_date = Carbon::now()->addDays(10)
     * (3) Create invoice with status=IMPAYEE, paid_amount=0, balance=total
     * (4) Create invoice_items with designation/original_price snapshotted
     * (5) Call StockService::recordExit() for each line
     * (6) Copy to ghost_invoices + ghost_invoice_items
     * (7) Log to activity_log
     *
     * @param array $data
     * @param int $userId
     * @return Invoice
     * @throws \Exception
     */
    public static function create(array $data, int $userId): Invoice
    {
        return DB::transaction(function () use ($data, $userId) {
            $user = User::findOrFail($userId);

            // (1) Generate invoice number via InvoiceNumberService
            $invoiceNumber = InvoiceNumberService::generate();

            // (2) Calculate due_date
            $dueDate = Carbon::now()->addDays(10);

            // (3) Create invoice
            $invoice = Invoice::create([
                'number' => $invoiceNumber,
                'status' => 'IMPAYEE',
                'client_name' => $data['client_name'],
                'client_phone' => $data['client_phone'] ?? null,
                'total' => $data['total'],
                'paid_amount' => 0,
                'balance' => $data['total'],
                'due_date' => $dueDate,
                'created_by' => $userId,
            ]);

            $createdItems = [];

            // (4) & (5) Create invoice items and deduct stock
            if (!empty($data['items'])) {
                foreach ($data['items'] as $itemData) {
                    $item = self::createInvoiceItem($invoice, $itemData, $user);
                    $createdItems[] = $item;
                }
            }

            // (6) Copy to ghost tables (immutable backup)
            self::createGhostCopy($invoice, $createdItems);

            // (7) Log to activity_log
            activity('invoice')
                ->performedOn($invoice)
                ->causedBy($user)
                ->withProperties([
                    'invoice_number' => $invoiceNumber,
                    'client_name' => $data['client_name'],
                    'total' => $data['total'],
                    'item_count' => count($createdItems),
                    'due_date' => $dueDate->format('Y-m-d'),
                ])
                ->log('Facture créée: ' . $invoiceNumber . ' pour ' . $data['client_name'] . ' (' . number_format($data['total'], 2) . ' FCFA)');

            return $invoice->load('items');
        });
    }

    /**
     * Create an invoice item with stock deduction
     *
     * @param Invoice $invoice
     * @param array $itemData
     * @param User $user
     * @return InvoiceItem
     * @throws \Exception
     */
    private static function createInvoiceItem(Invoice $invoice, array $itemData, User $user): InvoiceItem
    {
        $product = Product::with('unitConversions')->find($itemData['product_id']);

        // Calculate quantity deducted from stock
        $quantitySold = $itemData['quantity_sold'];
        $unitSold = $itemData['unit_sold'];
        $quantityDeducted = $quantitySold;

        // (4) Calculate quantity_deducted based on unit sold
        if ($product && $unitSold) {
            // If selling in base unit, no conversion needed
            if ($unitSold === $product->unit) {
                $quantityDeducted = $quantitySold;
            }
            // If selling in purchase unit, apply conversion rate
            elseif ($unitSold === $product->purchase_unit && !empty($product->purchase_conversion_rate)) {
                $quantityDeducted = $quantitySold * $product->purchase_conversion_rate;
            }
            // If selling in sale unit, apply conversion rate
            elseif ($unitSold === $product->sale_unit && !empty($product->sale_conversion_rate)) {
                $quantityDeducted = $quantitySold * $product->sale_conversion_rate;
            }
            // Check unit_conversions table for other units
            else {
                $conversion = $product->unitConversions()
                    ->where('unit', $unitSold)
                    ->first();
                if ($conversion) {
                    $quantityDeducted = $quantitySold * $conversion->conversion_rate;
                }
            }
        }

        // Create invoice item with snapshotted values
        $item = InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'product_id' => $itemData['product_id'],
            'designation' => $itemData['designation'] ?? $product?->name ?? 'Produit inconnu',
            'unit_sold' => $unitSold,
            'quantity_sold' => $quantitySold,
            'quantity_deducted' => $quantityDeducted,
            'unit_price' => $itemData['unit_price'],
            'original_price' => $itemData['original_price'] ?? $itemData['unit_price'],
            'total_price' => $itemData['total_price'] ?? ($quantitySold * $itemData['unit_price']),
        ]);

        // (5) Record stock exit using StockService::recordExit()
        if ($product) {
            $stockService = new StockService();
            $stockService->recordExit(
                product: $product,
                quantity: (int) $quantityDeducted,
                note: 'Vente facture ' . $invoice->number,
                by: $user,
                refType: 'invoice',
                refId: $invoice->id,
                inputUnit: null,
                inputQuantity: null
            );
        }

        return $item;
    }

    /**
     * Create ghost copy of invoice and items (immutable backup)
     * Quantities and prices are divided by the creator's ghost_division_coefficient
     *
     * @param Invoice $invoice
     * @param array $items
     * @return GhostInvoice
     */
    private static function createGhostCopy(Invoice $invoice, array $items): GhostInvoice
    {
        // Get division coefficient from the creator (default to 2)
        $creator = User::find($invoice->created_by);
        $coefficient = $creator?->ghost_division_coefficient ?? 2.0;
        $coefficient = max(1.0, $coefficient); // Ensure minimum of 1

        // Create ghost invoice with divided amounts
        $ghostInvoice = GhostInvoice::create([
            'real_invoice_id' => $invoice->id,
            'number' => $invoice->number,
            'status' => $invoice->status,
            'client_name' => $invoice->client_name,
            'client_phone' => $invoice->client_phone,
            'total' => round($invoice->total / $coefficient, 2),
            'paid_amount' => round($invoice->paid_amount / $coefficient, 2),
            'balance' => round($invoice->balance / $coefficient, 2),
            'due_date' => $invoice->due_date,
            'cancelled_at' => $invoice->cancelled_at,
            'cancel_reason' => $invoice->cancel_reason,
            'created_by' => $invoice->created_by,
        ]);

        // Create ghost items with divided quantities and prices
        foreach ($items as $item) {
            GhostInvoiceItem::create([
                'ghost_invoice_id' => $ghostInvoice->id,
                'product_id' => $item->product_id,
                'designation' => $item->designation,
                'unit_sold' => $item->unit_sold,
                'quantity_sold' => round($item->quantity_sold / $coefficient, 2),
                'quantity_deducted' => round($item->quantity_deducted / $coefficient, 2),
                'unit_price' => round($item->unit_price / $coefficient, 2),
                'original_price' => round($item->original_price / $coefficient, 2),
                'total_price' => round($item->total_price / $coefficient, 2),
            ]);
        }

        return $ghostInvoice;
    }

    /**
     * Record a payment on an invoice - progressive payment tracking
     *
     * Steps:
     * (1) Verify invoice.status != ANNULEE
     * (2) Verify amount > 0 and amount <= invoice.balance
     * (3) Create invoice_payment record
     * (4) Update invoice: paid_amount += amount, balance -= amount
     * (5) Update status automatically: balance == 0 → SOLDEE, paid_amount > 0 → PARTIELLE
     * (6) Log to activity_log with old and new balance
     *
     * @param Invoice $invoice
     * @param array $data
     * @param User $by
     * @return Invoice
     * @throws \Exception
     */
    public static function recordPayment(Invoice $invoice, array $data, User $by): Invoice
    {
        return DB::transaction(function () use ($invoice, $data, $by) {
            // Reload invoice to get fresh data within transaction
            $invoice->refresh();

            // (1) Verify invoice is not cancelled
            if ($invoice->status === 'ANNULEE') {
                throw new \Exception('Impossible d\'enregistrer un paiement sur une facture annulée.');
            }

            $amount = (float) $data['amount'];

            // (2) Verify amount is valid
            if ($amount <= 0) {
                throw new \Exception('Le montant du paiement doit être supérieur à 0.');
            }

            if ($amount > $invoice->balance) {
                throw new \Exception(
                    'Montant dépasse le solde restant de ' . number_format($invoice->balance, 2) . ' FCFA'
                );
            }

            $oldBalance = $invoice->balance;
            $oldStatus = $invoice->status;

            // (3) Create payment record
            InvoicePayment::create([
                'invoice_id' => $invoice->id,
                'amount' => $amount,
                'payment_date' => $data['payment_date'] ?? now()->format('Y-m-d'),
                'payment_method' => $data['payment_method'] ?? null,
                'note' => $data['note'] ?? null,
                'created_by' => $by->id,
            ]);

            // (4) Update invoice amounts
            $newPaidAmount = $invoice->paid_amount + $amount;
            $newBalance = $invoice->balance - $amount;

            // (5) Determine new status
            $newStatus = $oldStatus;
            if ($newBalance == 0) {
                $newStatus = 'SOLDEE';
            } elseif ($newPaidAmount > 0) {
                $newStatus = 'PARTIELLE';
            }

            $invoice->update([
                'paid_amount' => $newPaidAmount,
                'balance' => $newBalance,
                'status' => $newStatus,
            ]);

            // (6) Create accounting entry (recette)
            AccountingEntry::create([
                'type' => 'recette',
                'amount' => $amount,
                'date' => $data['payment_date'] ?? now()->format('Y-m-d'),
                'reference_type' => 'invoice_payment',
                'reference_id' => $invoice->id,
                'description' => 'Paiement facture ' . $invoice->number . ' (' . ($data['payment_method'] ?? 'Espèces') . ')',
                'status' => 'active',
                'created_by' => $by->id,
            ]);

            // (7) Log to activity_log
            activity('invoice_payment')
                ->performedOn($invoice)
                ->causedBy($by)
                ->withProperties([
                    'invoice_number' => $invoice->number,
                    'payment_amount' => $amount,
                    'old_balance' => $oldBalance,
                    'new_balance' => $newBalance,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'payment_method' => $data['payment_method'] ?? null,
                ])
                ->log('Paiement enregistré: ' . number_format($amount, 2) . ' FCFA sur facture ' . $invoice->number . ' (Solde: ' . number_format($oldBalance, 2) . ' → ' . number_format($newBalance, 2) . ' FCFA)');

            return $invoice->fresh();
        });
    }

    /**
     * Cancel an invoice - admin only operation
     *
     * Steps:
     * (1) Verify permission facture.cancel
     * (2) Verify status != ANNULEE
     * (3) Update invoice: status ANNULEE, cancelled_by, cancelled_at, cancel_reason
     * (4) For each item, call StockService::recordEntry() with quantity_deducted to restore stock
     * (5) Preserve all payments in invoice_payments for accounting traceability
     * (6) Do not modify ghost tables
     * (7) Log to activity_log with reason
     *
     * @param Invoice $invoice
     * @param string $reason
     * @param User $by
     * @return Invoice
     * @throws \Exception
     */
    public static function cancel(Invoice $invoice, string $reason, User $by): Invoice
    {
        return DB::transaction(function () use ($invoice, $reason, $by) {
            // Reload invoice to get fresh data within transaction
            $invoice->refresh();

            // (1) Verify permission
            if (!$by->hasPermissionTo('facture.cancel')) {
                throw new \Exception('Vous n\'avez pas la permission d\'annuler des factures.');
            }

            // (2) Verify invoice is not already cancelled
            if ($invoice->status === 'ANNULEE') {
                throw new \Exception('Cette facture est déjà annulée.');
            }

            // (3) Update invoice status and cancellation info
            $invoice->update([
                'status' => 'ANNULEE',
                'cancelled_by' => $by->id,
                'cancelled_at' => now(),
                'cancel_reason' => $reason,
            ]);

            // (4) Restore stock for each item using quantity_deducted
            $items = $invoice->items;
            foreach ($items as $item) {
                $product = Product::find($item->product_id);
                if ($product && $item->quantity_deducted > 0) {
                    $stockService = new StockService();
                    $stockService->recordEntry(
                        product: $product,
                        quantity: (int) $item->quantity_deducted,
                        note: 'Restitution annulation facture ' . $invoice->number . ' - ' . $reason,
                        by: $by,
                        inputUnit: null,
                        inputQuantity: null
                    );
                }
            }

            // (5) Payments are preserved automatically - no action needed

            // (6) Ghost tables are not modified - no action needed

            // (7) Log to activity_log
            activity('invoice_cancel')
                ->performedOn($invoice)
                ->causedBy($by)
                ->withProperties([
                    'invoice_number' => $invoice->number,
                    'client_name' => $invoice->client_name,
                    'total' => $invoice->total,
                    'reason' => $reason,
                    'previous_status' => $invoice->getOriginal('status') ?? 'unknown',
                    'cancelled_at' => now()->format('Y-m-d H:i:s'),
                ])
                ->log('Facture annulée: ' . $invoice->number . ' pour ' . $invoice->client_name . ' (Motif: ' . $reason . ')');

            return $invoice->fresh();
        });
    }
}
