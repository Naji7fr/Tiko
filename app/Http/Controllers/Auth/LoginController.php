<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Controller voor inloggen en uitloggen (authenticatie).
 *
 * Redirect na login:
 *   admin   → eigenaar-dashboard
 *   medewerker → productbeheer
 *   klant   → klant-dashboard
 */
class LoginController extends Controller
{
    /**
     * Toon het inlogformulier.
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Verwerk een inlogpoging.
     *
     * @throws ValidationException
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (! Auth::attempt($credentials, $request->filled('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'De opgegeven inloggegevens komen niet overeen met onze gegevens.',
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // Alleen actieve accounts mogen inloggen
        if (strtolower($user->status ?? '') !== 'actief') {
            Auth::logout();

            return back()->withErrors([
                'email' => 'Uw account is niet actief. Neem contact op met de beheerder.',
            ]);
        }

        // Eigenaar → dashboard, medewerker → producten, klant → klant-portaal
        if ($user->isEigenaar()) {
            return redirect()->intended(route('eigenaar.dashboard'));
        }

        if ($user->isMedewerker()) {
            return redirect()->intended(route('producten.index'));
        }

        if ($user->isKlant()) {
            return redirect()->intended(route('klant.dashboard'));
        }

        return redirect()->intended(route('home'));
    }

    /**
     * Log de gebruiker uit en invalideer de sessie.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
