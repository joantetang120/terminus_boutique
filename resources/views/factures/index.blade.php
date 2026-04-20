<x-app-layout title="Factures">
    <div class="page-header">
        <div class="page-header-actions">
            <div>
                <h1>Factures</h1>
                <div class="breadcrumb">
                    <a href="{{ route('dashboard') }}">Accueil</a> > Ventes > Factures
                </div>
            </div>
            @can('facture.create')
            <a href="{{ route('factures.create') }}" class="btn btn-primary">+ Nouvelle facture</a>
            @endcan
        </div>
    </div>

    {{-- Summary indicator --}}
    @if($unpaidCount > 0)
    <div style="margin-bottom:16px;padding:12px 16px;background:#FFF3CD;border:1px solid #FFEAA7;border-radius:6px;display:flex;align-items:center;gap:12px;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#F39C12" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
        </svg>
        <div>
            <strong style="color:#B7791F;">{{ $unpaidCount }} facture{{ $unpaidCount > 1 ? 's' : '' }} impayée{{ $unpaidCount > 1 ? 's' : '' }}</strong>
            <span style="color:#7D6608;"> — Total soldes restants: <strong>{{ number_format($unpaidTotal, 0, ',', ' ') }} FCFA</strong></span>
        </div>
    </div>
    @endif

    <div class="table-wrapper">
        <form method="GET" action="{{ route('factures.index') }}" class="table-toolbar" style="flex-wrap:wrap;gap:8px;">
            <input type="text" name="search" class="form-input search-input" placeholder="Rechercher..." value="{{ request('search') }}">
            <select name="status" class="form-select" style="width:140px;">
                <option value="">Statut</option>
                <option value="IMPAYEE" {{ request('status') === 'IMPAYEE' ? 'selected' : '' }}>Impayée</option>
                <option value="PARTIELLE" {{ request('status') === 'PARTIELLE' ? 'selected' : '' }}>Partielle</option>
                <option value="SOLDEE" {{ request('status') === 'SOLDEE' ? 'selected' : '' }}>Soldée</option>
                <option value="ANNULEE" {{ request('status') === 'ANNULEE' ? 'selected' : '' }}>Annulée</option>
            </select>
            <div style="display:flex;align-items:center;gap:4px;">
                <input type="date" name="date_from" class="form-input" style="width:130px;" value="{{ request('date_from') }}" title="Du">
                <span style="color:#64748B;">→</span>
                <input type="date" name="date_to" class="form-input" style="width:130px;" value="{{ request('date_to') }}" title="Au">
            </div>
            <input type="text" name="client" class="form-input" style="width:140px;" placeholder="Client..." value="{{ request('client') }}">
            <a href="{{ route('factures.index') }}" class="btn btn-sm" style="background:#e2e8f0;color:#475569;">Réinit.</a>
            <button type="submit" class="btn btn-secondary btn-sm">Filtrer</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Date</th>
                    <th>Client</th>
                    <th>Total</th>
                    <th>Payé</th>
                    <th>Solde</th>
                    <th>Échéance</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                @php
                    $dueDiff = now()->diffInDays($invoice->due_date, false);
                @endphp
                <tr class="clickable-row" onclick="window.location.href='{{ route('factures.show', $invoice) }}'" style="cursor:pointer;">
                    <td><strong>{{ $invoice->number }}</strong></td>
                    <td>{{ $invoice->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $invoice->client_name }}</td>
                    <td>{{ number_format($invoice->total, 0, ',', ' ') }} FCFA</td>
                    <td>{{ number_format($invoice->paid_amount, 0, ',', ' ') }} FCFA</td>
                    <td>
                        @if($invoice->balance > 0 && !$invoice->isCancelled())
                            <strong style="color:#C0392B;">{{ number_format($invoice->balance, 0, ',', ' ') }} FCFA</strong>
                        @else
                            {{ number_format($invoice->balance, 0, ',', ' ') }} FCFA
                        @endif
                    </td>
                    <td>
                        @php
                            $dueStyle = '';
                            if ($invoice->isCancelled()) {
                                $dueStyle = 'text-decoration:line-through;color:#999;';
                            } elseif ($dueDiff < 0) {
                                $dueStyle = 'color:#C0392B;';
                            } elseif ($dueDiff <= 3) {
                                $dueStyle = 'color:#E67E22;';
                            }
                        @endphp
                        <span style="{{ $dueStyle }}">
                            {{ $invoice->due_date->format('d/m/Y') }}
                            @if($dueDiff < 0 && !$invoice->isCancelled())
                                <small style="display:block;">({{ abs($dueDiff) }}j retard)</small>
                            @elseif($dueDiff <= 3 && $dueDiff >= 0 && !$invoice->isPaid() && !$invoice->isCancelled())
                                <small style="display:block;">({{ $dueDiff }}j restants)</small>
                            @endif
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $invoice->getStatusBadgeClass() }}" style="{{ $invoice->isCancelled() ? 'text-decoration:line-through;' : '' }}">
                            {{ $invoice->getStatusLabel() }}
                        </span>
                    </td>
                    <td>
                        <div class="table-actions" onclick="event.stopPropagation();">
                            <a href="{{ route('factures.show', $invoice) }}" class="btn btn-secondary btn-sm">Voir</a>
                            @can('facture.cancel')
                                @if(!$invoice->isCancelled())
                                <button class="btn btn-danger btn-sm"
                                        x-data
                                        @click="$dispatch('open-cancel-modal', { id: {{ $invoice->id }}, number: '{{ $invoice->number }}' })">
                                    Annuler
                                </button>
                                @endif
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="table-empty">
                    <td colspan="9">Aucune facture.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div style="padding:16px;">
            {{ $invoices->links() }}
        </div>
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
                        <label class="form-label" for="cancel_reason" style="color:#374151;font-weight:500;">Motif d'annulation <span style="color:#DC2626;">*</span></label>
                        <textarea
                            class="form-textarea"
                            id="cancel_reason"
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
