<x-app-layout title="Journal d'audit">
    <div class="page-header">
        <h1>Journal d'audit</h1>
        <div class="breadcrumb"><a href="{{ route('dashboard') }}">Accueil</a> > Administration > Journal d'audit</div>
    </div>

    <div class="table-wrapper">
        <form method="GET" class="table-toolbar">
            <select name="user_id" class="form-select" style="width:160px;">
                <option value="">Utilisateur</option>
                @foreach($users as $user)
                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
            <input type="text" name="action" class="form-input" placeholder="Action" value="{{ request('action') }}" style="width:140px;">
            <input type="date" name="date_from" class="form-input" style="width:140px;" value="{{ request('date_from') }}">
            <input type="date" name="date_to" class="form-input" style="width:140px;" value="{{ request('date_to') }}">
            <button type="submit" class="btn btn-secondary btn-sm">Filtrer</button>
        </form>

        <table>
            <thead><tr><th>Date/Heure</th><th>Utilisateur</th><th>Action</th><th>Entité</th><th>Détails</th></tr></thead>
            <tbody>
                @forelse($activities as $activity)
                <tr>
                    <td>{{ $activity->created_at->format('d/m/Y H:i:s') }}</td>
                    <td>{{ $activity->causer?->name ?? 'Système' }}</td>
                    <td><span class="badge badge-primary">{{ $activity->description }}</span></td>
                    <td>{{ class_basename($activity->subject_type) }} #{{ $activity->subject_id }}</td>
                    <td>
                        @if($activity->properties?->has('old') || $activity->properties?->has('attributes'))
                        <button class="btn btn-secondary btn-sm" x-data @click="$toggle('detail-{{ $activity->id }}')">Voir détails</button>
                        <div x-show="detail-{{ $activity->id }}" x-cloak style="margin-top:8px;font-size:0.75rem;font-family:monospace;background:#F8FAFC;padding:8px;border-radius:6px;">
                            @if($activity->properties->has('old'))
                            <div><strong>Ancien:</strong> {{ json_encode($activity->properties->get('old')) }}</div>
                            @endif
                            @if($activity->properties->has('attributes'))
                            <div><strong>Nouveau:</strong> {{ json_encode($activity->properties->get('attributes')) }}</div>
                            @endif
                        </div>
                        @else
                        —
                        @endif
                    </td>
                </tr>
                @empty
                <tr class="table-empty"><td colspan="5">Aucune entrée dans le journal.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:16px;">{{ $activities->links() }}</div>
    </div>
</x-app-layout>
