<x-app-layout title="Stock">
    <div class="page-header">
        <div class="page-header-actions">
            <div>
                <h1>Stock</h1>
                <div class="breadcrumb">
                    <a href="{{ route('dashboard') }}">Accueil</a> > Inventaire > Stock
                </div>
            </div>
            @can('stock.create')
            <div style="display:flex;gap:8px;">
                <button class="btn btn-success btn-sm" x-data @click="$dispatch('open-entry-modal')">+ Entrée stock</button>
                <button class="btn btn-warning btn-sm" style="background:#E67E22;color:white;" x-data @click="$dispatch('open-exit-modal')">+ Sortie manuelle</button>
            </div>
            @endcan
        </div>
    </div>

    {{-- Stock Table --}}
    <div class="table-wrapper" style="margin-bottom:24px;">
        <table>
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Unité</th>
                    <th>Stock actuel</th>
                    <th>Seuil alerte</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
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
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Mouvements récents --}}
    <h2 style="font-size:1.125rem;font-weight:600;margin-bottom:16px;">Mouvements récents</h2>
    <div class="table-wrapper">
        <form method="GET" action="{{ route('stock.index') }}" class="table-toolbar">
            <select name="product_id" class="form-select" style="width:160px;">
                <option value="">Tous les produits</option>
                @foreach($products as $product)
                <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                @endforeach
            </select>
            <select name="type" class="form-select" style="width:160px;">
                <option value="">Type</option>
                <option value="entry" {{ request('type') === 'entry' ? 'selected' : '' }}>Entrée</option>
                <option value="exit" {{ request('type') === 'exit' ? 'selected' : '' }}>Sortie</option>
                <option value="cancel" {{ request('type') === 'cancel' ? 'selected' : '' }}>Annulation</option>
            </select>
            <input type="date" name="date" class="form-input" style="width:160px;" value="{{ request('date') }}">
            <button type="submit" class="btn btn-secondary btn-sm">Filtrer</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Date/Heure</th>
                    <th>Produit</th>
                    <th>Type</th>
                    <th>Quantité</th>
                    <th>Référence</th>
                    <th>Par</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movements as $movement)
                <tr>
                    <td>{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $movement->product->name ?? '—' }}</td>
                    <td>
                        @if($movement->type === 'entry')
                            <span class="badge badge-success">Entrée</span>
                        @elseif($movement->type === 'exit')
                            <span class="badge badge-warning">Sortie</span>
                        @elseif($movement->type === 'cancel')
                            <span class="badge badge-danger">Annulation</span>
                        @endif
                    </td>
                    <td>{{ number_format($movement->quantity, 2, ',', ' ') }}</td>
                    <td>{{ $movement->reference_type ? '#' . $movement->reference_id : '—' }}</td>
                    <td>{{ $movement->createdBy->name ?? '—' }}</td>
                    <td>
                        @can('stock.cancel')
                            @if($movement->type !== 'cancel' && $movement->reference_type !== 'invoice')
                            <button class="btn btn-danger btn-sm"
                                    x-data
                                    @click="$dispatch('open-cancel-movement', { id: {{ $movement->id }}, product: '{{ $movement->product->name ?? '' }}' })">
                                Annuler
                            </button>
                            @endif
                        @endcan
                    </td>
                </tr>
                @empty
                <tr class="table-empty">
                    <td colspan="7">Aucun mouvement.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div style="padding:16px;">
            {{ $movements->links() }}
        </div>
    </div>

    {{-- Entry Modal --}}
    <div x-data="{ open: false }"
         @open-entry-modal.window="open = true"
         x-show="open"
         x-cloak
         class="modal-backdrop"
         style="display:none;">
        <div class="modal" @click.away="open = false">
            <div class="modal-header">
                <h3>Entrée de stock</h3>
                <button @click="open = false" style="background:none;border:none;cursor:pointer;font-size:1.25rem;">&times;</button>
            </div>
            <form action="{{ route('stock.entree') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="entry_product">Produit</label>
                        <select class="form-select" id="entry_product" name="product_id" required>
                            <option value="">Sélectionner...</option>
                            @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="entry_qty">Quantité</label>
                        <input class="form-input" type="number" id="entry_qty" name="quantity" min="0.01" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="entry_note">Note (optionnel)</label>
                        <textarea class="form-textarea" id="entry_note" name="note" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="open = false">Annuler</button>
                    <button type="submit" class="btn btn-success">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Exit Modal --}}
    <div x-data="{ open: false }"
         @open-exit-modal.window="open = true"
         x-show="open"
         x-cloak
         class="modal-backdrop"
         style="display:none;">
        <div class="modal" @click.away="open = false">
            <div class="modal-header">
                <h3>Sortie manuelle de stock</h3>
                <button @click="open = false" style="background:none;border:none;cursor:pointer;font-size:1.25rem;">&times;</button>
            </div>
            <form action="{{ route('stock.sortie') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="exit_product">Produit</label>
                        <select class="form-select" id="exit_product" name="product_id" required>
                            <option value="">Sélectionner...</option>
                            @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->current_stock }} dispo)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="exit_qty">Quantité</label>
                        <input class="form-input" type="number" id="exit_qty" name="quantity" min="0.01" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="exit_note">Justification (obligatoire)</label>
                        <textarea class="form-textarea" id="exit_note" name="note" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="open = false">Annuler</button>
                    <button type="submit" class="btn btn-danger">Enregistrer la sortie</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Cancel Movement Modal --}}
    <div x-data="{ open: false, id: null, product: '' }"
         @open-cancel-movement.window="open = true; id = $event.detail.id; product = $event.detail.product"
         x-show="open"
         x-cloak
         class="modal-backdrop"
         style="display:none;">
        <div class="modal" @click.away="open = false">
            <div class="modal-header">
                <h3>⚠ Confirmer l'annulation</h3>
                <button @click="open = false" style="background:none;border:none;cursor:pointer;font-size:1.25rem;">&times;</button>
            </div>
            <form :action="'/stock/' + id + '/cancel'" method="POST">
                @csrf
                <div class="modal-body">
                    <p style="margin-bottom:16px;color:#64748B;">Annuler le mouvement pour <strong x-text="product"></strong> ?</p>
                    <div class="form-group">
                        <label class="form-label" for="cancel_reason">Motif (obligatoire)</label>
                        <textarea class="form-textarea" id="cancel_reason" name="cancel_reason" required rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="open = false">Annuler</button>
                    <button type="submit" class="btn btn-danger">Confirmer ⚠</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
