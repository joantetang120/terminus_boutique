<x-app-layout title="Modifier produit">
    <div class="page-header">
        <h1>Modifier produit</h1>
        <div class="breadcrumb">
            <a href="{{ route('dashboard') }}">Accueil</a> > <a href="{{ route('produits.index') }}">Produits</a> > Modifier
        </div>
    </div>

    <div class="card" style="max-width:600px;">
        <form action="{{ route('produits.update', $produit) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label" for="name">Nom du produit</label>
                <input class="form-input" type="text" id="name" name="name" value="{{ old('name', $produit->name) }}" required>
                @error('name')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Description (optionnel)</label>
                <textarea class="form-textarea" id="description" name="description" rows="3">{{ old('description', $produit->description) }}</textarea>
                @error('description')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="unit">Unité</label>
                <select class="form-select" id="unit" name="unit" required>
                    <option value="piece" {{ old('unit', $produit->unit) === 'piece' ? 'selected' : '' }}>Pièce</option>
                    <option value="paquet" {{ old('unit', $produit->unit) === 'paquet' ? 'selected' : '' }}>Paquet</option>
                    <option value="boite" {{ old('unit', $produit->unit) === 'boite' ? 'selected' : '' }}>Boîte</option>
                    <option value="carton" {{ old('unit', $produit->unit) === 'carton' ? 'selected' : '' }}>Carton</option>
                </select>
                @error('unit')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="form-group">
                    <label class="form-label" for="current_stock">Stock actuel</label>
                    <input class="form-input" type="number" id="current_stock" name="current_stock" value="{{ old('current_stock', $produit->current_stock) }}" min="0" step="1" required>
                    @error('current_stock')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="alert_threshold">Seuil d'alerte</label>
                    <input class="form-input" type="number" id="alert_threshold" name="alert_threshold" value="{{ old('alert_threshold', $produit->alert_threshold) }}" min="0" step="1" required>
                    @error('alert_threshold')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-checkbox" style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $produit->is_active) ? 'checked' : '' }}>
                    <span>Produit actif</span>
                </label>
                @error('is_active')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div style="display:flex;gap:12px;margin-top:16px;">
                <a href="{{ route('produits.index') }}" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">Mettre à jour</button>
            </div>
        </form>
    </div>
</x-app-layout>
