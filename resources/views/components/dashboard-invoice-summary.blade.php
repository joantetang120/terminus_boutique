@props(['stats'])

<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px;">
    
    {{-- Carte 1 : Factures du jour (Neutre) --}}
    <div style="padding: 20px; background: white; border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="color: #64748b; font-size: 0.875rem; font-weight: 500;">Factures du jour</div>
        <div style="display: flex; align-items: baseline; gap: 8px; margin-top: 8px;">
            <span style="font-size: 1.5rem; font-weight: 700; color: #1e293b;">{{ $stats['today_count'] }}</span>
            <span style="color: #94a3b8; font-size: 0.875rem;">({{ number_format($stats['today_total'], 0, ',', ' ') }} FCFA)</span>
        </div>
    </div>

    {{-- Carte 2 : Soldes en attente (Orange si > 0) --}}
    <div style="padding: 20px; background: {{ $stats['unpaid_count'] > 0 ? '#fff7ed' : 'white' }}; border-radius: 8px; border: 1px solid {{ $stats['unpaid_count'] > 0 ? '#fdba74' : '#e2e8f0' }};">
        <div style="color: #64748b; font-size: 0.875rem; font-weight: 500;">Soldes en attente</div>
        <div style="display: flex; align-items: baseline; gap: 8px; margin-top: 8px;">
            <span style="font-size: 1.5rem; font-weight: 700; color: {{ $stats['unpaid_count'] > 0 ? '#ea580c' : '#1e293b' }};">{{ $stats['unpaid_count'] }}</span>
            <span style="color: #94a3b8; font-size: 0.875rem;">({{ number_format($stats['unpaid_balance'], 0, ',', ' ') }} FCFA)</span>
        </div>
    </div>

    {{-- Carte 3 : Alertes & Retards (Rouge si > 0 + Cliquable) --}}
    @php
        $hasAlerts = $stats['overdue_count'] > 0 || $stats['alert_count'] > 0;
        $cardStyle = $hasAlerts 
            ? "background: #fef2f2; border: 1px solid #fecaca; cursor: pointer;" 
            : "background: white; border: 1px solid #e2e8f0;";
    @endphp
    
    <div 
        @if($hasAlerts) onclick="window.location.href='/factures?status=IMPAYEE'" @endif
        style="padding: 20px; border-radius: 8px; {{ $cardStyle }}">
        <div style="color: #64748b; font-size: 0.875rem; font-weight: 500;">Alertes & Retards</div>
        <div style="display: flex; flex-direction: column; margin-top: 8px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 0.75rem; color: #ef4444; font-weight: 600;">RETARDS : {{ $stats['overdue_count'] }}</span>
                <span style="font-size: 0.75rem; color: #f59e0b; font-weight: 600;">ÉCHÉANCE J-3 : {{ $stats['alert_count'] }}</span>
            </div>
            @if($hasAlerts)
                <div style="margin-top: 8px; font-size: 0.7rem; color: #ef4444; text-align: right;">→ Cliquer pour voir</div>
            @endif
        </div>
    </div>

</div>