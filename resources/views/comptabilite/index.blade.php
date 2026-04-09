<x-app-layout title="Comptabilité">
    <div class="page-header">
        <h1>Comptabilité</h1>
        <div class="breadcrumb">
            <a href="{{ route('dashboard') }}">Accueil</a> > Finances > Comptabilité
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="stats-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:24px;">
        <div class="stat-card">
            <div class="stat-icon" style="background:#E8F5EE;color:#1A7A4A;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" /></svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($todayRecettes, 0, ',', ' ') }} FCFA</div>
                <div class="stat-label">Recettes du jour</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#FDECEA;color:#C0392B;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.423.331" /></svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($todayDepenses, 0, ',', ' ') }} FCFA</div>
                <div class="stat-label">Dépenses du jour</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#EBF4FF;color:#2E75B6;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0012 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-3-.52l2.62 10.726c.122.499-.106 1.028-.589 1.202a5.988 5.988 0 01-2.153.043m-7.106-.043a5.988 5.988 0 01-2.153-.043c-.483-.174-.711-.703-.589-1.202L4.5 4.97" /></svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($soldeNet, 0, ',', ' ') }} FCFA</div>
                <div class="stat-label">Solde net</div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div style="display:flex;gap:8px;margin-bottom:24px;">
        <a href="{{ route('comptabilite.index', ['tab' => 'entries']) }}" 
           class="btn {{ $tab !== 'modifications' ? 'btn-primary' : 'btn-secondary' }}">
            Écritures
        </a>
        <a href="{{ route('comptabilite.index', ['tab' => 'modifications']) }}" 
           class="btn {{ $tab === 'modifications' ? 'btn-primary' : 'btn-secondary' }}">
            Modifications en attente
            @if($pendingCount > 0)
            <span class="badge badge-danger" style="margin-left:4px;">{{ $pendingCount }}</span>
            @endif
        </a>
        <button class="btn btn-secondary" x-data @click="$dispatch('open-entry-modal')">+ Nouvelle écriture</button>
    </div>

    @if($tab !== 'modifications')
    {{-- Écritures Table --}}
    <div class="table-wrapper">
        <form method="GET" action="{{ route('comptabilite.index') }}" class="table-toolbar">
            <input type="hidden" name="tab" value="entries">
            <select name="type" class="form-select" style="width:160px;">
                <option value="">Type</option>
                <option value="recette" {{ request('type') === 'recette' ? 'selected' : '' }}>Recette</option>
                <option value="depense" {{ request('type') === 'depense' ? 'selected' : '' }}>Dépense</option>
            </select>
            <input type="date" name="date" class="form-input" style="width:160px;" value="{{ request('date') }}">
            <button type="submit" class="btn btn-secondary btn-sm">Filtrer</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Montant</th>
                    <th>Référence</th>
                    <th>Par</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($entries as $entry)
                <tr>
                    <td>{{ $entry->date->format('d/m/Y') }}</td>
                    <td>
                        @if($entry->type === 'recette')
                            <span class="badge badge-success">Recette</span>
                        @else
                            <span class="badge badge-danger">Dépense</span>
                        @endif
                    </td>
                    <td>{{ Str::limit($entry->description, 50) }}</td>
                    <td><strong>{{ number_format($entry->amount, 0, ',', ' ') }} FCFA</strong></td>
                    <td>{{ $entry->reference_type ? '#' . $entry->reference_id : '—' }}</td>
                    <td>{{ $entry->createdBy->name ?? '—' }}</td>
                    <td>
                        @can('compta.edit')
                        <button class="btn btn-secondary btn-sm" 
                                x-data 
                                @click="$dispatch('open-edit-entry', { id: {{ $entry->id }}, amount: {{ $entry->amount }}, description: '{{ addslashes($entry->description) }}' })">
                            Modifier
                        </button>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr class="table-empty">
                    <td colspan="7">Aucune écriture.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:16px;">{{ $entries->links() }}</div>
    </div>
    @else
    {{-- Modifications Table --}}
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Date demande</th>
                    <th>Écriture</th>
                    <th>Demandé par</th>
                    <th>Ancien</th>
                    <th>Nouveau</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($modifications as $mod)
                <tr>
                    <td>{{ $mod->requested_at->format('d/m/Y H:i') }}</td>
                    <td>{{ Str::limit($mod->entry->description ?? '', 30) }}</td>
                    <td>{{ $mod->requestedBy->name ?? '—' }}</td>
                    <td>{{ number_format($mod->old_values['amount'] ?? 0, 0, ',', ' ') }} FCFA</td>
                    <td>{{ number_format($mod->new_values['amount'] ?? 0, 0, ',', ' ') }} FCFA</td>
                    <td>
                        @if($mod->status === 'pending')
                            <span class="badge badge-warning">En attente</span>
                        @elseif($mod->status === 'approved')
                            <span class="badge badge-success">Approuvée</span>
                        @else
                            <span class="badge badge-danger">Rejetée</span>
                        @endif
                    </td>
                    <td>
                        @can('compta.approve')
                        @if($mod->isPending())
                        <form action="{{ route('compta.approuver', $mod) }}" method="POST" style="display:inline;">
                            @csrf
                            <button class="btn btn-success btn-sm">✔</button>
                        </form>
                        <button class="btn btn-danger btn-sm" x-data @click="$dispatch('open-reject-mod', {id: {{ $mod->id }}})">✘</button>
                        @endif
                        @endcan
                    </td>
                </tr>
                @empty
                <tr class="table-empty"><td colspan="7">Aucune modification.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:16px;">{{ $modifications->links() }}</div>
    </div>
    @endif

    {{-- Entry Modal --}}
    <div x-data="{ open: false }" @open-entry-modal.window="open = true" x-show="open" x-cloak class="modal-backdrop" style="display:none;">
        <div class="modal" @click.away="open = false">
            <div class="modal-header"><h3>Nouvelle écriture</h3><button @click="open = false" style="background:none;border:none;cursor:pointer;font-size:1.25rem;">&times;</button></div>
            <form action="{{ route('comptabilite.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Type</label>
                        <select class="form-select" name="type" required>
                            <option value="recette">Recette</option>
                            <option value="depense">Dépense</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Montant (FCFA)</label>
                        <input class="form-input" type="number" name="amount" min="0.01" step="1" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea class="form-textarea" name="description" required rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Date</label>
                        <input class="form-input" type="date" name="date" value="{{ today()->format('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="open = false">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Entry Modal --}}
    <div x-data="{ open: false, id: null, amount: 0, description: '' }"
         @open-edit-entry.window="open=true;id=$event.detail.id;amount=$event.detail.amount;description=$event.detail.description"
         x-show="open" x-cloak class="modal-backdrop" style="display:none;">
        <div class="modal" @click.away="open = false">
            <div class="modal-header"><h3>Modifier l'écriture</h3><button @click="open = false" style="background:none;border:none;cursor:pointer;font-size:1.25rem;">&times;</button></div>
            <form :action="'/comptabilite/' + id" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Montant</label>
                        <input class="form-input" type="number" name="amount" :value="amount" min="0.01" step="1" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea class="form-textarea" name="description" x-model="description" required rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="open = false">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Reject Modal --}}
    <div x-data="{ open: false, id: null }" @open-reject-mod.window="open=true;id=$event.detail.id" x-show="open" x-cloak class="modal-backdrop" style="display:none;">
        <div class="modal" @click.away="open = false">
            <div class="modal-header"><h3>Rejeter la modification</h3><button @click="open = false" style="background:none;border:none;cursor:pointer;font-size:1.25rem;">&times;</button></div>
            <form :action="'/comptabilite/modifications/' + id + '/rejeter'" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Motif</label>
                        <textarea class="form-textarea" name="review_note" required rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="open = false">Annuler</button>
                    <button type="submit" class="btn btn-danger">Rejeter</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
