<x-app-layout>
    <div class="page-wrapper">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Hero Banner: Breadcrumb + Header fused --}}
            <div class="product-hero">
                <div class="product-hero__bg"></div>
                <div class="product-hero__content">

                    {{-- Breadcrumb --}}
                    <nav class="product-hero__breadcrumb" aria-label="Breadcrumb">
                        <ol class="breadcrumb-list">
                            <li>
                                <a href="{{ route('dashboard') }}" class="breadcrumb-link">
                                    <svg class="breadcrumb-icon" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2">
                                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                                    </svg>
                                    Tableau de bord
                                </a>
                            </li>
                            <li class="breadcrumb-sep" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </li>
                            <li>
                                <a href="{{ route('produits.index') }}" class="breadcrumb-link">Produits</a>
                            </li>
                            <li class="breadcrumb-sep" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </li>
                            <li>
                                <span class="breadcrumb-current"
                                    title="{{ $produit->name }}">{{ $produit->name }}</span>
                            </li>
                        </ol>
                    </nav>

                    {{-- Title Row --}}
                    <div class="product-hero__header">
                        <div class="product-hero__title-wrap">
                            <h1 class="product-hero__title">{{ $produit->name }}</h1>
                            @if ($produit->description)
                                <p class="product-hero__subtitle">{{ Str::limit($produit->description, 140) }}</p>
                            @endif
                        </div>
                        <div class="product-hero__actions">
                            @can('product.edit')
                                <a href="{{ route('produits.edit', $produit) }}" class="action-btn action-btn--primary">
                                    <svg class="action-btn__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                    <span>Modifier</span>
                                </a>
                            @endcan
                            <button type="button" onclick="window.history.back()" class="action-btn action-btn--ghost">
                                <svg class="action-btn__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <line x1="19" y1="12" x2="5" y2="12"></line>
                                    <polyline points="12 19 5 12 12 5"></polyline>
                                </svg>
                                <span>Retour</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="product-grid">

                {{-- Main Column --}}
                <div class="product-grid__main">

                    {{-- Stock Card --}}
                    <div class="panel stock-panel @if ($produit->isLowStock()) stock-panel--low @endif">
                        <div class="panel__header">
                            <div class="panel__header-left">
                                <svg class="panel__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
                                </svg>
                                <span class="panel__title">Stock actuel</span>
                            </div>
                            @if ($produit->isLowStock())
                                <span class="pill pill--danger">
                                    <svg class="pill__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="12" y1="8" x2="12" y2="12"></line>
                                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                    </svg>
                                    Stock bas
                                </span>
                            @endif
                        </div>

                        <div class="stock-panel__body">
                            {{-- Big Number --}}
                            <div class="stock-display">
                                <div class="stock-display__figure">
                                    <span
                                        class="stock-display__value {{ $produit->isLowStock() ? 'text-danger' : 'text-success' }}">
                                        {{ number_format($produit->current_stock, 0, ',', ' ') }}
                                    </span>
                                    <span
                                        class="stock-display__unit {{ $produit->isLowStock() ? 'text-danger' : 'text-success' }}">
                                        {{ $produit->unit }}
                                    </span>
                                </div>

                                @php
                                    $stockPercent =
                                        $produit->alert_threshold > 0
                                            ? min(
                                                100,
                                                max(
                                                    0,
                                                    ($produit->current_stock / ($produit->alert_threshold * 3)) * 100,
                                                ),
                                            )
                                            : 100;
                                @endphp

                                <div class="stock-gauge">
                                    <div class="stock-gauge__label">
                                        <span>Niveau</span>
                                        <span class="stock-gauge__percent">{{ round($stockPercent) }}%</span>
                                    </div>
                                    <div class="stock-gauge__track">
                                        <div class="stock-gauge__bar {{ $produit->isLowStock() ? 'stock-gauge__bar--low' : 'stock-gauge__bar--ok' }}"
                                            style="width: {{ $stockPercent }}%"></div>
                                    </div>
                                </div>
                            </div>

                            {{-- Threshold + Conversions row --}}
                            <div class="stock-panel__meta">
                                <div class="alert-row {{ $produit->isLowStock() ? 'alert-row--warn' : '' }}">
                                    <svg class="alert-row__icon" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2">
                                        <path
                                            d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z">
                                        </path>
                                        <line x1="12" y1="9" x2="12" y2="13"></line>
                                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                                    </svg>
                                    Seuil d'alerte : <strong>{{ $produit->alert_threshold }}
                                        {{ $produit->unit }}</strong>
                                </div>

                                @if ($produit->hasConversions() || $produit->purchase_unit || $produit->sale_unit)
                                    <div class="conversions">
                                        <span class="conversions__heading">Conversions</span>
                                        <div class="conversions__list">
                                            @if ($produit->purchase_unit)
                                                <span class="chip chip--blue">
                                                    <span class="chip__tag">Achat</span>
                                                    <span class="chip__text">1 {{ $produit->purchase_unit }} =
                                                        {{ $produit->purchase_conversion_rate }}
                                                        {{ $produit->unit }}</span>
                                                </span>
                                            @endif
                                            @forelse($produit->saleConversions as $conversion)
                                                <span class="chip chip--green">
                                                    <span class="chip__tag">Vente</span>
                                                    <span class="chip__text">1 {{ $conversion->unit }} =
                                                        {{ $conversion->conversion_rate }} {{ $produit->unit }}</span>
                                                </span>
                                            @empty
                                                @if ($produit->sale_unit)
                                                    <span class="chip chip--green">
                                                        <span class="chip__tag">Vente</span>
                                                        <span class="chip__text">1 {{ $produit->sale_unit }} =
                                                            {{ $produit->sale_conversion_rate }}
                                                            {{ $produit->unit }}</span>
                                                    </span>
                                                @endif
                                            @endforelse
                                        </div>
                                    </div>
                                @endif

                                {{-- Base Unit Price Section --}}
                                @if ($produit->base_sale_price)
                                    <div class="prices-section" style="margin-bottom: 16px;">
                                        <span class="conversions__heading" style="color: #1B3A6B;">Prix unité de base ({{ $produit->unit }})</span>
                                        <div class="price-card" style="border-color: #1B3A6B;">
                                            <div class="price-card__header" style="background: linear-gradient(135deg, #1B3A6B 0%, #2E75B6 100%);">
                                                <span class="price-card__unit" style="color: white;">{{ $produit->unit }}</span>
                                                <span class="price-card__rate" style="color: #BFDBFE;">Prix de base</span>
                                            </div>
                                            <div class="price-card__body">
                                                <div class="price-row">
                                                    <span class="price-label">Prix de vente</span>
                                                    <span class="price-value price-value--main">{{ number_format($produit->base_sale_price, 2, ',', ' ') }} FCFA</span>
                                                </div>
                                                @if ($produit->base_sale_margin_percentage)
                                                    <div class="price-row">
                                                        <span class="price-label">Marge réduction</span>
                                                        <span class="price-value price-value--margin">{{ $produit->base_sale_margin_percentage }}%</span>
                                                    </div>
                                                    <div class="price-row price-row--highlight">
                                                        <span class="price-label">Prix minimum</span>
                                                        <span class="price-value price-value--min">{{ number_format($produit->base_sale_price * (1 - $produit->base_sale_margin_percentage / 100), 2, ',', ' ') }} FCFA</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- Sale Prices Section --}}
                                @if ($produit->saleConversions->count() > 0 && $produit->saleConversions->first()->sale_price)
                                    <div class="prices-section">
                                        <span class="conversions__heading">Prix unités de vente</span>
                                        <div class="prices-list">
                                            @foreach ($produit->saleConversions as $conversion)
                                                @if ($conversion->sale_price)
                                                    <div class="price-card">
                                                        <div class="price-card__header">
                                                            <span
                                                                class="price-card__unit">{{ $conversion->unit }}</span>
                                                            <span class="price-card__rate">1 {{ $conversion->unit }} =
                                                                {{ $conversion->conversion_rate }}
                                                                {{ $produit->unit }}</span>
                                                        </div>
                                                        <div class="price-card__body">
                                                            <div class="price-row">
                                                                <span class="price-label">Prix de vente</span>
                                                                <span
                                                                    class="price-value price-value--main">{{ number_format($conversion->sale_price, 2, ',', ' ') }}
                                                                    FCFA</span>
                                                            </div>
                                                            @if ($conversion->sale_margin_percentage)
                                                                <div class="price-row">
                                                                    <span class="price-label">Marge réduction</span>
                                                                    <span
                                                                        class="price-value price-value--margin">{{ $conversion->sale_margin_percentage }}%</span>
                                                                </div>
                                                                <div class="price-row price-row--highlight">
                                                                    <span class="price-label">Prix minimum</span>
                                                                    <span
                                                                        class="price-value price-value--min">{{ number_format($conversion->minimum_price, 2, ',', ' ') }}
                                                                        FCFA</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Quick Actions --}}
                    @can('stock.create')
                        <div class="panel actions-panel">
                            <div class="panel__header">
                                <div class="panel__header-left">
                                    <svg class="panel__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="12" y1="8" x2="12" y2="16"></line>
                                        <line x1="8" y1="12" x2="16" y2="12"></line>
                                    </svg>
                                    <span class="panel__title">Actions rapides</span>
                                </div>
                            </div>
                            <div class="actions-panel__body">
                                <div class="action-cards">
                                    <a href="{{ route('stock.index') }}?type=entry&product_id={{ $produit->id }}"
                                        class="action-card action-card--entry">
                                        <span class="action-card__icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2.5">
                                                <line x1="12" y1="5" x2="12" y2="19">
                                                </line>
                                                <line x1="5" y1="12" x2="19" y2="12">
                                                </line>
                                            </svg>
                                        </span>
                                        <span class="action-card__label">Entrée de stock</span>
                                        <span class="action-card__sub">Ajouter au stock</span>
                                        <svg class="action-card__arrow" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2">
                                            <polyline points="9 18 15 12 9 6"></polyline>
                                        </svg>
                                    </a>
                                    <a href="{{ route('stock.index') }}?type=exit&product_id={{ $produit->id }}"
                                        class="action-card action-card--exit">
                                        <span class="action-card__icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2.5">
                                                <line x1="5" y1="12" x2="19" y2="12">
                                                </line>
                                            </svg>
                                        </span>
                                        <span class="action-card__label">Sortie de stock</span>
                                        <span class="action-card__sub">Retirer du stock</span>
                                        <svg class="action-card__arrow" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2">
                                            <polyline points="9 18 15 12 9 6"></polyline>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endcan

                    {{-- Stock History --}}
                    <div class="panel history-panel">
                        <div class="panel__header panel__header--split">
                            <div class="panel__header-left">
                                <svg class="panel__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                <span class="panel__title">Historique des mouvements</span>
                            </div>
                            <span class="panel__counter">{{ $produit->stockMovements->take(20)->count() }}
                                éléments</span>
                        </div>
                        <div class="history-panel__body">
                            @if ($produit->stockMovements->count() > 0)
                                <div class="timeline">
                                    @foreach ($produit->stockMovements->take(20) as $movement)
                                        <div
                                            class="timeline__item @if ($movement->is_cancelled) timeline__item--cancelled @endif">
                                            <span class="timeline__dot timeline__dot--{{ $movement->type }}">
                                                @if ($movement->type === 'entry')
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="3">
                                                        <line x1="12" y1="5" x2="12"
                                                            y2="19" />
                                                        <line x1="5" y1="12" x2="19"
                                                            y2="12" />
                                                    </svg>
                                                @else
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="3">
                                                        <line x1="5" y1="12" x2="19"
                                                            y2="12" />
                                                    </svg>
                                                @endif
                                            </span>
                                            <div class="timeline__card">
                                                <div class="timeline__top">
                                                    <span class="timeline__tag timeline__tag--{{ $movement->type }}">
                                                        {{ $movement->type === 'entry' ? 'Entrée' : 'Sortie' }}
                                                    </span>
                                                    <span
                                                        class="timeline__amount timeline__amount--{{ $movement->type }}">
                                                        {{ $movement->type === 'entry' ? '+' : '-' }}{{ number_format($movement->quantity, 0, ',', ' ') }}
                                                        {{ $produit->unit }}
                                                    </span>
                                                    @if ($movement->input_quantity && $movement->input_unit !== $produit->unit)
                                                        <span class="timeline__equiv">({{ $movement->input_quantity }}
                                                            {{ $movement->input_unit }})</span>
                                                    @endif
                                                </div>
                                                <p class="timeline__note">{{ $movement->note ?: '—' }}</p>
                                                <div class="timeline__footer">
                                                    <span>{{ $movement->createdBy->name ?? 'Système' }}</span>
                                                    <span class="timeline__dot-sep" aria-hidden="true">·</span>
                                                    <time
                                                        datetime="{{ $movement->created_at->toIso8601String() }}">{{ $movement->created_at->format('d/m/Y H:i') }}</time>
                                                    @if ($movement->is_cancelled)
                                                        <span class="timeline__dot-sep" aria-hidden="true">·</span>
                                                        <span class="timeline__cancelled">Annulé</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty-state">
                                    <div class="empty-state__icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="1.5">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <polyline points="12 6 12 12 16 14"></polyline>
                                        </svg>
                                    </div>
                                    <p class="empty-state__title">Aucun mouvement de stock</p>
                                    <p class="empty-state__desc">Les entrées et sorties apparaîtront ici</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Sidebar Column --}}
                <div class="product-grid__sidebar">
                    <div class="panel details-panel">
                        <div class="panel__header">
                            <div class="panel__header-left">
                                <svg class="panel__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2">
                                    </rect>
                                    <line x1="9" y1="9" x2="15" y2="9"></line>
                                    <line x1="9" y1="15" x2="15" y2="15"></line>
                                </svg>
                                <span class="panel__title">Détails du produit</span>
                            </div>
                        </div>
                        <div class="details-panel__body">
                            <div class="detail">
                                <span class="detail__label">Nom</span>
                                <span class="detail__value">{{ $produit->name }}</span>
                            </div>

                            @if ($produit->description)
                                <div class="detail">
                                    <span class="detail__label">Description</span>
                                    <span class="detail__value detail__value--body">{{ $produit->description }}</span>
                                </div>
                            @endif

                            <div class="detail">
                                <span class="detail__label">Unité de base</span>
                                <span class="detail__value">
                                    <span class="badge badge--indigo">{{ $produit->unit }}</span>
                                </span>
                            </div>

                            <div class="detail">
                                <span class="detail__label">Statut</span>
                                <span class="detail__value">
                                    @if ($produit->is_active)
                                        <span class="badge badge--success"><span
                                                class="badge__dot"></span>Actif</span>
                                    @else
                                        <span class="badge badge--danger"><span
                                                class="badge__dot badge__dot--off"></span>Inactif</span>
                                    @endif
                                </span>
                            </div>

                            <div class="detail__separator"></div>

                            <div class="detail">
                                <span class="detail__label">Créé par</span>
                                <span class="detail__value">{{ $produit->creator->name ?? 'Inconnu' }}</span>
                            </div>

                            <div class="detail">
                                <span class="detail__label">Date de création</span>
                                <span class="detail__value">{{ $produit->created_at->format('d/m/Y à H:i') }}</span>
                            </div>

                            @if ($produit->updated_at != $produit->created_at)
                                <div class="detail">
                                    <span class="detail__label">Dernière modification</span>
                                    <span
                                        class="detail__value">{{ $produit->updated_at->format('d/m/Y à H:i') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* ===== PAGE WRAPPER ===== */
        .page-wrapper {
            padding: 24px 0 48px;
        }

        /* ===== HERO BANNER ===== */
        .product-hero {
            position: relative;
            background: #fff;
            border: 1px solid var(--color-border, #e2e8f0);
            border-radius: 16px;
            padding: 20px 24px 22px;
            margin-bottom: 24px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .product-hero__bg {
            position: absolute;
            top: 0;
            right: 0;
            width: 320px;
            height: 100%;
            background: linear-gradient(135deg, rgba(27, 58, 107, 0.03) 0%, rgba(240, 165, 0, 0.04) 100%);
            border-radius: 0 16px 16px 0;
            pointer-events: none;
        }

        .product-hero__content {
            position: relative;
            z-index: 1;
        }

        /* Breadcrumb */
        .product-hero__breadcrumb {
            margin-bottom: 12px;
        }

        .breadcrumb-list {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 4px 6px;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .breadcrumb-link {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 0.8125rem;
            color: var(--color-text-muted, #64748b);
            text-decoration: none;
            transition: color 0.15s;
        }

        .breadcrumb-link:hover {
            color: var(--color-primary, #1B3A6B);
        }

        .breadcrumb-icon {
            width: 14px;
            height: 14px;
        }

        .breadcrumb-sep {
            width: 13px;
            height: 13px;
            color: #cbd5e1;
        }

        .breadcrumb-current {
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--color-text, #1a202c);
            max-width: 180px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Hero Header */
        .product-hero__header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
        }

        .product-hero__title {
            font-size: clamp(1.25rem, 2.5vw, 1.625rem);
            font-weight: 800;
            color: var(--color-text, #1a202c);
            letter-spacing: -0.02em;
            line-height: 1.25;
        }

        .product-hero__subtitle {
            margin-top: 4px;
            font-size: 0.875rem;
            color: var(--color-text-muted, #64748b);
            line-height: 1.5;
        }

        /* Hero Actions */
        .product-hero__actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 16px;
            border-radius: 10px;
            font-size: 0.8125rem;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            font-family: inherit;
            white-space: nowrap;
        }

        .action-btn__icon {
            width: 16px;
            height: 16px;
        }

        .action-btn--primary {
            background: var(--color-primary, #1B3A6B);
            color: #fff;
            box-shadow: 0 2px 8px rgba(27, 58, 107, 0.25);
        }

        .action-btn--primary:hover {
            background: var(--color-primary-hover, #15305A);
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(27, 58, 107, 0.32);
        }

        .action-btn--ghost {
            background: #f1f5f9;
            color: #475569;
        }

        .action-btn--ghost:hover {
            background: #e2e8f0;
            color: #334155;
        }

        /* ===== GRID LAYOUT ===== */
        .product-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        @media (min-width: 1024px) {
            .product-grid {
                grid-template-columns: minmax(0, 2fr) 340px;
                gap: 24px;
            }
        }

        .product-grid__sidebar {
            order: -1;
        }

        @media (min-width: 1024px) {
            .product-grid__sidebar {
                order: 0;
            }
        }

        /* ===== PANELS ===== */
        .panel {
            background: var(--color-surface, #fff);
            border: 1px solid var(--color-border, #e2e8f0);
            border-radius: 14px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            transition: box-shadow 0.2s;
        }

        .panel:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .panel__header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 14px 20px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.875rem;
        }

        .panel__header-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .panel__icon {
            width: 18px;
            height: 18px;
            color: #64748b;
        }

        .panel__title {
            font-weight: 700;
            color: var(--color-text, #1a202c);
        }

        .panel__counter {
            font-size: 0.6875rem;
            font-weight: 600;
            color: #94a3b8;
            background: #f1f5f9;
            padding: 3px 10px;
            border-radius: 9999px;
        }

        /* Pill */
        .pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 0.6875rem;
            font-weight: 700;
        }

        .pill--danger {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .pill__icon {
            width: 13px;
            height: 13px;
        }

        /* ===== STOCK PANEL ===== */
        .stock-panel__body {
            padding: 20px;
        }

        .stock-display {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .stock-display__figure {
            display: flex;
            align-items: baseline;
            gap: 6px;
        }

        .stock-display__value {
            font-size: clamp(2.5rem, 5vw, 3.5rem);
            font-weight: 900;
            letter-spacing: -0.03em;
            line-height: 1;
        }

        .stock-display__unit {
            font-size: clamp(1rem, 2vw, 1.25rem);
            font-weight: 600;
        }

        .text-success {
            color: #059669;
        }

        .text-danger {
            color: #dc2626;
        }

        /* Stock Gauge */
        .stock-gauge {
            min-width: 160px;
            max-width: 220px;
            flex: 1;
        }

        .stock-gauge__label {
            display: flex;
            justify-content: space-between;
            font-size: 0.75rem;
            font-weight: 600;
            color: #6b7280;
            margin-bottom: 6px;
        }

        .stock-gauge__percent {
            color: #9ca3af;
            font-weight: 500;
        }

        .stock-gauge__track {
            width: 100%;
            height: 7px;
            background: #f1f5f9;
            border-radius: 9999px;
            overflow: hidden;
        }

        .stock-gauge__bar {
            height: 100%;
            border-radius: 9999px;
            transition: width 0.7s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .stock-gauge__bar--ok {
            background: linear-gradient(90deg, #34d399, #059669);
        }

        .stock-gauge__bar--low {
            background: linear-gradient(90deg, #f87171, #dc2626);
        }

        /* Stock Meta */
        .stock-panel__meta {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        /* Alert Row */
        .alert-row {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 0.8125rem;
            padding: 10px 14px;
            border-radius: 10px;
            background: #f8fafc;
            color: #4b5563;
            transition: background 0.2s, color 0.2s;
        }

        .alert-row--warn {
            background: #fffbeb;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        .alert-row__icon {
            width: 15px;
            height: 15px;
            flex-shrink: 0;
        }

        /* Conversions */
        .conversions__heading {
            display: block;
            font-size: 0.6875rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #9ca3af;
            margin-bottom: 8px;
        }

        .conversions__list {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 5px 5px 5px 10px;
            border-radius: 8px;
            font-size: 0.8125rem;
            font-weight: 500;
        }

        .chip--blue {
            background: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }

        .chip--green {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .chip__tag {
            font-size: 0.625rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            opacity: 0.65;
        }

        /* ===== ACTION CARDS ===== */
        .actions-panel__body {
            padding: 16px 20px 20px;
        }

        .action-cards {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
        }

        @media (min-width: 480px) {
            .action-cards {
                grid-template-columns: 1fr 1fr;
            }
        }

        .action-card {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 2px;
            padding: 16px;
            border-radius: 12px;
            text-decoration: none;
            position: relative;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .action-card__arrow {
            position: absolute;
            right: 14px;
            top: 50%;
            width: 16px;
            height: 16px;
            transform: translateY(-50%);
            opacity: 0;
            transition: all 0.2s;
        }

        .action-card:hover .action-card__arrow {
            opacity: 1;
            right: 10px;
        }

        .action-card--entry {
            background: #f0fdf4;
            border: 1.5px solid #bbf7d0;
            color: #166534;
        }

        .action-card--entry:hover {
            background: #dcfce7;
            border-color: #86efac;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.12);
        }

        .action-card--exit {
            background: #fffbeb;
            border: 1.5px solid #fde68a;
            color: #92400e;
        }

        .action-card--exit:hover {
            background: #fef3c7;
            border-color: #fcd34d;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.12);
        }

        .action-card__icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            margin-bottom: 6px;
        }

        .action-card--entry .action-card__icon {
            background: #10b981;
            color: #fff;
        }

        .action-card--exit .action-card__icon {
            background: #f59e0b;
            color: #fff;
        }

        .action-card__icon svg {
            width: 18px;
            height: 18px;
        }

        .action-card__label {
            font-size: 0.8125rem;
            font-weight: 700;
        }

        .action-card__sub {
            font-size: 0.6875rem;
            opacity: 0.7;
        }

        /* ===== HISTORY / TIMELINE ===== */
        .history-panel__body {
            padding: 20px;
        }

        .timeline {
            position: relative;
            padding-left: 26px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 11px;
            top: 6px;
            bottom: 6px;
            width: 2px;
            background: #f1f5f9;
            border-radius: 1px;
        }

        .timeline__item {
            position: relative;
            padding-bottom: 18px;
        }

        .timeline__item:last-child {
            padding-bottom: 0;
        }

        .timeline__item--cancelled {
            opacity: 0.5;
        }

        .timeline__dot {
            position: absolute;
            left: -26px;
            top: 3px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid #fff;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
            z-index: 1;
            transition: transform 0.15s;
        }

        .timeline__item:hover .timeline__dot {
            transform: scale(1.15);
        }

        .timeline__dot svg {
            width: 12px;
            height: 12px;
            color: #fff;
        }

        .timeline__dot--entry {
            background: #10b981;
        }

        .timeline__dot--exit {
            background: #ef4444;
        }

        .timeline__card {
            background: #fafbfc;
            padding: 12px 14px;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            transition: all 0.15s;
        }

        .timeline__item:hover .timeline__card {
            border-color: #d1d5db;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .timeline__top {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 5px;
        }

        .timeline__tag {
            font-size: 0.625rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 3px 8px;
            border-radius: 6px;
        }

        .timeline__tag--entry {
            background: #d1fae5;
            color: #065f46;
        }

        .timeline__tag--exit {
            background: #fee2e2;
            color: #991b1b;
        }

        .timeline__amount {
            font-size: 0.875rem;
            font-weight: 700;
        }

        .timeline__amount--entry {
            color: #059669;
        }

        .timeline__amount--exit {
            color: #dc2626;
        }

        .timeline__equiv {
            font-size: 0.75rem;
            color: #9ca3af;
        }

        .timeline__note {
            font-size: 0.8125rem;
            color: #374151;
            line-height: 1.45;
            margin-bottom: 5px;
        }

        .timeline__footer {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.6875rem;
            color: #9ca3af;
        }

        .timeline__dot-sep {
            user-select: none;
        }

        .timeline__cancelled {
            background: #fecaca;
            color: #991b1b;
            padding: 1px 7px;
            border-radius: 4px;
            font-weight: 700;
        }

        /* ===== EMPTY STATE ===== */
        .empty-state {
            text-align: center;
            padding: 40px 16px 44px;
        }

        .empty-state__icon {
            width: 56px;
            height: 56px;
            margin: 0 auto 14px;
            border-radius: 16px;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .empty-state__icon svg {
            width: 26px;
            height: 26px;
            color: #9ca3af;
        }

        .empty-state__title {
            font-size: 0.875rem;
            font-weight: 600;
            color: #6b7280;
        }

        .empty-state__desc {
            font-size: 0.75rem;
            color: #9ca3af;
            margin-top: 3px;
        }

        /* ===== DETAILS PANEL (SIDEBAR) ===== */
        .details-panel__body {
            padding: 20px;
        }

        .detail {
            display: flex;
            flex-direction: column;
            gap: 5px;
            margin-bottom: 16px;
        }

        .detail:last-child {
            margin-bottom: 0;
        }

        .detail__label {
            font-size: 0.6875rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #9ca3af;
        }

        .detail__value {
            font-size: 0.875rem;
            color: var(--color-text, #1a202c);
            font-weight: 500;
        }

        .detail__value--body {
            font-weight: 400;
            line-height: 1.6;
            color: #374151;
        }

        .detail__separator {
            height: 1px;
            background: #f1f5f9;
            margin: 20px 0;
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 0.8125rem;
            font-weight: 600;
        }

        .badge--indigo {
            background: #eef2ff;
            color: #4338ca;
            border: 1px solid #c7d2fe;
        }

        .badge--success {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .badge--danger {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .badge__dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #22c55e;
        }

        .badge__dot--off {
            background: #ef4444;
        }

        /* ===== PRICES SECTION ===== */
        .prices-section {
            margin-top: 20px;
        }

        .prices-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .price-card {
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.2s ease;
        }

        .price-card:hover {
            border-color: #10b981;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.1);
        }

        .price-card__header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 14px;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border-bottom: 2px solid #bbf7d0;
        }

        .price-card__unit {
            font-size: 0.875rem;
            font-weight: 700;
            color: #166534;
            text-transform: capitalize;
        }

        .price-card__rate {
            font-size: 0.75rem;
            color: #15803d;
            font-weight: 500;
        }

        .price-card__body {
            padding: 14px;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px dashed #e5e7eb;
        }

        .price-row:last-child {
            border-bottom: none;
        }

        .price-row--highlight {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            margin: 8px -14px -14px -14px;
            padding: 12px 14px;
            border-radius: 0 0 10px 10px;
            border-top: 2px solid #f59e0b;
        }

        .price-label {
            font-size: 0.8125rem;
            color: #6b7280;
            font-weight: 500;
        }

        .price-value {
            font-size: 0.9375rem;
            font-weight: 700;
            color: #1f2937;
        }

        .price-value--main {
            color: #059669;
            font-size: 1.0625rem;
        }

        .price-value--margin {
            color: #7c3aed;
        }

        .price-value--min {
            color: #92400e;
            font-size: 1.0625rem;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 640px) {
            .product-hero {
                padding: 16px;
            }

            .product-hero__header {
                flex-direction: column;
            }

            .product-hero__actions {
                align-self: flex-start;
            }

            .stock-display {
                flex-direction: column;
                align-items: flex-start;
            }

            .stock-gauge {
                min-width: 100%;
                max-width: 100%;
            }

            .stock-display__value {
                font-size: 2.5rem;
            }
        }
    </style>
</x-app-layout>
