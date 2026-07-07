<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Medewerker\ContactGegevensModel;
use App\Models\Medewerker\GebruikerModel;
use App\Models\Klant;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Controller voor klant-registratie.
 *
 * Maakt alleen klant-accounts aan (nooit admin).
 * Koppelt User (login) + Gebruiker + Klant voor toekomstige features.
 */
class RegisterController extends Controller
{
    /**
     * Toon het registratieformulier voor klanten.
     */
    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    /**
     * Registreer een nieuw klant-account.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'voornaam' => ['required', 'string', 'max:50', 'regex:/^[\pL\s\-\'\.]+$/u'],
            'achternaam' => ['required', 'string', 'max:50', 'regex:/^[\pL\s\-\'\.]+$/u'],
            'email' => 'required|email|max:254|unique:users,email|unique:contact_gegevens,email',
            'telefoon' => ['nullable', 'string', 'max:25', 'regex:/^(?:\+31|0031|0)[1-9][0-9]{8}$/'],
            'password' => 'required|string|min:8|confirmed',
        ], [
            'email.unique' => 'Dit e-mailadres is al in gebruik.',
            'voornaam.regex' => 'Voornaam bevat ongeldige tekens.',
            'achternaam.regex' => 'Achternaam bevat ongeldige tekens.',
            'password.min' => 'Het wachtwoord moet minimaal 8 tekens bevatten.',
            'password.confirmed' => 'De wachtwoorden komen niet overeen.',
            'telefoon.regex' => 'Ongeldig telefoonnummer.',
        ]);

        $username = $this->generateUniqueUsername($validated['voornaam'], $validated['achternaam']);

        // Transactie: User + ContactGegevens + Gebruiker + Klant atomisch aanmaken
        DB::transaction(function () use ($validated, $username): void {
            // 1. Login-account (Laravel users)
            $user = User::create([
                'name' => $username,
                'voornaam' => $validated['voornaam'],
                'achternaam' => $validated['achternaam'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'klant',
                'status' => 'Actief',
            ]);

            // 2. Contactgegevens (e-mail, telefoon)
            $contact = ContactGegevensModel::create([
                'email' => $validated['email'],
                'telefoon' => $validated['telefoon'] ?? null,
            ]);

            // 3. Persoonsgegevens (gebruikers-tabel)
            $gebruiker = GebruikerModel::create([
                'contact_gegevens_id' => $contact->id,
                'voornaam' => $validated['voornaam'],
                'achternaam' => $validated['achternaam'],
                'volledig_naam' => GebruikerModel::bouwVolledigNaam(
                    $validated['voornaam'],
                    null,
                    $validated['achternaam']
                ),
            ]);

            // 4. Klantprofiel koppelen aan user + gebruiker
            Klant::create([
                'user_id' => $user->id,
                'gebruiker_id' => $gebruiker->id,
            ]);
        });

        // Direct inloggen na succesvolle registratie
        Auth::attempt([
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        $request->session()->regenerate();

        return redirect()->route('klant.dashboard')
            ->with('success', 'Welkom bij Tiko! Je klantaccount is aangemaakt.');
    }

    /**
     * Genereer een unieke gebruikersnaam op basis van naam.
     */
    private function generateUniqueUsername(string $voornaam, string $achternaam): string
    {
        $base = Str::slug($voornaam . '.' . $achternaam, '.');
        $username = $base;
        $counter = 1;

        while (User::where('name', $username)->exists()) {
            $username = $base . $counter;
            $counter++;
        }

        return $username;
    }
}
