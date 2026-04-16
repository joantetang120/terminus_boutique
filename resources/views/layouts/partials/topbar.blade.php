<div class="topbar">
    {{-- On remplace le texte "Terminus-Boutique" par ton logo --}}
    <a href="{{ route('dashboard') }}" class="logo-container">
        <img src="{{ asset('img/logo-blue.png') }}" 
             alt="Terminus-Boutique" 
             style="height: 120px; width: 200px; object-fit: contain;">
    </a>

    <div class="topbar-right">
        <div style="font-size:0.875rem;color:#64748B; font-weight: 500;">
            {{ now()->format('d/m/Y') }}
        </div>
    </div>
</div>

<style>
    /* Ajoute ou modifie ces styles pour que le rendu soit propre */
    .topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 1.1rem;
        margin-bottom: 1rem;
        padding-top: 0.7rem;
        
        height: 80px;
        background: #ffffff;
        border-bottom: 1px solid #E2E8F0;
    }

    .logo-container {
        display: flex;
        align-items: center;
        text-decoration: none;
    }

    //
</style>