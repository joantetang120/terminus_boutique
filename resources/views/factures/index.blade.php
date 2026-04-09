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

    <div class="table-wrapper">
        <form method="GET" action="{{ route('factures.index') }}" class="table-toolbar">
            <input type="text" name="search" class="form-input search-input" placeholder="Rechercher..." value="{{ request('search') }}">
            <select name="type" class="form-select" style="width:160px;">
                <option value="">Type</option>
                <option value="comptant" {{ request('type') === 'comptant' ? 'selected' : '' }}>Comptant</option>
                <option value="credit" {{ request('type') === 'credit' ? 'selected' : '' }}>Crédit</option>
                <option value="avance" {{ request('type') === 'avance' ? 'selected' : '' }}>Avance</option>
            </select>
            <select name="status" class="form-select" style="width:160px;">
                <option value="">Statut</option>
                <option value="payee" {{ request('status') === 'payee' ? 'selected' : '' }}>Payée</option>
                <option value="credit" {{ request('status') === 'credit' ? 'selected' : '' }}>Crédit</option>
                <option value="avance" {{ request('status') === 'avance' ? 'selected' : '' }}>Avance</option>
                <option value="annulee" {{ request('status') === 'annulee' ? 'selected' : '' }}>Annulée</option>
            </select>
            <input type="date" name="date" class="form-input" style="width:160px;" value="{{ request('date') }}">
            <button type="submit" class="btn btn-secondary btn-sm">Filtrer</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>N° Facture</th>
                    <th>Client</th>
                    <th>Type</th>
                    <th>Total</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th>Créée par</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                <tr>
                    <td><strong>{{ $invoice->number }}</strong></td>
                    <td>{{ $invoice->client_name }}</td>
                    <td>
                        @if($invoice->type === 'comptant')
                            <span class="badge badge-info">Comptant</span>
                        @elseif($invoice->type === 'credit')
                            <span class="badge badge-warning">Crédit</span>
                        @elseif($invoice->type === 'avance')
                            <span class="badge badge-accent">Avance</span>
                        @endif
                    </td>
                    <td>{{ number_format($invoice->total, 0, ',', ' ') }} FCFA</td>
                    <td>
                        @if($invoice->status === 'payee')
                            <span class="badge badge-success">Payée</span>
                        @elseif($invoice->status === 'credit')
                            <span class="badge badge-warning">Crédit</span>
                        @elseif($invoice->status === 'avance')
                            <span class="badge badge-accent">Avance</span>
                        @elseif($invoice->status === 'annulee')
                            <span class="badge badge-danger">Annulée</span>
                        @endif
                    </td>
                    <td>{{ $invoice->created_at->format('d/m/Y') }}</td>
                    <td>{{ $invoice->createdBy->name ?? '—' }}</td>
                    <td>
                        <div class="table-actions">
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
                    <td colspan="8">Aucune facture.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div style="padding:16px;">
            {{ $invoices->links() }}
        </div>
    </div>

    {{-- Cancel Modal --}}
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
