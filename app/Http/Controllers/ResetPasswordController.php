<?php

namespace App\Http\Controllers;

use App\Mail\PasswordResetCodeMail;
use App\Models\PasswordResetCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    /**
     * Step 1: Show forgot password form (enter email).
     */
    public function showForgot()
    {
        return view('auth.forgot-password');
    }

    /**
     * Step 1: Process email, generate code, send email.
     */
    public function sendCode(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Rate limit: max 3 emails per hour per IP
        $emailLimiterKey = 'password-reset-email:' . $request->ip();
        if (RateLimiter::tooManyAttempts($emailLimiterKey, 3)) {
            $seconds = RateLimiter::availableIn($emailLimiterKey);
            return back()->withErrors(['email' => "Trop de tentatives. Réessayez dans {$seconds} secondes."]);
        }

        // Check if user exists
        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            RateLimiter::hit($emailLimiterKey, 3600);
            return back()->withErrors(['email' => 'Cette adresse email n\'est associée à aucun compte.']);
        }

        RateLimiter::hit($emailLimiterKey, 3600);

        $code = PasswordResetCode::generateFor($validated['email']);
        Mail::to($validated['email'])->send(new PasswordResetCodeMail($code->code, 10));

        // Store email in session for next step
        $request->session()->put('reset_email', $validated['email']);

        return redirect()->route('password.verify');
    }

    /**
     * Step 2: Show code verification form.
     */
    public function showVerify(Request $request)
    {
        if (!$request->session()->has('reset_email')) {
            return redirect()->route('password.forgot');
        }

        return view('auth.verify-code', [
            'email' => $request->session()->get('reset_email'),
        ]);
    }

    /**
     * Step 2: Verify the 4-digit code.
     */
    public function verifyCode(Request $request)
    {
        if (!$request->session()->has('reset_email')) {
            return redirect()->route('password.forgot');
        }

        $validated = $request->validate([
            'code' => ['required', 'string', 'digits:4'],
        ]);

        $email = $request->session()->get('reset_email');

        // Rate limit: max 10 attempts per minute per email
        $attemptLimiterKey = 'password-reset-attempt:' . $email;
        if (RateLimiter::tooManyAttempts($attemptLimiterKey, 10)) {
            $seconds = RateLimiter::availableIn($attemptLimiterKey);
            return back()->withErrors(['code' => "Trop de tentatives. Réessayez dans {$seconds} secondes."]);
        }

        $codeRecord = PasswordResetCode::where('email', $email)
            ->where('used', false)
            ->where('code', $validated['code'])
            ->latest()
            ->first();

        if (!$codeRecord) {
            // Check if there's any active code for this email to track attempts
            $activeCode = PasswordResetCode::where('email', $email)
                ->where('used', false)
                ->latest()
                ->first();

            if ($activeCode) {
                RateLimiter::hit($attemptLimiterKey, 60);
                $activeCode->increment('attempts');
                $activeCode->update(['last_attempt_at' => now()]);

                if ($activeCode->isLocked()) {
                    return back()->withErrors(['code' => 'Code bloqué après trop de tentatives. Demandez un nouveau code.'])->with('code_locked', true);
                }

                $remaining = $activeCode->remainingAttempts();
                return back()->withErrors(['code' => "Code incorrect. {$remaining} essai(s) restant(s)."]);
            }

            return back()->withErrors(['code' => 'Aucun code actif trouvé. Demandez-en un nouveau.']);
        }

        if ($codeRecord->isExpired()) {
            return back()->withErrors(['code' => 'Ce code a expiré. Demandez-en un nouveau.']);
        }

        if ($codeRecord->isLocked()) {
            return back()->withErrors(['code' => 'Code bloqué après trop de tentatives. Demandez un nouveau code.'])->with('code_locked', true);
        }

        // Mark as used
        $codeRecord->update(['used' => true]);

        // Store in session that code is verified
        $request->session()->put('password_reset_verified', true);
        $request->session()->put('password_reset_verified_at', now());

        return redirect()->route('password.reset');
    }

    /**
     * Step 2b: Resend code.
     */
    public function resendCode(Request $request)
    {
        if (!$request->session()->has('reset_email')) {
            return redirect()->route('password.forgot');
        }

        $email = $request->session()->get('reset_email');

        // Rate limit resend: 1 per 60 seconds
        $resendKey = 'password-resend:' . $email;
        if (RateLimiter::tooManyAttempts($resendKey, 1)) {
            $seconds = RateLimiter::availableIn($resendKey);
            return back()->withErrors(['code' => "Veuillez attendre {$seconds} secondes avant de renvoyer un code."]);
        }
        RateLimiter::hit($resendKey, 60);

        $userExists = User::where('email', $email)->exists();

        if ($userExists) {
            // Invalidate all previous unused codes for this email
            PasswordResetCode::where('email', $email)
                ->where('used', false)
                ->update(['used' => true]);

            // Generate and send new code
            $code = PasswordResetCode::generateFor($email);
            Mail::to($email)->send(new PasswordResetCodeMail($code->code, 10));
        }

        return back()->with('success', 'Un nouveau code a été envoyé. L\'ancien code n\'est plus valide.');
    }

    /**
     * Step 3: Show new password form.
     */
    public function showReset(Request $request)
    {
        if (!$request->session()->get('password_reset_verified')) {
            return redirect()->route('password.forgot');
        }

        // Verify code was verified within last 15 minutes
        $verifiedAt = $request->session()->get('password_reset_verified_at');
        if (!$verifiedAt || now()->diffInMinutes($verifiedAt) > 15) {
            $request->session()->forget(['password_reset_verified', 'password_reset_verified_at', 'reset_email']);
            return redirect()->route('password.forgot')->with('warning', 'La session a expiré. Recommencez la procédure.');
        }

        return view('auth.reset-password', [
            'email' => $request->session()->get('reset_email'),
        ]);
    }

    /**
     * Step 3: Process new password.
     */
    public function resetPassword(Request $request)
    {
        if (!$request->session()->get('password_reset_verified')) {
            return redirect()->route('password.forgot');
        }

        $verifiedAt = $request->session()->get('password_reset_verified_at');
        if (!$verifiedAt || now()->diffInMinutes($verifiedAt) > 15) {
            $request->session()->forget(['password_reset_verified', 'password_reset_verified_at', 'reset_email']);
            return redirect()->route('password.forgot');
        }

        $validated = $request->validate([
            'password' => ['required', 'min:6', 'confirmed'],
        ]);

        $email = $request->session()->get('reset_email');
        $user = User::where('email', $email)->first();

        if ($user) {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        // Clean up session
        $request->session()->forget(['password_reset_verified', 'password_reset_verified_at', 'reset_email']);

        // Log the user in automatically
        if ($user) {
            Auth::login($user);
            return redirect()->route('dashboard')->with('success', 'Mot de passe réinitialisé avec succès.');
        }

        return redirect()->route('login')->with('success', 'Mot de passe réinitialisé. Vous pouvez maintenant vous connecter.');
    }
}
