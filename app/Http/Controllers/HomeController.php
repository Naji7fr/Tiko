<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * Controller voor de publieke startpagina.
 */
class HomeController extends Controller
{
    /**
     * Toon de welkomstpagina.
     */
    public function index(): View
    {
        return view('home');
    }
}
