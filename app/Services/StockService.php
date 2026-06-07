<?php

namespace App\Services;

use App\Models\AccountingEntry;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Record a stock entry (incoming)
     * @param string|null $inputUnit The unit entered by user (null = base unit)
     * @param int|null $inputQuantity The quantity entered by user (null = use $quantity)
     * @param float|null $unitCost The purchase price per unit
     * @param float|null $totalCost The total cost of the entry
     */
    public function recordEntry(
        Product $product, 
        int $quantity, 
        string $note, 
        User $by,
        ?string $inputUnit = null,
        ?int $inputQuantity = null,
        ?float $unitCost = null,
        ?float $totalCost = null
    ): StockMovement {
        return DB::transaction(function () use ($product, $quantity, $note, $by, $inputUnit, $inputQuantity, $unitCost, $totalCost) {
            // Convert to base unit if input unit is different
            $baseQuantity = $quantity;
            if ($inputUnit && $inputUnit !== $product->unit) {
                $baseQuantity = $product->convertToBaseUnit($quantity, $inputUnit);
            }

            // Calculate total cost if not provided
            if ($totalCost === null && $unitCost !== null) {
                $totalCost = $unitCost * $quantity;
            }

            $movement = StockMovement::create([
                'product_id' => $product->id,
                'type' => 'entry',
                'quantity' => $baseQuantity,
                'input_quantity' => $inputQuantity ?? $quantity,
                'input_unit' => $inputUnit ?? $product->unit,
                'unit_cost' => $unitCost,
                'total_cost' => $totalCost,
                'note' => $note,
                'created_by' => $by->id,
            ]);

            $product->increment('current_stock', $baseQuantity);

            // Create accounting entry for expense if there's a cost
            if ($totalCost && $totalCost > 0) {
                AccountingEntry::create([
                    'date' => now(),
                    'type' => 'depense',
                    'amount' => $totalCost,
                    'reference_type' => StockMovement::class,
                    'reference_id' => $movement->id,
                    'description' => 'Achat stock: ' . ($inputQuantity ?? $quantity) . ' ' . ($inputUnit ?? $product->unit) . ' de ' . $product->name . ' @ ' . number_format($unitCost ?? 0, 0) . ' FCFA/' . ($inputUnit ?? $product->unit),
                    'status' => 'active',
                    'created_by' => $by->id,
                ]);
            }

            activity('stock')
                ->performedOn($product)
                ->withProperties([
                    'product_name' => $product->name,
                    'quantity' => $baseQuantity,
                    'input_quantity' => $inputQuantity ?? $quantity,
                    'input_unit' => $inputUnit ?? $product->unit,
                    'unit_cost' => $unitCost,
                    'total_cost' => $totalCost,
                    'stock_after' => $product->current_stock + $baseQuantity,
                    'movement_id' => $movement->id,
                ])
                ->log('Entrée stock: +' . ($inputQuantity ?? $quantity) . ' ' . ($inputUnit ?? $product->unit) . ' = ' . $baseQuantity . ' ' . $product->unit . ' de ' . $product->name . ($unitCost ? ' (Coût: ' . number_format($totalCost, 2) . ' FCFA)' : ''));

            return $movement;
        });
    }

    /**
     * Record a stock exit (outgoing)
     * @param string|null $inputUnit The unit entered by user (null = base unit)
     * @param int|null $inputQuantity The quantity entered by user (null = use $quantity)
     */
    public function recordExit(
        Product $product,
        int $quantity,
        string $note,
        User $by,
        ?string $refType = null,
        ?int $refId = null,
        ?string $inputUnit = null,
        ?int $inputQuantity = null
    ): StockMovement {
        return DB::transaction(function () use ($product, $quantity, $note, $by, $refType, $refId, $inputUnit, $inputQuantity) {
            // Convert to base unit if input unit is different
            $baseQuantity = $quantity;
            if ($inputUnit && $inputUnit !== $product->unit) {
                $baseQuantity = $product->convertToBaseUnit($quantity, $inputUnit);
            }

            if ($product->current_stock < $baseQuantity) {
                $availableInInputUnit = $inputUnit ? $product->convertFromBaseUnit($product->current_stock, $inputUnit) : $product->current_stock;
                throw new \Exception('Stock insuffisant. Disponible: ' . $availableInInputUnit . ' ' . ($inputUnit ?? $product->unit));
            }

            // Calculate total cost for exit (COGS)
            $purchasePrice = $product->purchase_price ?? 0;
            if ($product->purchase_unit && $product->purchase_conversion_rate && $product->purchase_unit !== $product->unit) {
                $costPerBaseUnit = $purchasePrice / $product->purchase_conversion_rate;
            } else {
                $costPerBaseUnit = $purchasePrice;
            }
            $totalCost = $costPerBaseUnit * $baseQuantity;

            $movement = StockMovement::create([
                'product_id' => $product->id,
                'type' => 'exit',
                'quantity' => $baseQuantity,
                'input_quantity' => $inputQuantity ?? $quantity,
                'input_unit' => $inputUnit ?? $product->unit,
                'unit_cost' => $costPerBaseUnit,
                'total_cost' => $totalCost,
                'reference_type' => $refType,
                'reference_id' => $refId,
                'note' => $note,
                'created_by' => $by->id,
            ]);

            $product->decrement('current_stock', $baseQuantity);

            $stockAfter = $product->current_stock - $baseQuantity;

            activity('stock')
                ->performedOn($product)
                ->withProperties([
                    'product_name' => $product->name,
                    'quantity' => $baseQuantity,
                    'input_quantity' => $inputQuantity ?? $quantity,
                    'input_unit' => $inputUnit ?? $product->unit,
                    'stock_after' => $stockAfter,
                    'movement_id' => $movement->id,
                    'reference_type' => $refType,
                    'reference_id' => $refId,
                ])
                ->log('Sortie stock: -' . ($inputQuantity ?? $quantity) . ' ' . ($inputUnit ?? $product->unit) . ' = ' . $baseQuantity . ' ' . $product->unit . ' de ' . $product->name);

            return $movement;
        });
    }

    /**
     * Cancel a stock movement
     */
    public function cancelMovement(StockMovement $movement, string $reason, User $by): StockMovement
    {
        return DB::transaction(function () use ($movement, $reason, $by) {
            if ($movement->type === 'cancel') {
                throw new \Exception('Ce mouvement est déjà une annulation.');
            }

            if ($movement->is_cancelled) {
                throw new \Exception('Ce mouvement a déjà été annulé.');
            }

            if ($movement->reference_type === 'invoice') {
                throw new \Exception('Impossible d\'annuler un mouvement lié à une facture.');
            }

            // Mark original movement as cancelled
            $movement->update([
                'cancelled_by' => $by->id,
                'cancelled_at' => now(),
                'cancel_reason' => $reason,
            ]);

            // Create counter-entry
            $cancelMovement = StockMovement::create([
                'product_id' => $movement->product_id,
                'type' => 'cancel',
                'quantity' => $movement->quantity,
                'reference_type' => 'stock_movement',
                'reference_id' => $movement->id,
                'unit_cost' => 0,
                'total_cost' => 0,
                'note' => 'Annulation du mouvement #' . $movement->id . ': ' . $reason,
                'created_by' => $by->id,
            ]);

            // Reverse the stock
            $product = $movement->product;
            if ($movement->type === 'entry') {
                $product->decrement('current_stock', $movement->quantity);
            } else {
                $product->increment('current_stock', $movement->quantity);
            }

            activity('stock')
                ->performedOn($product)
                ->withProperties([
                    'product_name' => $product->name,
                    'cancel_movement_id' => $cancelMovement->id,
                    'original_movement_id' => $movement->id,
                    'original_type' => $movement->type,
                    'reason' => $reason,
                    'quantity' => $movement->quantity,
                    'stock_after' => $product->current_stock,
                ])
                ->log('Annulation: ' . $movement->type . ' de ' . $movement->quantity . ' ' . $product->name . ' - ' . $reason);

            return $cancelMovement;
        });
    }
}
