<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255|unique:users,name',
            'voornaam'   => 'required|string|max:255',
            'achternaam' => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:users,email',
            'password'   => 'required|string|min:8|confirmed',
        ], [
            'name.unique'      => 'Dit gebruikersnaam is al geregistreerd. Log in of gebruik een ander gebruikersnaam.',
            'email.unique'     => 'Dit e-mailadres is al in gebruik.',
            'password.min'     => 'Het wachtwoord moet minimaal 8 tekens bevatten.',
            'password.confirmed' => 'De wachtwoorden komen niet overeen.',
        ]);

        $user = User::create([
            'name'       => $request->name,
            'voornaam'   => $request->voornaam,
            'achternaam' => $request->achternaam,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => 'klant',
            'status'     => 'Actief',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('patient.dashboard')->with('success', 'Account succesvol aangemaakt! Welkom bij BNG Reizen!');
    }
}

