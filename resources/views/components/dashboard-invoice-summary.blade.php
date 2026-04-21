@props(['stats'])

<div class="dashboard-invoice-summary">
    {{-- Card 1: Today's invoices (neutral) --}}
    <div class="summary-card summary-card-neutral">
        <div class="summary-icon" style="background:#F1F5F9;color:#64748B;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="20" height="20">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
            </svg>
        </div>
        <div class="summary-content">
            <div class="summary-value">{{ $stats['today_count'] ?? 0 }}</div>
            <div class="summary-label">Factures aujourd'hui</div>
            <div class="summary-subtitle">{{ number_format($stats['today_total'] ?? 0, 0, ',', ' ') }} FCFA</div>
        </div>
    </div>

    {{-- Card 2: Pending balances (orange if > 0) --}}
    @php
        $hasUnpaid = ($stats['unpaid_count'] ?? 0) > 0;
    @endphp
    <div class="summary-card {{ $hasUnpaid ? 'summary-card-warning' : '' }}">
        @if($hasUnpaid)
            <a href="{{ route('factures.index') }}" class="summary-card-link">
        @endif
        <div class="summary-icon" style="{{ $hasUnpaid ? 'background:#FEF5EC;color:#E67E22;' : 'background:#F1F5F9;color:#64748B;' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="20" height="20">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.423.331" />
            </svg>
        </div>
        <div class="summary-content">
            <div class="summary-value">{{ $stats['unpaid_count'] ?? 0 }}</div>
            <div class="summary-label">Soldes en attente</div>
            <div class="summary-subtitle">{{ number_format($stats['unpaid_balance'] ?? 0, 0, ',', ' ') }} FCFA</div>
        </div>
        @if($hasUnpaid)
            </a>
        @endif
    </div>

    {{-- Card 3: Overdue + J-3 alerts (red if > 0) --}}
    @php
        $hasAlerts = ($stats['overdue_count'] ?? 0) > 0 || ($stats['alert_count'] ?? 0) > 0;
    @endphp
    <div class="summary-card {{ $hasAlerts ? 'summary-card-danger' : '' }}">
        @if($hasAlerts)
            <a href="{{ route('factures.index') }}" class="summary-card-link">
        @endif
        <div class="summary-icon" style="{{ $hasAlerts ? 'background:#FDECEA;color:#C0392B;' : 'background:#F1F5F9;color:#64748B;' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="20" height="20">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>
        </div>
        <div class="summary-content">
            <div class="summary-value">{{ ($stats['overdue_count'] ?? 0) + ($stats['alert_count'] ?? 0) }}</div>
            <div class="summary-label">
                @if(($stats['overdue_count'] ?? 0) > 0)
                    Factures en retard
                @else
                    Alertes J-3
                @endif
            </div>
            <div class="summary-subtitle">
                @if(($stats['overdue_count'] ?? 0) > 0 && ($stats['alert_count'] ?? 0) > 0)
                    {{ $stats['overdue_count'] }} retard + {{ $stats['alert_count'] }} J-3
                @elseif(($stats['overdue_count'] ?? 0) > 0)
                    {{ $stats['overdue_count'] }} facture(s) en retard
                @elseif(($stats['alert_count'] ?? 0) > 0)
                    {{ $stats['alert_count'] }} échéance dans 3 jours
                @else
                    Aucune alerte
                @endif
            </div>
        </div>
        @if($hasAlerts)
            </a>
        @endif
    </div>
</div>

<style>
.dashboard-invoice-summary {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}

.summary-card {
    background: white;
    border-radius: 8px;
    padding: 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    border: 1px solid #E2E8F0;
    transition: all 0.2s;
}

.summary-card-warning {
    border-color: #E67E22;
    background: #FFFBF5;
}

.summary-card-danger {
    border-color: #C0392B;
    background: #FEF7F6;
}

.summary-card-link {
    display: flex;
    align-items: center;
    gap: 12px;
    text-decoration: none;
    color: inherit;
    width: 100%;
}

.summary-card-link:hover {
    opacity: 0.9;
}

.summary-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.summary-content {
    flex: 1;
}

.summary-value {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1E293B;
    line-height: 1.2;
}

.summary-label {
    font-size: 0.75rem;
    color: #64748B;
    margin-top: 2px;
}

.summary-subtitle {
    font-size: 0.875rem;
    font-weight: 500;
    color: #334155;
    margin-top: 4px;
}

@media (max-width: 768px) {
    .dashboard-invoice-summary {
        grid-template-columns: 1fr;
    }
}
</style>
