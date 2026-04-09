<x-app-layout title="Tableau de bord">
    <div class="page-header">
        <h1>Tableau de bord</h1>
        <div class="breadcrumb">
            <a href="{{ route('dashboard') }}">Accueil</a> > Tableau de bord
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background:#EBF4FF;color:#2E75B6;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
            </div>
            <div>
                <div class="stat-value">{{ $invoicesToday }}</div>
                <div class="stat-label">Factures du jour</div>
                <div class="stat-subtitle">{{ $invoiceTrend > 0 ? '+' : '' }}{{ $invoiceTrend }} hier</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background:#FDECEA;color:#C0392B;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
            </div>
            <div>
                <div class="stat-value">{{ $lowStock }}</div>
                <div class="stat-label">Stock alertes</div>
                <div class="stat-subtitle">sur {{ $totalProducts }} articles</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background:#E8F5EE;color:#1A7A4A;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" /></svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($todayRevenue, 0, ',', ' ') }} FCFA</div>
                <div class="stat-label">Recettes du jour</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background:#FEF5EC;color:#E67E22;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.423.331" /></svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($todayExpenses, 0, ',', ' ') }} FCFA</div>
                <div class="stat-label">Dépenses du jour</div>
            </div>
        </div>
    </div>

    {{-- Second Row --}}
    <div class="dashboard-grid">
        {{-- Dernières factures --}}
        <div class="card">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                <h2 style="font-size:1rem;font-weight:600;">Dernières factures</h2>
                <a href="{{ route('factures.index') }}" style="font-size:0.875rem;color:#2E75B6;text-decoration:none;">Voir tout →</a>
            </div>
            <div class="table-wrapper" style="border:none;box-shadow:none;">
                <table>
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>Client</th>
                            <th>Total</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentInvoices as $invoice)
                        <tr>
                            <td>{{ $invoice->number }}</td>
                            <td>{{ $invoice->client_name }}</td>
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
                        </tr>
                        @empty
                        <tr class="table-empty">
                            <td colspan="4">Aucune facture.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Stock faible --}}
        <div class="card">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                <h2 style="font-size:1rem;font-weight:600;">Stock faible</h2>
                <a href="{{ route('stock.index') }}" style="font-size:0.875rem;color:#2E75B6;text-decoration:none;">Voir tout →</a>
            </div>
            @forelse($lowStockProducts as $product)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid #F1F5F9;">
                <div>
                    <div style="font-weight:500;font-size:0.875rem;">{{ $product->name }}</div>
                    <div style="font-size:0.75rem;color:#94A3B8;">Seuil: {{ $product->alert_threshold }}</div>
                </div>
                <span class="badge badge-danger">{{ $product->current_stock }}</span>
            </div>
            @empty
            <div style="text-align:center;padding:32px 0;color:#94A3B8;">
                Aucun stock faible.
            </div>
            @endforelse
        </div>
    </div>

    {{-- Factures non soldées --}}
    <div class="dashboard-full">
        <div class="card">
            <h2 style="font-size:1rem;font-weight:600;margin-bottom:16px;">Factures non soldées (crédit en attente)</h2>
            <div class="table-wrapper" style="border:none;box-shadow:none;">
                <table>
                    <thead>
                        <tr>
                            <th>N° Facture</th>
                            <th>Client</th>
                            <th>Montant</th>
                            <th>Date</th>
                            <th>Solde restant</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($unpaidInvoices as $invoice)
                        <tr>
                            <td>{{ $invoice->number }}</td>
                            <td>{{ $invoice->client_name }}</td>
                            <td>{{ number_format($invoice->total, 0, ',', ' ') }} FCFA</td>
                            <td>{{ $invoice->created_at->format('d/m/Y') }}</td>
                            <td><span class="badge badge-warning">{{ number_format($invoice->balance, 0, ',', ' ') }} FCFA</span></td>
                            <td>
                                <a href="{{ route('factures.show', $invoice) }}" class="btn btn-secondary btn-sm">Voir</a>
                            </td>
                        </tr>
                        @empty
                        <tr class="table-empty">
                            <td colspan="6">Aucune facture en attente.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
