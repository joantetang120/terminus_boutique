<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProduitController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:product.view')->only(['index', 'show']);
        $this->middleware('can:product.create')->only(['create', 'store']);
        $this->middleware('can:product.edit')->only(['edit', 'update']);
    }

    public function index(Request $request)
    {
        $this->authorize('product.view');

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
        $this->authorize('product.view');

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
        $this->authorize('product.create');

        return view('produits.create');
    }

    public function store(StoreProductRequest $request)
    {
        $this->authorize('product.create');

        $validated = $request->validated();
        $validated['created_by'] = Auth::id();

        $product = Product::create($validated);

        // Create initial stock movement if stock > 0
        if ($validated['current_stock'] > 0) {
            $product->stockMovements()->create([
                'type' => 'entry',
                'quantity' => $validated['current_stock'],
                'note' => 'Stock initial',
                'created_by' => Auth::id(),
            ]);
        }

        activity('product')
            ->performedOn($product)
            ->log('Création du produit : ' . $product->name);

        return redirect()->route('produits.index')->with('success', 'Produit créé avec succès.');
    }

    public function edit(Product $produit)
    {
        $this->authorize('product.edit');

        return view('produits.edit', compact('produit'));
    }

    public function update(UpdateProductRequest $request, Product $produit)
    {
        $this->authorize('product.edit');

        $validated = $request->validated();

        $produit->update($validated);

        activity('product')
            ->performedOn($produit)
            ->log('Modification du produit : ' . $produit->name);

        return redirect()->route('produits.index')->with('success', 'Produit mis à jour avec succès.');
    }

    public function destroy(Product $produit)
    {
        $this->authorize('product.edit');

        $productName = $produit->name;
        $produit->delete();

        activity('product')
            ->performedOn($produit)
            ->log('Suppression du produit : ' . $productName);

        return redirect()->route('produits.index')->with('success', 'Produit supprimé avec succès.');
    }
}
