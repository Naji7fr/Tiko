<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Check if user is active
            if (strtolower($user->status ?? '') !== 'actief') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Uw account is niet actief. Neem contact op met de beheerder.',
                ]);
            }

            // Redirect based on role
            if (in_array($user->role, ['admin', 'manager'])) {
                return redirect()->intended(route('home'));
            } else {
                return redirect()->intended(route('patient.dashboard'));
            }
        }

        throw ValidationException::withMessages([
            'email' => 'De opgegeven inloggegevens komen niet overeen met onze gegevens.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}

