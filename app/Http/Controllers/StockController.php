<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::where('is_active', true)
            ->orderBy('name')
            ->get();

        $movementsQuery = StockMovement::with(['product', 'createdBy', 'cancelledBy']);

        if ($request->filled('product_id')) {
            $movementsQuery->where('product_id', $request->product_id);
        }

        if ($request->filled('type')) {
            $movementsQuery->where('type', $request->type);
        }

        if ($request->filled('date')) {
            $movementsQuery->whereDate('created_at', $request->date);
        }

        $movements = $movementsQuery->latest()->paginate(20);

        return view('stock.index', compact('products', 'movements'));
    }

    public function entree(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.01',
            'note' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            StockMovement::create([
                'product_id' => $validated['product_id'],
                'type' => 'entry',
                'quantity' => $validated['quantity'],
                'note' => $validated['note'] ?? null,
                'created_by' => Auth::id(),
            ]);

            Product::where('id', $validated['product_id'])
                ->increment('current_stock', $validated['quantity']);
        });

        return back()->with('success', 'Entrée de stock enregistrée.');
    }

    public function sortie(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.01',
            'note' => 'required|string|min:5',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if ($product->current_stock < $validated['quantity']) {
            return back()->with('error', 'Stock insuffisant pour ce produit.');
        }

        DB::transaction(function () use ($validated) {
            StockMovement::create([
                'product_id' => $validated['product_id'],
                'type' => 'exit',
                'quantity' => $validated['quantity'],
                'note' => $validated['note'],
                'created_by' => Auth::id(),
            ]);

            Product::where('id', $validated['product_id'])
                ->decrement('current_stock', $validated['quantity']);
        });

        return back()->with('success', 'Sortie de stock enregistrée.');
    }

    public function annuler(Request $request, StockMovement $mouvement)
    {
        $this->authorize('stock.cancel');

        if ($mouvement->type === 'cancel') {
            return back()->with('error', 'Ce mouvement est déjà une annulation.');
        }

        if ($mouvement->reference_type === 'invoice') {
            return back()->with('error', 'Impossible d\'annuler un mouvement lié à une facture. Annulez la facture concernée.');
        }

        $validated = $request->validate([
            'cancel_reason' => 'required|string|min:10',
        ]);

        DB::transaction(function () use ($mouvement, $validated) {
            // Counter-entry
            StockMovement::create([
                'product_id' => $mouvement->product_id,
                'type' => 'cancel',
                'quantity' => $mouvement->quantity,
                'reference_type' => $mouvement->reference_type,
                'reference_id' => $mouvement->reference_id,
                'note' => 'Annulation: ' . $validated['cancel_reason'],
                'created_by' => Auth::id(),
            ]);

            // Reverse the stock
            if ($mouvement->type === 'entry') {
                Product::where('id', $mouvement->product_id)
                    ->decrement('current_stock', $mouvement->quantity);
            } else {
                Product::where('id', $mouvement->product_id)
                    ->increment('current_stock', $mouvement->quantity);
            }
        });

        return back()->with('success', 'Mouvement annulé avec succès.');
    }
}
