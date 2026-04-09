<x-app-layout title="{{ isset($utilisateur) ? 'Modifier' : 'Créer' }} un utilisateur">
    <div class="page-header">
        <h1>{{ isset($utilisateur) ? 'Modifier' : 'Créer' }} un utilisateur</h1>
        <div class="breadcrumb"><a href="{{ route('utilisateurs.index') }}">Utilisateurs</a> > {{ isset($utilisateur) ? 'Modifier' : 'Nouveau' }}</div>
    </div>

    <div class="card" style="max-width:800px;">
        <form action="{{ isset($utilisateur) ? route('utilisateurs.update', $utilisateur) : route('utilisateurs.store') }}" method="POST">
            @csrf
            @if(isset($utilisateur)) @method('PUT') @endif

            <div class="form-group">
                <label class="form-label" for="name">Nom</label>
                <input class="form-input" type="text" id="name" name="name" value="{{ old('name', $utilisateur->name ?? '') }}" required>
                @error('name')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input class="form-input" type="email" id="email" name="email" value="{{ old('email', $utilisateur->email ?? '') }}" required>
                @error('email')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Mot de passe {{ isset($utilisateur) ? '(laisser vide si inchangé)' : '' }}</label>
                <input class="form-input" type="password" id="password" name="password" {{ !isset($utilisateur) ? 'required' : '' }}>
                @error('password')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            @if(!isset($utilisateur))
            <div class="form-group">
                <label class="form-label" for="password_confirmation">Confirmer mot de passe</label>
                <input class="form-input" type="password" id="password_confirmation" name="password_confirmation" required>
            </div>
            @endif

            <h3 style="font-size:1rem;font-weight:600;margin:24px 0 16px;">Permissions</h3>
            <div style="display:flex;gap:8px;margin-bottom:12px;">
                <button type="button" class="btn btn-secondary btn-sm" onclick="selectAll()">Tout sélectionner</button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="deselectAll()">Tout désélectionner</button>
            </div>

            <div class="permissions-grid" style="grid-template-columns:repeat(5,1fr);">
                <div class="perm-header">Module</div>
                <div class="perm-header">Voir</div>
                <div class="perm-header">Créer</div>
                <div class="perm-header">Modifier</div>
                <div class="perm-header">Annuler</div>

                @php
                    $moduleList = [
                        'facture' => 'Facturation',
                        'stock' => 'Stock',
                        'compta' => 'Comptabilité',
                        'ghost' => 'Fact. Fantôme',
                        'user' => 'Utilisateurs',
                        'audit' => "Journal d'audit",
                        'product' => 'Produits',
                    ];
                    $actionList = ['view', 'create', 'edit', 'cancel'];
                    $allPerms = ($permissions ?? collect())->flatten()->pluck('name')->toArray();
                @endphp

                @foreach($moduleList as $mKey => $mLabel)
                <div class="perm-label">{{ $mLabel }}</div>
                @foreach($actionList as $act)
                @php
                    $pName = $mKey . '.' . $act;
                    $pExists = in_array($pName, $allPerms);
                    $pChecked = isset($utilisateur) ? $utilisateur->hasPermissionTo($pName) : false;
                    /* cancel doesn't exist for ghost, user, audit, product */
                    $showCheckbox = $pExists && !($act === 'cancel' && in_array($mKey, ['ghost', 'user', 'audit', 'product']));
                @endphp
                <div class="perm-cell">
                    @if($showCheckbox)
                        <input type="checkbox" name="permissions[]" value="{{ $pName }}" {{ $pChecked ? 'checked' : '' }}>
                    @else
                        —
                    @endif
                </div>
                @endforeach
                @endforeach
            </div>

            {{-- Approuver — only for Comptabilité — separate row --}}
            @php
                $approvePerm = 'compta.approve';
                $approveExists = in_array($approvePerm, $allPerms);
                $approveChecked = isset($utilisateur) ? $utilisateur->hasPermissionTo($approvePerm) : false;
            @endphp
            <div style="margin-top:12px;padding:12px 16px;background:#F8FAFC;border-radius:8px;border:1px solid #E2E8F0;display:flex;align-items:center;gap:12px;">
                <input type="checkbox" name="permissions[]" value="{{ $approvePerm }}" {{ ($approveExists && $approveChecked) ? 'checked' : '' }} id="perm_approve" style="width:18px;height:18px;cursor:pointer;">
                <label for="perm_approve" style="cursor:pointer;font-size:0.875rem;font-weight:500;color:#1A202C;">
                    Approuver — Comptabilité
                </label>
                <span style="font-size:0.75rem;color:#64748B;margin-left:auto;">Permet d'approuver ou rejeter les modifications comptables</span>
            </div>

            <div style="display:flex;gap:12px;margin-top:24px;">
                <a href="{{ route('utilisateurs.index') }}" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>

    <script>
        function selectAll() {
            document.querySelectorAll('input[name="permissions[]"]').forEach(c => c.checked = true);
        }
        function deselectAll() {
            document.querySelectorAll('input[name="permissions[]"]').forEach(c => c.checked = false);
        }
    </script>
</x-app-layout>
