<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProduitController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->boolean('low_stock')) {
            $query->whereColumn('current_stock', '<=', 'alert_threshold');
        }

        $products = $query->orderBy('name')->paginate(20);

        return view('produits.index', compact('products'));
    }

    public function create()
    {
        return view('produits.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|in:carton,boite,paquet,piece',
            'current_stock' => 'required|numeric|min:0',
            'alert_threshold' => 'required|numeric|min:0',
        ]);

        Product::create($validated);

        return redirect()->route('produits.index')->with('success', 'Produit créé avec succès.');
    }
}
