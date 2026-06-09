<x-app-layout title="Marges">
    <div class="page-header">
        <h1>Marges</h1>
        <div class="breadcrumb">
            <a href="{{ route('dashboard') }}">Accueil</a> > Finances > Marges
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="stats-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:24px;">
        <div class="stat-card">
            <div class="stat-icon" style="background:#EBF4FF;color:#2E75B6;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.423.331" /></svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($totalCa, 0, ',', ' ') }} FCFA</div>
                <div class="stat-label">Chiffre d'Affaires</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#FDECEA;color:#C0392B;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" /></svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($totalCost, 0, ',', ' ') }} FCFA</div>
                <div class="stat-label">Coût Total</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#E8F5EE;color:#1A7A4A;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0012 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-3-.52l2.62 10.726c.122.499-.106 1.028-.589 1.202a5.988 5.988 0 01-2.153.043m-7.106-.043a5.988 5.988 0 01-2.153-.043c-.483-.174-.711-.703-.589-1.202L4.5 4.97" /></svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($totalMargin, 0, ',', ' ') }} FCFA</div>
                <div class="stat-label">Marge Totale</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#FEF3C7;color:#D97706;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg>
            </div>
            <div>
                <div class="stat-value">{{ $marginRate }}%</div>
                <div class="stat-label">Taux de Marge</div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('marge.index') }}" class="table-toolbar" style="display:flex;gap:8px;margin-bottom:16px;">
        <input type="date" name="date" class="form-input" style="width:160px;" value="{{ request('date') }}">
        <select name="month" class="form-select" style="width:140px;">
            <option value="">Tous les mois</option>
            @foreach(range(1, 12) as $m)
                <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ DateTime::createFromFormat('!m', $m)->format('F') }}</option>
            @endforeach
        </select>
        <select name="year" class="form-select" style="width:120px;">
            <option value="">Toutes les années</option>
            @for($y = now()->year; $y >= 2024; $y--)
                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
        <button type="submit" class="btn btn-secondary btn-sm">Filtrer</button>
        @if(request()->anyFilled(['date', 'month', 'year']))
            <a href="{{ route('marge.index') }}" class="btn btn-secondary btn-sm">Réinitialiser</a>
        @endif
    </form>

    {{-- Table --}}
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Facture</th>
                    <th>Date</th>
                    <th>Produit</th>
                    <th>Qté</th>
                    <th>Prix unit.</th>
                    <th>Total</th>
                    <th>Coût</th>
                    <th>Marge</th>
                    <th>%</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr>
                    <td><a href="{{ route('factures.show', $item->invoice_id) }}" class="fw-medium">{{ $item->invoice_number }}</a></td>
                    <td>{{ \Carbon\Carbon::parse($item->invoice_date)->format('d/m/Y') }}</td>
                    <td>{{ $item->product_name ?? $item->designation }}</td>
                    <td>{{ number_format($item->quantity_sold, 2, ',', ' ') }} {{ $item->unit_sold }}</td>
                    <td>{{ number_format($item->unit_price, 0, ',', ' ') }}</td>
                    <td><strong>{{ number_format($item->total_price, 0, ',', ' ') }}</strong></td>
                    <td>{{ number_format($item->cost, 0, ',', ' ') }}</td>
                    <td style="color:{{ $item->margin >= 0 ? '#1A7A4A' : '#C0392B' }};font-weight:600;">
                        {{ number_format($item->margin, 0, ',', ' ') }}
                    </td>
                    <td>
                        <span class="badge {{ $item->margin_pct >= 0 ? 'badge-success' : 'badge-danger' }}">
                            {{ $item->margin_pct }}%
                        </span>
                    </td>
                </tr>
                @empty
                <tr class="table-empty"><td colspan="9">Aucune donnée de marge.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination-wrapper">
            {{ $items->links() }}
        </div>
    </div>
</x-app-layout>
