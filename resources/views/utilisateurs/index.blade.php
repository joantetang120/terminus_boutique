<x-app-layout title="Utilisateurs">
    <div class="page-header">
        <div class="page-header-actions">
            <div>
                <h1>Utilisateurs</h1>
                <div class="breadcrumb"><a href="{{ route('dashboard') }}">Accueil</a> > Administration > Utilisateurs</div>
            </div>
            @can('user.create')
            <a href="{{ route('utilisateurs.create') }}" class="btn btn-primary">+ Nouvel utilisateur</a>
            @endcan
        </div>
    </div>

    <div class="table-wrapper">
        <table>
            <thead><tr><th>Nom</th><th>Email</th><th>Permissions</th><th>Statut</th><th>Créé le</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td><strong>{{ $user->name }}</strong></td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->permissions_count }} permissions</td>
                    <td>
                        @if($user->is_active)
                            <span class="badge badge-success">Actif</span>
                        @else
                            <span class="badge badge-danger">Inactif</span>
                        @endif
                    </td>
                    <td>{{ $user->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div class="table-actions">
                            <a href="{{ route('utilisateurs.edit', $user) }}" class="btn btn-secondary btn-sm">Modifier</a>
                            <form action="{{ route('utilisateurs.toggle-status', $user) }}" method="POST" style="display:inline;">
                                @csrf @method('PATCH')
                                <button class="btn {{ $user->is_active ? 'btn-danger' : 'btn-success' }} btn-sm">
                                    {{ $user->is_active ? 'Désactiver' : 'Activer' }}
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="table-empty"><td colspan="6">Aucun utilisateur.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:16px;">{{ $users->links() }}</div>
    </div>
</x-app-layout>
