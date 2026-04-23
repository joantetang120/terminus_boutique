<x-app-layout title="Factures Fantômes - Archive">
    {{-- Bandeau distinctif Ghost Invoices --}}
    <div style="background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%); color: white; padding: 12px 24px; border-bottom: 3px solid #ed8936;">
        <div style="max-width: 100%; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; padding: 0 24px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                <div>
                    <h2 style="margin: 0; font-size: 16px; font-weight: 600;">ARCHIVE DES FACTURES - MODE CONSULTATION</h2>
                    <p style="margin: 0; font-size: 12px; opacity: 0.8;">Données historiques immuables • Lecture seule • Aucune modification possible</p>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 16px;">
                <span style="font-size: 12px; opacity: 0.8;">
                    Accès vérifié: {{ session('ghost_access_verified_at') ? \Carbon\Carbon::parse(session('ghost_access_verified_at'))->format('d/m/Y H:i') : 'N/A' }}
                </span>
                <form method="POST" action="{{ route('ghost.logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-sm" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3);">
                        Quitter l'archive
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Fond légèrement teinté pour distinction --}}
    <div style="background: #f7fafc; min-height: calc(100vh - 200px); padding: 24px 0;">
        <div class="page-header" style="max-width: 100%; margin: 0 auto 24px; padding: 0 24px;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                <h1 style="margin: 0; color: #4a5568;">Factures Fantômes</h1>
                <span style="background: #ed8936; color: white; padding: 4px 12px; border-radius: 12px; font-size: 11px; font-weight: 600; text-transform: uppercase;">Archive</span>
            </div>
            <div class="breadcrumb" style="color: #718096;">
                <a href="{{ route('dashboard') }}" style="color: #4a5568;">Accueil</a> >
                <a href="{{ route('factures.index') }}" style="color: #4a5568;">Factures</a> >
                <span style="color: #ed8936; font-weight: 500;">Archive Fantômes</span>
            </div>
        </div>

        <div class="table-wrapper" style="max-width: 100%; margin: 0 24px; background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0;">
            {{-- Filtres --}}
            <form method="GET" class="table-toolbar" style="padding: 16px; background: #edf2f7; border-bottom: 1px solid #e2e8f0; border-radius: 8px 8px 0 0;">
                <div style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
                    <input type="text" name="search" class="form-input" placeholder="N° ou client..." value="{{ request('search') }}" style="min-width: 200px;">
                    <input type="date" name="date" class="form-input" value="{{ request('date') }}">
                    <input type="text" name="client" class="form-input" placeholder="Filtrer par client..." value="{{ request('client') }}">
                    <button type="submit" class="btn btn-secondary btn-sm">Filtrer</button>
                    @if(request()->hasAny(['search', 'date', 'client']))
                        <a href="{{ route('ghost.index') }}" class="btn btn-sm" style="color: #718096;">Réinitialiser</a>
                    @endif
                </div>
            </form>

            {{-- Table --}}
            <table style="margin: 0; border: none;">
                <thead style="background: #f7fafc;">
                    <tr>
                        <th style="border-bottom: 2px solid #e2e8f0; color: #4a5568;">N° Fantôme</th>
                        <th style="border-bottom: 2px solid #e2e8f0; color: #4a5568;">Client</th>
                        <th style="border-bottom: 2px solid #e2e8f0; color: #4a5568;">Date création</th>
                        <th style="border-bottom: 2px solid #e2e8f0; color: #4a5568; text-align: right;">Total</th>
                        <th style="border-bottom: 2px solid #e2e8f0; color: #4a5568;">Statut original</th>
                        <th style="border-bottom: 2px solid #e2e8f0; color: #4a5568; text-align: center;">État actuel</th>
                        <th style="border-bottom: 2px solid #e2e8f0; color: #4a5568;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ghostInvoices as $gi)
                    @php
                        $isCancelled = in_array($gi->real_invoice_id, $cancelledInvoiceIds ?? []);
                    @endphp
                    <tr style="{{ $isCancelled ? 'background: #fff5f5;' : '' }}">
                        <td style="font-family: monospace; font-size: 13px; color: #4a5568;">
                            <span style="background: #e2e8f0; padding: 2px 6px; border-radius: 4px;">{{ $gi->number }}</span>
                        </td>
                        <td style="color: #2d3748; font-weight: 500;">{{ $gi->client_name }}</td>
                        <td style="color: #718096;">{{ $gi->created_at->format('d/m/Y H:i') }}</td>
                        <td style="text-align: right; font-weight: 600; color: #2d3748;">
                            {{ number_format($gi->total, 0, ',', ' ') }} FCFA
                        </td>
                        <td>
                            <span class="badge" style="background: #e2e8f0; color: #4a5568; text-transform: uppercase; font-size: 11px;">
                                {{ $gi->status }}
                            </span>
                        </td>
                        <td style="text-align: center;">
                            @if($isCancelled)
                                <span style="display: inline-flex; align-items: center; gap: 4px; background: #fed7d7; color: #c53030; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                                    ANNULÉE
                                </span>
                            @else
                                <span style="display: inline-flex; align-items: center; gap: 4px; background: #c6f6d5; color: #22543d; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    Active
                                </span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('ghost.show', $gi) }}" class="btn btn-sm btn-secondary" style="font-size: 12px;">
                                Voir détail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr class="table-empty">
                        <td colspan="7" style="padding: 48px; text-align: center; color: #718096;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 16px; opacity: 0.4;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                            <p style="margin: 0;">Aucune facture fantôme trouvée.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="pagination-wrapper">
                {{ $ghostInvoices->links() }}
                <div class="pagination-info">
                    Affichage {{ $ghostInvoices->firstItem() ?? 0 }} - {{ $ghostInvoices->lastItem() ?? 0 }} sur {{ $ghostInvoices->total() }} factures fantômes
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
