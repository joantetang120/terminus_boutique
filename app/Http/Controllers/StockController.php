<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockEntryRequest;
use App\Http\Requests\StockExitRequest;
use App\Models\StockMovement;
use App\Models\Product;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{
    protected StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }
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

    public function entree(StockEntryRequest $request)
    {
        $validated = $request->validated();
        $product = Product::findOrFail($validated['product_id']);

        try {
            $this->stockService->recordEntry(
                $product,
                $validated['quantity'],
                $validated['note'] ?? '',
                Auth::user()
            );

            return back()->with('success', 'Entrée de stock enregistrée.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function sortie(StockExitRequest $request)
    {
        $validated = $request->validated();
        $product = Product::findOrFail($validated['product_id']);

        try {
            $this->stockService->recordExit(
                $product,
                $validated['quantity'],
                $validated['note'],
                Auth::user()
            );

            return back()->with('success', 'Sortie de stock enregistrée.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function annuler(Request $request, StockMovement $mouvement)
    {
        $validated = $request->validate([
            'cancel_reason' => 'required|string|min:10',
        ]);

        try {
            $this->stockService->cancelMovement(
                $mouvement,
                $validated['cancel_reason'],
                Auth::user()
            );

            return back()->with('success', 'Mouvement annulé avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
