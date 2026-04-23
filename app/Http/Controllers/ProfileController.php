<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use App\Mail\ProfileUpdateVerification;
use Spatie\Activitylog\Models\Activity;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        // Check if user has ghost.view permission
        $hasGhostView = $user->hasPermissionTo('ghost.view');

        return view('profile.show', compact('hasGhostView'));
    }

    public function requestUpdate(Request $request)
    {
        $user = Auth::user();

        // Check if user has ghost.view permission
        $hasGhostView = $user->hasPermissionTo('ghost.view');

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'old_password' => 'required',
            'password' => 'nullable|min:8|confirmed',
            'ghost_division_coefficient' => 'nullable|numeric|min:1|max:100',
            'ghost_access_password' => 'nullable|min:4|max:255',
        ]);

        if (!Hash::check($request->old_password, $user->password)) {
            return back()->withErrors(['old_password' => 'L\'ancien mot de passe est incorrect.']);
        }

        // Determine what changed
        $changes = [];
        if ($request->name !== $user->name) {
            $changes['name'] = ['old' => $user->name, 'new' => $request->name];
        }
        if ($request->email !== $user->email) {
            $changes['email'] = ['old' => $user->email, 'new' => $request->email];
        }
        if ($request->password) {
            $changes['password'] = ['old' => 'hidden', 'new' => 'hidden'];
        }

        // Only allow ghost settings change if user has ghost.view permission
        $oldCoefficient = $user->ghost_division_coefficient ?? 2.0;
        $newCoefficient = $hasGhostView
            ? (float) ($request->ghost_division_coefficient ?? $oldCoefficient)
            : $oldCoefficient;

        if ($hasGhostView && abs($oldCoefficient - $newCoefficient) > 0.01) {
            $changes['ghost_division_coefficient'] = ['old' => $oldCoefficient, 'new' => $newCoefficient];
        }

        // Handle ghost access password
        $newGhostPassword = null;
        if ($hasGhostView && $request->ghost_access_password) {
            $newGhostPassword = Hash::make($request->ghost_access_password);
            $changes['ghost_access_password'] = ['old' => 'hidden', 'new' => 'hidden'];
        }

        $code = random_int(1000, 9999);
        $emailChanged = $request->email !== $user->email;

        // Store update data in session
        Session::put('profile_update_data', [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : null,
            'ghost_division_coefficient' => $newCoefficient,
            'ghost_access_password' => $newGhostPassword,
            'code' => $code,
            'old_email' => $user->email,
            'changes' => $changes,
            'email_changed' => $emailChanged,
        ]);

        // Send email to NEW email if changed, otherwise to OLD email
        $recipientEmail = $emailChanged ? $request->email : $user->email;

        Mail::to($recipientEmail)->send(new ProfileUpdateVerification($code, $changes, $emailChanged));

        $message = $emailChanged
            ? 'Un code de confirmation a été envoyé à votre nouvelle adresse email : ' . $request->email
            : 'Un code de confirmation a été envoyé à votre email.';

        return redirect()->route('profile.verify_form')
                         ->with('success', $message)
                         ->with('recipient_email', $recipientEmail);
    }

    public function verifyForm()
    {
        if (!Session::has('profile_update_data')) {
            return redirect()->route('profile.show');
        }

        $data = Session::get('profile_update_data');
        $recipientEmail = $data['email_changed'] ? $data['email'] : $data['old_email'];

        return view('profile.verify', [
            'recipient_email' => $recipientEmail,
            'email_changed' => $data['email_changed'],
            'changes' => $data['changes'],
        ]);
    }

    public function confirmUpdate(Request $request)
    {
        if (!Session::has('profile_update_data')) {
            return redirect()->route('profile.show')->withErrors(['error' => 'Session expirée.']);
        }

        $request->validate([
            'code' => 'required|string|size:4',
        ]);

        $data = Session::get('profile_update_data');

        if ($request->code !== (string) $data['code']) {
            return back()->withErrors(['code' => 'Le code est incorrect.']);
        }

        $user = Auth::user();

        // Log the changes before updating
        $activityDescription = $this->buildActivityDescription($data['changes']);

        $oldEmail = $user->email;

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'] ?? $user->password,
            'ghost_division_coefficient' => $data['ghost_division_coefficient'] ?? ($user->ghost_division_coefficient ?? 2.0),
        ];

        // Only update ghost_access_password if it was changed
        if (!empty($data['ghost_access_password'])) {
            $updateData['ghost_access_password'] = $data['ghost_access_password'];
        }

        $user->update($updateData);

        // Log activity manually for detailed tracking
        Activity::create([
            'log_name' => 'default',
            'description' => $activityDescription,
            'subject_type' => get_class($user),
            'subject_id' => $user->id,
            'causer_type' => get_class($user),
            'causer_id' => $user->id,
            'properties' => [
                'changes' => $data['changes'],
                'old_email' => $oldEmail,
                'new_email' => $data['email'],
                'verification_method' => 'email_code',
            ],
            'event' => 'updated',
        ]);

        Session::forget('profile_update_data');

        $successMessage = $data['email_changed']
            ? 'Profil mis à jour ! Votre nouvelle adresse email (' . $data['email'] . ') est maintenant active.'
            : 'Profil mis à jour avec succès !';

        return redirect()->route('profile.show')->with('success', $successMessage);
    }

    /**
     * Build a human-readable description of the changes made
     */
    private function buildActivityDescription(array $changes): string
    {
        $parts = [];

        if (isset($changes['name'])) {
            $parts[] = 'nom';
        }
        if (isset($changes['email'])) {
            $parts[] = 'email';
        }
        if (isset($changes['password'])) {
            $parts[] = 'mot de passe';
        }
        if (isset($changes['ghost_division_coefficient'])) {
            $parts[] = 'coefficient fantôme';
        }
        if (isset($changes['ghost_access_password'])) {
            $parts[] = 'mot de passe fantôme';
        }

        if (count($parts) === 1) {
            return 'Mise à jour du profil : ' . $parts[0] . ' modifié';
        } elseif (count($parts) > 1) {
            $last = array_pop($parts);
            return 'Mise à jour du profil : ' . implode(', ', $parts) . ' et ' . $last . ' modifiés';
        }

        return 'Mise à jour du profil';
    }
}