<?php

namespace App\Http\Controllers\Eigenaar;

use App\Http\Controllers\Controller;
use App\Models\Medewerker\TechnischeLogModel;
use App\Services\Eigenaar\EigenaarDashboardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * eigenaar.controller — Dashboard en overzicht voor de eigenaar.
 *
 * De eigenaar beheert medewerkers, klanten, afspraken, producten,
 * behandelingen, bestellingen en heeft toegang tot alle rapportages.
 */
class EigenaarDashboardController extends Controller
{
    public function __construct(
        private readonly EigenaarDashboardService $dashboardService
    ) {}

    /** GET /eigenaar/dashboard — Hoofdoverzicht. */
    public function index(): View|RedirectResponse
    {
        try {
            $gebruiker = Auth::user();
            $statistieken = $this->dashboardService->haalStatistiekenOp();
            $komendeAfspraken = $this->dashboardService->haalKomendeAfsprakenOp();
            $recenteBestellingen = $this->dashboardService->haalRecenteBestellingenOp();

            $modules = $this->haalBeheermodules();

            return view('eigenaar.dashboard.view', compact(
                'gebruiker',
                'statistieken',
                'komendeAfspraken',
                'recenteBestellingen',
                'modules'
            ));
        } catch (\Throwable $exception) {
            TechnischeLogModel::registreer('error', 'eigenaar', 'dashboard', $exception->getMessage());

            return redirect()->route('home')
                ->with('error', 'Dashboard kon niet worden geladen.');
        }
    }

    /** GET /eigenaar/rapportages — Rapportages en samenvattingen. */
    public function rapportages(): View|RedirectResponse
    {
        try {
            $rapportage = $this->dashboardService->haalRapportageSamenvattingOp();

            return view('eigenaar.rapportages.view', compact('rapportage'));
        } catch (\Throwable $exception) {
            TechnischeLogModel::registreer('error', 'eigenaar', 'rapportages', $exception->getMessage());

            return redirect()->route('eigenaar.dashboard')
                ->with('error', 'Rapportages konden niet worden geladen.');
        }
    }

    /** GET /eigenaar/{module} — Placeholder voor modules in ontwikkeling. */
    public function modulePlaceholder(string $module): View
    {
        $titel = match ($module) {
            'behandelingen' => 'Behandelingen',
            'bestellingen' => 'Bestellingen',
            default => 'Module',
        };

        return view('eigenaar.module.placeholder.view', compact('module', 'titel'));
    }

    /** @return list<array<string, mixed>> Beheermodule-kaarten voor het eigenaar-dashboard. */
    private function haalBeheermodules(): array
    {
        // actief: true = module is gebouwd; false = placeholder-pagina
        return [
            [
                'slug' => 'accounts',
                'titel' => 'Accounts',
                'icoon' => 'fa-user-shield',
                'beschrijving' => 'Loginaccounts voor eigenaar, medewerkers en klanten',
                'route' => route('eigenaar.accounts.index'),
                'actief' => true,
            ],
            [
                'slug' => 'medewerkers',
                'titel' => 'Medewerkers beheren',
                'icoon' => 'fa-users',
                'beschrijving' => 'Personeel toevoegen, wijzigen en verwijderen',
                'route' => route('medewerkers.index'),
                'actief' => true,
            ],
            [
                'slug' => 'klanten',
                'titel' => 'Klanten',
                'icoon' => 'fa-user-friends',
                'beschrijving' => 'Klantaccounts en contactgegevens',
                'route' => route('eigenaar.klanten.index'),
                'actief' => true,
            ],
            [
                'slug' => 'afspraken',
                'titel' => 'Afspraken',
                'icoon' => 'fa-calendar-check',
                'beschrijving' => 'Planning en agenda',
                'route' => route('afspraken.index'),
                'actief' => true,
            ],
            [
                'slug' => 'behandelingen',
                'titel' => 'Behandelingen',
                'icoon' => 'fa-cut',
                'beschrijving' => 'Diensten, prijzen en duur',
                'route' => route('eigenaar.module', 'behandelingen'),
                'actief' => false,
            ],
            [
                'slug' => 'producten',
                'titel' => 'Producten',
                'icoon' => 'fa-box',
                'beschrijving' => 'Voorraad, categorieën en leveranciers',
                'route' => route('producten.index'),
                'actief' => true,
            ],
            [
                'slug' => 'bestellingen',
                'titel' => 'Bestellingen',
                'icoon' => 'fa-shopping-cart',
                'beschrijving' => 'Orders en omzet',
                'route' => route('eigenaar.module', 'bestellingen'),
                'actief' => false,
            ],
            [
                'slug' => 'rapportages',
                'titel' => 'Rapportages',
                'icoon' => 'fa-chart-bar',
                'beschrijving' => 'Omzet, statistieken en analyses',
                'route' => route('eigenaar.rapportages'),
                'actief' => true,
            ],
        ];
    }
}
