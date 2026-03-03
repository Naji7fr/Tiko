<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class KlantController extends Controller
{
    /**
     * Overzicht van alle klanten (users met role klant).
     */
    public function index()
    {
        $klanten = User::where('role', 'klant')->orderBy('name')->get();
        return view('klanten.index', compact('klanten'));
    }
}
