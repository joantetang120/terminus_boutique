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
            <div style="display:flex;gap:6px;">
                <a href="{{ route('reports.export', array_merge(request()->query(), ['source' => 'produits', 'format' => 'pdf'])) }}" class="btn btn-secondary btn-sm">
                    PDF
                </a>
                <a href="{{ route('reports.export', array_merge(request()->query(), ['source' => 'produits', 'format' => 'docx'])) }}" class="btn btn-secondary btn-sm">
                    Word
                </a>
                <a href="{{ route('reports.export', array_merge(request()->query(), ['source' => 'produits', 'format' => 'csv'])) }}" class="btn btn-secondary btn-sm">
                    CSV
                </a>
            </div>
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
                    <td>
                        <strong>{{ $product->name }}</strong>
                        @if($product->hasConversion())
                            <br><small style="color:#64748B;font-size:0.7rem;">
                                @if($product->purchase_unit)
                                    achat: {{ $product->purchase_unit }} (1={{ $product->purchase_conversion_rate }})
                                @endif
                                @if($product->sale_unit)
                                    {{ $product->purchase_unit ? ' | ' : '' }}vente: {{ $product->sale_unit }} (1={{ $product->sale_conversion_rate }})
                                @endif
                            </small>
                        @endif
                    </td>
                    <td>{{ $product->unit }}</td>
                    <td>
                        {{ number_format($product->current_stock, 0, ',', ' ') }}
                        @if($product->sale_unit && $product->sale_conversion_rate)
                            <br><small style="color:#64748B;font-size:0.7rem;">
                                ({{ floor($product->current_stock / $product->sale_conversion_rate) }} {{ $product->sale_unit }})
                            </small>
                        @endif
                    </td>
                    <td>{{ number_format($product->alert_threshold, 0, ',', ' ') }}</td>
                    <td>
                        @if($product->isLowStock())
                            <span class="badge badge-danger">Alerte</span>
                        @else
                            <span class="badge badge-success">OK</span>
                        @endif
                    </td>
                    <td style="display:flex;gap:8px;">
                        <a href="{{ route('produits.show', $product) }}" class="btn btn-primary btn-sm">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            Détails
                        </a>
                        <a href="{{ route('stock.index', ['product_id' => $product->id]) }}" class="btn btn-secondary btn-sm">Mouvements</a>
                        @can('product.edit')
                        <a href="{{ route('produits.edit', $product) }}" class="btn btn-accent btn-sm">Modifier</a>
                        <button class="btn btn-danger btn-sm"
                                x-data
                                @click="$dispatch('open-delete-product', { id: {{ $product->id }}, name: '{{ $product->name }}' })">
                            Supprimer
                        </button>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr class="table-empty">
                    <td colspan="6">Aucun produit.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $products->links() }}
            <div class="pagination-info">
                Affichage {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} sur {{ $products->total() }} produits
            </div>
        </div>
    </div>

    {{-- Delete Product Modal --}}
    <div x-data="{ open: false, id: null, name: '' }"
         @open-delete-product.window="open = true; id = $event.detail.id; name = $event.detail.name"
         x-show="open"
         x-cloak
         class="modal-backdrop"
         style="display:none;">
        <div class="modal" @click.away="open = false">
            <div class="modal-header">
                <h3>⚠ Confirmer la suppression</h3>
                <button @click="open = false" style="background:none;border:none;cursor:pointer;font-size:1.25rem;">&times;</button>
            </div>
            <form :action="'/products/' + id" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p style="margin-bottom:16px;color:#64748B;">
                        Êtes-vous sûr de vouloir supprimer <strong x-text="name"></strong> ?
                    </p>
                    <p style="color:#C0392B;font-size:0.875rem;">
                        Cette action est irréversible.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="open = false">Annuler</button>
                    <button type="submit" class="btn btn-danger">Confirmer la suppression</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
