<x-app-layout title="Nouveau produit">
    <div class="product-form-wrapper">
        {{-- Back / Cancel Button --}}
        <a href="{{ route('produits.index') }}" class="back-button" id="backButton">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"/>
                <polyline points="12 19 5 12 12 5"/>
            </svg>
            <span>Retour</span>
        </a>

        {{-- Form Header --}}
        <div class="form-page-header">
            <div class="header-badge">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                <span>Création</span>
            </div>
            <h1 class="form-page-title">Nouveau produit</h1>
            <p class="form-page-subtitle">Ajoutez un produit à votre inventaire avec toutes ses informations</p>
        </div>

        {{-- Progress Steps --}}
        <div class="progress-tracker">
            <div class="step-item active" data-step="1">
                <div class="step-circle">
                    <svg class="step-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                </div>
                <span class="step-label">Informations</span>
            </div>
            <div class="step-connector"></div>
            <div class="step-item" data-step="2">
                <div class="step-circle">
                    <svg class="step-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M12 1v6m0 6v6m11-7h-6m-6 0H1m16.36-5.36l-4.24 4.24M6.88 17.12l4.24-4.24m6.36 0l-4.24-4.24M6.88 6.88l4.24 4.24"/>
                    </svg>
                </div>
                <span class="step-label">Unités</span>
            </div>
            <div class="step-connector"></div>
            <div class="step-item" data-step="3">
                <div class="step-circle">
                    <svg class="step-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                        <line x1="12" y1="22.08" x2="12" y2="12"/>
                    </svg>
                </div>
                <span class="step-label">Stock</span>
            </div>
        </div>

        <form action="{{ route('produits.store') }}" method="POST" id="productForm" x-data="{ showBackModal: false }">
            @csrf

            {{-- Step 1: Basic Information --}}
            <div class="form-card" id="step-1">
                <div class="card-header">
                    <div class="header-icon-box">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                    </div>
                    <div class="header-text">
                        <h2>Informations du produit</h2>
                        <p>Nom et description de votre produit</p>
                    </div>
                </div>

                <div class="card-body">
                    <div class="field-group">
                        <label for="name" class="field-label">
                            Nom du produit
                            <span class="required-dot" title="Requis"></span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}"
                               class="styled-input" 
                               placeholder="Ex: Cahier A4 200 pages"
                               required
                               autofocus>
                        @error('name')
                            <div class="error-message">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="12" y1="8" x2="12" y2="12"/>
                                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                                </svg>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label for="description" class="field-label">Description <span class="optional">(optionnel)</span></label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="4"
                                  class="styled-textarea"
                                  placeholder="Décrivez votre produit en quelques mots...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="error-message">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="12" y1="8" x2="12" y2="12"/>
                                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                                </svg>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="card-footer">
                    <button type="button" class="btn-secondary" @click="showBackModal = true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 14 4 9l5-5"/>
                            <path d="M4 9h10.5a5.5 5.5 0 0 1 5.5 5.5v0a5.5 5.5 0 0 1-5.5 5.5H11"/>
                        </svg>
                        <span>Retour</span>
                    </button>
                    <button type="button" class="btn-primary" onclick="goToStep(2)">
                        <span>Continuer</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"/>
                            <polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Step 2: Unit Configuration --}}
            <div class="form-card hidden" id="step-2">
                <div class="card-header">
                    <div class="header-icon-box">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="3"/>
                            <path d="M12 1v6m0 6v6m11-7h-6m-6 0H1m16.36-5.36l-4.24 4.24M6.88 17.12l4.24-4.24m6.36 0l-4.24-4.24M6.88 6.88l4.24 4.24"/>
                        </svg>
                    </div>
                    <div class="header-text">
                        <h2>Configuration des unités</h2>
                        <p>Définissez l'unité de stock et les conversions</p>
                    </div>
                </div>

                <div class="card-body">
                    {{-- Base Unit --}}
                    <div class="highlight-card">
                        <div class="highlight-badge">Principal</div>
                        <div class="field-group">
                            <label for="unit" class="field-label">
                                Unité de base
                                <span class="required-dot" title="Requis"></span>
                            </label>
                            <div class="select-wrapper">
                                <select id="unit" name="unit" class="styled-select" required onchange="updateUnitLabels()">
                                    <option value="piece" {{ old('unit') === 'piece' ? 'selected' : '' }}>Pièce (unité)</option>
                                    <option value="paquet" {{ old('unit') === 'paquet' ? 'selected' : '' }}>Paquet</option>
                                    <option value="boite" {{ old('unit') === 'boite' ? 'selected' : '' }}>Boîte</option>
                                    <option value="carton" {{ old('unit') === 'carton' ? 'selected' : '' }}>Carton</option>
                                    <option value="sceau" {{ old('unit') === 'sceau' ? 'selected' : '' }}>Sceau</option>
                                    <option value="sacs" {{ old('unit') === 'sacs' ? 'selected' : '' }}>Sacs</option>
                                    <option value="palettes" {{ old('unit') === 'palettes' ? 'selected' : '' }}>Palettes</option>
                                </select>
                            </div>
                            <p class="field-note">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="12" y1="16" x2="12" y2="12"/>
                                    <line x1="12" y1="8" x2="12.01" y2="8"/>
                                </svg>
                                Tout le stock sera compté dans cette unité
                            </p>
                            @error('unit')
                                <div class="error-message">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"/>
                                        <line x1="12" y1="8" x2="12" y2="12"/>
                                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                                    </svg>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>
                    </div>

                    {{-- Optional Conversions --}}
                    <div class="conversion-section">
                        <div class="conversion-header">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="16 3 21 3 21 8"/>
                                <line x1="4" y1="20" x2="21" y2="3"/>
                                <polyline points="21 16 21 21 16 21"/>
                                <line x1="15" y1="15" x2="21" y2="21"/>
                                <line x1="4" y1="4" x2="9" y2="9"/>
                            </svg>
                            <div>
                                <h3>Conversions optionnelles</h3>
                                <p>Pour faciliter les achats et les ventes</p>
                            </div>
                        </div>

                        {{-- Purchase Conversion --}}
                        <div class="conversion-card">
                            <div class="conversion-type-badge purchase">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                                </svg>
                                <span>Achat</span>
                            </div>
                            <div class="conversion-fields">
                                <div class="field-group">
                                    <label class="field-label">Unité fournisseur</label>
                                    <div class="select-wrapper">
                                        <select id="purchase_unit" name="purchase_unit" class="styled-select" onchange="updateStockPreview()">
                                            <option value="">Identique à la base</option>
                                            <option value="piece" {{ old('purchase_unit') === 'piece' ? 'selected' : '' }}>Pièce</option>
                                            <option value="paquet" {{ old('purchase_unit') === 'paquet' ? 'selected' : '' }}>Paquet</option>
                                            <option value="boite" {{ old('purchase_unit') === 'boite' ? 'selected' : '' }}>Boîte</option>
                                            <option value="carton" {{ old('purchase_unit') === 'carton' ? 'selected' : '' }}>Carton</option>
                                            <option value="sceau" {{ old('purchase_unit') === 'sceau' ? 'selected' : '' }}>Sceau</option>
                                            <option value="sacs" {{ old('purchase_unit') === 'sacs' ? 'selected' : '' }}>Sacs</option>
                                            <option value="palettes" {{ old('purchase_unit') === 'palettes' ? 'selected' : '' }}>Palettes</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="field-group">
                                    <label class="field-label">Taux de conversion</label>
                                    <div class="rate-input-group">
                                        <span class="rate-prefix">1 unité =</span>
                                        <input type="number" 
                                               id="purchase_conversion_rate" 
                                               name="purchase_conversion_rate"
                                               value="{{ old('purchase_conversion_rate') }}" 
                                               min="1"
                                               class="styled-input rate-input" 
                                               placeholder="12">
                                        <span class="rate-suffix">pièces</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Sale Conversions (Multiple) --}}
                        <div class="conversion-card">
                            <div class="conversion-type-badge sale">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="1" x2="12" y2="23"/>
                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                </svg>
                                <span>Vente</span>
                            </div>
                            <div id="sale-conversions-container">
                                @if(old('sale_conversions'))
                                    @foreach(old('sale_conversions') as $index => $conversion)
                                        <div class="conversion-row" data-index="{{ $index }}">
                                            <div class="conversion-fields">
                                                <div class="field-group">
                                                    <label class="field-label">Unité de vente</label>
                                                    <div class="select-wrapper">
                                                        <select name="sale_conversions[{{ $index }}][unit]" class="styled-select sale-unit-select" onchange="updateSaleSuffixes()">
                                                            <option value="">Identique à la base</option>
                                                            <option value="piece" {{ $conversion['unit'] === 'piece' ? 'selected' : '' }}>Pièce</option>
                                                            <option value="paquet" {{ $conversion['unit'] === 'paquet' ? 'selected' : '' }}>Paquet</option>
                                                            <option value="boite" {{ $conversion['unit'] === 'boite' ? 'selected' : '' }}>Boîte</option>
                                                            <option value="carton" {{ $conversion['unit'] === 'carton' ? 'selected' : '' }}>Carton</option>
                                                            <option value="sceau" {{ $conversion['unit'] === 'sceau' ? 'selected' : '' }}>Sceau</option>
                                                            <option value="sacs" {{ $conversion['unit'] === 'sacs' ? 'selected' : '' }}>Sacs</option>
                                                            <option value="palettes" {{ $conversion['unit'] === 'palettes' ? 'selected' : '' }}>Palettes</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="field-group">
                                                    <label class="field-label">Taux de conversion</label>
                                                    <div class="rate-input-group">
                                                        <span class="rate-prefix">1 <span class="sale-unit-label">{{ $conversion['unit'] ?: 'unité' }}</span> =</span>
                                                        <input type="number" 
                                                               name="sale_conversions[{{ $index }}][conversion_rate]"
                                                               value="{{ $conversion['conversion_rate'] }}" 
                                                               min="1"
                                                               class="styled-input rate-input" 
                                                               placeholder="12">
                                                        <span class="rate-suffix base-unit-label">pièces</span>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn-remove-conversion" onclick="removeConversion(this)" title="Supprimer">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <line x1="18" y1="6" x2="6" y2="18"/>
                                                        <line x1="6" y1="6" x2="18" y2="18"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="button" class="btn-add-conversion" onclick="addSaleConversion()">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="5" x2="12" y2="19"/>
                                    <line x1="5" y1="12" x2="19" y2="12"/>
                                </svg>
                                Ajouter une unité de vente
                            </button>
                        </div>
                    </div>

                    @error('purchase_unit')
                        <div class="error-message">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                    @error('purchase_conversion_rate')
                        <div class="error-message">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                    @error('sale_unit')
                        <div class="error-message">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                    @error('sale_conversion_rate')
                        <div class="error-message">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                <div class="card-footer">
                    <button type="button" class="btn-secondary" onclick="goToStep(1)">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="19" y1="12" x2="5" y2="12"/>
                            <polyline points="12 19 5 12 12 5"/>
                        </svg>
                        <span>Retour</span>
                    </button>
                    <button type="button" class="btn-primary" onclick="goToStep(3)">
                        <span>Continuer</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"/>
                            <polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Step 3: Stock Management --}}
            <div class="form-card hidden" id="step-3">
                <div class="card-header">
                    <div class="header-icon-box">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                            <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                            <line x1="12" y1="22.08" x2="12" y2="12"/>
                        </svg>
                    </div>
                    <div class="header-text">
                        <h2>Stock initial</h2>
                        <p>Quantité de départ et seuil d'alerte</p>
                    </div>
                </div>

                <div class="card-body">
                    {{-- Stock Input --}}
                    <div class="stock-input-section">
                        <div class="field-group">
                            <label for="current_stock" class="field-label">
                                Quantité initiale
                                <span class="required-dot" title="Requis"></span>
                            </label>
                            <div class="stock-input-wrapper">
                                <input type="number" 
                                       id="current_stock" 
                                       name="current_stock"
                                       value="{{ old('current_stock', 0) }}" 
                                       min="0" 
                                       step="1"
                                       class="styled-input stock-number-input" 
                                       required
                                       oninput="updateStockPreview()">
                                <div class="stock-unit-select">
                                    <select id="initial_stock_unit" name="initial_stock_unit" class="styled-select" onchange="updateStockPreview()">
                                        <option value="">-- <span id="default-unit-label">pièce</span> (base) --</option>
                                        <option value="carton" {{ old('initial_stock_unit') === 'carton' ? 'selected' : '' }}>carton</option>
                                        <option value="boite" {{ old('initial_stock_unit') === 'boite' ? 'selected' : '' }}>boite</option>
                                        <option value="paquet" {{ old('initial_stock_unit') === 'paquet' ? 'selected' : '' }}>paquet</option>
                                        <option value="piece" {{ old('initial_stock_unit') === 'piece' ? 'selected' : '' }}>piece</option>
                                        <option value="sceau" {{ old('initial_stock_unit') === 'sceau' ? 'selected' : '' }}>sceau</option>
                                        <option value="sacs" {{ old('initial_stock_unit') === 'sacs' ? 'selected' : '' }}>sacs</option>
                                        <option value="palettes" {{ old('initial_stock_unit') === 'palettes' ? 'selected' : '' }}>palettes</option>
                                    </select>
                                </div>
                            </div>
                            @error('current_stock')
                                <div class="error-message">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"/>
                                        <line x1="12" y1="8" x2="12" y2="12"/>
                                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                                    </svg>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                            @error('initial_stock_unit')
                                <div class="error-message">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"/>
                                        <line x1="12" y1="8" x2="12" y2="12"/>
                                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                                    </svg>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        {{-- Conversion Rate Field (shown when needed) --}}
                        <div class="field-group" id="stock-conversion-field" style="display:none;">
                            <label for="initial_stock_conversion_rate" class="field-label">
                                Taux de conversion
                                <span class="required-dot" title="Requis"></span>
                            </label>
                            <div class="rate-input-group">
                                <span class="rate-prefix">1 <span id="stock-unit-label">carton</span> =</span>
                                <input type="number" 
                                       id="initial_stock_conversion_rate" 
                                       name="initial_stock_conversion_rate"
                                       value="{{ old('initial_stock_conversion_rate') }}" 
                                       min="1"
                                       class="styled-input rate-input" 
                                       placeholder="12"
                                       oninput="updateStockPreview()">
                                <span class="rate-suffix" id="base-unit-display">pièces</span>
                            </div>
                            @error('initial_stock_conversion_rate')
                                <div class="error-message">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"/>
                                        <line x1="12" y1="8" x2="12" y2="12"/>
                                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                                    </svg>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>
                    </div>

                    {{-- Stock Preview Card --}}
                    <div class="preview-card" id="stock-preview">
                        <div class="preview-header">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            <span>Aperçu du stock</span>
                        </div>
                        <div class="preview-body">
                            <div class="preview-row">
                                <span class="preview-label">Votre saisie</span>
                                <span class="preview-value" id="preview-input">0</span>
                            </div>
                            <div class="preview-arrow">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="5" x2="12" y2="19"/>
                                    <polyline points="19 12 12 19 5 12"/>
                                </svg>
                            </div>
                            <div class="preview-row highlighted">
                                <span class="preview-label">Stock réel en base</span>
                                <span class="preview-value large" id="preview-base">0 <span id="preview-base-unit">pièces</span></span>
                            </div>
                        </div>
                    </div>

                    {{-- Alert Threshold --}}
                    <div class="alert-section">
                        <div class="field-group">
                            <label for="alert_threshold" class="field-label">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                                    <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                                </svg>
                                Seuil d'alerte stock bas
                            </label>
                            <div class="alert-input-group">
                                <input type="number" 
                                       id="alert_threshold" 
                                       name="alert_threshold"
                                       value="{{ old('alert_threshold', 0) }}" 
                                       min="0" 
                                       step="1"
                                       class="styled-input alert-number-input">
                                <span class="alert-unit-label" id="alert-unit-label">pièces</span>
                            </div>
                            <p class="field-note">
                                Notification automatique quand le stock passe sous ce seuil
                            </p>
                            @error('alert_threshold')
                                <div class="error-message">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"/>
                                        <line x1="12" y1="8" x2="12" y2="12"/>
                                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                                    </svg>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>
                    </div>

                    {{-- Active Status Toggle --}}
                    <div class="status-section">
                        <label class="toggle-wrapper">
                            <input type="checkbox" 
                                   name="is_active" 
                                   value="1"
                                   {{ old('is_active', true) ? 'checked' : '' }} 
                                   class="toggle-input">
                            <div class="toggle-track">
                                <div class="toggle-thumb"></div>
                            </div>
                            <div class="toggle-content">
                                <span class="toggle-label-active">Produit actif</span>
                                <span class="toggle-label-inactive">Produit inactif</span>
                            </div>
                        </label>
                        @error('is_active')
                            <div class="error-message">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="12" y1="8" x2="12" y2="12"/>
                                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                                </svg>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="card-footer">
                    <button type="button" class="btn-secondary" onclick="goToStep(2)">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="19" y1="12" x2="5" y2="12"/>
                            <polyline points="12 19 5 12 12 5"/>
                        </svg>
                        <span>Retour</span>
                    </button>
                    <button type="submit" class="btn-success">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                            <polyline points="17 21 17 13 7 13 7 21"/>
                            <polyline points="7 3 7 8 15 8"/>
                        </svg>
                        <span>Créer le produit</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

<style>
.product-form-wrapper {
    max-width: 780px;
    margin: 0 auto;
    padding: 32px 24px;
}

.form-page-header {
    text-align: center;
    margin-bottom: 36px;
}

.header-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    color: white;
    border-radius: 20px;
    font-size: 0.8125rem;
    font-weight: 600;
    margin-bottom: 16px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.form-page-title {
    font-size: 2rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 8px 0;
    letter-spacing: -0.5px;
}

.form-page-subtitle {
    color: #64748b;
    font-size: 1rem;
    margin: 0;
    line-height: 1.5;
}

/* Progress Tracker */
.progress-tracker {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    margin-bottom: 40px;
    padding: 20px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05), 0 8px 24px rgba(0,0,0,0.03);
}

.step-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
}

.step-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #94a3b8;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.step-icon {
    opacity: 0;
    transition: opacity 0.3s ease;
}

.step-item.active .step-circle {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

.step-item.active .step-icon {
    opacity: 1;
}

.step-item.completed .step-circle {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.step-label {
    font-size: 0.8125rem;
    font-weight: 500;
    color: #94a3b8;
    transition: color 0.3s ease;
}

.step-item.active .step-label,
.step-item.completed .step-label {
    color: #0f172a;
}

.step-connector {
    width: 48px;
    height: 2px;
    background: #e2e8f0;
    border-radius: 1px;
    transition: background 0.3s ease;
}

.step-connector.completed {
    background: #10b981;
}

/* Form Cards */
.form-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05), 0 12px 32px rgba(0,0,0,0.04);
    overflow: hidden;
    animation: slideUp 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.form-card.hidden {
    display: none;
}

@keyframes slideUp {
    from { 
        opacity: 0; 
        transform: translateY(20px); 
    }
    to { 
        opacity: 1; 
        transform: translateY(0); 
    }
}

.card-header {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    padding: 28px 32px;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-bottom: 1px solid #e2e8f0;
}

.header-icon-box {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
    flex-shrink: 0;
}

.header-text h2 {
    font-size: 1.375rem;
    font-weight: 600;
    color: #0f172a;
    margin: 0 0 4px 0;
}

.header-text p {
    color: #64748b;
    font-size: 0.9375rem;
    margin: 0;
}

.card-body {
    padding: 32px;
}

.card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding: 24px 32px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
}

/* Fields */
.field-group {
    margin-bottom: 24px;
}

.field-group:last-child {
    margin-bottom: 0;
}

.field-label {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.9375rem;
    font-weight: 500;
    color: #334155;
    margin-bottom: 8px;
}

.required-dot {
    width: 6px;
    height: 6px;
    background: #ef4444;
    border-radius: 50%;
}

.optional {
    color: #94a3b8;
    font-weight: 400;
    font-size: 0.875rem;
}

.styled-input,
.styled-textarea,
.styled-select {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 0.9375rem;
    background: #fafafa;
    color: #0f172a;
    transition: all 0.2s ease;
}

.styled-input::placeholder,
.styled-textarea::placeholder {
    color: #94a3b8;
}

.styled-input:focus,
.styled-textarea:focus,
.styled-select:focus {
    outline: none;
    border-color: #6366f1;
    background: white;
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
}

.styled-textarea {
    resize: vertical;
    min-height: 100px;
    line-height: 1.6;
}

.select-wrapper {
    position: relative;
}

.styled-select {
    appearance: none;
    cursor: pointer;
    padding-right: 40px;
}

.select-wrapper::after {
    content: '';
    position: absolute;
    right: 16px;
    top: 50%;
    transform: translateY(-50%);
    width: 0;
    height: 0;
    border-left: 5px solid transparent;
    border-right: 5px solid transparent;
    border-top: 5px solid #64748b;
    pointer-events: none;
}

.field-note {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 8px;
    font-size: 0.8125rem;
    color: #64748b;
}

.field-note svg {
    color: #94a3b8;
    flex-shrink: 0;
}

.error-message {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 8px;
    font-size: 0.8125rem;
    color: #ef4444;
}

.error-message svg {
    flex-shrink: 0;
}

/* Highlight Card */
.highlight-card {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border: 2px solid #3b82f6;
    border-radius: 14px;
    padding: 24px;
    margin-bottom: 28px;
    position: relative;
}

.highlight-badge {
    position: absolute;
    top: -10px;
    left: 20px;
    padding: 4px 12px;
    background: #3b82f6;
    color: white;
    border-radius: 12px;
    font-size: 0.6875rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Conversion Section */
.conversion-section {
    background: #fafafa;
    border: 2px dashed #e2e8f0;
    border-radius: 14px;
    padding: 24px;
}

.conversion-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
    color: #475569;
}

.conversion-header svg {
    color: #6366f1;
    flex-shrink: 0;
}

.conversion-header h3 {
    font-size: 1rem;
    font-weight: 600;
    color: #0f172a;
    margin: 0 0 2px 0;
}

.conversion-header p {
    font-size: 0.8125rem;
    color: #64748b;
    margin: 0;
}

.conversion-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 16px;
}

.conversion-card:last-child {
    margin-bottom: 0;
}

.conversion-type-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 0.8125rem;
    font-weight: 600;
    margin-bottom: 16px;
}

.conversion-type-badge.purchase {
    background: #fef3c7;
    color: #92400e;
}

.conversion-type-badge.sale {
    background: #d1fae5;
    color: #065f46;
}

.conversion-type-badge svg {
    flex-shrink: 0;
}

.conversion-fields {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.rate-input-group {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 16px;
    background: #fafafa;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
}

.rate-prefix,
.rate-suffix {
    font-size: 0.875rem;
    color: #64748b;
    font-weight: 500;
    white-space: nowrap;
}

.rate-input {
    flex: 1;
    text-align: center;
    padding: 8px;
    border: none;
    background: transparent;
    font-weight: 600;
}

.rate-input:focus {
    box-shadow: none;
}

/* Stock Input Section */
.stock-input-section {
    margin-bottom: 28px;
}

.stock-input-wrapper {
    display: flex;
    gap: 12px;
}

.stock-number-input {
    flex: 1;
    text-align: center;
    font-size: 1.125rem;
    font-weight: 600;
    padding: 14px 16px;
}

.stock-unit-select {
    width: 200px;
}

/* Preview Card */
.preview-card {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    border: 2px solid #f59e0b;
    border-radius: 14px;
    padding: 20px;
    margin-bottom: 28px;
}

.preview-header {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.875rem;
    font-weight: 600;
    color: #92400e;
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 1px dashed #f59e0b;
}

.preview-body {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.preview-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 14px;
    background: rgba(255, 255, 255, 0.6);
    border-radius: 10px;
}

.preview-row.highlighted {
    background: white;
    border: 2px solid #f59e0b;
    box-shadow: 0 2px 8px rgba(245, 158, 11, 0.15);
}

.preview-label {
    font-size: 0.8125rem;
    color: #78716c;
}

.preview-value {
    font-size: 0.9375rem;
    font-weight: 600;
    color: #1c1917;
}

.preview-value.large {
    font-size: 1.125rem;
}

.preview-arrow {
    text-align: center;
    color: #f59e0b;
}

/* Alert Section */
.alert-section {
    background: #fef2f2;
    border: 2px solid #fecaca;
    border-radius: 14px;
    padding: 24px;
    margin-bottom: 28px;
}

.alert-input-group {
    display: flex;
    align-items: center;
    gap: 12px;
}

.alert-number-input {
    flex: 1;
}

.alert-unit-label {
    font-size: 0.9375rem;
    font-weight: 500;
    color: #991b1b;
    white-space: nowrap;
}

/* Status Section */
.status-section {
    background: #f0fdf4;
    border: 2px solid #bbf7d0;
    border-radius: 14px;
    padding: 24px;
}

.toggle-wrapper {
    display: flex;
    align-items: center;
    gap: 16px;
    cursor: pointer;
}

.toggle-input {
    display: none;
}

.toggle-track {
    width: 52px;
    height: 28px;
    background: #cbd5e1;
    border-radius: 14px;
    position: relative;
    transition: background 0.3s ease;
    flex-shrink: 0;
}

.toggle-thumb {
    position: absolute;
    top: 2px;
    left: 2px;
    width: 24px;
    height: 24px;
    background: white;
    border-radius: 50%;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
    transition: transform 0.3s ease;
}

.toggle-input:checked + .toggle-track {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.toggle-input:checked + .toggle-track .toggle-thumb {
    transform: translateX(24px);
}

.toggle-content {
    display: flex;
    flex-direction: column;
}

.toggle-label-active {
    font-size: 0.9375rem;
    font-weight: 500;
    color: #15803d;
}

.toggle-label-inactive {
    font-size: 0.875rem;
    color: #94a3b8;
    display: none;
}

.toggle-input:not(:checked) ~ .toggle-track ~ .toggle-content .toggle-label-active {
    display: none;
}

.toggle-input:not(:checked) ~ .toggle-track ~ .toggle-content .toggle-label-inactive {
    display: block;
    color: #64748b;
}

/* Buttons */
.btn-primary,
.btn-secondary,
.btn-success {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border: none;
    border-radius: 12px;
    font-size: 0.9375rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.btn-primary {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(99, 102, 241, 0.35);
}

.btn-secondary {
    background: #f1f5f9;
    color: #475569;
}

.btn-secondary:hover {
    background: #e2e8f0;
}

.btn-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    font-size: 1rem;
    padding: 14px 32px;
    box-shadow: 0 4px 16px rgba(16, 185, 129, 0.25);
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(16, 185, 129, 0.35);
}

/* Responsive */
@media (max-width: 768px) {
    .product-form-wrapper {
        padding: 24px 16px;
    }

    .form-page-title {
        font-size: 1.75rem;
    }

    .progress-tracker {
        padding: 16px;
        gap: 8px;
    }

    .step-circle {
        width: 36px;
        height: 36px;
    }

    .step-connector {
        width: 32px;
    }

    .card-header,
    .card-body,
    .card-footer {
        padding-left: 20px;
        padding-right: 20px;
    }

    .conversion-fields {
        grid-template-columns: 1fr;
    }

    .stock-input-wrapper {
        flex-direction: column;
    }

    .stock-unit-select {
        width: 100%;
    }
}
</style>

<script>
// Step Navigation
function goToStep(step) {
    document.querySelectorAll('.form-card').forEach(card => {
        card.classList.add('hidden');
    });

    document.getElementById('step-' + step).classList.remove('hidden');

    document.querySelectorAll('.step-item').forEach((el, index) => {
        const stepNum = index + 1;
        el.classList.remove('active', 'completed');
        if (stepNum === step) {
            el.classList.add('active');
        } else if (stepNum < step) {
            el.classList.add('completed');
        }
    });

    document.querySelectorAll('.step-connector').forEach((connector, index) => {
        if (index < step - 1) {
            connector.classList.add('completed');
        } else {
            connector.classList.remove('completed');
        }
    });
}

// Update unit labels dynamically
function updateUnitLabels() {
    const unitSelect = document.getElementById('unit');
    const baseUnit = unitSelect.value;

    document.getElementById('default-unit-label').textContent = baseUnit;
    document.getElementById('alert-unit-label').textContent = baseUnit + 's';
    document.getElementById('preview-base-unit').textContent = baseUnit + 's';
    document.getElementById('base-unit-display').textContent = baseUnit + 's';
    document.getElementById('sale-rate-suffix').textContent = baseUnit + 's';

    const initialUnitSelect = document.getElementById('initial_stock_unit');
    initialUnitSelect.options[0].text = '-- ' + baseUnit + ' (base) --';

    updateStockPreview();
}

// Stock preview calculation
function updateStockPreview() {
    const quantity = parseInt(document.getElementById('current_stock').value) || 0;
    const inputUnit = document.getElementById('initial_stock_unit').value;
    const baseUnit = document.getElementById('unit').value;
    const purchaseUnit = document.getElementById('purchase_unit').value;
    const purchaseRate = parseInt(document.getElementById('purchase_conversion_rate').value) || 0;
    const manualRate = parseInt(document.getElementById('initial_stock_conversion_rate').value) || 0;

    let convertedQuantity = quantity;
    let rate = 1;
    let needsManualRate = false;

    if (inputUnit && inputUnit !== baseUnit) {
        if (inputUnit === purchaseUnit && purchaseRate > 0) {
            rate = purchaseRate;
        } else {
            // Check multiple sale conversions
            const saleConversions = document.querySelectorAll('#sale-conversions-container .conversion-row');
            let foundRate = null;
            saleConversions.forEach(row => {
                const unitSelect = row.querySelector('select[name*="[unit]"]');
                const rateInput = row.querySelector('input[name*="[conversion_rate]"]');
                if (unitSelect && rateInput && unitSelect.value === inputUnit) {
                    const convRate = parseInt(rateInput.value) || 0;
                    if (convRate > 0) {
                        foundRate = convRate;
                    }
                }
            });

            if (foundRate !== null) {
                rate = foundRate;
            } else if (manualRate > 0) {
                rate = manualRate;
            } else {
                needsManualRate = true;
            }
        }
        convertedQuantity = quantity * rate;
    }

    const conversionField = document.getElementById('stock-conversion-field');
    const stockUnitLabel = document.getElementById('stock-unit-label');

    if (needsManualRate) {
        conversionField.style.display = 'block';
    } else {
        conversionField.style.display = 'none';
        if (inputUnit && inputUnit !== baseUnit) {
            document.getElementById('initial_stock_conversion_rate').value = rate;
        }
    }

    stockUnitLabel.textContent = inputUnit || baseUnit;

    const inputText = quantity + ' ' + (inputUnit || baseUnit) + (quantity > 1 ? 's' : '');
    const baseText = convertedQuantity + ' ' + baseUnit + (convertedQuantity > 1 ? 's' : '');

    document.getElementById('preview-input').textContent = inputText;
    document.getElementById('preview-base').textContent = baseText;
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateUnitLabels();
    updateStockPreview();
});
</script>

    {{-- Back Confirmation Modal --}}
    <div class="modal-overlay" id="cancelModal">
        <div class="modal-content" id="modalContent">
            <div class="modal-header">
                <div class="modal-icon warning">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                        <line x1="12" y1="9" x2="12" y2="13"/>
                        <line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                </div>
                <h3 class="modal-title">Quitter sans enregistrer ?</h3>
                <p class="modal-desc">Les informations saisies seront perdues.</p>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-secondary" id="modalCancel">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18"/>
                        <path d="m6 6 12 12"/>
                    </svg>
                    <span>Rester</span>
                </button>
                <a href="{{ route('produits.index') }}" class="btn-danger" id="modalConfirm">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 14 4 9l5-5"/>
                        <path d="M4 9h10.5a5.5 5.5 0 0 1 5.5 5.5v0a5.5 5.5 0 0 1-5.5 5.5H11"/>
                    </svg>
                    <span>Quitter</span>
                </a>
            </div>
        </div>
    </div>

<script>
// Cancel Modal Logic
const backButton = document.getElementById('backButton');
const cancelModal = document.getElementById('cancelModal');
const modalContent = document.getElementById('modalContent');
const modalCancel = document.getElementById('modalCancel');
const modalConfirm = document.getElementById('modalConfirm');

let hasChanges = false;

// Track form changes
document.getElementById('productForm').addEventListener('input', function() {
    hasChanges = true;
});

// Intercept back button click
backButton.addEventListener('click', function(e) {
    if (hasChanges) {
        e.preventDefault();
        cancelModal.classList.add('active');
    }
});

// Modal cancel button (stay on page)
modalCancel.addEventListener('click', function() {
    cancelModal.classList.remove('active');
});

// Close modal on overlay click
cancelModal.addEventListener('click', function(e) {
    if (e.target === cancelModal) {
        cancelModal.classList.remove('active');
    }
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && cancelModal.classList.contains('active')) {
        cancelModal.classList.remove('active');
    }
});

// Dynamic Sale Conversions
let saleConversionIndex = {{ old('sale_conversions') ? count(old('sale_conversions')) : 0 }};

function addSaleConversion() {
    const container = document.getElementById('sale-conversions-container');
    const baseUnit = document.getElementById('unit').value || 'pièce';
    
    const row = document.createElement('div');
    row.className = 'conversion-row';
    row.dataset.index = saleConversionIndex;
    
    row.innerHTML = `
        <div class="conversion-fields">
            <div class="field-group">
                <label class="field-label">Unité de vente</label>
                <div class="select-wrapper">
                    <select name="sale_conversions[${saleConversionIndex}][unit]" class="styled-select sale-unit-select" onchange="updateSaleSuffixes()">
                        <option value="">Identique à la base</option>
                        <option value="piece">Pièce</option>
                        <option value="paquet">Paquet</option>
                        <option value="boite">Boîte</option>
                        <option value="carton">Carton</option>
                        <option value="sceau">Sceau</option>
                        <option value="sacs">Sacs</option>
                        <option value="palettes">Palettes</option>
                    </select>
                </div>
            </div>
            <div class="field-group">
                <label class="field-label">Taux de conversion</label>
                <div class="rate-input-group">
                    <span class="rate-prefix">1 <span class="sale-unit-label">unité</span> =</span>
                    <input type="number" 
                           name="sale_conversions[${saleConversionIndex}][conversion_rate]"
                           min="1"
                           class="styled-input rate-input" 
                           placeholder="12">
                    <span class="rate-suffix base-unit-label">${baseUnit}</span>
                </div>
            </div>
            <button type="button" class="btn-remove-conversion" onclick="removeConversion(this)" title="Supprimer">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
    `;
    
    container.appendChild(row);
    saleConversionIndex++;
}

function removeConversion(button) {
    const row = button.closest('.conversion-row');
    row.remove();
    updateSaleSuffixes();
}

function updateSaleSuffixes() {
    const baseUnit = document.getElementById('unit').value || 'pièce';
    document.querySelectorAll('.base-unit-label').forEach(el => {
        el.textContent = baseUnit;
    });
    
    document.querySelectorAll('.sale-unit-select').forEach(select => {
        const row = select.closest('.conversion-row');
        const unitLabel = row.querySelector('.sale-unit-label');
        if (unitLabel) {
            unitLabel.textContent = select.value || 'unité';
        }
    });
}

// Update unit labels when base unit changes
document.getElementById('unit').addEventListener('change', updateSaleSuffixes);
</script>

<style>
/* Modal Styles */
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(4px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    padding: 20px;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.modal-overlay.active {
    opacity: 1;
    visibility: visible;
}

.modal-content {
    background: white;
    border-radius: 20px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    max-width: 420px;
    width: 100%;
    overflow: hidden;
    transform: scale(0.95) translateY(20px);
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.modal-overlay.active .modal-content {
    transform: scale(1) translateY(0);
}

.modal-header {
    padding: 32px 32px 24px;
    text-align: center;
}

.modal-icon {
    width: 56px;
    height: 56px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
}

.modal-icon.warning {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #92400e;
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
}

.modal-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #0f172a;
    margin: 0 0 8px 0;
}

.modal-desc {
    font-size: 0.9375rem;
    color: #64748b;
    margin: 0;
    line-height: 1.5;
}

.modal-actions {
    display: flex;
    gap: 12px;
    padding: 0 32px 32px;
}

.modal-actions .btn-secondary,
.modal-actions .btn-danger {
    flex: 1;
    justify-content: center;
}

.btn-danger {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 24px;
    border: none;
    border-radius: 12px;
    font-size: 0.9375rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
    text-decoration: none;
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25);
}

.btn-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(239, 68, 68, 0.35);
}

/* Back Button */
.back-button {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    background: white;
    color: #475569;
    border-radius: 10px;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    margin-bottom: 20px;
    transition: all 0.2s ease;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    width: fit-content;
}

.back-button:hover {
    background: #f8fafc;
    color: #0f172a;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

[x-cloak] { display: none !important; }

/* Dynamic Conversion Styles */
.conversion-row {
    margin-bottom: 12px;
    padding: 12px;
    background: #f8fafc;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
}

.conversion-row .conversion-fields {
    display: grid;
    grid-template-columns: 1fr 1fr auto;
    gap: 12px;
    align-items: end;
}

.btn-add-conversion {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    margin-top: 12px;
}

.btn-add-conversion:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.btn-remove-conversion {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: #fef2f2;
    color: #ef4444;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-remove-conversion:hover {
    background: #ef4444;
    color: white;
}

.sale-unit-label {
    font-weight: 500;
    color: #059669;
}

.base-unit-label {
    font-weight: 500;
    color: #3b82f6;
}
</style>
</x-app-layout>
