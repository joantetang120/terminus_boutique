<nav class="sidebar"
     x-data="{ expanded: false }"
     :class="{ 'sidebar-expanded': expanded }">

    {{-- Navigation --}}
    <div class="sidebar-section-label" x-show="expanded" x-cloak>Général</div>
    <ul class="sidebar-nav">
        <li>
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"
               :title="!expanded ? 'Tableau de bord' : ''">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" /></svg>
                <span x-show="expanded" x-cloak>Tableau de bord</span>
            </a>
        </li>
    </ul>

    @canany(['facture.view', 'ghost.view'])
    <div class="sidebar-section-label" x-show="expanded" x-cloak>Ventes</div>
    <ul class="sidebar-nav">
        @can('facture.view')
        <li>
            <a href="{{ route('factures.index') }}" class="{{ request()->routeIs('factures.*') ? 'active' : '' }}"
               :title="!expanded ? 'Factures' : ''">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                <span x-show="expanded" x-cloak>Factures</span>
            </a>
        </li>
        @endcan
        @can('ghost.view')
        <li>
            <a href="{{ route('ghost.index') }}" class="{{ request()->routeIs('ghost.*') ? 'active' : '' }}"
               :title="!expanded ? 'Factures Fantômes' : ''">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                <span x-show="expanded" x-cloak>Factures Fantômes</span>
            </a>
        </li>
        @endcan
    </ul>
    @endcanany

    @canany(['stock.view', 'product.view'])
    <div class="sidebar-section-label" x-show="expanded" x-cloak>Inventaire</div>
    <ul class="sidebar-nav">
        @can('stock.view')
        <li>
            <a href="{{ route('stock.index') }}" class="{{ request()->routeIs('stock.*') ? 'active' : '' }}"
               :title="!expanded ? 'Stock' : ''">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" /></svg>
                <span x-show="expanded" x-cloak>Stock</span>
            </a>
        </li>
        @endcan
        @can('product.view')
        <li>
            <a href="{{ route('produits.index') }}" class="{{ request()->routeIs('produits.*') ? 'active' : '' }}"
               :title="!expanded ? 'Produits' : ''">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" /></svg>
                <span x-show="expanded" x-cloak>Produits</span>
            </a>
        </li>
        @endcan
    </ul>
    @endcanany

    @canany(['compta.view', 'facture.payment'])
    <div class="sidebar-section-label" x-show="expanded" x-cloak>Finances</div>
    <ul class="sidebar-nav">
        @can('compta.view')
        <li>
            <a href="{{ route('comptabilite.index') }}" class="{{ request()->routeIs('comptabilite.index') ? 'active' : '' }}"
               :title="!expanded ? 'Comptabilité' : ''">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" /></svg>
                <span x-show="expanded" x-cloak>Comptabilité</span>
            </a>
        </li>
        @endcan
        @can('facture.view')
        <li class="nav-item {{ request()->routeIs('comptabilite.factures*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('comptabilite.factures.index') }}">
                <span class="nav-icon">📊</span>
                <span class="nav-text">Comptabilité des factures</span>
            </a>
        </li>
        @endcan
        @can('compta.view')
        <li>
            <a href="{{ route('expenses.index') }}" class="{{ request()->routeIs('expenses.*') ? 'active' : '' }}"
               :title="!expanded ? 'Dépenses' : ''">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" /></svg>
                <span x-show="expanded" x-cloak>Dépenses</span>
            </a>
        </li>
        @endcan
    </ul>
    @endcanany

    @canany(['user.view', 'audit.view'])
    <div class="sidebar-section-label" x-show="expanded" x-cloak>Administration</div>
    <ul class="sidebar-nav">
        @can('user.view')
        <li>
            <a href="{{ route('utilisateurs.index') }}" class="{{ request()->routeIs('utilisateurs.*') ? 'active' : '' }}"
               :title="!expanded ? 'Utilisateurs' : ''">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                <span x-show="expanded" x-cloak>Utilisateurs</span>
            </a>
        </li>
        @endcan
        @can('audit.view')
        <li>
            <a href="{{ route('audit.index') }}" class="{{ request()->routeIs('audit.*') ? 'active' : '' }}"
               :title="!expanded ? 'Journal d\'audit' : ''">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span x-show="expanded" x-cloak>Journal d'audit</span>
            </a>
        </li>
        @endcan
    </ul>
    @endcanany

    {{-- Footer: Profile + Collapse Toggle --}}
    <div class="sidebar-footer">
        {{-- Profile --}}
        <div class="sidebar-profile" x-data="{ open: false }">
            <button class="sidebar-profile-btn" @click="open = !open">
                <div class="sidebar-profile-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                <div class="sidebar-profile-info" x-show="expanded" x-cloak>
                    <div class="name">{{ auth()->user()->name }}</div>
                    <div class="role">{{ auth()->user()->getAllPermissions()->count() }} permissions</div>
                </div>
            </button>

            {{-- Dropdown --}}
            <div class="sidebar-profile-dropdown" x-show="open" @click.away="open = false" x-cloak>
                <a href="{{ route('profile.show') }}">Mon profil</a>
                <a href="#">Changer mot de passe</a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit">Déconnexion</button>
                </form>
            </div>
        </div>

        {{-- Divider --}}
        <div class="sidebar-footer-divider"></div>

        {{-- Collapse/Expand Button --}}
        <button class="sidebar-toggle-btn" @click="expanded = !expanded" :title="expanded ? 'Réduire' : 'Agrandir'">
            <svg x-show="!expanded" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
            <svg x-show="expanded" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
            <span x-show="expanded" x-cloak x-text="expanded ? 'Réduire' : 'Agrandir'"></span>
        </button>
    </div>
</nav>
