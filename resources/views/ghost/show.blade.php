<x-app-layout title="Détail Fantôme - {{ $ghostInvoice->number }}">
    {{-- Bandeau distinctif Ghost Invoices --}}
    <div style="background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%); color: white; padding: 12px 24px; border-bottom: 3px solid #ed8936;">
        <div style="max-width: 1200px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between;">
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
        <div style="max-width: 1000px; margin: 0 auto; padding: 0 24px;">
            {{-- Header avec navigation --}}
            <div style="margin-bottom: 24px;">
                <a href="{{ route('ghost.index') }}" style="display: inline-flex; align-items: center; gap: 8px; color: #4a5568; font-size: 14px; text-decoration: none; margin-bottom: 16px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    Retour à la liste
                </a>

                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <h1 style="margin: 0; color: #4a5568; font-size: 24px;">Facture Fantôme</h1>
                        <span style="background: #ed8936; color: white; padding: 4px 12px; border-radius: 12px; font-size: 11px; font-weight: 600; text-transform: uppercase;">Archive</span>
                    </div>

                    {{-- Bouton d'impression --}}
                    <a href="{{ route('ghost.print', $ghostInvoice) }}" target="_blank" class="btn btn-sm" style="background: #4a5568; color: white; border: none; padding: 8px 16px; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; font-size: 13px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 6 2 18 2 18 9"></polyline>
                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                            <rect x="6" y="14" width="12" height="8"></rect>
                        </svg>
                        Imprimer PDF
                    </a>

                    {{-- Indicateur ANNULEE si applicable --}}
                    @if($realInvoiceCancelled)
                        <div style="display: flex; align-items: center; gap: 8px; background: #fed7d7; color: #c53030; padding: 12px 20px; border-radius: 8px; border-left: 4px solid #fc8181;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                            <div>
                                <strong>LA FACTURE RÉELLE A ÉTÉ ANNULÉE</strong>
                                <p style="margin: 0; font-size: 12px;">Cette archive concerne une facture qui a été annulée dans le système.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Carte principale --}}
            <div style="background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; overflow: hidden;">
                {{-- En-tête de la facture --}}
                <div style="padding: 24px; background: #edf2f7; border-bottom: 1px solid #e2e8f0;">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px;">
                        <div>
                            <p style="margin: 0 0 4px; color: #718096; font-size: 12px; text-transform: uppercase;">Numéro Fantôme</p>
                            <p style="margin: 0; font-family: monospace; font-size: 18px; color: #2d3748; font-weight: 600;">{{ $ghostInvoice->number }}</p>
                        </div>
                        <div>
                            <p style="margin: 0 0 4px; color: #718096; font-size: 12px; text-transform: uppercase;">Client</p>
                            <p style="margin: 0; font-size: 18px; color: #2d3748; font-weight: 600;">{{ $ghostInvoice->client_name }}</p>
                            @if($ghostInvoice->client_phone)
                                <p style="margin: 4px 0 0; font-size: 14px; color: #718096;">{{ $ghostInvoice->client_phone }}</p>
                            @endif
                        </div>
                        <div>
                            <p style="margin: 0 0 4px; color: #718096; font-size: 12px; text-transform: uppercase;">Date de création</p>
                            <p style="margin: 0; font-size: 18px; color: #2d3748; font-weight: 600;">{{ $ghostInvoice->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <p style="margin: 0 0 4px; color: #718096; font-size: 12px; text-transform: uppercase;">Créée par</p>
                            <p style="margin: 0; font-size: 18px; color: #2d3748; font-weight: 600;">{{ $ghostInvoice->createdBy?->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Lignes articles --}}
                <div style="padding: 24px;">
                    <h3 style="margin: 0 0 16px; color: #4a5568; font-size: 16px; display: flex; align-items: center; gap: 8px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                        Articles (Lecture seule)
                    </h3>

                    <table style="width: 100%; border-collapse: collapse; background: #f7fafc; border-radius: 8px; overflow: hidden;">
                        <thead>
                            <tr style="background: #edf2f7;">
                                <th style="padding: 12px 16px; text-align: left; font-size: 12px; color: #4a5568; text-transform: uppercase; border-bottom: 2px solid #e2e8f0;">Désignation</th>
                                <th style="padding: 12px 16px; text-align: left; font-size: 12px; color: #4a5568; text-transform: uppercase; border-bottom: 2px solid #e2e8f0;">Unité</th>
                                <th style="padding: 12px 16px; text-align: right; font-size: 12px; color: #4a5568; text-transform: uppercase; border-bottom: 2px solid #e2e8f0;">Qté</th>
                                <th style="padding: 12px 16px; text-align: right; font-size: 12px; color: #4a5568; text-transform: uppercase; border-bottom: 2px solid #e2e8f0;">Prix unit.</th>
                                <th style="padding: 12px 16px; text-align: right; font-size: 12px; color: #4a5568; text-transform: uppercase; border-bottom: 2px solid #e2e8f0;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ghostInvoice->items as $item)
                                <tr style="border-bottom: 1px solid #e2e8f0;">
                                    <td style="padding: 16px; color: #2d3748; font-weight: 500;">{{ $item->designation }}</td>
                                    <td style="padding: 16px; color: #718096;">{{ $item->unit_sold ?? $item->unit ?? 'N/A' }}</td>
                                    <td style="padding: 16px; text-align: right; color: #2d3748;">{{ number_format($item->quantity_sold ?? $item->quantity ?? 0, 2) }}</td>
                                    <td style="padding: 16px; text-align: right; color: #2d3748;">{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                                    <td style="padding: 16px; text-align: right; color: #2d3748; font-weight: 600;">{{ number_format($item->total_price, 0, ',', ' ') }} FCFA</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="padding: 32px; text-align: center; color: #718096;">
                                        Aucun article enregistré.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Totaux --}}
                <div style="padding: 24px; background: #f7fafc; border-top: 1px solid #e2e8f0;">
                    <div style="display: flex; justify-content: flex-end;">
                        <div style="width: 100%; max-width: 300px;">
                            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e2e8f0;">
                                <span style="color: #718096;">Total</span>
                                <span style="font-weight: 600; color: #2d3748;">{{ number_format($ghostInvoice->total, 0, ',', ' ') }} FCFA</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e2e8f0;">
                                <span style="color: #718096;">Montant payé</span>
                                <span style="font-weight: 600; color: #2d3748;">{{ number_format($ghostInvoice->paid_amount, 0, ',', ' ') }} FCFA</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 12px 0; background: #edf2f7; margin: 8px -12px; padding: 12px; border-radius: 6px;">
                                <span style="color: #4a5568; font-weight: 600;">Solde restant</span>
                                <span style="font-weight: 700; color: #2d3748; font-size: 18px;">{{ number_format($ghostInvoice->balance, 0, ',', ' ') }} FCFA</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Informations complémentaires --}}
                <div style="padding: 24px; background: #edf2f7; border-top: 1px solid #e2e8f0;">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                        <div>
                            <p style="margin: 0 0 4px; color: #718096; font-size: 11px; text-transform: uppercase;">Statut original</p>
                            <span style="display: inline-block; padding: 4px 12px; background: #e2e8f0; color: #4a5568; border-radius: 4px; font-size: 12px; font-weight: 600; text-transform: uppercase;">
                                {{ $ghostInvoice->status }}
                            </span>
                        </div>
                        @if($ghostInvoice->due_date)
                            <div>
                                <p style="margin: 0 0 4px; color: #718096; font-size: 11px; text-transform: uppercase;">Date d'échéance</p>
                                <p style="margin: 0; font-size: 14px; color: #2d3748;">{{ $ghostInvoice->due_date->format('d/m/Y') }}</p>
                            </div>
                        @endif
                        @if($realInvoice)
                            <div>
                                <p style="margin: 0 0 4px; color: #718096; font-size: 11px; text-transform: uppercase;">ID Facture réelle</p>
                                <p style="margin: 0; font-size: 14px; color: #2d3748; font-family: monospace;">#{{ $realInvoice->id }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Footer informatif --}}
            <div style="margin-top: 24px; padding: 16px; background: #edf2f7; border-radius: 8px; border-left: 4px solid #ed8936;">
                <p style="margin: 0; color: #4a5568; font-size: 13px;">
                    <strong>Document d'archive :</strong> Cette facture fantôme est une copie immuable de la facture originale au moment de sa création. Elle ne peut pas être modifiée et sert de référence historique.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
