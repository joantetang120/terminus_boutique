<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    public function index()
    {
        $users = User::withCount(['permissions'])->latest()->paginate(20);
        return view('utilisateurs.index', compact('users'));
    }

    public function create()
    {
        $permissions = Permission::all()->groupBy(function($p) {
            return explode('.', $p->name)[0];
        });
        return view('utilisateurs.form', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'permissions' => 'array',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'created_by' => Auth::id(),
        ]);

        if (!empty($validated['permissions'])) {
            $user->syncPermissions($validated['permissions']);
        }

        // Send welcome email to the new user
        $createdByName = Auth::user()->name;
        Mail::to($user->email)->send(new WelcomeMail($user->name, $createdByName));

        return redirect()->route('utilisateurs.index')->with('success', 'Utilisateur créé avec succès.');
    }

    public function edit(User $utilisateur)
    {
        $permissions = Permission::all()->groupBy(function($p) {
            return explode('.', $p->name)[0];
        });
        return view('utilisateurs.form', compact('utilisateur', 'permissions'));
    }

    public function update(Request $request, User $utilisateur)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $utilisateur->id,
            'password' => 'nullable|min:6',
            'permissions' => 'array',
        ]);

        $utilisateur->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if (!empty($validated['password'])) {
            $utilisateur->update(['password' => Hash::make($validated['password'])]);
        }

        $utilisateur->syncPermissions($validated['permissions'] ?? []);

        return redirect()->route('utilisateurs.index')->with('success', 'Utilisateur modifié avec succès.');
    }

    public function toggleStatus(User $utilisateur)
    {
        $utilisateur->update(['is_active' => !$utilisateur->is_active]);
        $message = $utilisateur->is_active ? 'Utilisateur activé.' : 'Utilisateur désactivé.';
        return back()->with('success', $message);
    }
}
