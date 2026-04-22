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
                @can('facture.print')
                <a href="{{ route('factures.preview', $facture) }}" class="btn btn-secondary btn-sm" target="_blank" title="Imprimer la facture">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline;vertical-align:middle;margin-right:4px;">
                        <path d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 18v5h12v-5M6 18h12"></path>
                    </svg>
                    Imprimer
                </a>
                @endcan
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
                    <span class="badge {{ $facture->getStatusBadgeClass() }}">
                        {{ $facture->getStatusLabel() }}
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
                    <td>{{ $item->unit_sold }}</td>
                    <td>{{ number_format($item->quantity_sold, 2, ',', ' ') }}</td>
                    <td>{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                    <td><strong>{{ number_format($item->total_price, 0, ',', ' ') }} FCFA</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top:24px;display:flex;flex-direction:column;gap:8px;align-items:flex-end;">
            <div><strong>Total:</strong> {{ number_format($facture->total, 0, ',', ' ') }} FCFA</div>
            @if($facture->paid_amount > 0)
            <div><strong>Payé:</strong> {{ number_format($facture->paid_amount, 0, ',', ' ') }} FCFA</div>
            @endif
            @if($facture->balance > 0 && $facture->status !== 'SOLDEE')
            <div><strong style="color:#C0392B;">Solde dû:</strong> <span class="badge badge-warning">{{ number_format($facture->balance, 0, ',', ' ') }} FCFA</span></div>
            @endif
            <div style="margin-top:8px;font-size:0.875rem;color:#64748B;">
                <strong>Échéance:</strong> {{ $facture->due_date->format('d/m/Y') }}
            </div>
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

    {{-- Cancel Modal --}}
    <div x-data="{ open: false, id: null, number: '', reason: '' }"
         @open-cancel-modal.window="open = true; id = $event.detail.id; number = $event.detail.number; reason = ''"
         x-show="open"
         x-cloak
         class="modal-backdrop"
         style="display:none;">
        <div class="modal" @click.away="open = false">
            <div class="modal-header" style="background:#FDECEA;border-bottom:1px solid #F5B7B1;">
                <h3 style="color:#C0392B;">⚠ Confirmer l'annulation</h3>
                <button @click="open = false" style="background:none;border:none;cursor:pointer;font-size:1.25rem;color:#C0392B;">&times;</button>
            </div>
            <form :action="'/factures/' + id + '/cancel'" method="POST">
                @csrf
                <div class="modal-body">
                    <p style="margin-bottom:16px;color:#64748B;">Êtes-vous sûr de vouloir annuler la facture <strong x-text="number"></strong> ? Cette action est irréversible.</p>

                    {{-- Warning message --}}
                    <div style="margin-bottom:16px;padding:12px 16px;background:#FEF3C7;border:1px solid #FCD34D;border-radius:6px;">
                        <div style="display:flex;align-items:flex-start;gap:12px;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2" style="flex-shrink:0;margin-top:2px;">
                                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                                <line x1="12" y1="9" x2="12" y2="13"></line>
                                <line x1="12" y1="17" x2="12.01" y2="17"></line>
                            </svg>
                            <div style="font-size:0.875rem;color:#92400E;">
                                <strong>Attention :</strong> Les articles seront remis en stock automatiquement. Les paiements déjà enregistrés restent dans l'historique comptable.
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="cancel_reason_show" style="color:#374151;font-weight:500;">Motif d'annulation <span style="color:#DC2626;">*</span></label>
                        <textarea
                            class="form-textarea"
                            id="cancel_reason_show"
                            name="cancel_reason"
                            x-model="reason"
                            required
                            rows="3"
                            placeholder="Décrivez la raison de l'annulation (minimum 10 caractères)..."></textarea>
                        <p style="margin-top:4px;font-size:0.75rem;color:#6B7280;"><span x-text="reason.length"></span> caractères (min. 10 requis)</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="open = false">Fermer</button>
                    <button
                        type="submit"
                        class="btn btn-danger"
                        :disabled="reason.length < 10"
                        :style="reason.length < 10 ? 'opacity:0.5;cursor:not-allowed;' : ''">
                        Confirmer l'annulation
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
