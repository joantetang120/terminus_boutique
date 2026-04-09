<x-app-layout title="Nouveau produit">
    <div class="page-header">
        <h1>Nouveau produit</h1>
        <div class="breadcrumb">
            <a href="{{ route('dashboard') }}">Accueil</a> > <a href="{{ route('produits.index') }}">Produits</a> > Nouveau
        </div>
    </div>

    <div class="card" style="max-width:600px;">
        <form action="{{ route('produits.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label" for="name">Nom du produit</label>
                <input class="form-input" type="text" id="name" name="name" value="{{ old('name') }}" required>
                @error('name')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="unit">Unité</label>
                <select class="form-select" id="unit" name="unit" required>
                    <option value="piece">Pièce</option>
                    <option value="paquet">Paquet</option>
                    <option value="boite">Boîte</option>
                    <option value="carton">Carton</option>
                </select>
                @error('unit')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="form-group">
                    <label class="form-label" for="current_stock">Stock initial</label>
                    <input class="form-input" type="number" id="current_stock" name="current_stock" value="{{ old('current_stock', 0) }}" min="0" step="0.01" required>
                    @error('current_stock')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="alert_threshold">Seuil d'alerte</label>
                    <input class="form-input" type="number" id="alert_threshold" name="alert_threshold" value="{{ old('alert_threshold', 0) }}" min="0" step="0.01" required>
                    @error('alert_threshold')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div style="display:flex;gap:12px;margin-top:16px;">
                <a href="{{ route('produits.index') }}" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</x-app-layout>
