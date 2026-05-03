<x-app-layout title="Comptabilité des factures">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Alert Banners --}}
    @php
        $dueSoonCount = \App\Models\Invoice::whereIn('status', ['IMPAYEE', 'PARTIELLE'])
            ->whereBetween('due_date', [now()->format('Y-m-d'), now()->addDays(3)->format('Y-m-d')])
            ->count();
        $overdueInvoices = \App\Models\Invoice::whereIn('status', ['IMPAYEE', 'PARTIELLE'])
            ->where('due_date', '<', now()->format('Y-m-d'))
            ->get();
        $overdueCount = $overdueInvoices->count();
        $overdueAmount = $overdueInvoices->sum('balance');
    @endphp

    <div x-data="invoiceAccounting()">
        <div class="page-header">
            <div>
                <h1>Comptabilité des factures</h1>
                <div class="breadcrumb">
                    <a href="{{ route('dashboard') }}">Accueil</a> > Finances > Comptabilité des factures
                </div>
            </div>
        </div>

        {{-- Alert banners --}}
        @if($dueSoonCount > 0 || $overdueCount > 0)
            <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:20px;">
                @if($dueSoonCount > 0)
                    <div style="display:flex;align-items:center;gap:12px;padding:12px 16px;background:#FFF7ED;border:1px solid #FDBA74;border-radius:6px;">
                        <svg width="18" height="18" fill="none" stroke="#EA580C" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span style="font-size:13px;color:#9A3412;font-weight:500;">
                            {{ $dueSoonCount }} facture{{ $dueSoonCount > 1 ? 's' : '' }} arrivent à échéance dans 3 jours
                        </span>
                    </div>
                @endif
                @if($overdueCount > 0)
                    <div style="display:flex;align-items:center;gap:12px;padding:12px 16px;background:#FEF2F2;border:1px solid #FCA5A5;border-radius:6px;">
                        <svg width="18" height="18" fill="none" stroke="#DC2626" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span style="font-size:13px;color:#991B1B;font-weight:500;">
                            {{ $overdueCount }} facture{{ $overdueCount > 1 ? 's' : '' }} en retard — {{ number_format($overdueAmount, 0, ',', ' ') }} FCFA en attente
                        </span>
                    </div>
                @endif
            </div>
        @endif

        {{-- Stats --}}
        <div class="stats-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:24px;">
            <div class="stat-card">
                <div class="stat-icon" style="background:#FDECEA;color:#C0392B;">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="stat-value" style="color:#C0392B;">{{ number_format($stats['total_unpaid'], 0, ',', ' ') }} FCFA</div>
                    <div class="stat-label">Solde impayé total</div>
                    <div style="font-size:11px;color:#94A3B8;margin-top:2px;">{{ $stats['count_unpaid'] }} factures</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:#FEF2F2;color:#DC2626;">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <div class="stat-value" style="color:#DC2626;">{{ number_format($stats['total_overdue'], 0, ',', ' ') }} FCFA</div>
                    <div class="stat-label">En retard</div>
                    <div style="font-size:11px;color:#94A3B8;margin-top:2px;">{{ $stats['count_overdue'] }} factures</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:#E8F5EE;color:#1A7A4A;">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="stat-value" style="color:#1A7A4A;">{{ number_format($stats['total_paid'], 0, ',', ' ') }} FCFA</div>
                    <div class="stat-label">Soldées</div>
                    <div style="font-size:11px;color:#94A3B8;margin-top:2px;">{{ $stats['count_paid'] }} factures</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:#EBF4FF;color:#2E75B6;">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <div>
                    <div class="stat-value" style="color:#2E75B6;">{{ number_format($stats['total_ca'], 0, ',', ' ') }} FCFA</div>
                    <div class="stat-label">Chiffre d'affaires</div>
                    <div style="font-size:11px;color:#94A3B8;margin-top:2px;">{{ $stats['count_ca'] }} factures</div>
                </div>
            </div>
        </div>

        {{-- Table + Filters --}}
        <div class="table-wrapper">
            <form id="filter-form-factures" method="GET" action="{{ route('comptabilite.factures.index') }}" class="table-toolbar" style="flex-wrap:wrap;gap:8px;">
                <select name="status" class="form-select" style="width:130px;">
                    <option value="">Statut</option>
                    <option value="IMPAYEE"   {{ request('status') === 'IMPAYEE'   ? 'selected' : '' }}>Impayée</option>
                    <option value="PARTIELLE" {{ request('status') === 'PARTIELLE' ? 'selected' : '' }}>Partielle</option>
                    <option value="SOLDEE"    {{ request('status') === 'SOLDEE'    ? 'selected' : '' }}>Soldée</option>
                    <option value="EN_RETARD" {{ request('status') === 'EN_RETARD' ? 'selected' : '' }}>En retard</option>
                </select>
                <select name="due_status" class="form-select" style="width:140px;">
                    <option value="">Échéance</option>
                    <option value="overdue"   {{ request('due_status') === 'overdue'   ? 'selected' : '' }}>En retard</option>
                    <option value="due_today" {{ request('due_status') === 'due_today' ? 'selected' : '' }}>Aujourd'hui</option>
                    <option value="due_soon"  {{ request('due_status') === 'due_soon'  ? 'selected' : '' }}>Dans 3 jours</option>
                </select>
                <input type="text" name="client" class="form-input" style="width:160px;" placeholder="Client..." value="{{ request('client') }}">
                <div style="display:flex;align-items:center;gap:4px;">
                    <input type="date" name="date_from" class="form-input" style="width:130px;" value="{{ request('date_from') }}" title="Du">
                    <span style="color:#64748B;">→</span>
                    <input type="date" name="date_to" class="form-input" style="width:130px;" value="{{ request('date_to') }}" title="Au">
                </div>
                <a href="{{ route('comptabilite.factures.index') }}" class="btn btn-sm" style="background:#e2e8f0;color:#475569;">Réinit.</a>
                <button type="submit" class="btn btn-secondary btn-sm">Filtrer</button>
            </form>

            {{-- Bouton Export --}}
            <div class="relative inline-block text-left" x-data="{ open: false }" style="margin-bottom:15px;">
                <button @click="open = !open" type="button" class="btn-export-navy">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="white" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:8px;">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    <span>Exporter</span>
                    <svg class="ml-2 w-4 h-4" fill="none" stroke="white" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" @click.away="open = false" x-transition class="export-dropdown-menu left-0">
                    <div class="py-1">
                        <a href="javascript:void(0)" onclick="triggerExportFactures('csv')" class="export-item">
                            <span class="icon-csv">CSV</span> Format Excel (.csv)
                        </a>
                        <a href="javascript:void(0)" onclick="triggerExportFactures('pdf')" class="export-item">
                            <span class="icon-pdf">PDF</span> Format Document (.pdf)
                        </a>
                    </div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th style="width:30px;"></th>
                        <th>N° Facture</th>
                        <th>Client</th>
                        <th>Date</th>
                        <th style="text-align:right;">Total</th>
                        <th style="text-align:right;">Payé</th>
                        <th style="text-align:right;">Solde</th>
                        <th style="text-align:center;">Statut</th>
                        <th style="text-align:center;">Échéance</th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        <tr id="invoice-row-{{ $invoice->id }}"
                            style="cursor:pointer;transition:background 0.15s;"
                            :style="updatedInvoiceId === {{ $invoice->id }} ? 'background:#F0FDF4;' : ''"
                            @click="toggleAccordion({{ $invoice->id }})"
                            @mouseenter="$el.style.background = updatedInvoiceId === {{ $invoice->id }} ? '#F0FDF4' : '#F8FAFC'"
                            @mouseleave="$el.style.background = updatedInvoiceId === {{ $invoice->id }} ? '#F0FDF4' : ''">
                            <td style="padding:10px 8px 10px 16px;text-align:center;">
                                <svg x-show="!isExpanded({{ $invoice->id }})" width="14" height="14" fill="none" stroke="#94A3B8" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                <svg x-show="isExpanded({{ $invoice->id }})" width="14" height="14" fill="none" stroke="#3B82F6" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </td>
                            <td>
                                <a href="{{ route('factures.show', $invoice) }}" style="color:#2E75B6;font-weight:600;" @click.stop>
                                    {{ $invoice->number }}
                                </a>
                            </td>
                            <td>
                                <div style="font-weight:500;">{{ $invoice->client_name }}</div>
                                @if($invoice->client_phone)
                                    <div style="font-size:11px;color:#94A3B8;">{{ $invoice->client_phone }}</div>
                                @endif
                            </td>
                            <td style="color:#64748B;white-space:nowrap;">{{ $invoice->created_at->format('d/m/Y') }}</td>
                            <td style="text-align:right;font-weight:600;white-space:nowrap;">{{ number_format($invoice->total, 0, ',', ' ') }} FCFA</td>
                            <td style="text-align:right;color:#1A7A4A;font-weight:500;white-space:nowrap;">{{ number_format($invoice->paid_amount, 0, ',', ' ') }} FCFA</td>
                            <td id="balance-cell-{{ $invoice->id }}" style="text-align:right;font-weight:600;white-space:nowrap;color:{{ $invoice->balance > 0 ? '#C0392B' : '#1A7A4A' }};">
                                {{ number_format($invoice->balance, 0, ',', ' ') }} FCFA
                            </td>
                            <td style="text-align:center;">
                                <span id="status-badge-{{ $invoice->id }}" class="{{ $invoice->getStatusBadgeClass() }}" style="font-size:11px;padding:2px 8px;border-radius:10px;font-weight:600;">
                                    {{ $invoice->getStatusLabel() }}
                                </span>
                                @if($invoice->marked_for_cancellation)
                                    <div style="font-size:10px;color:#E67E22;margin-top:2px;">À annuler</div>
                                @endif
                            </td>
                            <td style="text-align:center;white-space:nowrap;">
                                @if($invoice->due_date && $invoice->due_date < now()->format('Y-m-d') && in_array($invoice->status, ['IMPAYEE', 'PARTIELLE']))
                                    <span style="color:#C0392B;font-weight:600;font-size:12px;">{{ \Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y') }}</span>
                                    <div style="font-size:10px;color:#E74C3C;">En retard</div>
                                @elseif($invoice->due_date)
                                    <span style="font-size:12px;color:#64748B;">{{ \Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y') }}</span>
                                @else
                                    <span style="color:#CBD5E1;">—</span>
                                @endif
                            </td>
                            <td style="text-align:center;">
                                <div style="display:flex;justify-content:center;align-items:center;gap:8px;">
                                    <a href="{{ route('factures.show', $invoice) }}" title="Voir" style="color:#64748B;display:inline-flex;" @click.stop>
                                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    @if($invoice->status !== 'SOLDEE' && $invoice->status !== 'ANNULEE')
                                        @can('compta.create')
                                            <button @click.stop="openPaymentModal({{ $invoice->id }}, {{ $invoice->balance }}, '{{ addslashes($invoice->number) }}')"
                                                title="Enregistrer un paiement"
                                                style="display:inline-flex;color:#1A7A4A;background:none;border:none;cursor:pointer;padding:0;">
                                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                            </button>
                                        @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                        <tr x-show="isExpanded({{ $invoice->id }})" style="display:none;" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                            <td colspan="10" style="padding:0;background:#F8FAFC;border-top:none;">
                                <div style="margin:0 16px 12px 40px;background:#fff;border:1px solid #E2E8F0;border-radius:6px;overflow:hidden;">
                                    <div style="padding:10px 16px;background:#F1F5F9;border-bottom:1px solid #E2E8F0;font-size:12px;font-weight:600;color:#475569;letter-spacing:0.05em;text-transform:uppercase;">
                                        Historique des paiements
                                    </div>
                                    @if($invoice->payments->count() > 0)
                                        <table style="width:100%;border-collapse:collapse;">
                                            <thead>
                                                <tr style="background:#F8FAFC;">
                                                    <th style="padding:8px 16px;text-align:left;font-size:11px;font-weight:600;color:#64748B;border-bottom:1px solid #E2E8F0;">Date</th>
                                                    <th style="padding:8px 16px;text-align:right;font-size:11px;font-weight:600;color:#64748B;border-bottom:1px solid #E2E8F0;">Montant</th>
                                                    <th style="padding:8px 16px;text-align:left;font-size:11px;font-weight:600;color:#64748B;border-bottom:1px solid #E2E8F0;">Méthode</th>
                                                    <th style="padding:8px 16px;text-align:left;font-size:11px;font-weight:600;color:#64748B;border-bottom:1px solid #E2E8F0;">Enregistré par</th>
                                                    <th style="padding:8px 16px;text-align:left;font-size:11px;font-weight:600;color:#64748B;border-bottom:1px solid #E2E8F0;">Note</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($invoice->payments as $payment)
                                                    <tr>
                                                        <td style="padding:8px 16px;font-size:12px;color:#374151;border-bottom:1px solid #F1F5F9;">{{ $payment->payment_date->format('d/m/Y') }}</td>
                                                        <td style="padding:8px 16px;font-size:12px;font-weight:600;color:#1A7A4A;text-align:right;border-bottom:1px solid #F1F5F9;">{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</td>
                                                        <td style="padding:8px 16px;font-size:12px;color:#64748B;border-bottom:1px solid #F1F5F9;">
                                                            @switch($payment->payment_method)
                                                                @case('cash') Espèces @break
                                                                @case('transfer') Virement @break
                                                                @case('mobile_money') Mobile Money @break
                                                                @case('check') Chèque @break
                                                                @case('card') Carte bancaire @break
                                                                @default {{ $payment->payment_method }}
                                                            @endswitch
                                                        </td>
                                                        <td style="padding:8px 16px;font-size:12px;color:#64748B;border-bottom:1px solid #F1F5F9;">{{ $payment->createdBy->name ?? '—' }}</td>
                                                        <td style="padding:8px 16px;font-size:12px;color:#64748B;border-bottom:1px solid #F1F5F9;">{{ $payment->note ?? '—' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div style="padding:16px;font-size:13px;color:#94A3B8;font-style:italic;">Aucun paiement enregistré.</div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="padding:32px;text-align:center;color:#94A3B8;">
                                Aucune facture trouvée.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div style="padding:12px 0 4px;">
                {{ $invoices->links() }}
            </div>
        </div>

        {{-- Success Modal --}}
        <div x-show="showSuccessModal" style="display:none;position:fixed;inset:0;z-index:60;overflow-y:auto;" role="dialog" aria-modal="true">
            <div style="display:flex;align-items:center;justify-content:center;min-height:100vh;padding:16px;">
                <div x-show="showSuccessModal"
                     x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                     style="position:fixed;inset:0;background:rgba(0,0,0,0.4);"></div>
                <div x-show="showSuccessModal"
                     x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                     style="position:relative;background:#fff;border-radius:10px;box-shadow:0 20px 60px rgba(0,0,0,0.15);width:100%;max-width:400px;z-index:61;text-align:center;padding:32px 24px;">
                    <div style="width:56px;height:56px;border-radius:50%;background:#E8F5EE;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                        <svg width="28" height="28" fill="none" stroke="#1A7A4A" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div style="font-size:17px;font-weight:600;color:#1E293B;margin-bottom:6px;">Paiement enregistré !</div>
                    <div style="font-size:13px;color:#64748B;margin-bottom:20px;" x-text="successMessage"></div>
                    <button @click="closeSuccessAndRefresh" class="btn btn-primary btn-sm" style="min-width:120px;">Fermer</button>
                </div>
            </div>
        </div>

        {{-- Payment Modal --}}
        <div x-show="showPaymentModal" style="display:none;position:fixed;inset:0;z-index:50;overflow-y:auto;" role="dialog" aria-modal="true">
            <div style="display:flex;align-items:center;justify-content:center;min-height:100vh;padding:16px;">
                <div x-show="showPaymentModal"
                     x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                     style="position:fixed;inset:0;background:rgba(0,0,0,0.4);"
                     @click="showPaymentModal = false"></div>
                <div x-show="showPaymentModal"
                     x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                     style="position:relative;background:#fff;border-radius:10px;box-shadow:0 20px 60px rgba(0,0,0,0.15);width:100%;max-width:480px;z-index:51;">
                    <div style="padding:20px 24px;border-bottom:1px solid #F1F5F9;display:flex;align-items:center;gap:12px;">
                        <div style="width:36px;height:36px;border-radius:50%;background:#E8F5EE;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="18" height="18" fill="none" stroke="#1A7A4A" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <div>
                            <div style="font-weight:600;font-size:15px;color:#1E293B;">Enregistrer un paiement</div>
                            <div style="font-size:12px;color:#64748B;">
                                Facture <strong x-text="selectedInvoiceNumber"></strong> &mdash; Solde: <strong style="color:#C0392B;" x-text="formatCurrency(selectedInvoiceBalance)"></strong>
                            </div>
                        </div>
                    </div>
                    <form @submit.prevent="submitPayment" style="padding:20px 24px;">
                        <input type="hidden" x-model="paymentForm.invoice_id">
                        <div style="margin-bottom:16px;">
                            <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px;">Montant (FCFA)</label>
                            <input type="number" x-model.number="paymentForm.amount" min="1" :max="selectedInvoiceBalance" required class="form-input" style="width:100%;">
                            <div x-show="paymentForm.amount > selectedInvoiceBalance" style="margin-top:4px;font-size:12px;color:#C0392B;">Le montant dépasse le solde restant.</div>
                        </div>
                        <div style="margin-bottom:16px;">
                            <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px;">Date de paiement</label>
                            <input type="date" x-model="paymentForm.payment_date" required class="form-input" style="width:100%;">
                        </div>
                        <div style="margin-bottom:16px;">
                            <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px;">Mode de paiement</label>
                            <select x-model="paymentForm.payment_method" required class="form-select" style="width:100%;">
                                <option value="">Choisir...</option>
                                <option value="cash">Espèces</option>
                                <option value="transfer">Virement</option>
                                <option value="mobile_money">Mobile Money</option>
                                <option value="check">Chèque</option>
                                <option value="card">Carte bancaire</option>
                            </select>
                        </div>
                        <div style="margin-bottom:20px;">
                            <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px;">Note <span style="font-weight:400;color:#94A3B8;">(optionnel)</span></label>
                            <textarea x-model="paymentForm.note" rows="2" class="form-input" style="width:100%;resize:vertical;" placeholder="Référence, nom du payeur..."></textarea>
                        </div>
                        <div x-show="paymentError" style="margin-bottom:16px;padding:10px 14px;background:#FEF2F2;border:1px solid #FCA5A5;border-radius:6px;font-size:13px;color:#991B1B;" x-text="paymentError"></div>
                        <div style="display:flex;justify-content:flex-end;gap:10px;">
                            <button type="button" @click="showPaymentModal = false" class="btn btn-sm" style="background:#e2e8f0;color:#475569;">Annuler</button>
                            <button type="submit" class="btn btn-primary btn-sm" :disabled="paymentForm.amount > selectedInvoiceBalance || paymentForm.amount <= 0 || submitting" style="min-width:120px;">
                                <span x-show="!submitting">Enregistrer</span>
                                <span x-show="submitting">Enregistrement...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .btn-export-navy { background-color: #1e3a8a; color: #ffffff !important; padding: 8px 16px; border-radius: 8px; font-weight: 600; display: flex; align-items: center; border: none; cursor: pointer; }
        .export-dropdown-menu { position: absolute; left: 0; margin-top: 8px; min-width: 220px; background: white; border-radius: 10px; z-index: 9999; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.2); border: 1px solid #e5e7eb; }
        .export-item { display: flex; align-items: center; padding: 10px 15px; font-size: 14px; color: #374151; text-decoration: none; }
        .export-item:hover { background-color: #f3f4f6; color: #1e3a8a; }
        .icon-csv { background: #dcfce7; color: #166534; font-size: 10px; padding: 2px 4px; border-radius: 4px; margin-right: 10px; font-weight: bold; }
        .icon-pdf { background: #fee2e2; color: #991b1b; font-size: 10px; padding: 2px 4px; border-radius: 4px; margin-right: 10px; font-weight: bold; }
    </style>

    <script>
        function triggerExportFactures(format) {
            const filterForm = document.querySelector('#filter-form-factures');
            let params = new URLSearchParams();
            if (filterForm) {
                const formData = new FormData(filterForm);
                params = new URLSearchParams(formData);
            }
            params.set('format', format);
            params.set('source', 'factures');
            const url = "{{ route('reports.export') }}?" + params.toString();
            if (format === 'pdf') {
                window.location.href = "{{ route('reports.export.pdf') }}?" + params.toString();
            } else {
                window.location.href = url;
            }
        }

        function invoiceAccounting() {
            return {
                showPaymentModal: false,
                showSuccessModal: false,
                successMessage: '',
                submitting: false,
                paymentError: null,
                updatedInvoiceId: null,
                selectedInvoiceBalance: 0,
                selectedInvoiceNumber: '',
                paymentForm: {
                    invoice_id: null,
                    amount: 0,
                    payment_date: new Date().toISOString().split('T')[0],
                    payment_method: '',
                    note: ''
                },
                expandedRows: [],

                toggleAccordion(invoiceId) {
                    if (this.expandedRows.includes(invoiceId)) {
                        this.expandedRows = this.expandedRows.filter(id => id !== invoiceId);
                    } else {
                        this.expandedRows.push(invoiceId);
                    }
                },

                isExpanded(invoiceId) {
                    return this.expandedRows.includes(invoiceId);
                },

                openPaymentModal(invoiceId, balance, number) {
                    this.selectedInvoiceBalance = balance;
                    this.selectedInvoiceNumber = number;
                    this.paymentForm.invoice_id = invoiceId;
                    this.paymentForm.amount = balance;
                    this.paymentForm.payment_date = new Date().toISOString().split('T')[0];
                    this.paymentForm.payment_method = '';
                    this.paymentForm.note = '';
                    this.paymentError = null;
                    this.showPaymentModal = true;
                },

                async submitPayment() {
                    this.submitting = true;
                    this.paymentError = null;
                    try {
                        const response = await fetch('{{ route('comptabilite.factures.payment') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.paymentForm)
                        });
                        const data = await response.json();
                        if (response.ok && data.success) {
                            this.showPaymentModal = false;
                            const inv = data.invoice;
                            this.successMessage = 'Facture ' + inv.number + ' — ' + this.formatCurrency(inv.paid_amount) + ' payé / ' + this.formatCurrency(inv.total) + ' — Solde restant : ' + this.formatCurrency(inv.balance);
                            this.showSuccessModal = true;
                        } else {
                            this.paymentError = data.message || 'Une erreur est survenue.';
                        }
                    } catch (error) {
                        this.paymentError = 'Erreur de connexion. Veuillez réessayer.';
                        console.error(error);
                    } finally {
                        this.submitting = false;
                    }
                },

                updateInvoiceRow(invoice) {
                    const statusBadge = document.getElementById(`status-badge-${invoice.id}`);
                    if (statusBadge) {
                        statusBadge.textContent = invoice.status_label;
                        statusBadge.className = invoice.status_badge_class;
                    }
                    const row = document.getElementById(`invoice-row-${invoice.id}`);
                    if (row) {
                        const cells = row.getElementsByTagName('td');
                        if (cells[5]) cells[5].textContent = this.formatCurrency(invoice.paid_amount);
                        const balanceCell = document.getElementById(`balance-cell-${invoice.id}`);
                        if (balanceCell) {
                            balanceCell.textContent = this.formatCurrency(invoice.balance);
                            balanceCell.style.color = invoice.balance > 0 ? '#C0392B' : '#1A7A4A';
                        }
                    }
                },

                closeSuccessAndRefresh() {
                    this.showSuccessModal = false;
                    window.location.reload();
                },

                formatCurrency(amount) {
                    return new Intl.NumberFormat('fr-FR').format(Math.round(amount)) + ' FCFA';
                }
            };
        }
    </script>
</x-app-layout>