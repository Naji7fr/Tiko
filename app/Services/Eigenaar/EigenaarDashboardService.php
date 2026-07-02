<?php

namespace App\Services\Eigenaar;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * eigenaar.service — Statistieken en rapportages voor het eigenaar-dashboard.
 *
 * Gebruikt JOIN-queries waar mogelijk; valt terug op 0 als tabellen ontbreken.
 */
class EigenaarDashboardService
{
    /** @return array<string, int|float> */
    public function haalStatistiekenOp(): array
    {
        return [
            'medewerkers_totaal' => $this->telRecords('medewerkers'),
            'medewerkers_actief' => $this->telRecordsWaar('medewerkers', 'is_actief', 1),
            'klanten_totaal' => $this->telRecords('klanten'),
            'afspraken_totaal' => $this->telRecords('afspraken'),
            'afspraken_vandaag' => $this->telAfsprakenOpDatum(now()->toDateString()),
            'afspraken_komende_week' => $this->telAfsprakenKomendeDagen(7),
            'behandelingen_totaal' => $this->telRecords('behandelingen'),
            'producten_totaal' => $this->telRecords('producten'),
            'bestellingen_totaal' => $this->telRecords('bestellingen'),
            'omzet_totaal' => $this->somOmzet(),
            'omzet_maand' => $this->somOmzetMaand(),
        ];
    }

    /** @return Collection<int, object> */
    public function haalKomendeAfsprakenOp(int $limiet = 5): Collection
    {
        if (! $this->heeftTabellen(['afspraken', 'klanten', 'gebruikers', 'medewerkers', 'behandelingen'])) {
            return collect();
        }

        return collect(DB::table('afspraken as a')
            ->select([
                'a.id',
                'a.afspraak_datum',
                'a.afspraak_tijd',
                'kg.volledig_naam as klant_naam',
                'mg.volledig_naam as medewerker_naam',
                'b.naam as behandeling_naam',
            ])
            ->join('klanten as k', 'a.klant_id', '=', 'k.id')
            ->join('gebruikers as kg', 'k.gebruiker_id', '=', 'kg.id')
            ->join('medewerkers as m', 'a.medewerker_id', '=', 'm.id')
            ->join('gebruikers as mg', 'm.gebruiker_id', '=', 'mg.id')
            ->join('behandelingen as b', 'a.behandeling_id', '=', 'b.id')
            ->where('a.afspraak_datum', '>=', now()->toDateString())
            ->orderBy('a.afspraak_datum')
            ->orderBy('a.afspraak_tijd')
            ->limit($limiet)
            ->get());
    }

    /** @return Collection<int, object> */
    public function haalRecenteBestellingenOp(int $limiet = 5): Collection
    {
        if (! $this->heeftTabellen(['bestellingen', 'klanten', 'gebruikers'])) {
            return collect();
        }

        return collect(DB::table('bestellingen as bs')
            ->select([
                'bs.id',
                'bs.bestel_datum',
                'bs.status',
                'bs.totaal_prijs',
                'g.volledig_naam as klant_naam',
            ])
            ->join('klanten as k', 'bs.klant_id', '=', 'k.id')
            ->join('gebruikers as g', 'k.gebruiker_id', '=', 'g.id')
            ->orderByDesc('bs.bestel_datum')
            ->orderByDesc('bs.id')
            ->limit($limiet)
            ->get());
    }

    /** @return array<string, mixed> */
    public function haalRapportageSamenvattingOp(): array
    {
        $statistieken = $this->haalStatistiekenOp();

        return [
            'statistieken' => $statistieken,
            'top_behandelingen' => $this->haalTopBehandelingen(),
            'bestellingen_per_status' => $this->haalBestellingenPerStatus(),
        ];
    }

    /** @return Collection<int, object> */
    private function haalTopBehandelingen(): Collection
    {
        if (! $this->heeftTabellen(['afspraken', 'behandelingen'])) {
            return collect();
        }

        return collect(DB::table('afspraken as a')
            ->select('b.naam', DB::raw('COUNT(*) as aantal'))
            ->join('behandelingen as b', 'a.behandeling_id', '=', 'b.id')
            ->groupBy('b.naam')
            ->orderByDesc('aantal')
            ->limit(5)
            ->get());
    }

    /** @return Collection<int, object> */
    private function haalBestellingenPerStatus(): Collection
    {
        if (! Schema::hasTable('bestellingen')) {
            return collect();
        }

        return collect(DB::table('bestellingen')
            ->select('status', DB::raw('COUNT(*) as aantal'), DB::raw('SUM(totaal_prijs) as omzet'))
            ->groupBy('status')
            ->orderBy('status')
            ->get());
    }

    private function telRecords(string $tabel): int
    {
        if (! Schema::hasTable($tabel)) {
            return 0;
        }

        return (int) DB::table($tabel)->count();
    }

    private function telRecordsWaar(string $tabel, string $kolom, mixed $waarde): int
    {
        if (! Schema::hasTable($tabel)) {
            return 0;
        }

        return (int) DB::table($tabel)->where($kolom, $waarde)->count();
    }

    private function telAfsprakenOpDatum(string $datum): int
    {
        if (! Schema::hasTable('afspraken')) {
            return 0;
        }

        return (int) DB::table('afspraken')->whereDate('afspraak_datum', $datum)->count();
    }

    private function telAfsprakenKomendeDagen(int $dagen): int
    {
        if (! Schema::hasTable('afspraken')) {
            return 0;
        }

        return (int) DB::table('afspraken')
            ->whereBetween('afspraak_datum', [now()->toDateString(), now()->addDays($dagen)->toDateString()])
            ->count();
    }

    private function somOmzet(): float
    {
        if (! Schema::hasTable('bestellingen')) {
            return 0.0;
        }

        return (float) DB::table('bestellingen')->sum('totaal_prijs');
    }

    private function somOmzetMaand(): float
    {
        if (! Schema::hasTable('bestellingen')) {
            return 0.0;
        }

        return (float) DB::table('bestellingen')
            ->whereYear('bestel_datum', now()->year)
            ->whereMonth('bestel_datum', now()->month)
            ->sum('totaal_prijs');
    }

    /** @param list<string> $tabellen */
    private function heeftTabellen(array $tabellen): bool
    {
        foreach ($tabellen as $tabel) {
            if (! Schema::hasTable($tabel)) {
                return false;
            }
        }

        return true;
    }
}
