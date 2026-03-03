<?php

namespace App\Http\Controllers;

use App\Models\Reis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReisController extends Controller
{
    /**
     * Overzicht reizen voor ingelogde klant.
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->role !== 'klant') {
            abort(403, 'Alleen klanten hebben toegang tot het reisoverzicht.');
        }

        $reizen = Reis::orderBy('start_datum', 'desc')->get();
        return view('patient.reizen', compact('reizen'));
    }
}
