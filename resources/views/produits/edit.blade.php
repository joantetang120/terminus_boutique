<x-app-layout title="Modifier produit">
    <div class="modern-form-container">
        {{-- Header --}}
        <div class="form-header">
            <h1 class="form-title">
                <span class="title-icon">✏️</span>
                Modifier le produit
            </h1>
            <p class="form-subtitle">{{ $produit->name }}</p>
        </div>

        <form action="{{ route('produits.update', $produit) }}" method="POST" class="modern-form">
            @csrf
            @method('PUT')

            {{-- Section: Informations de base --}}
            <div class="form-section" id="section-1">
                <div class="section-header">
                    <div class="section-icon">📝</div>
                    <div>
                        <h2>Informations du produit</h2>
                        <p class="section-desc">Nom et description</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-field full-width">
                        <label for="name" class="field-label">
                            Nom du produit <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <input type="text" id="name" name="name" 
                                   value="{{ old('name', $produit->name) }}" 
                                   class="modern-input" placeholder="Ex: Cahier A4 200 pages" required>
                        </div>
                        @error('name')<span class="field-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-field full-width">
                        <label for="description" class="field-label">Description</label>
                        <div class="input-wrapper textarea-wrapper">
                            <textarea id="description" name="description" rows="3" 
                                      class="modern-input modern-textarea" 
                                      placeholder="Description optionnelle...">{{ old('description', $produit->description) }}</textarea>
                        </div>
                        @error('description')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="section-actions">
                    <button type="button" class="btn-next" onclick="goToStep(2)">
                        Continuer <span>→</span>
                    </button>
                </div>
            </div>

            {{-- Section: Configuration des unités --}}
            <div class="form-section hidden" id="section-2">
                <div class="section-header">
                    <div class="section-icon">⚖️</div>
                    <div>
                        <h2>Configuration des unités</h2>
                        <p class="section-desc">Définissez votre unité de stock et les conversions</p>
                    </div>
                </div>

                {{-- Unité de base --}}
                <div class="config-card primary">
                    <div class="config-badge base">Unité de stock</div>
                    <div class="form-field">
                        <label for="unit" class="field-label">
                            Unité de base <span class="required">*</span>
                        </label>
                        <div class="input-wrapper select-wrapper">
                            <span class="input-icon">📊</span>
                            <select id="unit" name="unit" class="modern-input modern-select" required 
                                    onchange="updateUnitLabels()">
                                <option value="piece" {{ old('unit', $produit->unit) === 'piece' ? 'selected' : '' }}>🧩 Pièce (unité)</option>
                                <option value="paquet" {{ old('unit', $produit->unit) === 'paquet' ? 'selected' : '' }}>📦 Paquet</option>
                                <option value="boite" {{ old('unit', $produit->unit) === 'boite' ? 'selected' : '' }}>🎁 Boîte</option>
                                <option value="carton" {{ old('unit', $produit->unit) === 'carton' ? 'selected' : '' }}>📦 Carton</option>
                            </select>
                        </div>
                        <span class="field-hint">
                            <span class="hint-icon">💡</span>
                            ⚠️ Changer l'unité de base n'affecte pas le stock existant
                        </span>
                        @error('unit')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                {{-- Conversions optionnelles --}}
                <div class="conversions-panel">
                    <div class="panel-header">
                        <span class="panel-icon">🔄</span>
                        <div>
                            <h3>Conversions optionnelles</h3>
                            <p>Pour faciliter les entrées (achat) et sorties (vente)</p>
                        </div>
                    </div>

                    <div class="conversion-row">
                        <div class="conversion-type">
                            <span class="type-icon">🛒</span>
                            <span class="type-label">Achat</span>
                        </div>
                        <div class="conversion-fields">
                            <div class="form-field">
                                <label class="field-label">Unité fournisseur</label>
                                <select id="purchase_unit" name="purchase_unit" class="modern-input modern-select">
                                    <option value="">-- Identique à la base --</option>
                                    <option value="piece" {{ old('purchase_unit', $produit->purchase_unit) === 'piece' ? 'selected' : '' }}>Pièce</option>
                                    <option value="paquet" {{ old('purchase_unit', $produit->purchase_unit) === 'paquet' ? 'selected' : '' }}>Paquet</option>
                                    <option value="boite" {{ old('purchase_unit', $produit->purchase_unit) === 'boite' ? 'selected' : '' }}>Boîte</option>
                                    <option value="carton" {{ old('purchase_unit', $produit->purchase_unit) === 'carton' ? 'selected' : '' }}>Carton</option>
                                </select>
                            </div>
                            <div class="form-field rate-field">
                                <label class="field-label">1 unité achat = ? base</label>
                                <input type="number" id="purchase_conversion_rate" name="purchase_conversion_rate" 
                                       value="{{ old('purchase_conversion_rate', $produit->purchase_conversion_rate) }}" min="1" 
                                       class="modern-input" placeholder="Ex: 12">
                            </div>
                        </div>
                    </div>

                    <div class="conversion-row">
                        <div class="conversion-type">
                            <span class="type-icon">💰</span>
                            <span class="type-label">Vente</span>
                        </div>
                        <div class="conversion-fields">
                            <div class="form-field">
                                <label class="field-label">Unité vente</label>
                                <select id="sale_unit" name="sale_unit" class="modern-input modern-select">
                                    <option value="">-- Identique à la base --</option>
                                    <option value="piece" {{ old('sale_unit', $produit->sale_unit) === 'piece' ? 'selected' : '' }}>Pièce</option>
                                    <option value="paquet" {{ old('sale_unit', $produit->sale_unit) === 'paquet' ? 'selected' : '' }}>Paquet</option>
                                    <option value="boite" {{ old('sale_unit', $produit->sale_unit) === 'boite' ? 'selected' : '' }}>Boîte</option>
                                    <option value="carton" {{ old('sale_unit', $produit->sale_unit) === 'carton' ? 'selected' : '' }}>Carton</option>
                                </select>
                            </div>
                            <div class="form-field rate-field">
                                <label class="field-label">1 unité vente = ? base</label>
                                <input type="number" id="sale_conversion_rate" name="sale_conversion_rate" 
                                       value="{{ old('sale_conversion_rate', $produit->sale_conversion_rate) }}" min="1" 
                                       class="modern-input" placeholder="Ex: 12">
                            </div>
                        </div>
                    </div>
                </div>

                @error('purchase_unit')<span class="field-error">{{ $message }}</span>@enderror
                @error('purchase_conversion_rate')<span class="field-error">{{ $message }}</span>@enderror
                @error('sale_unit')<span class="field-error">{{ $message }}</span>@enderror
                @error('sale_conversion_rate')<span class="field-error">{{ $message }}</span>@enderror

                <div class="section-actions">
                    <button type="button" class="btn-prev" onclick="goToStep(1)">
                        <span>←</span> Retour
                    </button>
                    <button type="button" class="btn-next" onclick="goToStep(3)">
                        Continuer <span>→</span>
                    </button>
                </div>
            </div>

            {{-- Section: Stock et Alertes --}}
            <div class="form-section hidden" id="section-3">
                <div class="section-header">
                    <div class="section-icon">📊</div>
                    <div>
                        <h2>Stock et Alertes</h2>
                        <p class="section-desc">Quantité actuelle et seuil d'alerte</p>
                    </div>
                </div>

                <div class="stock-panel">
                    {{-- Stock actuel --}}
                    <div class="config-card primary">
                        <div class="config-badge base">Stock actuel</div>
                        <div class="form-field main-stock">
                            <label for="current_stock" class="field-label">
                                Quantité en stock <span class="required">*</span>
                            </label>
                            <div class="stock-input-wrapper single">
                                <input type="number" id="current_stock" name="current_stock" 
                                       value="{{ old('current_stock', $produit->current_stock) }}" min="0" step="1"
                                       class="modern-input stock-number" required>
                                <span class="stock-unit-display" id="stock-unit-display">{{ $produit->unit }}s</span>
                            </div>
                            <span class="field-hint">
                                <span class="hint-icon">⚠️</span>
                                Modification manuelle - utiliser les mouvements de stock normalement
                            </span>
                            @error('current_stock')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    {{-- Seuil d'alerte --}}
                    <div class="form-field alert-field">
                        <label for="alert_threshold" class="field-label">
                            Seuil d'alerte stock bas
                        </label>
                        <div class="alert-input-wrapper">
                            <input type="number" id="alert_threshold" name="alert_threshold" 
                                   value="{{ old('alert_threshold', $produit->alert_threshold) }}" min="0" step="1"
                                   class="modern-input">
                            <span class="alert-unit" id="alert-unit-label">{{ $produit->unit }}s</span>
                        </div>
                        <span class="field-hint">
                            <span class="hint-icon">🔔</span>
                            Alerte quand le stock passe sous ce seuil
                        </span>
                        @error('alert_threshold')<span class="field-error">{{ $message }}</span>@enderror
                    </div>

                    {{-- Statut --}}
                    <div class="form-field status-field">
                        <label class="toggle-label">
                            <input type="checkbox" name="is_active" value="1" 
                                   {{ old('is_active', $produit->is_active) ? 'checked' : '' }} class="modern-toggle">
                            <span class="toggle-slider"></span>
                            <span class="toggle-text">
                                <span class="toggle-active">✅ Produit actif</span>
                                <span class="toggle-inactive">❌ Produit inactif</span>
                            </span>
                        </label>
                        @error('is_active')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="section-actions final-actions">
                    <button type="button" class="btn-prev" onclick="goToStep(2)">
                        <span>←</span> Retour
                    </button>
                    <button type="submit" class="btn-submit">
                        <span class="btn-icon">💾</span>
                        Mettre à jour
                    </button>
                </div>
            </div>
        </form>
    </div>

<style>
.modern-form-container {
    max-width: 720px;
    margin: 0 auto;
    padding: 24px;
}

.form-header {
    text-align: center;
    margin-bottom: 32px;
}

.form-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
}

.title-icon {
    font-size: 2rem;
}

.form-subtitle {
    color: #64748b;
    font-size: 1rem;
    margin: 0;
}

.modern-form {
    background: white;
    border-radius: 16px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1), 0 4px 20px rgba(0,0,0,0.05);
    overflow: hidden;
}

.form-section {
    padding: 32px;
    animation: fadeIn 0.4s ease;
}

.form-section.hidden {
    display: none;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.section-header {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    margin-bottom: 28px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f1f5f9;
}

.section-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
}

.section-header h2 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0 0 4px 0;
}

.section-desc {
    color: #64748b;
    font-size: 0.875rem;
    margin: 0;
}

.form-grid {
    display: grid;
    gap: 20px;
}

.form-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.form-field.full-width {
    grid-column: 1 / -1;
}

.field-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
    display: flex;
    align-items: center;
    gap: 4px;
}

.required {
    color: #ef4444;
}

.input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.input-icon {
    position: absolute;
    left: 14px;
    font-size: 1.125rem;
    color: #9ca3af;
    z-index: 1;
}

.modern-input {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 0.9375rem;
    transition: all 0.2s ease;
    background: #fafafa;
}

.modern-input:focus {
    outline: none;
    border-color: #f59e0b;
    background: white;
    box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1);
}

.modern-textarea {
    resize: vertical;
    min-height: 80px;
}

.textarea-wrapper .modern-input {
    padding: 12px 16px;
}

.select-wrapper .modern-input {
    appearance: none;
    cursor: pointer;
    padding-right: 40px;
}

.select-wrapper::after {
    content: '▼';
    position: absolute;
    right: 16px;
    color: #9ca3af;
    font-size: 0.75rem;
    pointer-events: none;
}

.field-hint {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.8125rem;
    color: #6b7280;
}

.hint-icon {
    font-size: 0.875rem;
}

.field-error {
    font-size: 0.8125rem;
    color: #ef4444;
    display: flex;
    align-items: center;
    gap: 4px;
}

.config-card {
    background: #f8fafc;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 20px;
    position: relative;
}

.config-card.primary {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    border-color: #f59e0b;
}

.config-badge {
    position: absolute;
    top: -10px;
    left: 20px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.config-badge.base {
    background: #f59e0b;
    color: white;
}

.conversions-panel {
    background: #fafaf9;
    border: 2px dashed #d6d3d1;
    border-radius: 12px;
    padding: 24px;
}

.panel-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
}

.panel-icon {
    font-size: 1.5rem;
}

.panel-header h3 {
    font-size: 1rem;
    font-weight: 600;
    color: #44403c;
    margin: 0 0 4px 0;
}

.panel-header p {
    font-size: 0.8125rem;
    color: #78716c;
    margin: 0;
}

.conversion-row {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    margin-bottom: 16px;
    padding: 16px;
    background: white;
    border-radius: 10px;
    border: 1px solid #e7e5e4;
}

.conversion-type {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    min-width: 60px;
}

.type-icon {
    font-size: 1.5rem;
}

.type-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #78716c;
    text-transform: uppercase;
}

.conversion-fields {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    flex: 1;
}

.rate-field .field-label {
    font-size: 0.8125rem;
}

.section-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 32px;
    padding-top: 24px;
    border-top: 1px solid #e5e7eb;
}

.section-actions.final-actions {
    justify-content: flex-end;
    gap: 12px;
}

.btn-prev, .btn-next, .btn-submit {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 10px;
    font-size: 0.9375rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    border: none;
}

.btn-prev {
    background: #f3f4f6;
    color: #4b5563;
}

.btn-prev:hover {
    background: #e5e7eb;
}

.btn-next {
    background: #f59e0b;
    color: white;
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}

.btn-next:hover {
    background: #d97706;
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(245, 158, 11, 0.4);
}

.btn-submit {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    font-size: 1rem;
    padding: 14px 32px;
    box-shadow: 0 4px 16px rgba(59, 130, 246, 0.3);
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
}

.btn-icon {
    font-size: 1.125rem;
}

.stock-panel {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.stock-input-wrapper {
    display: flex;
    gap: 12px;
    align-items: center;
}

.stock-input-wrapper.single {
    background: #f9fafb;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    padding: 4px;
}

.stock-number {
    flex: 1;
    text-align: center;
    font-size: 1.25rem;
    font-weight: 600;
    padding: 12px;
    border: none;
    background: transparent;
}

.stock-number:focus {
    box-shadow: none;
}

.stock-unit-display {
    padding: 12px 20px;
    font-size: 0.9375rem;
    font-weight: 500;
    color: #92400e;
    text-transform: lowercase;
}

.alert-field {
    background: #fef2f2;
    border: 2px solid #fecaca;
    border-radius: 12px;
    padding: 20px;
}

.alert-input-wrapper {
    display: flex;
    align-items: center;
    gap: 12px;
}

.alert-unit {
    font-size: 0.9375rem;
    color: #7f1d1d;
    font-weight: 500;
}

.status-field {
    display: flex;
    align-items: center;
    padding: 16px 20px;
    background: #f0fdf4;
    border: 2px solid #bbf7d0;
    border-radius: 12px;
}

.toggle-label {
    display: flex;
    align-items: center;
    gap: 16px;
    cursor: pointer;
    width: 100%;
}

.modern-toggle {
    display: none;
}

.toggle-slider {
    width: 56px;
    height: 28px;
    background: #d1d5db;
    border-radius: 14px;
    position: relative;
    transition: all 0.3s ease;
}

.toggle-slider::after {
    content: '';
    position: absolute;
    width: 24px;
    height: 24px;
    background: white;
    border-radius: 50%;
    top: 2px;
    left: 2px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.modern-toggle:checked + .toggle-slider {
    background: #22c55e;
}

.modern-toggle:checked + .toggle-slider::after {
    left: 30px;
}

.toggle-text {
    font-weight: 500;
    font-size: 0.9375rem;
}

.toggle-active {
    color: #15803d;
}

.toggle-inactive {
    color: #6b7280;
    display: none;
}

.modern-toggle:not(:checked) ~ .toggle-text .toggle-active {
    display: none;
}

.modern-toggle:not(:checked) ~ .toggle-text .toggle-inactive {
    display: inline;
}

@media (max-width: 640px) {
    .modern-form-container {
        padding: 16px;
    }
    
    .form-section {
        padding: 20px;
    }
    
    .conversion-fields {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
// Navigation entre étapes
function goToStep(step) {
    // Hide all sections
    document.querySelectorAll('.form-section').forEach(section => {
        section.classList.add('hidden');
    });
    
    // Show target section
    document.getElementById('section-' + step).classList.remove('hidden');
}

// Update labels when base unit changes
function updateUnitLabels() {
    const unitSelect = document.getElementById('unit');
    const baseUnit = unitSelect.value;
    
    // Update stock display unit
    document.getElementById('stock-unit-display').textContent = baseUnit + 's';
    
    // Update alert unit
    document.getElementById('alert-unit-label').textContent = baseUnit + 's';
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Add listener for unit change
    document.getElementById('unit').addEventListener('change', updateUnitLabels);
});
</script>
</x-app-layout>
