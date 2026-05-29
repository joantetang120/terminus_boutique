<x-app-layout title="Comptabilité">
    <div class="page-header">
        <h1>Comptabilité</h1>
        <div class="breadcrumb">
            <a href="{{ route('dashboard') }}">Accueil</a> > Finances > Comptabilité
        </div>
    </div>

    {{-- Overall Summary Cards --}}
    <div class="stats-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:24px;">
        <div class="stat-card">
            <div class="stat-icon" style="background:#E8F5EE;color:#1A7A4A;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" /></svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($totalRecettes, 0, ',', ' ') }} FCFA</div>
                <div class="stat-label">Total Recettes</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#FDECEA;color:#C0392B;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.423.331" /></svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($totalDepenses, 0, ',', ' ') }} FCFA</div>
                <div class="stat-label">Total Dépenses</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#EBF4FF;color:#2E75B6;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0012 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-3-.52l2.62 10.726c.122.499-.106 1.028-.589 1.202a5.988 5.988 0 01-2.153.043m-7.106-.043a5.988 5.988 0 01-2.153-.043c-.483-.174-.711-.703-.589-1.202L4.5 4.97" /></svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($totalSoldeNet, 0, ',', ' ') }} FCFA</div>
                <div class="stat-label">Solde Net Total</div>
            </div>
        </div>
    </div>

    {{-- Daily Summary Cards --}}
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
    </div>

    @if($tab !== 'modifications')
    {{-- Écritures Table --}}
    <div class="table-wrapper">
        {{-- AJOUT DE L'ID filter-form ICI --}}
        <form id="filter-form" method="GET" action="{{ route('comptabilite.index') }}" class="table-toolbar">
            <input type="hidden" name="tab" value="entries">
            <select name="type" class="form-select" style="width:160px;">
                <option value="">Type</option>
                <option value="recette" {{ request('type') === 'recette' ? 'selected' : '' }}>Recette</option>
                <option value="depense" {{ request('type') === 'depense' ? 'selected' : '' }}>Dépense</option>
            </select>
            <input type="date" name="date" class="form-input" style="width:160px;" value="{{ request('date') }}">
            <button type="submit" class="btn btn-secondary btn-sm">Filtrer</button>
        </form>

        <div class="relative inline-block text-left" x-data="{ open: false }" style="margin-bottom: 15px;">
            <button @click="open = !open" type="button" class="btn-export-navy">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="white" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                <span>Exporter</span>
                <svg class="ml-2 w-4 h-4" fill="none" stroke="white" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div x-show="open" 
                 @click.away="open = false" 
                 x-transition
                 class="export-dropdown-menu left-0">
                <div class="py-1">
                    <a href="javascript:void(0)" onclick="triggerExport('csv')" class="export-item">
                        <span class="icon-csv">CSV</span> Format Excel (.csv)
                    </a>
                    <a href="javascript:void(0)" onclick="triggerExport('pdf')" class="export-item">
                        <span class="icon-pdf">PDF</span> Format Document (.pdf)
                    </a>
                </div>
            </div>
        </div>

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
                    <td>
                        <div class="fw-medium">{{ $entry->description }}</div>
                        <div class="small text-muted">Par {{ $entry->createdBy?->name ?? 'N/A' }}</div>
                    </td>
                    <td><strong>{{ number_format($entry->amount, 0, ',', ' ') }} FCFA</strong></td>
                    <td>{{ $entry->reference_type ? '#' . $entry->reference_id : '—' }}</td>
                    <td>{{ $entry->createdBy->name ?? '—' }}</td>
                    <td>
                        @can('compta.edit')
                            @if($entry->type === 'depense')
                            <button class="btn btn-secondary btn-sm" x-data @click="$dispatch('open-edit-entry', { id: {{ $entry->id }}, amount: {{ $entry->amount }}, description: '{{ addslashes($entry->description) }}' })">
                                Modifier
                            </button>
                            @else
                            <span style="color:#94a3b8;font-size:0.875rem;">—</span>
                            @endif
                        @endcan
                    </td>
                </tr>
                @empty
                <tr class="table-empty"><td colspan="7">Aucune écriture.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination-wrapper">
            {{ $entries->links() }}
        </div>
    </div>
    @endif

    {{-- Modals (Edit & Reject) restants... --}}

    <style>
        .btn-export-navy { background-color: #1e3a8a; color: #ffffff !important; padding: 8px 16px; border-radius: 8px; font-weight: 600; display: flex; align-items: center; border: none; cursor: pointer; }
        .export-dropdown-menu { position: absolute; left: 0; margin-top: 8px; min-width: 220px; background: white; border-radius: 10px; z-index: 9999; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2); border: 1px solid #e5e7eb; }
        .export-item { display: flex; align-items: center; padding: 10px 15px; font-size: 14px; color: #374151; text-decoration: none; }
        .export-item:hover { background-color: #f3f4f6; color: #1e3a8a; }
        .icon-csv { background: #dcfce7; color: #166534; font-size: 10px; padding: 2px 4px; border-radius: 4px; margin-right: 10px; font-weight: bold; }
        .icon-pdf { background: #fee2e2; color: #991b1b; font-size: 10px; padding: 2px 4px; border-radius: 4px; margin-right: 10px; font-weight: bold; }
    </style>

    {{-- JAVASCRIPT FINAL --}}
    <script>
       function triggerExport(format) {
    const filterForm = document.querySelector('#filter-form');
    let params = new URLSearchParams();
    if (filterForm) {
        const formData = new FormData(filterForm);
        params = new URLSearchParams(formData);
    }
    params.append('format', format);
    params.append('source', 'ecritures');

    const url = "{{ route('reports.export') }}?" + params.toString();

    if (format === 'pdf') {
    window.location.href = "{{ route('reports.export.pdf') }}?" + params.toString();
} else {
    window.location.href = url;
}
}
    </script>
</x-app-layout>