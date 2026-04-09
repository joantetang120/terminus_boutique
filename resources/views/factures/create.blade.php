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
                            <label class="form-label" for="client_name">Nom du client</label>
                            <input class="form-input" type="text" id="client_name" name="client_name" value="{{ old('client_name') }}" required>
                            @error('client_name')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="client_phone">Téléphone (optionnel)</label>
                            <input class="form-input" type="text" id="client_phone" name="client_phone" value="{{ old('client_phone') }}">
                        </div>
                    </div>

                    <h3 style="font-size:1rem;font-weight:600;margin:16px 0;">Type de facture</h3>
                    <div style="display:flex;gap:16px;">
                        <label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
                            <input type="radio" name="type" value="comptant" x-model="invoiceType" checked> Comptant
                        </label>
                        <label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
                            <input type="radio" name="type" value="credit" x-model="invoiceType"> Crédit
                        </label>
                        <label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
                            <input type="radio" name="type" value="avance" x-model="invoiceType"> Avance
                        </label>
                    </div>
                </div>

                <div class="card">
                    <h3 style="font-size:1rem;font-weight:600;margin-bottom:16px;">Articles</h3>

                    <template x-for="(item, index) in items" :key="index">
                        <div style="display:grid;grid-template-columns:2fr 80px 80px 100px 40px;gap:8px;margin-bottom:8px;align-items:end;">
                            <div class="form-group" style="margin:0;">
                                <label class="form-label" style="font-size:0.75rem;">Désignation</label>
                                <input class="form-input" type="text" :name="'items[' + index + '][designation]'" x-model="item.designation" required style="padding:8px 10px;font-size:0.8125rem;">
                            </div>
                            <div class="form-group" style="margin:0;">
                                <label class="form-label" style="font-size:0.75rem;">Unité</label>
                                <select class="form-select" :name="'items[' + index + '][unit]'" x-model="item.unit" style="padding:8px 10px;font-size:0.8125rem;">
                                    <option value="piece">Pièce</option>
                                    <option value="paquet">Paquet</option>
                                    <option value="boite">Boîte</option>
                                    <option value="carton">Carton</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin:0;">
                                <label class="form-label" style="font-size:0.75rem;">Qté</label>
                                <input class="form-input" type="number" :name="'items[' + index + '][quantity]'" x-model.number="item.quantity" min="0.01" step="0.01" required style="padding:8px 10px;font-size:0.8125rem;">
                            </div>
                            <div class="form-group" style="margin:0;">
                                <label class="form-label" style="font-size:0.75rem;">Prix unit.</label>
                                <input class="form-input" type="number" :name="'items[' + index + '][unit_price]'" x-model.number="item.unit_price" min="0" step="1" required style="padding:8px 10px;font-size:0.8125rem;">
                            </div>
                            <button type="button" @click="removeItem(index)" style="background:none;border:none;cursor:pointer;color:#C0392B;font-size:1.25rem;padding:8px;">×</button>
                        </div>
                    </template>

                    <button type="button" @click="addItem()" class="btn btn-secondary btn-sm" style="margin-top:8px;">+ Ajouter ligne</button>

                    <template x-if="invoiceType === 'avance'">
                        <div class="form-group" style="margin-top:16px;">
                            <label class="form-label" for="advance_amount">Montant avance</label>
                            <input class="form-input" type="number" id="advance_amount" name="advance_amount" x-model.number="advanceAmount" min="0" step="1">
                        </div>
                    </template>

                    <div class="form-group" style="margin-top:16px;">
                        <label class="form-label" for="note">Note</label>
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
                            <span style="color:#64748B;">Sous-total:</span>
                            <strong x-text="formatCurrency(subtotal())"></strong>
                        </div>
                        <template x-if="invoiceType === 'avance'">
                            <div style="display:flex;justify-content:space-between;">
                                <span style="color:#64748B;">Avance:</span>
                                <span x-text="formatCurrency(advanceAmount)"></span>
                            </div>
                        </template>
                        <div style="display:flex;justify-content:space-between;padding-top:12px;border-top:1px solid #E2E8F0;">
                            <span style="font-weight:600;">Solde dû:</span>
                            <strong x-text="formatCurrency(balance())" style="color:#C0392B;"></strong>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width:100%;margin-top:20px;height:44px;">Enregistrer</button>
                </div>
            </div>
        </div>
    </form>

    <script>
        function invoiceForm() {
            return {
                invoiceType: 'comptant',
                advanceAmount: 0,
                items: [{ designation: '', unit: 'piece', quantity: 1, unit_price: 0 }],
                addItem() {
                    this.items.push({ designation: '', unit: 'piece', quantity: 1, unit_price: 0 });
                },
                removeItem(index) {
                    if (this.items.length > 1) this.items.splice(index, 1);
                },
                subtotal() {
                    return this.items.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0);
                },
                balance() {
                    const total = this.subtotal();
                    return this.invoiceType === 'avance' ? total - this.advanceAmount : (this.invoiceType === 'comptant' ? 0 : total);
                },
                formatCurrency(amount) {
                    return new Intl.NumberFormat('fr-FR').format(Math.round(amount)) + ' FCFA';
                }
            };
        }
    </script>
</x-app-layout>
