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
                    <th>Coût (FCFA)</th>
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
                    <td>
                        @if($movement->product)
                            @if($movement->input_unit && $movement->input_unit !== $movement->product->unit)
                                <span title="Converti: {{ $movement->quantity }} {{ $movement->product->unit }}">
                                    {{ number_format($movement->input_quantity, 0, ',', ' ') }} {{ $movement->input_unit }}
                                    <small style="color:#64748B;">({{ number_format($movement->quantity, 0, ',', ' ') }} {{ $movement->product->unit }})</small>
                                </span>
                            @else
                                {{ number_format($movement->quantity, 0, ',', ' ') }} {{ $movement->product->unit }}
                            @endif
                        @else
                            {{ number_format($movement->quantity, 0, ',', ' ') }} (unité inconnue)
                        @endif
                    </td>
                    <td>
                        @if($movement->type === 'entry' && $movement->total_cost > 0)
                            {{ number_format($movement->total_cost, 0, ',', ' ') }} FCFA
                            @if($movement->unit_cost > 0)
                                <small style="color:#64748B;">({{ number_format($movement->unit_cost, 0, ',', ' ') }}/{{ $movement->product->unit ?? 'unit' }})</small>
                            @endif
                        @else
                            —
                        @endif
                    </td>
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
                    <td colspan="8">Aucun mouvement.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $movements->links() }}
            <div class="pagination-info">
                Affichage {{ $movements->firstItem() ?? 0 }} - {{ $movements->lastItem() ?? 0 }} sur {{ $movements->total() }} mouvements
            </div>
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
                        <select class="form-select" id="entry_product" name="product_id" required onchange="updateEntryUnitOptions(); updateEntryPurchasePriceHint({{ $product->id }});">
                            <option value="">Sélectionner...</option>
                            @foreach($products as $product)
                            <option value="{{ $product->id }}" data-unit="{{ $product->unit }}" data-purchase-unit="{{ $product->purchase_unit }}" data-purchase-rate="{{ $product->purchase_conversion_rate }}" data-purchase-price="{{ $product->purchase_price }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="display:grid;grid-template-columns:2fr 1fr;gap:12px;">
                        <div class="form-group">
                            <label class="form-label" for="entry_qty">Quantité</label>
                            <input class="form-input" type="number" id="entry_qty" name="quantity" min="1" step="1" required onchange="calculateEntryCost()">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="entry_unit">Unité</label>
                            <select class="form-select" id="entry_unit" name="input_unit" required>
                                <option value="">--</option>
                            </select>
                        </div>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div class="form-group">
                            <label class="form-label" for="entry_unit_cost">Prix d'achat unitaire (FCFA)</label>
                            <input class="form-input" type="number" id="entry_unit_cost" name="unit_cost" min="0" step="0.01" onchange="calculateEntryCost()">
                            <small class="field-note" id="entry_purchase_price_hint" style="color:#64748B;">Prix par défaut: --</small>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="entry_total_cost">Coût total (FCFA)</label>
                            <input class="form-input" type="number" id="entry_total_cost" name="total_cost" min="0" step="0.01" readonly>
                        </div>
                    </div>
                    <div id="entry_conversion_info" style="display:none;padding:8px 12px;background:#F0FDF4;border:1px solid #86EFAC;border-radius:6px;margin-bottom:12px;font-size:0.75rem;color:#166534;">
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
                        <select class="form-select" id="exit_product" name="product_id" required onchange="updateExitUnitOptions()">
                            <option value="">Sélectionner...</option>
                            @foreach($products as $product)
                            @php
                                $saleConversions = $product->saleConversions->map(fn($c) => ['unit' => $c->unit, 'rate' => $c->conversion_rate])->toArray();
                                // Include legacy sale unit if exists and not already in conversions
                                if ($product->sale_unit && !collect($saleConversions)->first(fn($c) => $c['unit'] === $product->sale_unit)) {
                                    $saleConversions[] = ['unit' => $product->sale_unit, 'rate' => $product->sale_conversion_rate];
                                }
                            @endphp
                            <option value="{{ $product->id }}"
                                    data-unit="{{ $product->unit }}"
                                    data-current="{{ $product->current_stock }}"
                                    data-sale-unit="{{ $product->sale_unit }}"
                                    data-sale-rate="{{ $product->sale_conversion_rate }}"
                                    data-conversions='@json($saleConversions)'>
                                {{ $product->name }} ({{ $product->current_stock }} {{ $product->unit }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div style="display:grid;grid-template-columns:2fr 1fr;gap:12px;">
                        <div class="form-group">
                            <label class="form-label" for="exit_qty">Quantité</label>
                            <input class="form-input" type="number" id="exit_qty" name="quantity" min="1" step="1" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="exit_unit">Unité</label>
                            <select class="form-select" id="exit_unit" name="input_unit" required>
                                <option value="">--</option>
                            </select>
                        </div>
                    </div>
                    <div id="exit_stock_info" style="padding:8px 12px;background:#F1F5F9;border-radius:6px;margin-bottom:12px;font-size:0.75rem;color:#475569;">
                        Sélectionnez un produit
                    </div>
                    <div id="exit_conversion_info" style="display:none;padding:8px 12px;background:#FEF3C7;border:1px solid #FCD34D;border-radius:6px;margin-bottom:12px;font-size:0.75rem;color:#92400E;">
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
<script>
function updateEntryUnitOptions() {
    const productSelect = document.getElementById('entry_product');
    const unitSelect = document.getElementById('entry_unit');
    const conversionInfo = document.getElementById('entry_conversion_info');
    
    const selectedOption = productSelect.options[productSelect.selectedIndex];
    
    if (!selectedOption.value) {
        unitSelect.innerHTML = '<option value="">--</option>';
        conversionInfo.style.display = 'none';
        return;
    }
    
    const baseUnit = selectedOption.dataset.unit;
    const purchaseUnit = selectedOption.dataset.purchaseUnit;
    const purchaseRate = selectedOption.dataset.purchaseRate;
    
    let options = `<option value="${baseUnit}" selected>${baseUnit}</option>`;
    
    if (purchaseUnit && purchaseRate) {
        options += `<option value="${purchaseUnit}">${purchaseUnit}</option>`;
    }
    
    unitSelect.innerHTML = options;
    
    if (purchaseUnit && purchaseRate) {
        conversionInfo.innerHTML = `Conversion: 1 ${purchaseUnit} = ${purchaseRate} ${baseUnit}`;
        conversionInfo.style.display = 'block';
    } else {
        conversionInfo.style.display = 'none';
    }
}

function calculateEntryCost() {
    const qty = parseFloat(document.getElementById('entry_qty').value) || 0;
    const unitCost = parseFloat(document.getElementById('entry_unit_cost').value) || 0;
    document.getElementById('entry_total_cost').value = (qty * unitCost).toFixed(2);
}

function updateEntryPurchasePriceHint(productId) {
    const select = document.getElementById('entry_product');
    const selected = select.options[select.selectedIndex];
    const purchasePrice = selected?.dataset?.purchasePrice || '0';
    document.getElementById('entry_unit_cost').value = purchasePrice;
    const hint = document.getElementById('entry_purchase_price_hint');
    if (hint) {
        hint.textContent = 'Prix par défaut: ' + parseFloat(purchasePrice).toLocaleString('fr-FR') + ' FCFA';
    }
    calculateEntryCost();
}

function updateExitUnitOptions() {
    const productSelect = document.getElementById('exit_product');
    const unitSelect = document.getElementById('exit_unit');
    const stockInfo = document.getElementById('exit_stock_info');
    const conversionInfo = document.getElementById('exit_conversion_info');

    const selectedOption = productSelect.options[productSelect.selectedIndex];

    if (!selectedOption.value) {
        unitSelect.innerHTML = '<option value="">--</option>';
        stockInfo.innerHTML = 'Sélectionnez un produit';
        conversionInfo.style.display = 'none';
        return;
    }

    const baseUnit = selectedOption.dataset.unit;
    const currentStock = parseInt(selectedOption.dataset.current) || 0;

    // Parse all sale conversions
    let conversions = [];
    try {
        conversions = JSON.parse(selectedOption.dataset.conversions || '[]');
    } catch (e) {
        conversions = [];
    }

    // Add legacy sale unit if exists and not in conversions
    const legacyUnit = selectedOption.dataset.saleUnit;
    const legacyRate = parseInt(selectedOption.dataset.saleRate) || 0;
    if (legacyUnit && legacyRate && !conversions.find(c => c.unit === legacyUnit)) {
        conversions.push({ unit: legacyUnit, rate: legacyRate });
    }

    // Build unit options
    let options = `<option value="${baseUnit}" selected>${baseUnit}</option>`;
    conversions.forEach(conv => {
        if (conv.unit && conv.rate) {
            options += `<option value="${conv.unit}">${conv.unit}</option>`;
        }
    });
    unitSelect.innerHTML = options;

    // Calculate stock display with all conversions
    let stockDisplay = `Stock: ${currentStock} ${baseUnit}`;
    conversions.forEach(conv => {
        if (conv.unit && conv.rate) {
            const stockInUnit = Math.floor(currentStock / conv.rate);
            stockDisplay += ` (${stockInUnit} ${conv.unit})`;
        }
    });
    stockInfo.innerHTML = stockDisplay;

    // Show conversion info for first conversion
    if (conversions.length > 0 && conversions[0].unit && conversions[0].rate) {
        let convInfo = 'Conversions:<br>';
        conversions.forEach(conv => {
            if (conv.unit && conv.rate) {
                convInfo += `1 ${conv.unit} = ${conv.rate} ${baseUnit}<br>`;
            }
        });
        conversionInfo.innerHTML = convInfo;
        conversionInfo.style.display = 'block';
    } else {
        conversionInfo.style.display = 'none';
    }
}
</script>
</x-app-layout>
