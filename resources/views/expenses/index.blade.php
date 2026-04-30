<x-app-layout>
<x-slot name="title">Gestion des Dépenses</x-slot>

<div x-data="{ openModal: false }">
<div style="padding:24px;">
    <!-- Header -->
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
        <h1 style="font-size:1.5rem;font-weight:600;color:#1e293b;margin:0;">Gestion des Dépenses</h1>
        <button @click="openModal = true" class="btn btn-primary" style="display:flex;align-items:center;gap:8px;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Nouvelle dépense
        </button>
    </div>

    <!-- Statistics Cards -->
    <div style="display:grid;grid-template-columns:repeat(3, 1fr);gap:16px;margin-bottom:24px;">
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:20px;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;">
                <div>
                    <p style="font-size:0.875rem;color:#64748b;margin:0 0 8px 0;">Aujourd'hui</p>
                    <h3 style="font-size:1.5rem;font-weight:600;color:#1e293b;margin:0;">{{ number_format($todayExpenses, 0, ',', ' ') }} <span style="font-size:0.875rem;">FCFA</span></h3>
                </div>
                <div style="background:#dbeafe;border-radius:8px;padding:10px;">
                    <svg width="24" height="24" fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
        </div>
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:20px;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;">
                <div>
                    <p style="font-size:0.875rem;color:#64748b;margin:0 0 8px 0;">Ce mois</p>
                    <h3 style="font-size:1.5rem;font-weight:600;color:#1e293b;margin:0;">{{ number_format($thisMonthExpenses, 0, ',', ' ') }} <span style="font-size:0.875rem;">FCFA</span></h3>
                </div>
                <div style="background:#dcfce7;border-radius:8px;padding:10px;">
                    <svg width="24" height="24" fill="none" stroke="#16a34a" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
            </div>
        </div>
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:20px;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;">
                <div>
                    <p style="font-size:0.875rem;color:#64748b;margin:0 0 8px 0;">Total</p>
                    <h3 style="font-size:1.5rem;font-weight:600;color:#1e293b;margin:0;">{{ number_format($totalExpenses, 0, ',', ' ') }} <span style="font-size:0.875rem;">FCFA</span></h3>
                </div>
                <div style="background:#fef3c7;border-radius:8px;padding:10px;">
                    <svg width="24" height="24" fill="none" stroke="#d97706" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:16px;margin-bottom:24px;">
        <form method="GET" style="display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;">
            <div style="display:flex;flex-direction:column;gap:4px;">
                <label style="font-size:0.75rem;color:#64748b;font-weight:500;">Catégorie</label>
                <select name="category" style="padding:8px 12px;border:1px solid #e2e8f0;border-radius:6px;font-size:0.875rem;background:#fff;width:160px;">
                    <option value="">Toutes</option>
                    @foreach($usedCategories as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex;flex-direction:column;gap:4px;">
                <label style="font-size:0.75rem;color:#64748b;font-weight:500;">Du</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" style="padding:8px 12px;border:1px solid #e2e8f0;border-radius:6px;font-size:0.875rem;width:130px;">
            </div>
            <div style="display:flex;flex-direction:column;gap:4px;">
                <label style="font-size:0.75rem;color:#64748b;font-weight:500;">Au</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" style="padding:8px 12px;border:1px solid #e2e8f0;border-radius:6px;font-size:0.875rem;width:130px;">
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                <a href="{{ route('expenses.index') }}" style="padding:8px 16px;background:#e2e8f0;color:#475569;border-radius:6px;font-size:0.875rem;text-decoration:none;">Réinit.</a>
                <button type="submit" style="padding:8px 16px;background:#3b82f6;color:#fff;border:none;border-radius:6px;font-size:0.875rem;cursor:pointer;">Filtrer</button>
            </div>
        </form>
    </div>

    <!-- Expenses Table -->
    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                    <th style="padding:12px 16px;text-align:left;font-size:0.75rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;">Date</th>
                    <th style="padding:12px 16px;text-align:left;font-size:0.75rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;">Catégorie</th>
                    <th style="padding:12px 16px;text-align:left;font-size:0.75rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;">Libellé</th>
                    <th style="padding:12px 16px;text-align:right;font-size:0.75rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;">Montant</th>
                    <th style="padding:12px 16px;text-align:center;font-size:0.75rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;">Note</th>
                    <th style="padding:12px 16px;text-align:left;font-size:0.75rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;">Par</th>
                    <th style="padding:12px 16px;text-align:center;font-size:0.75rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                <tr style="border-bottom:1px solid #f1f5f9;transition:background 0.15s;" @mouseenter="$el.style.background='#f8fafc'" @mouseleave="$el.style.background=''">
                    <td style="padding:12px 16px;font-size:0.875rem;color:#64748b;white-space:nowrap;">{{ $expense->expense_date->format('d/m/Y') }}</td>
                    <td style="padding:12px 16px;">
                        <span style="display:inline-block;padding:4px 10px;background:#e2e8f0;color:#475569;font-size:0.75rem;font-weight:500;border-radius:12px;">{{ $expense->category }}</span>
                    </td>
                    <td style="padding:12px 16px;font-size:0.875rem;color:#1e293b;font-weight:500;">{{ $expense->label }}</td>
                    <td style="padding:12px 16px;font-size:0.875rem;font-weight:600;color:#dc2626;text-align:right;white-space:nowrap;">{{ number_format($expense->amount, 0, ',', ' ') }} FCFA</td>
                    <td style="padding:12px 16px;text-align:center;">
                        @if($expense->note)
                            <span style="color:#94a3b8;cursor:pointer;" title="{{ $expense->note }}">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                            </span>
                        @else
                            <span style="color:#cbd5e1;">—</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px;font-size:0.875rem;color:#64748b;">{{ $expense->createdBy?->name ?? 'N/A' }}</td>
                    <td style="padding:12px 16px;text-align:center;">
                        @can('compta.edit')
                        <div style="display:flex;justify-content:center;align-items:center;gap:8px;">
                            <button @click="$dispatch('open-edit-modal-{{ $expense->id }}')" style="background:none;border:none;cursor:pointer;padding:4px;color:#3b82f6;" title="Modifier">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <button @click="$dispatch('open-delete-modal-{{ $expense->id }}')" style="background:none;border:none;cursor:pointer;padding:4px;color:#dc2626;" title="Supprimer">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                        @else
                        <span style="color:#94a3b8;">—</span>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="padding:48px 16px;text-align:center;color:#94a3b8;">
                        Aucune dépense enregistrée
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:16px;border-top:1px solid #e2e8f0;">
            {{ $expenses->withQueryString()->links() }}
        </div>
    </div>
</div>

{{-- Create Modal --}}
<div x-show="openModal" style="display:none;position:fixed;inset:0;z-index:50;overflow-y:auto;" role="dialog" aria-modal="true">
    <div style="display:flex;align-items:center;justify-content:center;min-height:100vh;padding:16px;">
        <div x-show="openModal"
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             style="position:fixed;inset:0;background:rgba(0,0,0,0.4);"
             @click="openModal = false"></div>

        <div x-show="openModal"
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             style="position:relative;background:#fff;border-radius:10px;box-shadow:0 20px 60px rgba(0,0,0,0.15);width:100%;max-width:480px;z-index:51;">
            <form action="{{ route('expenses.store') }}" method="POST">
                @csrf
                <div style="padding:20px 24px;border-bottom:1px solid #e2e8f0;display:flex;justify-content:space-between;align-items:center;">
                    <h3 style="font-size:1.125rem;font-weight:600;color:#1e293b;margin:0;">Nouvelle dépense</h3>
                    <button type="button" @click="openModal = false" style="background:none;border:none;cursor:pointer;padding:4px;color:#64748b;">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div style="padding:20px 24px;">
                    <div style="margin-bottom:16px;" x-data="{ categoryType: 'existing', customCategory: '', selectedCategory: '' }">
                        <label style="display:block;font-size:0.875rem;font-weight:500;color:#374151;margin-bottom:6px;">Catégorie <span style="color:#dc2626;">*</span></label>
                        <div style="display:flex;gap:8px;margin-bottom:8px;">
                            <button type="button"
                                    @click="categoryType = 'existing'; customCategory = '';"
                                    :style="categoryType === 'existing' ? 'padding:6px 12px;background:#3b82f6;color:#fff;border:none;border-radius:6px;font-size:0.75rem;cursor:pointer;' : 'padding:6px 12px;background:#e2e8f0;color:#475569;border:none;border-radius:6px;font-size:0.75rem;cursor:pointer;'"
                                    style="padding:6px 12px;background:#3b82f6;color:#fff;border:none;border-radius:6px;font-size:0.75rem;cursor:pointer;">
                                Existante
                            </button>
                            <button type="button"
                                    @click="categoryType = 'custom'; selectedCategory = '';"
                                    :style="categoryType === 'custom' ? 'padding:6px 12px;background:#3b82f6;color:#fff;border:none;border-radius:6px;font-size:0.75rem;cursor:pointer;' : 'padding:6px 12px;background:#e2e8f0;color:#475569;border:none;border-radius:6px;font-size:0.75rem;cursor:pointer;'"
                                    style="padding:6px 12px;background:#e2e8f0;color:#475569;border:none;border-radius:6px;font-size:0.75rem;cursor:pointer;">
                                Nouvelle
                            </button>
                        </div>
                        <select x-show="categoryType === 'existing'"
                                x-model="selectedCategory"
                                @change="customCategory = selectedCategory"
                                name="category"
                                required
                                style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:6px;font-size:0.875rem;background:#fff;">
                            <option value="">Sélectionner une catégorie...</option>
                            @foreach($expenseCategories as $cat)
                                <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <input x-show="categoryType === 'custom'"
                               x-model="customCategory"
                               type="text"
                               name="category"
                               required
                               placeholder="Saisir une nouvelle catégorie..."
                               style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:6px;font-size:0.875rem;background:#fff;">
                    </div>
                    <div style="margin-bottom:16px;">
                        <label style="display:block;font-size:0.875rem;font-weight:500;color:#374151;margin-bottom:6px;">Libellé <span style="color:#dc2626;">*</span></label>
                        <input type="text" name="label" required placeholder="Description de la dépense" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:6px;font-size:0.875rem;background:#fff;">
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
                        <div>
                            <label style="display:block;font-size:0.875rem;font-weight:500;color:#374151;margin-bottom:6px;">Montant (FCFA) <span style="color:#dc2626;">*</span></label>
                            <input type="number" name="amount" step="0.01" min="0.01" required style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:6px;font-size:0.875rem;background:#fff;">
                        </div>
                        <div>
                            <label style="display:block;font-size:0.875rem;font-weight:500;color:#374151;margin-bottom:6px;">Date <span style="color:#dc2626;">*</span></label>
                            <input type="date" name="expense_date" value="{{ today()->format('Y-m-d') }}" required style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:6px;font-size:0.875rem;background:#fff;">
                        </div>
                    </div>
                    <div>
                        <label style="display:block;font-size:0.875rem;font-weight:500;color:#374151;margin-bottom:6px;">Note (optionnel)</label>
                        <textarea name="note" rows="3" placeholder="Détails supplémentaires..." style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:6px;font-size:0.875rem;background:#fff;resize:vertical;"></textarea>
                    </div>
                </div>
                <div style="padding:16px 24px;border-top:1px solid #e2e8f0;display:flex;justify-content:flex-end;gap:10px;">
                    <button type="button" @click="openModal = false" style="padding:10px 16px;background:#e2e8f0;color:#475569;border:none;border-radius:6px;font-size:0.875rem;cursor:pointer;font-weight:500;">Annuler</button>
                    <button type="submit" style="padding:10px 16px;background:#3b82f6;color:#fff;border:none;border-radius:6px;font-size:0.875rem;cursor:pointer;font-weight:500;">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Modals --}}
@foreach($expenses as $expense)
<div x-data="{ open: false, categoryType: {{ in_array($expense->category, $expenseCategories->pluck('name')->toArray()) ? "'existing'" : "'custom'" }}, customCategory: '{{ $expense->category }}', selectedCategory: '{{ $expense->category }}' }" @open-edit-modal-{{ $expense->id }}.window="open = true" x-show="open" style="display:none;position:fixed;inset:0;z-index:50;overflow-y:auto;" role="dialog" aria-modal="true">
    <div style="display:flex;align-items:center;justify-content:center;min-height:100vh;padding:16px;">
        <div x-show="open"
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             style="position:fixed;inset:0;background:rgba(0,0,0,0.4);"
             @click="open = false"></div>

        <div x-show="open"
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             style="position:relative;background:#fff;border-radius:10px;box-shadow:0 20px 60px rgba(0,0,0,0.15);width:100%;max-width:480px;z-index:51;">
            <form action="{{ route('expenses.update', $expense) }}" method="POST">
                @csrf
                @method('PUT')
                <div style="padding:20px 24px;border-bottom:1px solid #e2e8f0;display:flex;justify-content:space-between;align-items:center;">
                    <h3 style="font-size:1.125rem;font-weight:600;color:#1e293b;margin:0;">Modifier la dépense</h3>
                    <button type="button" @click="open = false" style="background:none;border:none;cursor:pointer;padding:4px;color:#64748b;">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div style="padding:20px 24px;">
                    <div style="margin-bottom:16px;">
                        <label style="display:block;font-size:0.875rem;font-weight:500;color:#374151;margin-bottom:6px;">Catégorie <span style="color:#dc2626;">*</span></label>
                        <div style="display:flex;gap:8px;margin-bottom:8px;">
                            <button type="button"
                                    @click="categoryType = 'existing'; customCategory = '';"
                                    :style="categoryType === 'existing' ? 'padding:6px 12px;background:#3b82f6;color:#fff;border:none;border-radius:6px;font-size:0.75rem;cursor:pointer;' : 'padding:6px 12px;background:#e2e8f0;color:#475569;border:none;border-radius:6px;font-size:0.75rem;cursor:pointer;'"
                                    style="padding:6px 12px;background:#3b82f6;color:#fff;border:none;border-radius:6px;font-size:0.75rem;cursor:pointer;">
                                Existante
                            </button>
                            <button type="button"
                                    @click="categoryType = 'custom'; selectedCategory = '';"
                                    :style="categoryType === 'custom' ? 'padding:6px 12px;background:#3b82f6;color:#fff;border:none;border-radius:6px;font-size:0.75rem;cursor:pointer;' : 'padding:6px 12px;background:#e2e8f0;color:#475569;border:none;border-radius:6px;font-size:0.75rem;cursor:pointer;'"
                                    style="padding:6px 12px;background:#e2e8f0;color:#475569;border:none;border-radius:6px;font-size:0.75rem;cursor:pointer;">
                                Nouvelle
                            </button>
                        </div>
                        <select x-show="categoryType === 'existing'"
                                x-model="selectedCategory"
                                @change="customCategory = selectedCategory"
                                name="category"
                                required
                                style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:6px;font-size:0.875rem;background:#fff;">
                            <option value="">Sélectionner une catégorie...</option>
                            @foreach($expenseCategories as $cat)
                                <option value="{{ $cat->name }}" {{ $cat->name == $expense->category ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <input x-show="categoryType === 'custom'"
                               x-model="customCategory"
                               type="text"
                               name="category"
                               required
                               placeholder="Saisir une nouvelle catégorie..."
                               style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:6px;font-size:0.875rem;background:#fff;">
                    </div>
                    <div style="margin-bottom:16px;">
                        <label style="display:block;font-size:0.875rem;font-weight:500;color:#374151;margin-bottom:6px;">Libellé <span style="color:#dc2626;">*</span></label>
                        <input type="text" name="label" value="{{ $expense->label }}" required style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:6px;font-size:0.875rem;background:#fff;">
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
                        <div>
                            <label style="display:block;font-size:0.875rem;font-weight:500;color:#374151;margin-bottom:6px;">Montant (FCFA) <span style="color:#dc2626;">*</span></label>
                            <input type="number" name="amount" step="0.01" min="0.01" value="{{ $expense->amount }}" required style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:6px;font-size:0.875rem;background:#fff;">
                        </div>
                        <div>
                            <label style="display:block;font-size:0.875rem;font-weight:500;color:#374151;margin-bottom:6px;">Date <span style="color:#dc2626;">*</span></label>
                            <input type="date" name="expense_date" value="{{ $expense->expense_date->format('Y-m-d') }}" required style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:6px;font-size:0.875rem;background:#fff;">
                        </div>
                    </div>
                    <div>
                        <label style="display:block;font-size:0.875rem;font-weight:500;color:#374151;margin-bottom:6px;">Note (optionnel)</label>
                        <textarea name="note" rows="3" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:6px;font-size:0.875rem;background:#fff;resize:vertical;">{{ $expense->note }}</textarea>
                    </div>
                </div>
                <div style="padding:16px 24px;border-top:1px solid #e2e8f0;display:flex;justify-content:flex-end;gap:10px;">
                    <button type="button" @click="open = false" style="padding:10px 16px;background:#e2e8f0;color:#475569;border:none;border-radius:6px;font-size:0.875rem;cursor:pointer;font-weight:500;">Annuler</button>
                    <button type="submit" style="padding:10px 16px;background:#3b82f6;color:#fff;border:none;border-radius:6px;font-size:0.875rem;cursor:pointer;font-weight:500;">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

{{-- Delete Confirmation Modals --}}
@foreach($expenses as $expense)
<div x-data="{ open: false }" @open-delete-modal-{{ $expense->id }}.window="open = true" x-show="open" style="display:none;position:fixed;inset:0;z-index:50;overflow-y:auto;" role="dialog" aria-modal="true">
    <div style="display:flex;align-items:center;justify-content:center;min-height:100vh;padding:16px;">
        <div x-show="open"
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             style="position:fixed;inset:0;background:rgba(0,0,0,0.4);"
             @click="open = false"></div>

        <div x-show="open"
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             style="position:relative;background:#fff;border-radius:10px;box-shadow:0 20px 60px rgba(0,0,0,0.15);width:100%;max-width:400px;z-index:51;">
            <form action="{{ route('expenses.destroy', $expense) }}" method="POST">
                @csrf
                @method('DELETE')
                <div style="padding:24px;text-align:center;">
                    <div style="width:56px;height:56px;background:#fee2e2;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                        <svg width="28" height="28" fill="none" stroke="#dc2626" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <h3 style="font-size:1.125rem;font-weight:600;color:#1e293b;margin:0 0 8px;">Confirmer la suppression</h3>
                    <p style="font-size:0.875rem;color:#64748b;margin:0 0 8px;">
                        Êtes-vous sûr de vouloir supprimer cette dépense ?
                    </p>
                    <div style="background:#f8fafc;border-radius:6px;padding:12px;margin-top:12px;text-align:left;">
                        <div style="font-size:0.75rem;color:#94a3b8;margin-bottom:4px;">Catégorie</div>
                        <div style="font-size:0.875rem;color:#374151;font-weight:500;">{{ $expense->category }}</div>
                        <div style="font-size:0.75rem;color:#94a3b8;margin:8px 0 4px;">Libellé</div>
                        <div style="font-size:0.875rem;color:#374151;font-weight:500;">{{ $expense->label }}</div>
                        <div style="font-size:0.75rem;color:#94a3b8;margin:8px 0 4px;">Montant</div>
                        <div style="font-size:0.875rem;color:#dc2626;font-weight:600;">{{ number_format($expense->amount, 0, ',', ' ') }} FCFA</div>
                    </div>
                </div>
                <div style="padding:16px 24px;border-top:1px solid #e2e8f0;display:flex;justify-content:center;gap:12px;">
                    <button type="button" @click="open = false" style="padding:10px 20px;background:#e2e8f0;color:#475569;border:none;border-radius:6px;font-size:0.875rem;cursor:pointer;font-weight:500;">Annuler</button>
                    <button type="submit" style="padding:10px 20px;background:#dc2626;color:#fff;border:none;border-radius:6px;font-size:0.875rem;cursor:pointer;font-weight:500;">Supprimer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
</div>
</x-app-layout>
