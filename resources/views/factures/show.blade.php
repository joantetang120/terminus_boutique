<x-app-layout title="Facture {{ $facture->number }}">
    <div class="page-header">
        <div class="page-header-actions">
            <div>
                <h1>Facture {{ $facture->number }}</h1>
                <div class="breadcrumb">
                    <a href="{{ route('dashboard') }}">Accueil</a> > <a href="{{ route('factures.index') }}">Factures</a> > {{ $facture->number }}
                </div>
            </div>
            <div style="display:flex;gap:8px;">
                @can('facture.cancel')
                    @if(!$facture->isCancelled())
                    <button class="btn btn-danger btn-sm"
                            x-data
                            @click="$dispatch('open-cancel-modal', { id: {{ $facture->id }}, number: '{{ $facture->number }}' })">
                        Annuler
                    </button>
                    @endif
                @endcan
            </div>
        </div>
    </div>

    <div class="card" style="margin-bottom:24px;">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:24px;">
            <div>
                <h3 style="font-size:0.875rem;font-weight:600;color:#64748B;margin-bottom:8px;">Informations</h3>
                <div style="display:flex;flex-direction:column;gap:8px;">
                    <div><strong>Client:</strong> {{ $facture->client_name }}</div>
                    @if($facture->client_phone)<div><strong>Téléphone:</strong> {{ $facture->client_phone }}</div>@endif
                    <div><strong>Date:</strong> {{ $facture->created_at->format('d/m/Y H:i') }}</div>
                    <div><strong>Créée par:</strong> {{ $facture->createdBy->name ?? '—' }}</div>
                </div>
            </div>
            <div>
                <h3 style="font-size:0.875rem;font-weight:600;color:#64748B;margin-bottom:8px;">Statut</h3>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <span class="badge {{ $facture->type === 'comptant' ? 'badge-info' : ($facture->type === 'credit' ? 'badge-warning' : 'badge-accent') }}">
                        {{ ucfirst($facture->type) }}
                    </span>
                    <span class="badge {{ $facture->status === 'payee' ? 'badge-success' : ($facture->status === 'credit' ? 'badge-warning' : ($facture->status === 'avance' ? 'badge-accent' : 'badge-danger')) }}">
                        {{ $facture->status === 'payee' ? 'Payée' : ($facture->status === 'credit' ? 'Crédit' : ($facture->status === 'avance' ? 'Avance' : 'Annulée')) }}
                    </span>
                </div>
            </div>
        </div>

        <h3 style="font-size:1rem;font-weight:600;margin-bottom:16px;">Articles</h3>
        <table>
            <thead>
                <tr>
                    <th>Désignation</th>
                    <th>Unité</th>
                    <th>Quantité</th>
                    <th>Prix unitaire</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($facture->items as $item)
                <tr>
                    <td>{{ $item->designation }}</td>
                    <td>{{ $item->unit }}</td>
                    <td>{{ number_format($item->quantity, 2, ',', ' ') }}</td>
                    <td>{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                    <td><strong>{{ number_format($item->total_price, 0, ',', ' ') }} FCFA</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top:24px;display:flex;flex-direction:column;gap:8px;align-items:flex-end;">
            <div><strong>Total:</strong> {{ number_format($facture->total, 0, ',', ' ') }} FCFA</div>
            @if($facture->advance_amount > 0)
            <div><strong>Avance:</strong> {{ number_format($facture->advance_amount, 0, ',', ' ') }} FCFA</div>
            @endif
            @if($facture->balance > 0)
            <div><strong style="color:#C0392B;">Solde dû:</strong> <span class="badge badge-warning">{{ number_format($facture->balance, 0, ',', ' ') }} FCFA</span></div>
            @endif
        </div>

        @if($facture->note)
        <div style="margin-top:16px;padding:12px;background:#F8FAFC;border-radius:8px;">
            <strong>Note:</strong> {{ $facture->note }}
        </div>
        @endif

        @if($facture->isCancelled())
        <div style="margin-top:16px;padding:12px;background:#FDECEA;border-radius:8px;border:1px solid #F5B7B1;">
            <strong style="color:#C0392B;">Annulée</strong> par {{ $facture->cancelledBy->name ?? '—' }} le {{ $facture->cancelled_at?->format('d/m/Y H:i') }}
            <br><strong>Motif:</strong> {{ $facture->cancel_reason }}
        </div>
        @endif
    </div>

    {{-- Cancel Modal (same as index) --}}
    <div x-data="{ open: false, id: null, number: '' }"
         @open-cancel-modal.window="open = true; id = $event.detail.id; number = $event.detail.number"
         x-show="open"
         x-cloak
         class="modal-backdrop"
         style="display:none;">
        <div class="modal" @click.away="open = false">
            <div class="modal-header">
                <h3>⚠ Confirmer l'annulation</h3>
                <button @click="open = false" style="background:none;border:none;cursor:pointer;font-size:1.25rem;">&times;</button>
            </div>
            <form :action="'/factures/' + id + '/annuler'" method="POST">
                @csrf
                <div class="modal-body">
                    <p style="margin-bottom:16px;color:#64748B;">Êtes-vous sûr de vouloir annuler la facture <strong x-text="number"></strong> ? Cette action est irréversible.</p>
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
