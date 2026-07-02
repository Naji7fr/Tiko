<?php

namespace App\Http\Controllers;

use App\Http\Requests\Afspraak\StoreAfspraakRequest;
use App\Http\Requests\Afspraak\UpdateAfspraakRequest;
use App\Models\Afspraak;
use App\Models\Behandeling;
use App\Models\Klant;
use App\Models\Medewerker\MedewerkerModel;
use App\Models\Medewerker\TechnischeLogModel;
use App\Services\Medewerker\MedewerkerBeschikbaarheidService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AfspraakController extends Controller
{
    public function __construct(
        private readonly MedewerkerBeschikbaarheidService $beschikbaarheidService
    ) {}

    /** GET /afspraken — Volledige planning voor medewerker en eigenaar. */
    public function index(): View|RedirectResponse
    {
        abort_unless(auth()->user()?->isAdmin() || auth()->user()?->isMedewerker(), 403);

        try {
            $afspraken = Afspraak::query()
                ->with(['klant.gebruiker.contactGegevens', 'medewerker.gebruiker.contactGegevens', 'behandeling'])
                ->orderBy('afspraak_datum')
                ->orderBy('afspraak_tijd')
                ->get();

            return view('afspraken.index', compact('afspraken'));
        } catch (\Throwable $exception) {
            TechnischeLogModel::registreer('error', 'afspraak', 'index', $exception->getMessage());

            return redirect()->route('home')->with('error', 'Het afsprakenoverzicht kon niet worden geladen.');
        }
    }

    /** GET /afspraken/create — Formulier voor nieuw afspraak. */
    public function create(): View
    {
        $behandelingen = Behandeling::orderBy('naam')->get();
        $medewerkers = MedewerkerModel::query()
            ->where('is_actief', true)
            ->with(['gebruiker.contactGegevens', 'specialisatie'])
            ->get();
        $klanten = $this->haalKlantenVoorFormulier();
        $eigenKlant = auth()->user()?->isKlant()
            ? auth()->user()->klant()->with('gebruiker.contactGegevens')->first()
            : null;

        return view('afspraken.create', compact('behandelingen', 'medewerkers', 'klanten', 'eigenKlant'));
    }

    /** POST /afspraken — Nieuwe afspraak opslaan. */
    public function store(StoreAfspraakRequest $request): RedirectResponse
    {
        try {
            $klant = $this->resolveKlant(
                $request->user(),
                $request->input('klant_id') ? (int) $request->input('klant_id') : null
            );

            $this->controleerBeschikbaarheid($request->medewerker_id, $request->afspraak_datum, $request->afspraak_tijd);

            $afspraak = DB::transaction(function () use ($request, $klant): Afspraak {
                return Afspraak::create([
                    'klant_id' => $klant->id,
                    'medewerker_id' => $request->medewerker_id,
                    'behandeling_id' => $request->behandeling_id,
                    'afspraak_datum' => $request->afspraak_datum,
                    'afspraak_tijd' => $request->afspraak_tijd,
                    'opmerking' => $request->opmerking,
                ]);
            });

            $afspraak->load(['behandeling', 'medewerker.gebruiker']);
            $bevestiging = $this->bouwBevestigingsMelding($afspraak);

            if ($request->user()?->isKlant()) {
                return redirect()->route('afspraken.create')->with('success', $bevestiging);
            }

            return redirect()->route('afspraken.index')->with('success', $bevestiging);
        } catch (\Throwable $exception) {
            TechnischeLogModel::registreer('error', 'afspraak', 'store', $exception->getMessage());

            if ($exception instanceof \Illuminate\Validation\ValidationException) {
                return redirect()->back()->withInput()->withErrors($exception->errors())->with('error', $exception->getMessage());
            }

            return redirect()->back()->withInput()->with('error', $exception->getMessage() ?: 'Afspraak kon niet worden ingepland.');
        }
    }

    /** GET /afspraken/{afspraak}/edit — Wijzigformulier. */
    public function edit(Afspraak $afspraak): View
    {
        abort_unless(auth()->user()?->isAdmin() || auth()->user()?->isMedewerker(), 403);

        $behandelingen = Behandeling::orderBy('naam')->get();
        $medewerkers = MedewerkerModel::query()
            ->where('is_actief', true)
            ->with(['gebruiker.contactGegevens', 'specialisatie'])
            ->get();
        $klanten = $this->haalKlantenVoorFormulier();

        return view('afspraken.edit', compact('afspraak', 'behandelingen', 'medewerkers', 'klanten'));
    }

    /** PUT /afspraken/{afspraak} — Afspraak bijwerken. */
    public function update(UpdateAfspraakRequest $request, Afspraak $afspraak): RedirectResponse
    {
        try {
            $this->controleerBeschikbaarheid($request->medewerker_id, $request->afspraak_datum, $request->afspraak_tijd, $afspraak->id);

            $afspraak->update([
                'klant_id' => $request->klant_id,
                'medewerker_id' => $request->medewerker_id,
                'behandeling_id' => $request->behandeling_id,
                'afspraak_datum' => $request->afspraak_datum,
                'afspraak_tijd' => $request->afspraak_tijd,
                'opmerking' => $request->opmerking,
            ]);

            return redirect()->route('afspraken.index')->with('success', 'Afspraak succesvol gewijzigd.');
        } catch (\Throwable $exception) {
            TechnischeLogModel::registreer('error', 'afspraak', 'update', $exception->getMessage());

            if ($exception instanceof \Illuminate\Validation\ValidationException) {
                return redirect()->back()->withInput()->withErrors($exception->errors())->with('error', $exception->getMessage());
            }

            return redirect()->back()->withInput()->with('error', $exception->getMessage() ?: 'Afspraak kon niet worden gewijzigd.');
        }
    }

    /** DELETE /afspraken/{afspraak} — Afspraak annuleren. */
    public function destroy(Afspraak $afspraak): RedirectResponse
    {
        abort_unless(auth()->user()?->isAdmin() || auth()->user()?->isMedewerker(), 403);

        try {
            $afspraak->load('behandeling');

            if ($afspraak->isLopend()) {
                throw ValidationException::withMessages([
                    'afspraak' => 'Een lopende afspraak kan niet meer worden geannuleerd',
                ]);
            }

            if ($afspraak->isVerstreken()) {
                throw ValidationException::withMessages([
                    'afspraak' => 'Een verstreken afspraak kan niet worden verwijderd',
                ]);
            }

            $afspraak->delete();

            return redirect()->route('afspraken.index')->with('success', 'Afspraak succesvol geannuleerd.');
        } catch (\Throwable $exception) {
            TechnischeLogModel::registreer('error', 'afspraak', 'destroy', $exception->getMessage());

            if ($exception instanceof \Illuminate\Validation\ValidationException) {
                return redirect()->route('afspraken.index')->withErrors($exception->errors())->with('error', $exception->getMessage());
            }

            return redirect()->route('afspraken.index')->with('error', $exception->getMessage() ?: 'Afspraak kon niet worden verwijderd.');
        }
    }

    private function resolveKlant($user, ?int $klantId = null): Klant
    {
        if ($user->isKlant()) {
            return $user->klant()->firstOrFail();
        }

        return Klant::query()->findOrFail($klantId);
    }

    /** @return \Illuminate\Support\Collection<int, Klant> */
    private function haalKlantenVoorFormulier()
    {
        return Klant::query()
            ->with('gebruiker.contactGegevens')
            ->get()
            ->sortBy(fn (Klant $klant): string => $klant->gebruiker?->volledig_naam ?? '')
            ->values();
    }

    /**
     * Controleert of een medewerker op een bepaald tijdstip beschikbaar is
     * en binnen de werkuren valt. De geboekte tijd is exclusief overlap.
     */
    private function controleerBeschikbaarheid(int $medewerkerId, string $datum, string $tijd, ?int $ignoreAfspraakId = null): void
    {
        MedewerkerModel::findOrFail($medewerkerId);

        if (! $this->beschikbaarheidService->isBeschikbaarOp($medewerkerId, $datum, $tijd)) {
            throw ValidationException::withMessages([
                'afspraak_tijd' => 'De gekozen tijd valt buiten de beschikbaarheid van deze medewerker.',
            ]);
        }

        $query = Afspraak::query()
            ->where('medewerker_id', $medewerkerId)
            ->whereDate('afspraak_datum', $datum)
            ->whereTime('afspraak_tijd', $tijd);

        if ($ignoreAfspraakId !== null) {
            $query->where('id', '!=', $ignoreAfspraakId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'afspraak_tijd' => 'Dit tijdstip is niet meer beschikbaar, kies een andere tijd',
            ]);
        }
    }

    private function bouwBevestigingsMelding(Afspraak $afspraak): string
    {
        $datum = \Carbon\Carbon::parse($afspraak->afspraak_datum)->format('d-m-Y');
        $tijd = substr((string) $afspraak->afspraak_tijd, 0, 5);
        $behandeling = $afspraak->behandeling?->naam ?? 'Behandeling';
        $specialist = $afspraak->medewerker?->gebruiker?->volledig_naam ?? 'Specialist';

        return "Afspraak ingepland: {$behandeling} bij {$specialist} op {$datum} om {$tijd}.";
    }
}
