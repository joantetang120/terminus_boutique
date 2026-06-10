<x-app-layout title="Nouvelle facture">
    <div class="page-header">
        <h1>Nouvelle facture</h1>
        <div class="breadcrumb">
            <a href="{{ route('dashboard') }}">Accueil</a> > <a href="{{ route('factures.index') }}">Factures</a> >
            Nouvelle
        </div>
    </div>

    <form action="{{ route('factures.store') }}" method="POST" x-data="invoiceForm()" @submit="formSubmitting = true">
        @csrf
        <div style="display:grid;grid-template-columns:40% 60%;gap:24px;">
            <div style="position:sticky;top:88px;align-self:start;">
                {{-- Invoice Preview --}}
                <div class="card" style="margin-bottom:24px;overflow:hidden;">
                    <h3 style="font-size:1rem;font-weight:600;margin-bottom:16px;">Aperçu de la facture</h3>

                    <div style="border-bottom:2px solid #1e3a8a;padding-bottom:12px;margin-bottom:12px;">
                        <h2 style="color:#1e3a8a;font-size:1.125rem;font-weight:700;margin:0;">TERMINUS BOUTIQUE</h2>
                        <div style="font-size:0.75rem;color:#64748b;">Bonamoussadi — Douala</div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:12px;font-size:0.75rem;">
                        <div>
                            <div style="color:#64748b;">Client</div>
                            <div style="font-weight:600;color:#1e293b;" x-text="clientName || '—'"></div>
                        </div>
                        <div style="text-align:right;">
                            <div style="color:#64748b;">Date</div>
                            <div style="font-weight:600;color:#1e293b;" x-text="new Date().toLocaleDateString('fr-FR')"></div>
                        </div>
                    </div>

                    <table style="width:100%;border-collapse:collapse;font-size:0.6875rem;">
                        <thead>
                            <tr style="background:#f8fafc;">
                                <th style="padding:6px 4px;text-align:left;font-weight:600;color:#475569;">Article</th>
                                <th style="padding:6px 4px;text-align:center;font-weight:600;color:#475569;width:40px;">Qté</th>
                                <th style="padding:6px 4px;text-align:right;font-weight:600;color:#475569;width:70px;">P.U.</th>
                                <th style="padding:6px 4px;text-align:right;font-weight:600;color:#475569;width:80px;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="item in items" :key="item.product_id">
                                <tr x-show="item.designation">
                                    <td style="padding:5px 4px;border-top:1px solid #e2e8f0;color:#1e293b;" x-text="item.designation"></td>
                                    <td style="padding:5px 4px;border-top:1px solid #e2e8f0;text-align:center;color:#1e293b;" x-text="item.quantity_sold"></td>
                                    <td style="padding:5px 4px;border-top:1px solid #e2e8f0;text-align:right;color:#1e293b;" x-text="formatCurrency(item.unit_price)"></td>
                                    <td style="padding:5px 4px;border-top:1px solid #e2e8f0;text-align:right;font-weight:600;color:#1e293b;" x-text="formatCurrency((item.quantity_sold || 0) * (item.unit_price || 0))"></td>
                                </tr>
                            </template>
                            <tr x-show="!items.length || !items.some(i => i.designation)">
                                <td colspan="4" style="padding:16px;text-align:center;color:#94a3b8;font-size:0.6875rem;">Aucun article ajouté</td>
                            </tr>
                        </tbody>
                    </table>

                    <div style="margin-top:10px;text-align:right;padding-top:8px;border-top:2px solid #1e3a8a;">
                        <span style="font-size:0.8125rem;color:#64748b;">Total</span>
                        <div style="font-weight:700;font-size:1.125rem;color:#1e3a8a;" x-text="formatCurrency(total())"></div>
                    </div>

                    <div x-show="note" style="margin-top:10px;padding:8px;background:#f8fafc;border-radius:4px;font-size:0.6875rem;color:#64748b;">
                        <strong>Note:</strong> <span x-text="note"></span>
                    </div>
                </div>

                {{-- Resume --}}
                <div class="card">
                    <div style="display:flex;flex-direction:column;gap:12px;">
                        <div style="display:flex;justify-content:space-between;">
                            <span style="color:#64748B;">Nombre d'articles:</span>
                            <strong x-text="items.length"></strong>
                        </div>
                        <div
                            style="display:flex;justify-content:space-between;padding-top:12px;border-top:1px solid #E2E8F0;">
                            <span style="font-weight:600;">Total facture:</span>
                            <strong x-text="formatCurrency(total())"
                                style="color:#1ABC9C;font-size:1.125rem;"></strong>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" :disabled="hasAnyPriceErrors()"
                        :style="{
                            width: '100%',
                            marginTop: '20px',
                            height: '44px',
                            opacity: hasAnyPriceErrors() ? '0.5' : '1',
                            cursor: hasAnyPriceErrors() ? 'not-allowed' : 'pointer'
                        }">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" style="display:inline;vertical-align:middle;margin-right:6px;">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span x-text="hasAnyPriceErrors() ? 'Prix invalide detecte' : 'Enregistrer la facture'"></span>
                    </button>
                </div>
            </div>

            <div>
                <div class="card" style="margin-bottom:24px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                        <h3 style="font-size:1rem;font-weight:600;margin:0;">Client</h3>
                        <button type="button" @click="togglePriceVisibility()"
                            style="background:#fff;border:2px solid #3b82f6;border-radius:8px;padding:8px 14px;cursor:pointer;display:flex;align-items:center;gap:8px;font-size:0.875rem;font-weight:600;color:#3b82f6;transition:all 0.2s;box-shadow:0 2px 4px rgba(59, 130, 246, 0.15);"
                            :title="pricesVisible ? 'Masquer le prix minimum' : 'Afficher le prix minimum'"
                            @mouseover="$event.target.style.background='#eff6ff'; $event.target.style.borderColor='#2563eb'"
                            @mouseout="$event.target.style.background='#fff'; $event.target.style.borderColor='#3b82f6'">
                            <svg x-show="pricesVisible" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <svg x-show="!pricesVisible" width="18" height="18" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"></path>
                                <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68">
                                </path>
                                <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"></path>
                                <line x1="2" x2="22" y1="2" y2="22"></line>
                            </svg>
                            <span x-text="pricesVisible ? 'Masquer prix min' : 'Afficher prix min'"></span>
                        </button>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                        <div class="form-group">
                            <label class="form-label" for="client_name">Nom du client <span
                                    style="color:#C0392B;">*</span></label>
                            <input class="form-input" type="text" id="client_name" name="client_name"
                                value="{{ old('client_name') }}" required x-model="clientName" @input="markDirty()">
                            @error('client_name')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="client_phone">Telephone (optionnel)</label>
                            <input class="form-input" type="text" id="client_phone" name="client_phone"
                                value="{{ old('client_phone') }}" @input="markDirty()">
                        </div>
                    </div>

                    @php
                        $dueDate = now()->addDays(10)->format('d/m/Y');
                    @endphp
                    <div
                        style="margin-top:16px;padding:12px 16px;background:#E8F6F3;border-left:4px solid #1ABC9C;border-radius:4px;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1ABC9C"
                                stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            <span style="font-size:0.875rem;color:#16A085;">
                                <strong>Echeance automatique dans 10 jours - {{ $dueDate }}</strong>
                            </span>
                        </div>
                        <p style="margin:4px 0 0 24px;font-size:0.75rem;color:#5DADE2;">
                            La date d'echeance est calculee automatiquement. Aucun champ de saisie requis.
                        </p>
                    </div>
                </div>

                <div class="card">
                    <h3 style="font-size:1rem;font-weight:600;margin-bottom:16px;">Articles</h3>

                    <template x-for="(item, index) in items" :key="index">
                        <div
                            :style="{
                                marginBottom: '16px',
                                padding: '16px',
                                background: item.hasPriceError ? '#fff5f5' : '#f8fafc',
                                border: item.hasPriceError ? '2px solid #ef4444' : '1px solid #e2e8f0',
                                borderRadius: '8px',
                                transition: 'all 0.2s ease'
                            }">
                            <div
                                style="display:grid;grid-template-columns:1fr auto;gap:12px;margin-bottom:12px;align-items:end;">
                                <div class="form-group" style="margin:0;position:relative;"
                                    @click.outside="closeProductDropdown(index)">
                                    <label class="form-label" style="font-size:0.75rem;">Produit <span
                                            style="color:#C0392B;">*</span></label>
                                    <div style="position:relative;">
                                        <input class="form-input" type="text" x-model="item.product_search"
                                            @focus="openProductDropdown(index)" @input="handleProductSearch(index)"
                                            @keydown.arrow-down.prevent="highlightNextProduct(index)"
                                            @keydown.arrow-up.prevent="highlightPreviousProduct(index)"
                                            @keydown.enter.prevent="selectHighlightedProduct(index)"
                                            @keydown.escape.prevent="closeProductDropdown(index)"
                                            placeholder="Rechercher ou choisir un produit..." autocomplete="off"
                                            style="padding:8px 40px 8px 10px;font-size:0.8125rem;">
                                        <button type="button" @click="toggleProductDropdown(index)"
                                            style="position:absolute;top:50%;right:8px;transform:translateY(-50%);border:none;background:transparent;color:#64748b;cursor:pointer;padding:4px;display:flex;align-items:center;justify-content:center;"
                                            title="Afficher les produits">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2">
                                                <polyline points="6 9 12 15 18 9"></polyline>
                                            </svg>
                                        </button>
                                    </div>

                                    <div x-show="item.productDropdownOpen" x-transition
                                        style="position:absolute;top:100%;left:0;right:0;z-index:30;margin-top:6px;background:#fff;border:1px solid #e2e8f0;border-radius:8px;box-shadow:0 12px 32px rgba(15, 23, 42, 0.12);max-height:260px;overflow:auto;">
                                        <template x-if="filteredProducts(index).length">
                                            <div style="padding:6px;">
                                                <template x-for="(product, productIndex) in filteredProducts(index)"
                                                    :key="product.id">
                                                    <button type="button" @click="selectProduct(index, product.id)"
                                                        :style="{
                                                            width: '100%',
                                                            border: 'none',
                                                            borderRadius: '6px',
                                                            background: item.highlightedProductIndex === productIndex ?
                                                                '#f1f5f9' : 'transparent',
                                                            cursor: 'pointer',
                                                            textAlign: 'left',
                                                            padding: '10px 12px',
                                                            marginBottom: '4px'
                                                        }">
                                                        <div
                                                            style="display:flex;justify-content:space-between;gap:12px;align-items:flex-start;">
                                                            <div>
                                                                <div style="font-size:0.8125rem;font-weight:600;color:#1e293b;"
                                                                    x-text="product.name"></div>
                                                                <div style="font-size:0.75rem;color:#64748b;">
                                                                    Stock:
                                                                    <strong
                                                                        x-text="formatStock(product.current_stock)"></strong>
                                                                    <span x-text="product.unit"></span>
                                                                </div>
                                                            </div>
                                                            <span
                                                                x-show="product.current_stock <= product.alert_threshold"
                                                                style="font-size:0.6875rem;font-weight:700;color:#c53030;background:#fff5f5;padding:4px 6px;border-radius:999px;white-space:nowrap;">
                                                                Stock bas
                                                            </span>
                                                        </div>
                                                    </button>
                                                </template>
                                            </div>
                                        </template>
                                        <div x-show="!filteredProducts(index).length"
                                            style="padding:12px;font-size:0.8125rem;color:#64748b;">
                                            Aucun produit ne correspond a votre recherche.
                                        </div>
                                    </div>

                                    <input type="hidden" :name="'items[' + index + '][product_id]'"
                                        x-model="item.product_id">
                                    <input type="hidden" :name="'items[' + index + '][designation]'"
                                        x-model="item.designation">
                                </div>

                                <button type="button" @click="removeItem(index)"
                                    style="background:#fed7d7;border:none;cursor:pointer;color:#c53030;font-size:1.25rem;padding:8px 12px;border-radius:6px;"
                                    title="Supprimer la ligne">&times;</button>
                            </div>

                            <div x-show="item.productData"
                                style="margin-bottom:12px;padding:10px 12px;background:#e6fffa;border-radius:4px;border-left:3px solid #38b2ac;">
                                <div
                                    style="display:flex;justify-content:space-between;align-items:center;font-size:0.8125rem;">
                                    <span style="color:#234e52;font-weight:500;" x-text="item.designation"></span>
                                    <span style="color:#2d6a4f;">
                                        Stock disponible: <strong
                                            x-text="formatStock(item.productData?.current_stock)"></strong> <span
                                            x-text="item.productData?.unit"></span>
                                    </span>
                                </div>
                            </div>

                            <div x-show="item.unitPriceInfo.hasPrice"
                                 style="margin-bottom:12px;padding:10px 12px;background:#fefce8;border-radius:4px;border-left:3px solid #f59e0b;">
                                 <div
                                     style="display:flex;justify-content:space-between;align-items:center;font-size:0.8125rem;flex-wrap:wrap;gap:8px;">
                                     <span style="color:#92400e;font-weight:500;">
                                         <span x-text="item.unitPriceInfo.unitLabel"></span>:
                                     </span>
                                     <div style="display:flex;gap:12px;align-items:center;">
                                         <span style="color:#166534;">
                                             Prix: <strong
                                                 x-text="formatCurrency(item.unitPriceInfo.salePrice)"></strong>
                                         </span>
                                         <span x-show="item.unitPriceInfo.marginPercentage > 0" style="color:#7c3aed;">
                                             Marge: <strong x-text="item.unitPriceInfo.marginPercentage + '%'"></strong>
                                         </span>
                                         <span x-show="pricesVisible" style="color:#dc2626;font-weight:600;">
                                             Min: <strong
                                                 x-text="formatCurrency(item.unitPriceInfo.minimumPrice)"></strong>
                                         </span>
                                     </div>
                                 </div>
                             </div>

                            <div x-show="item.hasPriceError"
                                style="margin-bottom:12px;padding:10px 12px;background:#fef2f2;border-radius:4px;border-left:3px solid #ef4444;">
                                <div
                                    style="display:flex;align-items:center;gap:8px;font-size:0.8125rem;color:#dc2626;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="12" y1="8" x2="12" y2="12"></line>
                                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                    </svg>
                                    <span
                                        x-text="'Prix trop bas ! Minimum requis: ' + formatCurrency(item.unitPriceInfo.minimumPrice)"></span>
                                </div>
                            </div>

                            <div
                                style="display:grid;grid-template-columns:1fr 100px 120px;gap:12px;margin-bottom:12px;align-items:end;">
                                <div class="form-group" style="margin:0;">
                                    <label class="form-label" style="font-size:0.75rem;">Unite de vente</label>
                                    <select class="form-select" :name="'items[' + index + '][unit_sold]'"
                                        x-model="item.unit_sold" @change="updateUnitPrice(index)"
                                        style="padding:8px 10px;font-size:0.8125rem;">
                                        <option value="">Choisir...</option>
                                        <template x-if="item.productData">
                                            <template x-for="(label, unitKey) in item.productData.available_units"
                                                :key="unitKey">
                                                <option :value="unitKey" x-text="label"></option>
                                            </template>
                                        </template>
                                    </select>
                                </div>
                                <div class="form-group" style="margin:0;">
                                    <label class="form-label" style="font-size:0.75rem;">Qte</label>
                                    <input class="form-input" type="number"
                                        :name="'items[' + index + '][quantity_sold]'"
                                        x-model.number="item.quantity_sold" @input="calculateDeduction(index)"
                                        min="0.01" step="0.01" required
                                        style="padding:8px 10px;font-size:0.8125rem;">
                                </div>
                                <div class="form-group" style="margin:0;">
                                    <label class="form-label" style="font-size:0.75rem;">
                                        Prix unit.
                                        <span
                                            x-show="item.unitPriceInfo.hasPrice && item.unit_price < item.unitPriceInfo.minimumPrice"
                                            style="color:#ef4444;margin-left:4px;">!</span>
                                    </label>
                                    <input class="form-input" type="number"
                                        :name="'items[' + index + '][unit_price]'" x-model.number="item.unit_price"
                                        @input="validatePrice(index)" min="0" step="1" required
                                        :style="{
                                            padding: '8px 10px',
                                            fontSize: '0.8125rem',
                                            borderColor: item.hasPriceError ? '#ef4444' : '',
                                            backgroundColor: item.hasPriceError ? '#fef2f2' : ''
                                        }">
                                </div>
                            </div>

                            <div
                                style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
                                <div x-show="item.quantity_deducted > 0 && item.unit_sold"
                                    style="padding:6px 12px;background:#ebf8ff;border-radius:4px;font-size:0.75rem;color:#2b6cb0;border-left:3px solid #4299e1;">
                                    <span x-text="item.quantity_sold + ' ' + item.unit_sold"></span>
                                    &rarr;
                                    <strong
                                        x-text="item.quantity_deducted + ' ' + (item.productData?.unit || 'pieces') + ' deduites du stock'"></strong>
                                </div>
                                <div x-show="!item.unit_sold || item.quantity_sold <= 0"
                                    style="padding:6px 12px;background:#f7fafc;border-radius:4px;font-size:0.75rem;color:#a0aec0;">
                                    Selectionnez une unite et une quantite pour voir la deduction du stock
                                </div>

                                <div style="text-align:right;">
                                    <span style="font-size:0.75rem;color:#718096;">Total ligne:</span>
                                    <strong style="font-size:1rem;color:#2d3748;"
                                        x-text="formatCurrency((item.quantity_sold || 0) * (item.unit_price || 0))"></strong>
                                    <input type="hidden" :name="'items[' + index + '][total_price]'"
                                        :value="(item.quantity_sold || 0) * (item.unit_price || 0)">
                                    <input type="hidden" :name="'items[' + index + '][conversion_rate]'"
                                        x-model="item.conversion_rate">
                                </div>
                            </div>
                        </div>
                    </template>

                    <button type="button" @click="addItem()" class="btn btn-secondary btn-sm"
                        style="margin-top:8px;display:flex;align-items:center;gap:6px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Ajouter une ligne
                    </button>

                    <input type="hidden" name="total" :value="total()">

                    <div class="form-group" style="margin-top:16px;">
                        <label class="form-label" for="note">Note (optionnel)</label>
                        <textarea class="form-textarea" id="note" name="note" rows="2" x-model="note" @input="markDirty()">{{ old('note') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

            {{-- Draft Recovery Modal --}}
            <div x-show="showDraftModal" class="modal-backdrop" style="display:none;z-index:9999;">
                <div class="modal" @click.away="() => {}" style="max-width:420px;">
                    <div class="modal-header">
                        <h3>Brouillon trouvé</h3>
                    </div>
                    <div class="modal-body">
                        <p style="margin-bottom:16px;color:#475569;">
                            Un brouillon de facture non terminée a été trouvé.
                            Voulez-vous le restaurer ?
                        </p>
                        <p style="font-size:0.8125rem;color:#94a3b8;">
                            Si vous choisissez de ne pas le restaurer, le brouillon sera supprimé.
                        </p>
                    </div>
                    <div class="modal-footer" style="display:flex;gap:8px;justify-content:flex-end;">
                        <button type="button" class="btn btn-secondary" @click="discardDraft()">
                            Nouvelle facture
                        </button>
                        <button type="button" class="btn btn-primary" @click="loadDraft()">
                            Restaurer le brouillon
                        </button>
                    </div>
                </div>
            </div>

            {{-- Leave Confirmation Modal --}}
            <div x-show="showLeaveModal" class="modal-backdrop" style="display:none;z-index:9999;">
                <div class="modal" @click.away="cancelLeave()" style="max-width:420px;">
                    <div class="modal-header">
                        <h3>Modifications non sauvegardées</h3>
                    </div>
                    <div class="modal-body">
                        <p style="margin-bottom:16px;color:#475569;">
                            Vous avez des modifications en cours. Que souhaitez-vous faire ?
                        </p>
                    </div>
                    <div class="modal-footer" style="display:flex;gap:8px;justify-content:flex-end;">
                        <button type="button" class="btn btn-secondary" @click="cancelLeave()">
                            Rester
                        </button>
                        <button type="button" class="btn btn-warning" style="background:#E67E22;color:white;"
                            @click="discardAndLeave()">
                            Ne pas sauvegarder
                        </button>
                        <button type="button" class="btn btn-success" @click="saveAndLeave()">
                            Sauvegarder le brouillon
                        </button>
                    </div>
                </div>
            </div>
    </form>

    @php
        $oldItems = old('items', []);
        $hasErrors = $errors->any();
    @endphp

    <script>
        const productsData = @json($products ?? []);
        const oldItemsData = @json($oldItems);
        const hasValidationErrors = @json($hasErrors);

        function emptyPriceInfo() {
            return {
                hasPrice: false,
                salePrice: 0,
                minimumPrice: 0,
                marginPercentage: 0,
                unitLabel: ''
            };
        }

        function createEmptyItem() {
            return {
                product_id: null,
                product_search: '',
                designation: '',
                unit_sold: '',
                quantity_sold: 1,
                unit_price: 0,
                total_price: 0,
                conversion_rate: 1,
                quantity_deducted: 0,
                productData: null,
                productDropdownOpen: false,
                highlightedProductIndex: -1,
                unitPriceInfo: emptyPriceInfo(),
                hasPriceError: false
            };
        }

        function invoiceForm() {
            return {
                items: [],
                pricesVisible: true,
                clientName: '{{ old('client_name') }}',
                note: '{{ old('note') }}',

                // Draft state
                showLeaveModal: false,
                pendingNavigation: null,
                isDirty: false,
                showDraftModal: false,
                formSubmitting: false,
                loadingDraft: false,

                init() {
                    // Restore from validation errors first (before draft)
                    if (oldItemsData.length > 0) {
                        this.restoreFromOld(oldItemsData);
                    } else {
                        this.addItem();
                    }

                    // Check for existing draft (only on fresh page load, not after validation errors)
                    if (!hasValidationErrors) {
                        this.$nextTick(() => this.checkDraft());
                    }

                    // Watch items for changes
                    this.$watch('items', () => {
                        if (!this.loadingDraft) this.markDirty();
                    }, { deep: true });

                    // Setup navigation guard
                    this.$nextTick(() => this.setupNavigationGuard());
                },

                // ── Draft methods ──

                setupNavigationGuard() {
                    document.addEventListener('click', (e) => {
                        if (this.formSubmitting) return;
                        const link = e.target.closest('a');
                        if (!link) return;
                        const href = link.getAttribute('href');
                        if (!href || href === '#' || href.startsWith('javascript:')) return;
                        if (link.hasAttribute('target')) return;
                        if (link.hasAttribute('data-no-guard')) return;
                        if (this.isDirty) {
                            e.preventDefault();
                            this.pendingNavigation = href;
                            this.showLeaveModal = true;
                        }
                    });

                    window.addEventListener('beforeunload', (e) => {
                        if (this.isDirty && !this.formSubmitting) {
                            e.preventDefault();
                            e.returnValue = '';
                        }
                    });
                },

                checkDraft() {
                    const saved = localStorage.getItem('facture_draft');
                    if (saved) {
                        try {
                            const parsed = JSON.parse(saved);
                            if (parsed && parsed.items && parsed.items.length > 0) {
                                this.showDraftModal = true;
                            }
                        } catch (e) {
                            localStorage.removeItem('facture_draft');
                        }
                    }
                },

                getFormFields() {
                    return {
                        client_name: document.getElementById('client_name')?.value || '',
                        client_phone: document.getElementById('client_phone')?.value || '',
                        note: document.getElementById('note')?.value || '',
                    };
                },

                setFormFields(fields) {
                    const nameInput = document.getElementById('client_name');
                    const phoneInput = document.getElementById('client_phone');
                    const noteInput = document.getElementById('note');
                    if (nameInput) nameInput.value = fields.client_name || '';
                    if (phoneInput) phoneInput.value = fields.client_phone || '';
                    if (noteInput) noteInput.value = fields.note || '';
                },

                saveDraft() {
                    const fields = this.getFormFields();
                    const draft = {
                        client_name: this.clientName || fields.client_name,
                        client_phone: fields.client_phone,
                        note: this.note || fields.note,
                        items: this.items.map(item => ({
                            product_id: item.product_id,
                            product_search: item.product_search,
                            designation: item.designation,
                            unit_sold: item.unit_sold,
                            quantity_sold: item.quantity_sold,
                            unit_price: item.unit_price,
                            total_price: item.total_price,
                            conversion_rate: item.conversion_rate,
                            quantity_deducted: item.quantity_deducted
                        })),
                        savedAt: new Date().toISOString()
                    };
                    localStorage.setItem('facture_draft', JSON.stringify(draft));
                },

                loadDraft() {
                    const saved = localStorage.getItem('facture_draft');
                    if (!saved) return;
                    try {
                        const draft = JSON.parse(saved);
                        this.loadingDraft = true;

                        // Restore text fields
                        this.setFormFields({
                            client_name: draft.client_name || '',
                            client_phone: draft.client_phone || '',
                            note: draft.note || '',
                        });
                        this.clientName = draft.client_name || '';
                        this.note = draft.note || '';

                        // Restore items
                        this.items = [];
                        if (draft.items && draft.items.length > 0) {
                            draft.items.forEach(itemData => {
                                const item = createEmptyItem();
                                const product = productsData.find(p => p.id == itemData.product_id);
                                item.product_id = itemData.product_id;
                                item.product_search = itemData.product_search || (product ? product.name : '');
                                item.designation = itemData.designation;
                                item.unit_sold = itemData.unit_sold;
                                item.quantity_sold = itemData.quantity_sold;
                                item.unit_price = itemData.unit_price;
                                item.total_price = itemData.total_price;
                                item.conversion_rate = itemData.conversion_rate;
                                item.quantity_deducted = itemData.quantity_deducted || 0;
                                item.productData = product || null;
                                item.hasPriceError = false;
                                this.items.push(item);
                                if (product && item.unit_sold) {
                                    this.updatePriceInfo(this.items.length - 1);
                                    this.validatePrice(this.items.length - 1);
                                }
                            });
                        }

                        this.loadingDraft = false;
                        this.isDirty = false;
                    } catch (e) {
                        localStorage.removeItem('facture_draft');
                    }
                    this.showDraftModal = false;
                },

                discardDraft() {
                    localStorage.removeItem('facture_draft');
                    this.showDraftModal = false;
                    this.isDirty = false;
                },

                saveAndLeave() {
                    this.saveDraft();
                    this.showLeaveModal = false;
                    this.isDirty = false;
                    if (this.pendingNavigation) {
                        window.location.href = this.pendingNavigation;
                    }
                },

                discardAndLeave() {
                    if (!this.showDraftModal) {
                        localStorage.removeItem('facture_draft');
                    }
                    this.showLeaveModal = false;
                    this.isDirty = false;
                    if (this.pendingNavigation) {
                        window.location.href = this.pendingNavigation;
                    }
                },

                cancelLeave() {
                    this.showLeaveModal = false;
                    this.pendingNavigation = null;
                },

                markDirty() {
                    this.isDirty = true;
                },

                restoreFromOld(oldItems) {
                    this.items = [];
                    oldItems.forEach(itemData => {
                        const item = createEmptyItem();
                        const product = productsData.find(p => p.id == itemData.product_id);
                        item.product_id = itemData.product_id;
                        item.product_search = product ? product.name : (itemData.designation || '');
                        item.designation = itemData.designation || '';
                        item.unit_sold = itemData.unit_sold || '';
                        item.quantity_sold = itemData.quantity_sold || 1;
                        item.unit_price = itemData.unit_price || 0;
                        item.total_price = itemData.total_price || 0;
                        item.conversion_rate = itemData.conversion_rate || 1;
                        item.quantity_deducted = Math.round((itemData.quantity_sold || 0) * (itemData.conversion_rate || 1));
                        item.productData = product || null;
                        this.items.push(item);
                        if (product && item.unit_sold) {
                            this.updatePriceInfo(this.items.length - 1);
                            this.validatePrice(this.items.length - 1);
                        }
                    });
                    this.isDirty = false;
                },

                // ── Original methods ──

                togglePriceVisibility() {
                    this.pricesVisible = !this.pricesVisible;
                },

                addItem() {
                    this.items.push(createEmptyItem());
                    this.markDirty();
                },

                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                        this.markDirty();
                    }
                },

                filteredProducts(index) {
                    const item = this.items[index];
                    const search = (item.product_search || '').trim().toLowerCase();

                    return productsData
                        .filter((product) => {
                            if (!search) {
                                return true;
                            }

                            return [
                                product.name,
                                product.unit,
                                product.current_stock,
                            ].join(' ').toLowerCase().includes(search);
                        })
                        .slice(0, 12);
                },

                openProductDropdown(index) {
                    const item = this.items[index];
                    item.productDropdownOpen = true;
                    item.highlightedProductIndex = 0;
                },

                closeProductDropdown(index) {
                    const item = this.items[index];
                    item.productDropdownOpen = false;
                    item.highlightedProductIndex = -1;
                },

                toggleProductDropdown(index) {
                    const item = this.items[index];

                    if (item.productDropdownOpen) {
                        this.closeProductDropdown(index);
                        return;
                    }

                    this.openProductDropdown(index);
                },

                handleProductSearch(index) {
                    const item = this.items[index];

                    if (item.productData && item.product_search !== item.productData.name) {
                        this.resetSelectedProduct(index, true);
                    }

                    item.productDropdownOpen = true;
                    item.highlightedProductIndex = 0;
                },

                highlightNextProduct(index) {
                    const item = this.items[index];
                    const results = this.filteredProducts(index);

                    if (!results.length) {
                        return;
                    }

                    item.productDropdownOpen = true;
                    item.highlightedProductIndex = item.highlightedProductIndex >= results.length - 1 ?
                        0 :
                        item.highlightedProductIndex + 1;
                },

                highlightPreviousProduct(index) {
                    const item = this.items[index];
                    const results = this.filteredProducts(index);

                    if (!results.length) {
                        return;
                    }

                    item.productDropdownOpen = true;
                    item.highlightedProductIndex = item.highlightedProductIndex <= 0 ?
                        results.length - 1 :
                        item.highlightedProductIndex - 1;
                },

                selectHighlightedProduct(index) {
                    const item = this.items[index];
                    const results = this.filteredProducts(index);
                    const product = results[item.highlightedProductIndex] || results[0];

                    if (product) {
                        this.selectProduct(index, product.id);
                    }
                },

                resetSelectedProduct(index, keepSearch = false) {
                    const item = this.items[index];
                    const searchValue = keepSearch ? item.product_search : '';

                    item.product_id = null;
                    item.product_search = searchValue;
                    item.designation = '';
                    item.unit_sold = '';
                    item.unit_price = 0;
                    item.total_price = 0;
                    item.conversion_rate = 1;
                    item.quantity_deducted = 0;
                    item.productData = null;
                    item.productDropdownOpen = true;
                    item.highlightedProductIndex = -1;
                    item.unitPriceInfo = emptyPriceInfo();
                    item.hasPriceError = false;
                },

                selectProduct(index, productId) {
                    const item = this.items[index];
                    const product = productsData.find((entry) => entry.id == productId);

                    if (!product) {
                        this.resetSelectedProduct(index);
                        return;
                    }

                    item.product_id = product.id;
                    item.product_search = product.name;
                    item.designation = product.name;
                    item.productData = product;
                    item.productDropdownOpen = false;
                    item.highlightedProductIndex = -1;

                    item.unit_sold = product.sale_unit || product.unit;
                    this.updateUnitPrice(index);
                },

                updateUnitPrice(index) {
                    const item = this.items[index];
                    if (!item.productData || !item.unit_sold) {
                        item.unitPriceInfo = emptyPriceInfo();
                        item.conversion_rate = 1;
                        item.quantity_deducted = 0;
                        item.hasPriceError = false;
                        return;
                    }

                    const product = item.productData;

                    if (item.unit_sold === product.unit) {
                        item.conversion_rate = 1;
                    } else if (item.unit_sold === product.sale_unit && product.sale_conversion_rate) {
                        item.conversion_rate = product.sale_conversion_rate;
                    } else if (item.unit_sold === product.purchase_unit && product.purchase_conversion_rate) {
                        item.conversion_rate = product.purchase_conversion_rate;
                    } else {
                        item.conversion_rate = 1;
                    }

                    this.updatePriceInfo(index);
                    this.calculateDeduction(index);
                    this.validatePrice(index);
                },

                updatePriceInfo(index) {
                    const item = this.items[index];
                    if (!item.productData || !item.unit_sold) {
                        item.unitPriceInfo = emptyPriceInfo();
                        return;
                    }

                    const product = item.productData;
                    const priceData = product.unit_prices?.[item.unit_sold];

                    if (!priceData) {
                        item.unitPriceInfo = emptyPriceInfo();
                        return;
                    }

                    item.unitPriceInfo = {
                        hasPrice: true,
                        salePrice: priceData.sale_price,
                        minimumPrice: priceData.minimum_price,
                        marginPercentage: priceData.margin_percentage,
                        unitLabel: item.unit_sold
                    };
                },

                validatePrice(index) {
                    const item = this.items[index];
                    if (!item.unitPriceInfo.hasPrice || !item.unit_price) {
                        item.hasPriceError = false;
                        return;
                    }

                    const enteredPrice = parseFloat(item.unit_price) || 0;
                    item.hasPriceError = enteredPrice < item.unitPriceInfo.minimumPrice;
                },

                hasAnyPriceErrors() {
                    return this.items.some((item) => item.hasPriceError);
                },

                calculateDeduction(index) {
                    const item = this.items[index];
                    if (!item.productData || !item.unit_sold || !item.quantity_sold) {
                        item.quantity_deducted = 0;
                        return;
                    }

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
                    return new Intl.NumberFormat('fr-FR').format(Math.round(amount || 0)) + ' FCFA';
                },

                formatStock(amount) {
                    const numericAmount = Number(amount || 0);

                    return new Intl.NumberFormat('fr-FR', {
                        minimumFractionDigits: Number.isInteger(numericAmount) ? 0 : 2,
                        maximumFractionDigits: 2,
                    }).format(numericAmount);
                }
            };
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</x-app-layout>
