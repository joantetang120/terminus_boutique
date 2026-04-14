<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use App\Mail\ProfileUpdateVerification;

class ProfileController extends Controller
{
    public function show()
    {
        return view('profile.show');
    }

    public function requestUpdate(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'old_password' => 'required',
            'password' => 'nullable|min:8|confirmed',
        ]);

        if (!Hash::check($request->old_password, $user->password)) {
            return back()->withErrors(['old_password' => 'L’ancien mot de passe est incorrect.']);
        }

        $code = rand(1000, 9999);

        Session::put('profile_update_data', [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : null,
            'code' => $code,
        ]);

        dd($code);

        Mail::to($user->email)->send(new ProfileUpdateVerification($code));

        return redirect()->route('profile.verify_form')
                         ->with('success', 'Un code de confirmation a été envoyé.');
    }

    public function verifyForm()
    {
        if (!Session::has('profile_update_data')) {
            return redirect()->route('profile.show');
        }
        return view('profile.verify');
    }

    public function confirmUpdate(Request $request)
    {
        if (!Session::has('profile_update_data')) {
            return redirect()->route('profile.show')->withErrors(['error' => 'Session expirée.']);
        }

        $data = Session::get('profile_update_data');

        if ($request->code != $data['code']) {
            return back()->withErrors(['code' => 'Le code est incorrect.']);
        }

        $user = Auth::user();
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'] ?? $user->password,
        ]);

        Session::forget('profile_update_data');

        return redirect()->route('profile.show')->with('success', 'Profil mis à jour !');
    }
}