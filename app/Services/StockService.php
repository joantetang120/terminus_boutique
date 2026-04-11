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
     */
    public function recordEntry(Product $product, int $quantity, string $note, User $by): StockMovement
    {
        return DB::transaction(function () use ($product, $quantity, $note, $by) {
            $movement = StockMovement::create([
                'product_id' => $product->id,
                'type' => 'entry',
                'quantity' => $quantity,
                'note' => $note,
                'created_by' => $by->id,
            ]);

            $product->increment('current_stock', $quantity);

            activity('stock')
                ->performedOn($movement)
                ->withProperties(['quantity' => $quantity, 'product_id' => $product->id])
                ->log('Entrée de stock enregistrée');

            return $movement;
        });
    }

    /**
     * Record a stock exit (outgoing)
     */
    public function recordExit(
        Product $product,
        int $quantity,
        string $note,
        User $by,
        ?string $refType = null,
        ?int $refId = null
    ): StockMovement {
        return DB::transaction(function () use ($product, $quantity, $note, $by, $refType, $refId) {
            if ($product->current_stock < $quantity) {
                throw new \Exception('Stock insuffisant pour ce produit.');
            }

            $movement = StockMovement::create([
                'product_id' => $product->id,
                'type' => 'exit',
                'quantity' => $quantity,
                'reference_type' => $refType,
                'reference_id' => $refId,
                'note' => $note,
                'created_by' => $by->id,
            ]);

            $product->decrement('current_stock', $quantity);

            activity('stock')
                ->performedOn($movement)
                ->withProperties(['quantity' => $quantity, 'product_id' => $product->id])
                ->log('Sortie de stock enregistrée');

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
                ->performedOn($movement)
                ->withProperties([
                    'cancel_movement_id' => $cancelMovement->id,
                    'reason' => $reason,
                    'quantity' => $movement->quantity,
                ])
                ->log('Mouvement de stock annulé');

            return $cancelMovement;
        });
    }
}
