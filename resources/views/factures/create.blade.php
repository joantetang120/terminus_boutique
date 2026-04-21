<x-app-layout title="Nouvelle facture">
    <div class="page-header">
        <h1>Nouvelle facture</h1>
        <div class="breadcrumb">
            <a href="{{ route('dashboard') }}">Accueil</a> > <a href="{{ route('factures.index') }}">Factures</a> > Nouvelle
        </div>
    </div>

    <form action="{{ route('factures.store') }}" method="POST" x-data="invoiceForm()">
        @csrf
        <div style="display:grid;grid-template-columns:60% 40%;gap:24px;">
            {{-- LEFT --}}
            <div>
                <div class="card" style="margin-bottom:24px;">
                    <h3 style="font-size:1rem;font-weight:600;margin-bottom:16px;">Client</h3>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                        <div class="form-group">
                            <label class="form-label" for="client_name">Nom du client <span style="color:#C0392B;">*</span></label>
                            <input class="form-input" type="text" id="client_name" name="client_name" value="{{ old('client_name') }}" required>
                            @error('client_name')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="client_phone">Téléphone (optionnel)</label>
                            <input class="form-input" type="text" id="client_phone" name="client_phone" value="{{ old('client_phone') }}">
                        </div>
                    </div>

                    {{-- Due date display (read-only, auto-calculated) --}}
                    @php
                        $dueDate = now()->addDays(10)->format('d/m/Y');
                    @endphp
                    <div style="margin-top:16px; padding:12px 16px; background:#E8F6F3; border-left:4px solid #1ABC9C; border-radius:4px;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1ABC9C" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            <span style="font-size:0.875rem;color:#16A085;">
                                <strong>Échéance automatique dans 10 jours — {{ $dueDate }}</strong>
                            </span>
                        </div>
                        <p style="margin:4px 0 0 24px;font-size:0.75rem;color:#5DADE2;">
                            La date d'échéance est calculée automatiquement. Aucun champ de saisie requis.
                        </p>
                    </div>
                </div>

                <div class="card">
                    <h3 style="font-size:1rem;font-weight:600;margin-bottom:16px;">Articles</h3>

                    <template x-for="(item, index) in items" :key="index">
                        <div style="margin-bottom:16px; padding:16px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px;">
                            {{-- Row 1: Product selector and delete --}}
                            <div style="display:grid;grid-template-columns:1fr auto;gap:12px;margin-bottom:12px;align-items:end;">
                                <div class="form-group" style="margin:0;">
                                    <label class="form-label" style="font-size:0.75rem;">Produit <span style="color:#C0392B;">*</span></label>
                                    <select class="form-select" x-model="item.product_id" @change="selectProduct(index, item.product_id)" style="padding:8px 10px;font-size:0.8125rem;">
                                        <option value="">-- Choisir un produit --</option>
                                        <template x-for="product in productsData" :key="product.id">
                                            <option
                                                :value="product.id"
                                                :style="product.current_stock <= product.alert_threshold ? 'background:#fff5f5;color:#c53030;' : ''"
                                                x-text="product.name + ' (Stock: ' + product.current_stock + ' ' + product.unit + (product.current_stock <= product.alert_threshold ? ' ⚠️ STOCK BAS' : '') + ')'"
                                            ></option>
                                        </template>
                                    </select>
                                    <input type="hidden" :name="'items[' + index + '][product_id]'" x-model="item.product_id">
                                    <input type="hidden" :name="'items[' + index + '][designation]'" x-model="item.designation">
                                </div>
                                <button type="button" @click="removeItem(index)" style="background:#fed7d7;border:none;cursor:pointer;color:#c53030;font-size:1.25rem;padding:8px 12px;border-radius:6px;" title="Supprimer la ligne">×</button>
                            </div>

                            {{-- Product info display when selected --}}
                            <div x-show="item.productData" style="margin-bottom:12px;padding:10px 12px;background:#e6fffa;border-radius:4px;border-left:3px solid #38b2ac;">
                                <div style="display:flex;justify-content:space-between;align-items:center;font-size:0.8125rem;">
                                    <span style="color:#234e52;font-weight:500;" x-text="item.designation"></span>
                                    <span style="color:#2d6a4f;">
                                        Stock disponible: <strong x-text="item.productData?.current_stock"></strong> <span x-text="item.productData?.unit"></span>
                                    </span>
                                </div>
                            </div>

                            {{-- Row 2: Unit, Quantity, Price --}}
                            <div style="display:grid;grid-template-columns:1fr 100px 120px;gap:12px;margin-bottom:12px;align-items:end;">
                                <div class="form-group" style="margin:0;">
                                    <label class="form-label" style="font-size:0.75rem;">Unité de vente</label>
                                    <select class="form-select" :name="'items[' + index + '][unit_sold]'" x-model="item.unit_sold" @change="updateUnitPrice(index)" style="padding:8px 10px;font-size:0.8125rem;">
                                        <option value="">Choisir...</option>
                                        <template x-if="item.productData">
                                            <template x-for="(label, unitKey) in item.productData.available_units" :key="unitKey">
                                                <option :value="unitKey" x-text="label"></option>
                                            </template>
                                        </template>
                                    </select>
                                </div>
                                <div class="form-group" style="margin:0;">
                                    <label class="form-label" style="font-size:0.75rem;">Qté</label>
                                    <input class="form-input" type="number" :name="'items[' + index + '][quantity_sold]'" x-model.number="item.quantity_sold" @input="calculateDeduction(index)" min="0.01" step="0.01" required style="padding:8px 10px;font-size:0.8125rem;">
                                </div>
                                <div class="form-group" style="margin:0;">
                                    <label class="form-label" style="font-size:0.75rem;">Prix unit.</label>
                                    <input class="form-input" type="number" :name="'items[' + index + '][unit_price]'" x-model.number="item.unit_price" min="0" step="1" required style="padding:8px 10px;font-size:0.8125rem;">
                                </div>
                            </div>

                            {{-- Row 3: Stock deduction info and line total --}}
                            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
                                {{-- Stock deduction info --}}
                                <div x-show="item.quantity_deducted > 0 && item.unit_sold" style="padding:6px 12px;background:#ebf8ff;border-radius:4px;font-size:0.75rem;color:#2b6cb0;border-left:3px solid #4299e1;">
                                    <span x-text="item.quantity_sold + ' ' + item.unit_sold"></span>
                                    →
                                    <strong x-text="item.quantity_deducted + ' ' + (item.productData?.unit || 'pièces') + ' déduites du stock'"></strong>
                                </div>
                                <div x-show="!item.unit_sold || item.quantity_sold <= 0" style="padding:6px 12px;background:#f7fafc;border-radius:4px;font-size:0.75rem;color:#a0aec0;">
                                    Sélectionnez une unité et une quantité pour voir le déduction du stock
                                </div>

                                {{-- Line total --}}
                                <div style="text-align:right;">
                                    <span style="font-size:0.75rem;color:#718096;">Total ligne:</span>
                                    <strong style="font-size:1rem;color:#2d3748;" x-text="formatCurrency((item.quantity_sold || 0) * (item.unit_price || 0))"></strong>
                                    <input type="hidden" :name="'items[' + index + '][total_price]'" :value="(item.quantity_sold || 0) * (item.unit_price || 0)">
                                    <input type="hidden" :name="'items[' + index + '][conversion_rate]'" x-model="item.conversion_rate">
                                </div>
                            </div>
                        </div>
                    </template>

                    <button type="button" @click="addItem()" class="btn btn-secondary btn-sm" style="margin-top:8px;display:flex;align-items:center;gap:6px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        Ajouter une ligne
                    </button>

                    <input type="hidden" name="total" :value="total()">

                    <div class="form-group" style="margin-top:16px;">
                        <label class="form-label" for="note">Note (optionnel)</label>
                        <textarea class="form-textarea" id="note" name="note" rows="2">{{ old('note') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- RIGHT --}}
            <div>
                <div class="card" style="position:sticky;top:88px;">
                    <h3 style="font-size:1rem;font-weight:600;margin-bottom:16px;">Résumé</h3>
                    <div style="display:flex;flex-direction:column;gap:12px;">
                        <div style="display:flex;justify-content:space-between;">
                            <span style="color:#64748B;">Nombre d'articles:</span>
                            <strong x-text="items.length"></strong>
                        </div>
                        <div style="display:flex;justify-content:space-between;padding-top:12px;border-top:1px solid #E2E8F0;">
                            <span style="font-weight:600;">Total facture:</span>
                            <strong x-text="formatCurrency(total())" style="color:#1ABC9C;font-size:1.125rem;"></strong>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width:100%;margin-top:20px;height:44px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline;vertical-align:middle;margin-right:6px;"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        Enregistrer la facture
                    </button>
                </div>
            </div>
        </div>
    </form>

    <script>
        // Products data from server
        const productsData = @json($products ?? []);

        function createEmptyItem() {
            return {
                product_id: null,
                designation: '',
                unit_sold: '',
                quantity_sold: 1,
                unit_price: 0,
                total_price: 0,
                conversion_rate: 1,
                quantity_deducted: 0,
                productData: null
            };
        }

        function invoiceForm() {
            return {
                items: [],

                init() {
                    this.addItem();
                },

                addItem() {
                    this.items.push(createEmptyItem());
                },

                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },

                selectProduct(index, productId) {
                    const item = this.items[index];
                    const product = productsData.find(p => p.id == productId);
                    if (!product) return;

                    item.designation = product.name;
                    item.productData = product;

                    // Auto-select sale unit if available, otherwise base unit
                    item.unit_sold = product.sale_unit || product.unit;
                    this.updateUnitPrice(index);
                },

                updateUnitPrice(index) {
                    const item = this.items[index];
                    if (!item.productData || !item.unit_sold) return;

                    const product = item.productData;

                    // Calculate conversion rate based on selected unit
                    if (item.unit_sold === product.unit) {
                        item.conversion_rate = 1;
                    } else if (item.unit_sold === product.sale_unit && product.sale_conversion_rate) {
                        item.conversion_rate = product.sale_conversion_rate;
                    } else if (item.unit_sold === product.purchase_unit && product.purchase_conversion_rate) {
                        item.conversion_rate = product.purchase_conversion_rate;
                    } else {
                        item.conversion_rate = 1;
                    }

                    this.calculateDeduction(index);
                },

                calculateDeduction(index) {
                    const item = this.items[index];
                    if (!item.productData || !item.unit_sold || !item.quantity_sold) {
                        item.quantity_deducted = 0;
                        return;
                    }

                    // Calculate quantity to deduct from stock
                    item.quantity_deducted = Math.round(item.quantity_sold * item.conversion_rate);
                },

                total() {
                    return this.items.reduce((sum, item) => {
                        const lineTotal = (item.quantity_sold || 0) * (item.unit_price || 0);
                        item.total_price = lineTotal;
                        return sum + lineTotal;
                    }, 0);
                },

                formatCurrency(amount) {
                    return new Intl.NumberFormat('fr-FR').format(Math.round(amount)) + ' FCFA';
                }
            };
        }
    </script>
</x-app-layout>
