<?php

namespace App\Services\Eigenaar;

use App\Models\Klant;
use App\Models\Medewerker\TechnischeLogModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * klanten.service — Businesslogica voor klantenoverzicht (eigenaar-dashboard).
 *
 * Gebruikt stored procedures op MySQL; valt terug op Eloquent-JOINs
 * in SQLite (tests). Alle fouten worden technisch gelogd.
 */
class KlantenService
{
    private ?bool $storedProceduresBeschikbaar = null;

    /**
     * Haalt klantenoverzicht op (stored procedure of JOIN-fallback).
     *
     * @return Collection<int, object>
     */
    public function haalKlantenOp(): Collection
    {
        try {
            if ($this->gebruiktStoredProcedures()) {
                return $this->mapStoredProcedureResultaten(
                    Klant::haalOverzichtViaStoredProcedure()
                );
            }

            return $this->mapJoinResultaten(Klant::haalOverzichtMetJoins());
        } catch (\Throwable $exception) {
            TechnischeLogModel::registreer(
                'error',
                'klanten',
                'overzicht_ophalen',
                $exception->getMessage()
            );

            throw $exception;
        }
    }

    /** Controleert of MySQL stored procedures geïmporteerd en bruikbaar zijn. */
    private function gebruiktStoredProcedures(): bool
    {
        if (DB::connection()->getDriverName() !== 'mysql'
            || ! app()->environment('production', 'local')) {
            return false;
        }

        return $this->storedProceduresZijnGeimporteerd();
    }

    /** Cached check: bestaan de klant stored procedures in de huidige database? */
    private function storedProceduresZijnGeimporteerd(): bool
    {
        if ($this->storedProceduresBeschikbaar !== null) {
            return $this->storedProceduresBeschikbaar;
        }

        try {
            $resultaat = DB::selectOne(
                'SELECT COUNT(*) AS aantal FROM information_schema.ROUTINES
                 WHERE ROUTINE_SCHEMA = DATABASE()
                   AND ROUTINE_TYPE = ?
                   AND ROUTINE_NAME IN (?, ?, ?, ?)',
                [
                    'PROCEDURE',
                    'sp_klant_overzicht',
                    'sp_klant_toevoegen',
                    'sp_klant_wijzigen',
                    'sp_klant_verwijderen',
                ]
            );

            $this->storedProceduresBeschikbaar = ((int) ($resultaat->aantal ?? 0)) === 4;
        } catch (\Throwable) {
            $this->storedProceduresBeschikbaar = false;
        }

        return $this->storedProceduresBeschikbaar;
    }

    /** @param array<int, object> $rijen */
    private function mapStoredProcedureResultaten(array $rijen): Collection
    {
        return collect($rijen)->map(static fn (object $rij): object => (object) [
            'klant_id' => $rij->klant_id,
            'user_id' => $rij->user_id,
            'volledig_naam' => $rij->volledig_naam,
            'email' => $rij->email,
            'telefoon' => $rij->telefoon,
            'straat' => $rij->straat,
            'huisnummer' => $rij->huisnummer,
            'postcode' => $rij->postcode,
            'plaats' => $rij->plaats,
            'account_status' => $rij->account_status,
        ])->values();
    }

    /** @param Collection<int, Klant> $klanten */
    private function mapJoinResultaten(Collection $klanten): Collection
    {
        return $klanten->map(static fn (Klant $klant): object => (object) [
            'klant_id' => $klant->klant_id ?? $klant->id,
            'user_id' => $klant->user_id,
            'volledig_naam' => $klant->volledig_naam ?? trim(
                ($klant->voornaam ?? '') . ' ' . ($klant->achternaam ?? '')
            ),
            'email' => $klant->email,
            'telefoon' => $klant->telefoon,
            'straat' => $klant->straat,
            'huisnummer' => $klant->huisnummer,
            'postcode' => $klant->postcode,
            'plaats' => $klant->plaats,
            'account_status' => $klant->account_status,
        ])->values();
    }
}
