<?php

namespace App\Http\Controllers;

use App\Http\Requests\Afspraak\StoreAfspraakRequest;
use App\Models\Afspraak;
use App\Models\Behandeling;
use App\Models\Klant;
use App\Models\Medewerker\MedewerkerModel;
use App\Models\Medewerker\TechnischeLogModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AfspraakController extends Controller
{
    /** GET /afspraken — Overzicht voor medewerker en klant. */
    public function index(): View|RedirectResponse
    {
        try {
            $afspraken = Afspraak::query()
                ->with(['klant.gebruiker.contactGegevens', 'medewerker.gebruiker.contactGegevens', 'behandeling'])
                ->whereDate('afspraak_datum', today())
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
        $medewerkers = MedewerkerModel::query()->with(['gebruiker.contactGegevens', 'specialisatie'])->get();

        return view('afspraken.create', compact('behandelingen', 'medewerkers'));
    }

    /** POST /afspraken — Nieuwe afspraak opslaan. */
    public function store(StoreAfspraakRequest $request): RedirectResponse
    {
        try {
            $klant = $this->resolveKlant($request->user());

            $this->controleerBeschikbaarheid($request->medewerker_id, $request->afspraak_datum, $request->afspraak_tijd);

            DB::transaction(function () use ($request, $klant): void {
                Afspraak::create([
                    'klant_id' => $klant->id,
                    'medewerker_id' => $request->medewerker_id,
                    'behandeling_id' => $request->behandeling_id,
                    'afspraak_datum' => $request->afspraak_datum,
                    'afspraak_tijd' => $request->afspraak_tijd,
                    'opmerking' => $request->opmerking,
                ]);
            });

            return redirect()->route('afspraken.index')->with('success', 'Afspraak succesvol ingepland.');
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
        $behandelingen = Behandeling::orderBy('naam')->get();
        $medewerkers = MedewerkerModel::query()->with(['gebruiker.contactGegevens', 'specialisatie'])->get();

        return view('afspraken.edit', compact('afspraak', 'behandelingen', 'medewerkers'));
    }

    /** PUT /afspraken/{afspraak} — Afspraak bijwerken. */
    public function update(StoreAfspraakRequest $request, Afspraak $afspraak): RedirectResponse
    {
        try {
            $this->controleerBeschikbaarheid($request->medewerker_id, $request->afspraak_datum, $request->afspraak_tijd, $afspraak->id);

            $afspraak->update([
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
        try {
            if ($afspraak->afspraak_datum < now()->toDateString()) {
                throw ValidationException::withMessages([
                    'afspraak' => 'Een verstreken afspraak kan niet worden verwijderd',
                ]);
            }

            $afspraak->delete();

            return redirect()->route('afspraken.index')->with('success', 'Afspraak succesvol verwijderd.');
        } catch (\Throwable $exception) {
            TechnischeLogModel::registreer('error', 'afspraak', 'destroy', $exception->getMessage());

            if ($exception instanceof \Illuminate\Validation\ValidationException) {
                return redirect()->route('afspraken.index')->withErrors($exception->errors())->with('error', $exception->getMessage());
            }

            return redirect()->route('afspraken.index')->with('error', $exception->getMessage() ?: 'Afspraak kon niet worden verwijderd.');
        }
    }

    private function resolveKlant($user): Klant
    {
        if ($user->isAdmin() || $user->isMedewerker()) {
            return Klant::query()->firstOrFail();
        }

        return $user->klant()->firstOrFail();
    }

    /**
     * Controleert of een medewerker op een bepaald tijdstip beschikbaar is
     * en binnen de werkuren valt. De geboekte tijd is exclusief overlap.
     */
    private function controleerBeschikbaarheid(int $medewerkerId, string $datum, string $tijd, ?int $ignoreAfspraakId = null): void
    {
        $medewerker = MedewerkerModel::findOrFail($medewerkerId);
        $start = \Carbon\Carbon::parse("{$datum} {$tijd}");

        if ($start->hour < 9 || $start->hour >= 20) {
            throw ValidationException::withMessages([
                'afspraak_tijd' => 'De gekozen tijd valt buiten de werkuren van Tiko.',
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
}
