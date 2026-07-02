<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Controller voor het klant-portaal.
 *
 * Klanten hebben een beperkte omgeving — geen toegang tot medewerkerbeheer.
 * Later uitbreidbaar met afspraken, profiel, etc.
 */
class KlantController extends Controller
{
    /**
     * Toon het klant-dashboard.
     */
    public function dashboard(): View
    {
        $user = Auth::user();
        $klant = $user->klant()?->with('gebruiker.contactGegevens')->first();

        return view('klant.dashboard', compact('user', 'klant'));
    }
}
