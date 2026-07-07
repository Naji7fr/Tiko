<?php

namespace App\Services\Afspraak;

use App\Models\Afspraak;
use App\Models\Behandeling;
use App\Models\Medewerker\MedewerkerModel;
use App\Models\Medewerker\SpecialisatieModel;
use App\Services\Medewerker\MedewerkerBeschikbaarheidService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Bepaalt welke specialisten, data en starttijden beschikbaar zijn voor een afspraak.
 */
class AfspraakPlanningService
{
    private const SLOT_INTERVAL_MINUTEN = 30;

    private const HORIZON_DAGEN = 60;

    public function __construct(
        private readonly MedewerkerBeschikbaarheidService $beschikbaarheidService
    ) {}

    /** @return Collection<int, MedewerkerModel> */
    public function haalMedewerkersVoorBehandeling(int $behandelingId): Collection
    {
        $behandeling = Behandeling::query()->findOrFail($behandelingId);
        $specialisatieNamen = $this->specialisatieNamenVoorBehandeling($behandeling);

        return MedewerkerModel::query()
            ->where('is_actief', true)
            ->whereHas('specialisatie', fn ($query) => $query->whereIn('naam', $specialisatieNamen))
            ->with(['gebruiker.contactGegevens', 'specialisatie'])
            ->get()
            ->sortBy(fn (MedewerkerModel $medewerker): string => $medewerker->gebruiker?->volledig_naam ?? '')
            ->values();
    }

    /**
     * @return list<string> Datums in Y-m-d
     */
    public function haalBeschikbareDatums(int $medewerkerId, int $behandelingId, ?int $ignoreAfspraakId = null): array
    {
        $behandeling = Behandeling::query()->findOrFail($behandelingId);
        MedewerkerModel::query()->findOrFail($medewerkerId);

        $datums = [];

        for ($offset = 0; $offset < self::HORIZON_DAGEN; $offset++) {
            $datum = now()->addDays($offset)->toDateString();

            if ($this->haalBeschikbareTijden($medewerkerId, $datum, $behandeling->duur_minuten, $ignoreAfspraakId) !== []) {
                $datums[] = $datum;
            }
        }

        return $datums;
    }

    /**
     * @return list<string> Starttijden in HH:MM
     */
    public function haalBeschikbareTijden(
        int $medewerkerId,
        string $datum,
        int $duurMinuten,
        ?int $ignoreAfspraakId = null
    ): array {
        if ($datum < now()->toDateString()) {
            return [];
        }

        $venster = $this->beschikbaarheidService->haalDagVenster($medewerkerId, $datum);

        if ($venster === null) {
            return [];
        }

        $geboekteTijden = $this->geboekteTijden($medewerkerId, $datum, $ignoreAfspraakId);
        $minimaleTijd = $datum === now()->toDateString()
            ? $this->volgendeSlot(now()->format('H:i'))
            : null;

        $beschikbaar = [];

        foreach ($this->genereerSlots($venster['start'], $venster['eind'], $duurMinuten) as $tijd) {
            if ($minimaleTijd !== null && $tijd < $minimaleTijd) {
                continue;
            }

            if (in_array($tijd, $geboekteTijden, true)) {
                continue;
            }

            if (! $this->beschikbaarheidService->isBeschikbaarOp($medewerkerId, $datum, $tijd)) {
                continue;
            }

            $beschikbaar[] = $tijd;
        }

        return $beschikbaar;
    }

    /** @return list<string> */
    private function specialisatieNamenVoorBehandeling(Behandeling $behandeling): array
    {
        $naam = strtolower($behandeling->naam);

        $mapping = match (true) {
            str_contains($naam, 'baard') || str_contains($naam, 'shave') => ['Baard'],
            str_contains($naam, 'fade') => ['Fade'],
            str_contains($naam, 'grey') || str_contains($naam, 'blend') => ['Kleuren'],
            str_contains($naam, 'knippen') => ['Fade', 'Styling'],
            default => null,
        };

        if ($mapping !== null) {
            return $mapping;
        }

        return SpecialisatieModel::query()->pluck('naam')->all();
    }

    /** @return list<string> */
    private function geboekteTijden(int $medewerkerId, string $datum, ?int $ignoreAfspraakId): array
    {
        $query = Afspraak::query()
            ->where('medewerker_id', $medewerkerId)
            ->whereDate('afspraak_datum', $datum);

        if ($ignoreAfspraakId !== null) {
            $query->where('id', '!=', $ignoreAfspraakId);
        }

        return $query
            ->pluck('afspraak_tijd')
            ->map(fn ($tijd): string => substr((string) $tijd, 0, 5))
            ->all();
    }

    /** @return list<string> */
    private function genereerSlots(string $start, string $eind, int $duurMinuten): array
    {
        $slots = [];
        $current = Carbon::createFromFormat('H:i', $start);
        $eindTijd = Carbon::createFromFormat('H:i', $eind);
        $laatsteStart = $eindTijd->copy()->subMinutes($duurMinuten);

        while ($current->lte($laatsteStart)) {
            $slots[] = $current->format('H:i');
            $current->addMinutes(self::SLOT_INTERVAL_MINUTEN);
        }

        return $slots;
    }

    private function volgendeSlot(string $tijd): string
    {
        $current = Carbon::createFromFormat('H:i', substr($tijd, 0, 5));
        $minuten = (int) $current->format('i');
        $rest = $minuten % self::SLOT_INTERVAL_MINUTEN;

        if ($rest !== 0) {
            $current->addMinutes(self::SLOT_INTERVAL_MINUTEN - $rest);
        }

        return $current->format('H:i');
    }
}
