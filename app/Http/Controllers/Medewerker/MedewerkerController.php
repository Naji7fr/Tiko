<?php

namespace App\Http\Controllers\Medewerker;

use App\Http\Controllers\Controller;
use App\Http\Requests\Medewerker\StoreMedewerkerRequest;
use App\Http\Requests\Medewerker\UpdateMedewerkerRequest;
use App\Models\Medewerker\MedewerkerModel;
use App\Models\Medewerker\SpecialisatieModel;
use App\Models\Medewerker\TechnischeLogModel;
use App\Services\Medewerker\MedewerkerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * medewerker.controller — MVC Controller voor medewerkerbeheer.
 *
 * Architectuur:
 *   View  ← Controller ← Service ← Model / Stored Procedures
 *
 * Bevat try-catch, technische logging en terugkoppeling via flash-meldingen.
 * Security: auth + role middleware, FormRequest validatie, CSRF in views.
 */
class MedewerkerController extends Controller
{
    public function __construct(
        private readonly MedewerkerService $medewerkerService
    ) {}

    /**
     * GET /medewerkers — Overzicht met JOINs.
     */
    public function index(): View|RedirectResponse
    {
        try {
            $medewerkers = $this->medewerkerService->haalMedewerkersOp();

            if ($medewerkers instanceof \Illuminate\Support\Collection) {
                return view('medewerker.index.view', compact('medewerkers'));
            }

            // Stored procedure resultaat omzetten naar collectie voor de view
            $medewerkers = collect($medewerkers)->map(function (object $rij): object {
                $rij->naam = $rij->volledig_naam;
                $rij->email = $rij->email;
                $rij->telefoonnummer = $rij->telefoon;
                $rij->status = $rij->is_actief ? 'Actief' : 'Inactief';
                $rij->specialisatie = (object) ['naam' => $rij->specialisatie_naam];
                $rij->id = $rij->medewerker_id;

                return $rij;
            });

            return view('medewerker.index.view', compact('medewerkers'));
        } catch (\Throwable $exception) {
            TechnischeLogModel::registreer('error', 'medewerker', 'index', $exception->getMessage());

            return redirect()->route('home')
                ->with('error', 'Overzicht kon niet worden geladen. Probeer het later opnieuw.');
        }
    }

    /** GET /medewerkers/create — Formulier nieuwe medewerker. */
    public function create(): View
    {
        $specialisaties = SpecialisatieModel::orderBy('naam')->get();

        return view('medewerker.create.view', compact('specialisaties'));
    }

    /** POST /medewerkers — Medewerker opslaan (server + client validatie). */
    public function store(StoreMedewerkerRequest $request): RedirectResponse
    {
        try {
            $this->medewerkerService->voegMedewerkerToe($request->validated());

            return redirect()
                ->route('medewerkers.index')
                ->with('success', 'Medewerker succesvol toegevoegd!');
        } catch (\Throwable $exception) {
            TechnischeLogModel::registreer('error', 'medewerker', 'store', $exception->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Medewerker kon niet worden toegevoegd. Controleer de gegevens.');
        }
    }

    /** GET /medewerkers/{id} — Detailweergave. */
    public function show(MedewerkerModel $medewerker): View|RedirectResponse
    {
        try {
            $medewerkerDetail = MedewerkerModel::haalDetailMetJoins($medewerker->id)
                ?? $medewerker->load(['gebruiker.contactGegevens', 'specialisatie']);

            return view('medewerker.show.view', [
                'medewerker' => $medewerkerDetail,
            ]);
        } catch (\Throwable $exception) {
            TechnischeLogModel::registreer('error', 'medewerker', 'show', $exception->getMessage());

            return redirect()
                ->route('medewerkers.index')
                ->with('error', 'Medewerker kon niet worden weergegeven.');
        }
    }

    /** GET /medewerkers/{id}/edit — Wijzigformulier. */
    public function edit(MedewerkerModel $medewerker): View
    {
        $medewerker->load(['gebruiker.contactGegevens', 'specialisatie']);
        $specialisaties = SpecialisatieModel::orderBy('naam')->get();

        return view('medewerker.edit.view', compact('medewerker', 'specialisaties'));
    }

    /** PUT /medewerkers/{id} — Medewerker bijwerken. */
    public function update(UpdateMedewerkerRequest $request, MedewerkerModel $medewerker): RedirectResponse
    {
        try {
            $medewerker->load('gebruiker.contactGegevens');
            $this->medewerkerService->wijzigMedewerker($medewerker, $request->validated());

            return redirect()
                ->route('medewerkers.index')
                ->with('success', 'Medewerker succesvol gewijzigd!');
        } catch (\Throwable $exception) {
            TechnischeLogModel::registreer('error', 'medewerker', 'update', $exception->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Wijzigingen konden niet worden opgeslagen.');
        }
    }

    /** DELETE /medewerkers/{id} — Inactieve medewerker verwijderen. */
    public function destroy(MedewerkerModel $medewerker): RedirectResponse
    {
        if ($medewerker->is_actief) {
            // Business rule: actieve medewerkers niet verwijderbaar (ook in SP + modal)
            return redirect()
                ->route('medewerkers.index')
                ->with('error', 'Actieve medewerkers kunnen niet worden verwijderd');
        }

        try {
            $this->medewerkerService->verwijderMedewerker($medewerker);

            return redirect()
                ->route('medewerkers.index')
                ->with('success', 'Medewerker succesvol verwijderd!');
        } catch (\Throwable $exception) {
            TechnischeLogModel::registreer('error', 'medewerker', 'destroy', $exception->getMessage());

            return redirect()
                ->route('medewerkers.index')
                ->with('error', 'Medewerker kon niet worden verwijderd.');
        }
    }
}
