<?php

namespace App\Services;

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
     */
    public function recordEntry(
        Product $product, 
        int $quantity, 
        string $note, 
        User $by,
        ?string $inputUnit = null,
        ?int $inputQuantity = null
    ): StockMovement {
        return DB::transaction(function () use ($product, $quantity, $note, $by, $inputUnit, $inputQuantity) {
            // Convert to base unit if input unit is different
            $baseQuantity = $quantity;
            if ($inputUnit && $inputUnit !== $product->unit) {
                $baseQuantity = $product->convertToBaseUnit($quantity, $inputUnit);
            }

            $movement = StockMovement::create([
                'product_id' => $product->id,
                'type' => 'entry',
                'quantity' => $baseQuantity,
                'input_quantity' => $inputQuantity ?? $quantity,
                'input_unit' => $inputUnit ?? $product->unit,
                'note' => $note,
                'created_by' => $by->id,
            ]);

            $product->increment('current_stock', $baseQuantity);

            activity('stock')
                ->performedOn($product)
                ->withProperties([
                    'product_name' => $product->name,
                    'quantity' => $baseQuantity,
                    'input_quantity' => $inputQuantity ?? $quantity,
                    'input_unit' => $inputUnit ?? $product->unit,
                    'stock_after' => $product->current_stock + $baseQuantity,
                    'movement_id' => $movement->id,
                ])
                ->log('Entrée stock: +' . ($inputQuantity ?? $quantity) . ' ' . ($inputUnit ?? $product->unit) . ' = ' . $baseQuantity . ' ' . $product->unit . ' de ' . $product->name);

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

            $movement = StockMovement::create([
                'product_id' => $product->id,
                'type' => 'exit',
                'quantity' => $baseQuantity,
                'input_quantity' => $inputQuantity ?? $quantity,
                'input_unit' => $inputUnit ?? $product->unit,
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
