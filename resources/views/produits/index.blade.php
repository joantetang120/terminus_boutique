<x-app-layout title="Produits">
    <div class="page-header">
        <div class="page-header-actions">
            <div>
                <h1>Produits</h1>
                <div class="breadcrumb">
                    <a href="{{ route('dashboard') }}">Accueil</a> > Inventaire > Produits
                </div>
            </div>
            @can('product.create')
            <a href="{{ route('produits.create') }}" class="btn btn-primary">+ Nouveau produit</a>
            @endcan
        </div>
    </div>

    <div class="table-wrapper">
        <form method="GET" action="{{ route('produits.index') }}" class="table-toolbar">
            <input type="text" name="search" class="form-input" placeholder="Rechercher un produit..." value="{{ request('search') }}">
            <label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
                <input type="checkbox" name="low_stock" value="1" {{ request('low_stock') ? 'checked' : '' }}>
                Stock faible uniquement
            </label>
            <button type="submit" class="btn btn-secondary btn-sm">Filtrer</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Unité</th>
                    <th>Stock actuel</th>
                    <th>Seuil alerte</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td><strong>{{ $product->name }}</strong></td>
                    <td>{{ $product->unit }}</td>
                    <td>{{ number_format($product->current_stock, 2, ',', ' ') }}</td>
                    <td>{{ number_format($product->alert_threshold, 2, ',', ' ') }}</td>
                    <td>
                        @if($product->isLowStock())
                            <span class="badge badge-danger">Alerte</span>
                        @else
                            <span class="badge badge-success">OK</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('stock.index', ['product_id' => $product->id]) }}" class="btn btn-secondary btn-sm">Voir mouvements</a>
                    </td>
                </tr>
                @empty
                <tr class="table-empty">
                    <td colspan="6">Aucun produit.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div style="padding:16px;">
            {{ $products->links() }}
        </div>
    </div>
</x-app-layout>
