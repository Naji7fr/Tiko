<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * klant.controller — MVC Controller voor het klant-portaal.
 *
 * Klanten hebben een beperkte omgeving (geen medewerker- of eigenaarbeheer).
 * Toont dashboard met profielgegevens; afspraken volgen later.
 *
 * Security: auth + role:klant middleware op routes.
 */
class KlantController extends Controller
{
    /**
     * GET /klant/dashboard — Klant-dashboard met profiel uit klanten → gebruikers.
     */
    public function dashboard(): View
    {
        $user = Auth::user();
        $klant = $user->klant()?->with('gebruiker.contactGegevens')->first();

        return view('klant.dashboard', compact('user', 'klant'));
    }
}
