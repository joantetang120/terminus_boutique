<x-app-layout title="Factures Fantômes">
    <div class="page-header">
        <h1>Factures Fantômes</h1>
        <div class="breadcrumb">
            <a href="{{ route('dashboard') }}">Accueil</a> > Ventes > Factures Fantômes
        </div>
    </div>

    <div class="table-wrapper">
        <form method="GET" class="table-toolbar">
            <input type="text" name="search" class="form-input" placeholder="Rechercher..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-secondary btn-sm">Filtrer</button>
        </form>
        <table>
            <thead><tr><th>N°</th><th>Client</th><th>Type</th><th>Total</th><th>Statut</th><th>Date</th><th>Créée par</th></tr></thead>
            <tbody>
                @forelse($ghostInvoices as $gi)
                <tr>
                    <td>{{ $gi->number }}</td>
                    <td>{{ $gi->client_name }}</td>
                    <td>{{ ucfirst($gi->type) }}</td>
                    <td>{{ number_format($gi->total, 0, ',', ' ') }} FCFA</td>
                    <td><span class="badge badge-muted">{{ $gi->status }}</span></td>
                    <td>{{ $gi->created_at->format('d/m/Y') }}</td>
                    <td>{{ $gi->createdBy->name ?? '—' }}</td>
                </tr>
                @empty
                <tr class="table-empty"><td colspan="7">Aucune facture fantôme.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:16px;">{{ $ghostInvoices->links() }}</div>
    </div>
</x-app-layout>
