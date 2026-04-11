<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProduitController extends Controller
{
    protected StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function index(Request $request)
    {

        $query = Product::query();

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by active/inactive status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Filter low stock
        if ($request->boolean('low_stock')) {
            $query->lowStock();
        }

        $products = $query->with('creator')->orderBy('name')->paginate(20);

        activity('product')
            ->log('Consultation de la liste des produits');

        return view('produits.index', compact('products'));
    }

    public function show(Product $produit)
    {

        $produit->load(['creator', 'stockMovements' => function ($query) {
            $query->with('createdBy')->latest()->take(50);
        }]);

        activity('product')
            ->performedOn($produit)
            ->log('Consultation du produit : ' . $produit->name);

        return view('produits.show', compact('produit'));
    }

    public function create()
    {

        return view('produits.create');
    }

    public function store(StoreProductRequest $request)
    {

        $validated = $request->validated();
        $validated['created_by'] = Auth::id();

        $product = Product::create($validated);

        // Create initial stock movement if stock > 0 (without incrementing since product already has the stock)
        if ($validated['current_stock'] > 0) {
            \App\Models\StockMovement::create([
                'product_id' => $product->id,
                'type' => 'entry',
                'quantity' => $validated['current_stock'],
                'note' => 'Stock initial',
                'created_by' => Auth::id(),
            ]);

            activity('stock')
                ->performedOn($product)
                ->withProperties([
                    'product_name' => $product->name,
                    'initial_stock' => $validated['current_stock'],
                    'unit' => $product->unit,
                ])
                ->log('Stock initial: ' . $validated['current_stock'] . ' ' . $product->unit . ' de ' . $product->name);
        }

        activity('product')
            ->performedOn($product)
            ->withProperties([
                'name' => $product->name,
                'unit' => $product->unit,
                'alert_threshold' => $product->alert_threshold,
            ])
            ->log('Produit créé: ' . $product->name);

        return redirect()->route('produits.index')->with('success', 'Produit créé avec succès.');
    }

    public function edit(Product $produit)
    {

        return view('produits.edit', compact('produit'));
    }

    public function update(UpdateProductRequest $request, Product $produit)
    {

        $validated = $request->validated();

        $produit->update($validated);

        activity('product')
            ->performedOn($produit)
            ->withProperties([
                'name' => $produit->name,
                'changes' => $produit->getChanges(),
            ])
            ->log('Produit modifié: ' . $produit->name);

        return redirect()->route('produits.index')->with('success', 'Produit mis à jour avec succès.');
    }

    public function destroy(Product $produit)
    {
        $productName = $produit->name;

        // Check if product has stock movements
        if ($produit->stockMovements()->count() > 0) {
            return redirect()->route('produits.index')->with('error', 'Impossible de supprimer : le produit a des mouvements de stock associés.');
        }

        $produit->forceDelete();

        activity('product')
            ->withProperties(['name' => $productName])
            ->log('Produit supprimé: ' . $productName);

        return redirect()->route('produits.index')->with('success', 'Produit supprimé avec succès.');
    }
}
