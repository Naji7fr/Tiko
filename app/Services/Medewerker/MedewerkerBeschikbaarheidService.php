<?php

namespace App\Services\Medewerker;

use App\Models\Medewerker\MedewerkerBeschikbaarheidModel;
use App\Models\Medewerker\MedewerkerModel;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

/**
 * medewerker.service — Weekrooster (beschikbaarheid) per medewerker.
 */
class MedewerkerBeschikbaarheidService
{
    /** @var array<int, string> ISO-dagnummer → Nederlandse afkorting */
    public const DAG_NAMEN = [
        1 => 'Ma',
        2 => 'Di',
        3 => 'Wo',
        4 => 'Do',
        5 => 'Vr',
        6 => 'Za',
        7 => 'Zo',
    ];

    /** @return array<int, array{actief: bool, start: string, eind: string}> */
    public static function standaardPerDag(): array
    {
        $schema = [];

        foreach (range(1, 7) as $dag) {
            $isWeekend = $dag === 6;
            $isZondag = $dag === 7;

            $schema[$dag] = [
                'actief' => ! $isZondag,
                'start' => $isWeekend ? '08:00' : '09:00',
                'eind' => $isWeekend ? '17:00' : '18:00',
            ];
        }

        return $schema;
    }

    /**
     * Data voor create/edit-formulier (old input, opgeslagen rooster of standaard).
     *
     * @return array<int, array{actief: bool, start: string, eind: string}>
     */
    public function voorFormulier(?MedewerkerModel $medewerker = null): array
    {
        $standaard = self::standaardPerDag();

        if ($medewerker !== null && $this->tabelBestaat()) {
            $opgeslagen = $medewerker->beschikbaarheid()
                ->orderBy('dag_van_week')
                ->get()
                ->keyBy('dag_van_week');

            foreach (range(1, 7) as $dag) {
                $rij = $opgeslagen->get($dag);
                if ($rij !== null) {
                    $standaard[$dag] = [
                        'actief' => (bool) $rij->is_beschikbaar,
                        'start' => substr((string) $rij->start_tijd, 0, 5),
                        'eind' => substr((string) $rij->eind_tijd, 0, 5),
                    ];
                }
            }
        }

        $old = old('beschikbaarheid');
        if (! is_array($old)) {
            return $standaard;
        }

        foreach (range(1, 7) as $dag) {
            if (! isset($old[$dag]) || ! is_array($old[$dag])) {
                continue;
            }

            $standaard[$dag] = [
                'actief' => isset($old[$dag]['actief']) && (string) $old[$dag]['actief'] === '1',
                'start' => (string) ($old[$dag]['start'] ?? $standaard[$dag]['start']),
                'eind' => (string) ($old[$dag]['eind'] ?? $standaard[$dag]['eind']),
            ];
        }

        return $standaard;
    }

    /**
     * Slaat het weekrooster op (upsert per dag).
     *
     * @param array<int|string, array<string, mixed>>|null $beschikbaarheid
     */
    public function sync(int $medewerkerId, ?array $beschikbaarheid = null): void
    {
        if (! $this->tabelBestaat()) {
            return;
        }

        $beschikbaarheid = $beschikbaarheid ?? self::standaardPerDag();

        foreach (range(1, 7) as $dag) {
            $dagData = $beschikbaarheid[$dag] ?? $beschikbaarheid[(string) $dag] ?? [];
            $actief = isset($dagData['actief']) && (string) $dagData['actief'] === '1';
            $start = (string) ($dagData['start'] ?? '09:00');
            $eind = (string) ($dagData['eind'] ?? '18:00');

            MedewerkerBeschikbaarheidModel::updateOrCreate(
                [
                    'medewerker_id' => $medewerkerId,
                    'dag_van_week' => $dag,
                ],
                [
                    'start_tijd' => $this->normaliseerTijd($start),
                    'eind_tijd' => $this->normaliseerTijd($eind),
                    'is_beschikbaar' => $actief,
                ]
            );
        }
    }

    /**
     * Korte samenvatting voor overzicht (bijv. "Ma–Vr 09:00–18:00, Za 08:00–17:00").
     *
     * @param Collection<int, MedewerkerBeschikbaarheidModel> $regels
     */
    public function samenvatting(Collection $regels): string
    {
        $actieveDagen = $regels
            ->filter(fn (MedewerkerBeschikbaarheidModel $rij): bool => $rij->is_beschikbaar)
            ->sortBy('dag_van_week')
            ->values();

        if ($actieveDagen->isEmpty()) {
            return 'Geen beschikbaarheid';
        }

        $groepen = [];
        $huidigeGroep = null;

        foreach ($actieveDagen as $rij) {
            $start = substr((string) $rij->start_tijd, 0, 5);
            $eind = substr((string) $rij->eind_tijd, 0, 5);
            $tijdLabel = "{$start}–{$eind}";

            if (
                $huidigeGroep !== null
                && $huidigeGroep['tijd'] === $tijdLabel
                && $rij->dag_van_week === $huidigeGroep['laatste_dag'] + 1
            ) {
                $huidigeGroep['laatste_dag'] = $rij->dag_van_week;

                continue;
            }

            if ($huidigeGroep !== null) {
                $groepen[] = $huidigeGroep;
            }

            $huidigeGroep = [
                'eerste_dag' => $rij->dag_van_week,
                'laatste_dag' => $rij->dag_van_week,
                'tijd' => $tijdLabel,
            ];
        }

        if ($huidigeGroep !== null) {
            $groepen[] = $huidigeGroep;
        }

        return collect($groepen)
            ->map(function (array $groep): string {
                $dagLabel = $groep['eerste_dag'] === $groep['laatste_dag']
                    ? self::DAG_NAMEN[$groep['eerste_dag']]
                    : self::DAG_NAMEN[$groep['eerste_dag']] . '–' . self::DAG_NAMEN[$groep['laatste_dag']];

                return "{$dagLabel} {$groep['tijd']}";
            })
            ->implode(', ');
    }

    /**
     * @param list<int> $medewerkerIds
     * @return array<int, string>
     */
    public function haalSamenvattingen(array $medewerkerIds): array
    {
        if ($medewerkerIds === [] || ! $this->tabelBestaat()) {
            return $medewerkerIds === []
                ? []
                : array_fill_keys($medewerkerIds, '—');
        }

        $regels = MedewerkerBeschikbaarheidModel::query()
            ->whereIn('medewerker_id', $medewerkerIds)
            ->orderBy('medewerker_id')
            ->orderBy('dag_van_week')
            ->get()
            ->groupBy('medewerker_id');

        $samenvattingen = [];

        foreach ($medewerkerIds as $id) {
            $samenvattingen[$id] = $this->samenvatting($regels->get($id, collect()));
        }

        return $samenvattingen;
    }

    public function isBeschikbaarOp(int $medewerkerId, string $datum, string $tijd): bool
    {
        $dag = Carbon::parse($datum)->dayOfWeekIso;
        $tijdNorm = substr($tijd, 0, 5);

        if (! $this->tabelBestaat()) {
            return $this->fallbackBeschikbaar($dag, $tijdNorm);
        }

        $schema = MedewerkerBeschikbaarheidModel::query()
            ->where('medewerker_id', $medewerkerId)
            ->where('dag_van_week', $dag)
            ->first();

        if ($schema === null) {
            return $this->fallbackBeschikbaar($dag, $tijdNorm);
        }

        if (! $schema->is_beschikbaar) {
            return false;
        }

        $start = substr((string) $schema->start_tijd, 0, 5);
        $eind = substr((string) $schema->eind_tijd, 0, 5);

        return $tijdNorm >= $start && $tijdNorm < $eind;
    }

    private function fallbackBeschikbaar(int $dag, string $tijd): bool
    {
        if ($dag === 7) {
            return false;
        }

        if ($dag === 6) {
            return $tijd >= '08:00' && $tijd < '17:00';
        }

        return $tijd >= '09:00' && $tijd < '20:00';
    }

    private function normaliseerTijd(string $tijd): string
    {
        if (strlen($tijd) === 5) {
            return $tijd . ':00';
        }

        return $tijd;
    }

    public function tabelBestaat(): bool
    {
        try {
            return Schema::hasTable('medewerker_beschikbaarheid');
        } catch (\Throwable) {
            return false;
        }
    }
}
