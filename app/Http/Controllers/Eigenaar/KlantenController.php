<?php

namespace App\Http\Controllers\Eigenaar;

use App\Http\Controllers\Controller;
use App\Models\Medewerker\TechnischeLogModel;
use App\Services\Eigenaar\KlantenService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * klanten.controller — MVC Controller voor klantenbeheer (eigenaar).
 *
 * Architectuur:
 *   View  ← Controller ← Service ← Model / Stored Procedures
 *
 * Alleen lezen (overzicht); wijzigen/verwijderen loopt via Accounts-module.
 * Security: auth + role:admin middleware.
 */
class KlantenController extends Controller
{
    public function __construct(
        private readonly KlantenService $klantenService
    ) {}

    /**
     * GET /eigenaar/klanten — Overzicht klantgegevens via sp_klant_overzicht of JOINs.
     */
    public function index(): View|RedirectResponse
    {
        try {
            $klanten = $this->klantenService->haalKlantenOp();

            return view('eigenaar.klant.index.view', compact('klanten'));
        } catch (\Throwable $exception) {
            TechnischeLogModel::registreer('error', 'klanten', 'index', $exception->getMessage());

            return redirect()->route('eigenaar.dashboard')
                ->with('error', 'Klantenoverzicht kon niet worden geladen.');
        }
    }
}
