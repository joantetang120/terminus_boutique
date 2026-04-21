<x-app-layout title="Factures">
    <div class="page-header">
        <div class="page-header-actions">
            <div>
                <h1>Factures</h1>
                <div class="breadcrumb">
                    <a href="{{ route('dashboard') }}">Accueil</a> > Ventes > Factures
                </div>
            </div>
            
            <div class="header-button-group">
                {{-- Bouton Demandes à valider (Design Pro & Premium) --}}
                @if(Auth::user()->can('facture.admin') || Auth::user()->is_admin)
                    <a href="{{ route('factures.index', ['filter_cancellation' => 1]) }}" class="btn-validation-premium">
                        <div class="icon-container">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M5.85 3.5a.75.75 0 00-1.117-1 9.719 9.719 0 00-2.348 4.876.75.75 0 001.479.248A8.219 8.219 0 015.85 3.5zM19.267 2.5a.75.75 0 10-1.117 1 8.219 8.219 0 011.986 4.124.75.75 0 101.48-.248 9.72 9.72 0 00-2.349-4.876z" />
                                <path fill-rule="evenodd" d="M12 2.25A6.75 6.75 0 005.25 9v.75a8.217 8.217 0 01-2.119 5.52.75.75 0 00.298 1.206c1.544.57 3.16.99 4.831 1.243a3.75 3.75 0 107.48 0 24.583 24.583 0 004.83-1.244.75.75 0 00.298-1.205 8.217 8.217 0 01-2.118-5.52V9A6.75 6.75 0 0012 2.25zM9.75 18c0-.034 0-.067.002-.1a25.05 25.05 0 004.496 0l.002.1a2.25 2.25 0 11-4.5 0z" clip-rule="evenodd" />
                            </svg>
                            <span class="badge-pulse"></span>
                        </div>
                        <div class="btn-content">
                            <span class="btn-label">Demandes à valider</span>
                            <span class="btn-subtitle">Annulations en attente</span>
                        </div>
                    </a>
                @endif
                
                @can('facture.create')
                    <a href="{{ route('factures.create') }}" class="btn-primary-custom">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Nouvelle facture
                    </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Date</th>
                    <th>Client</th>
                    <th>Total</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                <tr>
                    <td><strong>{{ $invoice->number }}</strong></td>
                    <td>{{ $invoice->created_at->format('d/m/Y') }}</td>
                    <td>{{ $invoice->client_name }}</td>
                    <td>{{ number_format($invoice->total, 0, ',', ' ') }} FCFA</td>
                    <td>
                        <x-invoice-status-badge :status="$invoice->status" />
                    </td>
                    <td>
                        <div class="table-actions">
                            <a href="{{ route('factures.show', $invoice) }}" class="btn-view">Voir</a>
                            
                            @if(!$invoice->cancellation_requested && $invoice->status !== 'annulee')
                                <form action="{{ route('factures.request-cancellation', $invoice) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn-request-cancel">À Annuler</button>
                                </form>
                            
                            @elseif($invoice->cancellation_requested && $invoice->status !== 'annulee')
                                <div class="status-badge-requested">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.401 3.003zM12 8.25a.75.75 0 01.75.75v3.75a.75.75 0 01-1.5 0V9a.75.75 0 01.75-.75zm0 8.25a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd" />
                                    </svg>
                                    En attente
                                </div>
                                
                                @if(Auth::user()->is_admin || Auth::id() === 1) 
                                    <button onclick="ouvrirModal('{{ route('factures.confirm-cancellation', $invoice) }}')" class="btn-confirm-premium">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                        Confirmer
                                    </button>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6">Aucune facture trouvée.</td></tr>
                @endforelse
            </tbody>       
        </table>
    </div>

    {{-- MODAL DE CONFIRMATION --}}
    <div id="modalConfirm" class="cancel-modal-backdrop hidden" onclick="fermerModal(event)">
        <div class="cancel-modal-container" onclick="event.stopPropagation()">
            <div class="cancel-modal-header">
                <div class="header-icon-container">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.401 3.003zM12 8.25a.75.75 0 01.75.75v3.75a.75.75 0 01-1.5 0V9a.75.75 0 01.75-.75zm0 8.25a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd" />
                    </svg>
                </div>
                <h3>Confirmer l'annulation</h3>
            </div>

            <div class="cancel-modal-body">
                <p class="main-text">Vous êtes sur le point de valider définitivement l'annulation de cette facture.</p>
                <p class="irreversible-alert"><span class="danger-dot"></span> Action irréversible.</p>
                <div class="stock-info-box">
                    <div class="info-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M11.644 1.59a.75.75 0 01.712 0l9.75 5.25a.75.75 0 010 1.32l-9.75 5.25a.75.75 0 01-.712 0l-9.75-5.25a.75.75 0 010-1.32l9.75-5.25z" />
                            <path d="M3.265 10.602l7.668 4.129a2.25 2.25 0 002.134 0l7.668-4.13a.75.75 0 01.713 1.32l-7.669 4.13a3.75 3.75 0 01-3.557 0l-7.668-4.13a.75.75 0 01.712-1.32z" />
                            <path d="M3.265 15.352l7.668 4.129a2.25 2.25 0 002.134 0l7.668-4.13a.75.75 0 01.713 1.32l-7.669 4.13a3.75 3.75 0 01-3.557 0l-7.668-4.13a.75.75 0 01.712-1.32z" />
                        </svg>
                    </div>
                    <p>Les stocks seront automatiquement remis à jour.</p>
                </div>
            </div>

            <div class="cancel-modal-footer">
                <button type="button" class="btn-secondary" onclick="fermerModal(event)">Retour</button>
                <form id="formConfirm" method="POST" style="margin: 0;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn-danger">Confirmer l'annulation</button>
                </form>
            </div>
        </div>
    </div>

    <style>
        /* --- EN-TÊTE ET BOUTONS GLOBAUX --- */
        .header-button-group {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        /* --- BOUTON DEMANDES À VALIDER (NOUVEAU DESIGN) --- */
        .btn-validation-premium {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 16px;
            background: #FFFBEB;
            border: 1px solid #FDE68A;
            border-radius: 12px;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(245, 158, 11, 0.05);
        }

        .btn-validation-premium:hover {
            background: #FEF3C7;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.15);
            border-color: #F59E0B;
        }

        .icon-container {
            position: relative;
            color: #D97706;
            display: flex;
        }

        .icon-container svg { width: 22px; height: 22px; }

        .badge-pulse {
            position: absolute;
            top: -2px;
            right: -2px;
            width: 8px;
            height: 8px;
            background: #EF4444;
            border-radius: 50%;
            border: 2px solid #FFFBEB;
            animation: pulse-red 2s infinite;
        }

        .btn-content { display: flex; flex-direction: column; }
        .btn-label { color: #92400E; font-weight: 800; font-size: 13px; line-height: 1.2; }
        .btn-subtitle { color: #B45309; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px; }

        /* --- BOUTON NOUVELLE FACTURE (BLEU MARINE) --- */
        .btn-primary-custom {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: #1E3A8A;
            color: white !important;
            border-radius: 12px;
            font-weight: 700;
            font-size: 14px;
            text-decoration: none;
            transition: 0.3s;
        }
        .btn-primary-custom:hover { background: #111827; transform: translateY(-2px); }

        /* --- TABLE ACTIONS --- */
        .table-actions { display: flex; align-items: center; gap: 8px; }
        .btn-view { padding: 6px 12px; background: #f3f4f6; color: #374151; border-radius: 6px; font-size: 12px; font-weight: 600; text-decoration: none; }
        .btn-request-cancel { padding: 6px 12px; border: 1px solid #fee2e2; color: #dc2626; background: #fff; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; }
        .btn-request-cancel:hover { background: #fef2f2; }

        /* --- BADGE DANS LE TABLEAU --- */
        .status-badge-requested {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            background: #FFFBEB;
            color: #92400E;
            border: 1px solid #FDE68A;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
        }
        .status-badge-requested svg { width: 12px; height: 12px; }

        /* --- BOUTON CONFIRMER DANS TABLEAU --- */
        .btn-confirm-premium {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.2s;
        }
        .btn-confirm-premium:hover { filter: brightness(1.1); transform: scale(1.05); }

        /* --- MODAL (IDENTIQUE MAIS ÉPURÉ) --- */
        .cancel-modal-backdrop {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(4px);
            z-index: 9999; display: none; justify-content: center; align-items: center;
        }
        .cancel-modal-backdrop.flex { display: flex; }
        .cancel-modal-container { background: white; width: 90%; max-width: 440px; border-radius: 24px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); }
        .cancel-modal-header { background: #FEF3C7; padding: 20px; display: flex; align-items: center; gap: 12px; border-bottom: 1px solid #FDE68A; }
        .cancel-modal-header h3 { margin: 0; color: #92400E; font-size: 18px; font-weight: 800; }
        .cancel-modal-body { padding: 24px; }
        .irreversible-alert { color: #dc2626; font-weight: 700; display: flex; align-items: center; gap: 6px; margin: 15px 0; }
        .danger-dot { width: 6px; height: 6px; background: #dc2626; border-radius: 50%; }
        .stock-info-box { background: #F0F9FF; border: 1px solid #BAE6FD; padding: 12px; border-radius: 12px; display: flex; gap: 10px; color: #0369A1; font-size: 13px; }
        .cancel-modal-footer { padding: 16px 24px; background: #F8FAFC; display: flex; justify-content: flex-end; gap: 10px; }
        .btn-secondary { background: white; border: 1px solid #E2E8F0; padding: 10px 20px; border-radius: 10px; cursor: pointer; font-weight: 600; }
        .btn-danger { background: #1E3A8A; color: white; border: none; padding: 10px 20px; border-radius: 10px; cursor: pointer; font-weight: 700; }

        @keyframes pulse-red {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(239, 68, 68, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }
    </style>

    <script>
        function ouvrirModal(url) {
            document.getElementById('formConfirm').action = url;
            const modal = document.getElementById('modalConfirm');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        function fermerModal(event) {
            const modal = document.getElementById('modalConfirm');
            if (event.target === modal || event.target.classList.contains('btn-secondary')) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }
    </script>
</x-app-layout>