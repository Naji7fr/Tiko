<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Accounts beheer (CRUD) voor gebruikersrecords.
 *
 * In deze applicatie zijn "accounts" gewoon `User` records met een `role` en `status`.
 * Toegang tot deze controller wordt doorgaans afgedwongen via routes/middleware.
 */
class AccountController extends Controller
{
    /**
     * Toon het overzicht van alle accounts.
     */
    public function index()
    {
        $accounts = User::all();
        return view('accounts.index', compact('accounts'));
    }

    /**
     * Toon het formulier om een nieuw account aan te maken.
     */
    public function create()
    {
        return view('accounts.create');
    }

    /**
     * Sla een nieuw account op.
     *
     * - Valideert input (incl. unieke email).
     * - Hash't het wachtwoord voordat het wordt opgeslagen.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255|unique:users,name',
            'voornaam'   => 'nullable|string|max:255',
            'achternaam' => 'nullable|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|min:8',
            'role'       => 'required|in:admin,manager,financieel_medewerker,reisadviseur,klant',
            'status'     => 'required|in:Actief,Inactief',
        ], [
            'name.unique' => 'Gebruikersnaam bestaat al.',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);
        return redirect()->route('accounts.index')->with('success', 'Account toegevoegd!');
    }

    /**
     * Toon details van één account.
     */
    public function show($id)
    {
        $account = User::findOrFail($id);
        return view('accounts.show', compact('account'));
    }

    /**
     * Toon het bewerkformulier voor een account.
     */
    public function edit($id)
    {
        $account = User::findOrFail($id);
        return view('accounts.edit', compact('account'));
    }

    /**
     * Werk een account bij.
     *
     * Let op: wachtwoord is optioneel; als het veld leeg is, blijft het huidige wachtwoord staan.
     */
    public function update(Request $request, $id)
    {
        $account = User::findOrFail($id);
        $validated = $request->validate([
            'name'       => 'required|string|max:255|unique:users,name,' . $id,
            'voornaam'   => 'nullable|string|max:255',
            'achternaam' => 'nullable|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $id,
            'role'       => 'required|in:admin,manager,financieel_medewerker,reisadviseur,klant',
            'status'     => 'required|in:Actief,Inactief',
        ], [
            'name.unique' => 'Gebruikersnaam bestaat al.',
        ]);

        if ($request->filled('password')) {
            // Alleen hashen/opslaan als er een nieuw wachtwoord is opgegeven.
            $validated['password'] = Hash::make($request->password);
        }

        $account->update($validated);
        return redirect()->route('accounts.index')->with('success', 'Account bijgewerkt!');
    }

    /**
     * Verwijder een account.
     */
    public function destroy($id)
    {
        $account = User::findOrFail($id);
        $account->delete();
        return redirect()->route('accounts.index')->with('success', 'Account verwijderd!');
    }
}
