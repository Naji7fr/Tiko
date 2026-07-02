<?php

namespace App\Services\Medewerker;

use App\Models\Medewerker\ContactGegevensModel;
use App\Models\Medewerker\GebruikerModel;
use App\Models\Medewerker\MedewerkerModel;
use App\Models\Medewerker\TechnischeLogModel;
use App\Services\Medewerker\MedewerkerBeschikbaarheidService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * medewerker.service — Businesslogica voor medewerker CRUD.
 *
 * Gebruikt stored procedures op MySQL; valt terug op Eloquent-transacties
 * in SQLite (tests). Alle acties worden technisch gelogd.
 */
class MedewerkerService
{
    private ?bool $storedProceduresBeschikbaar = null;

    public function __construct(
        private readonly MedewerkerBeschikbaarheidService $beschikbaarheidService
    ) {}

    /**
     * Haalt medewerkeroverzicht op (stored procedure of JOIN-fallback).
     *
     * @return Collection<int, MedewerkerModel>|array<int, object>
     */
    public function haalMedewerkersOp(): Collection|array
    {
        try {
            if ($this->gebruiktStoredProcedures()) {
                return MedewerkerModel::haalOverzichtViaStoredProcedure();
            }

            return MedewerkerModel::haalOverzichtMetJoins();
        } catch (\Throwable $exception) {
            TechnischeLogModel::registreer(
                'error',
                'medewerker',
                'overzicht_ophalen',
                $exception->getMessage()
            );

            throw $exception;
        }
    }

    /**
     * Voegt een nieuwe medewerker toe via stored procedure of transactie.
     *
     * @param array<string, mixed> $gevalideerdeData
     */
    public function voegMedewerkerToe(array $gevalideerdeData): MedewerkerModel
    {
        try {
            $medewerker = $this->gebruiktStoredProcedures()
                ? $this->voegToeViaStoredProcedure($gevalideerdeData)
                : $this->voegToeViaTransactie($gevalideerdeData);

            $this->beschikbaarheidService->sync(
                $medewerker->id,
                $gevalideerdeData['beschikbaarheid'] ?? null
            );

            return $medewerker;
        } catch (\Throwable $exception) {
            TechnischeLogModel::registreer(
                'error',
                'medewerker',
                'toevoegen',
                $exception->getMessage()
            );

            throw $exception;
        }
    }

    /**
     * Wijzigt een bestaande medewerker.
     *
     * @param array<string, mixed> $gevalideerdeData
     */
    public function wijzigMedewerker(MedewerkerModel $medewerker, array $gevalideerdeData): void
    {
        try {
            if ($this->gebruiktStoredProcedures()) {
                $this->wijzigViaStoredProcedure($medewerker->id, $gevalideerdeData);
            } else {
                $this->wijzigViaTransactie($medewerker, $gevalideerdeData);
            }

            if (array_key_exists('beschikbaarheid', $gevalideerdeData)) {
                $this->beschikbaarheidService->sync(
                    $medewerker->id,
                    $gevalideerdeData['beschikbaarheid']
                );
            }
        } catch (\Throwable $exception) {
            TechnischeLogModel::registreer(
                'error',
                'medewerker',
                'wijzigen',
                "Medewerker #{$medewerker->id}: {$exception->getMessage()}"
            );

            throw $exception;
        }
    }

    /**
     * Verwijdert een inactieve medewerker.
     */
    public function verwijderMedewerker(MedewerkerModel $medewerker): void
    {
        try {
            if ($this->gebruiktStoredProcedures()) {
                DB::statement('CALL sp_medewerker_verwijderen(?)', [$medewerker->id]);
            } else {
                $this->verwijderViaTransactie($medewerker);
            }

            TechnischeLogModel::registreer(
                'info',
                'medewerker',
                'verwijderen',
                "Medewerker #{$medewerker->id} verwijderd"
            );
        } catch (\Throwable $exception) {
            TechnischeLogModel::registreer(
                'error',
                'medewerker',
                'verwijderen',
                "Medewerker #{$medewerker->id}: {$exception->getMessage()}"
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

    /** Cached check: bestaan de medewerker stored procedures in de huidige database? */
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
                    'sp_medewerker_overzicht',
                    'sp_medewerker_toevoegen',
                    'sp_medewerker_wijzigen',
                    'sp_medewerker_verwijderen',
                ]
            );

            $this->storedProceduresBeschikbaar = ((int) ($resultaat->aantal ?? 0)) === 4;
        } catch (\Throwable) {
            $this->storedProceduresBeschikbaar = false;
        }

        return $this->storedProceduresBeschikbaar;
    }

    /** @param array<string, mixed> $gevalideerdeData */
    private function voegToeViaStoredProcedure(array $gevalideerdeData): MedewerkerModel
    {
        $volledigNaam = GebruikerModel::bouwVolledigNaam(
            $gevalideerdeData['voornaam'],
            $gevalideerdeData['tussenvoegsel'] ?? null,
            $gevalideerdeData['achternaam']
        );

        DB::statement(
            'CALL sp_medewerker_toevoegen(?, ?, ?, ?, ?, ?, ?, ?, @medewerker_id)',
            [
                $gevalideerdeData['voornaam'],
                $gevalideerdeData['tussenvoegsel'] ?? null,
                $gevalideerdeData['achternaam'],
                $volledigNaam,
                $gevalideerdeData['email'],
                $gevalideerdeData['telefoon'] ?? null,
                (int) $gevalideerdeData['specialisatie_id'],
                (int) $gevalideerdeData['is_actief'],
            ]
        );

        $resultaat = DB::select('SELECT @medewerker_id AS medewerker_id');
        $medewerkerId = (int) $resultaat[0]->medewerker_id;

        TechnischeLogModel::registreer(
            'info',
            'medewerker',
            'toevoegen',
            "Medewerker #{$medewerkerId} aangemaakt via stored procedure"
        );

        return MedewerkerModel::haalDetailMetJoins($medewerkerId)
            ?? MedewerkerModel::findOrFail($medewerkerId);
    }

    /** Fallback voor tests: Eloquent-transactie i.p.v. stored procedure. */
    /** @param array<string, mixed> $gevalideerdeData */
    private function voegToeViaTransactie(array $gevalideerdeData): MedewerkerModel
    {
        return DB::transaction(function () use ($gevalideerdeData): MedewerkerModel {
            // ERD: eerst contact, dan gebruiker, dan medewerker
            $contact = ContactGegevensModel::create([
                'email' => $gevalideerdeData['email'],
                'telefoon' => $gevalideerdeData['telefoon'] ?? null,
            ]);

            $gebruiker = GebruikerModel::create([
                'contact_gegevens_id' => $contact->id,
                'voornaam' => $gevalideerdeData['voornaam'],
                'tussenvoegsel' => $gevalideerdeData['tussenvoegsel'] ?? null,
                'achternaam' => $gevalideerdeData['achternaam'],
                'volledig_naam' => GebruikerModel::bouwVolledigNaam(
                    $gevalideerdeData['voornaam'],
                    $gevalideerdeData['tussenvoegsel'] ?? null,
                    $gevalideerdeData['achternaam']
                ),
            ]);

            $medewerker = MedewerkerModel::create([
                'gebruiker_id' => $gebruiker->id,
                'specialisatie_id' => $gevalideerdeData['specialisatie_id'],
                'is_actief' => (bool) $gevalideerdeData['is_actief'],
            ]);

            TechnischeLogModel::registreer(
                'info',
                'medewerker',
                'toevoegen',
                "Medewerker #{$medewerker->id} aangemaakt via transactie"
            );

            return $medewerker->load(['gebruiker.contactGegevens', 'specialisatie']);
        });
    }

    /** @param array<string, mixed> $gevalideerdeData */
    private function wijzigViaStoredProcedure(int $medewerkerId, array $gevalideerdeData): void
    {
        $volledigNaam = GebruikerModel::bouwVolledigNaam(
            $gevalideerdeData['voornaam'],
            $gevalideerdeData['tussenvoegsel'] ?? null,
            $gevalideerdeData['achternaam']
        );

        DB::statement(
            'CALL sp_medewerker_wijzigen(?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $medewerkerId,
                $gevalideerdeData['voornaam'],
                $gevalideerdeData['tussenvoegsel'] ?? null,
                $gevalideerdeData['achternaam'],
                $volledigNaam,
                $gevalideerdeData['email'],
                $gevalideerdeData['telefoon'] ?? null,
                (int) $gevalideerdeData['specialisatie_id'],
                (int) $gevalideerdeData['is_actief'],
            ]
        );

        TechnischeLogModel::registreer(
            'info',
            'medewerker',
            'wijzigen',
            "Medewerker #{$medewerkerId} gewijzigd via stored procedure"
        );
    }

    /** @param array<string, mixed> $gevalideerdeData */
    private function wijzigViaTransactie(MedewerkerModel $medewerker, array $gevalideerdeData): void
    {
        DB::transaction(function () use ($medewerker, $gevalideerdeData): void {
            $medewerker->load('gebruiker.contactGegevens');

            $medewerker->gebruiker->contactGegevens->update([
                'email' => $gevalideerdeData['email'],
                'telefoon' => $gevalideerdeData['telefoon'] ?? null,
            ]);

            $medewerker->gebruiker->update([
                'voornaam' => $gevalideerdeData['voornaam'],
                'tussenvoegsel' => $gevalideerdeData['tussenvoegsel'] ?? null,
                'achternaam' => $gevalideerdeData['achternaam'],
                'volledig_naam' => GebruikerModel::bouwVolledigNaam(
                    $gevalideerdeData['voornaam'],
                    $gevalideerdeData['tussenvoegsel'] ?? null,
                    $gevalideerdeData['achternaam']
                ),
            ]);

            $medewerker->update([
                'specialisatie_id' => $gevalideerdeData['specialisatie_id'],
                'is_actief' => (bool) $gevalideerdeData['is_actief'],
            ]);
        });

        TechnischeLogModel::registreer(
            'info',
            'medewerker',
            'wijzigen',
            "Medewerker #{$medewerker->id} gewijzigd via transactie"
        );
    }

    /** Verwijdert medewerker + gekoppelde gebruiker + contact (SQLite/tests). */
    private function verwijderViaTransactie(MedewerkerModel $medewerker): void
    {
        DB::transaction(function () use ($medewerker): void {
            $medewerker->load('gebruiker.contactGegevens');
            $contact = $medewerker->gebruiker->contactGegevens;

            $medewerker->delete();
            $medewerker->gebruiker->delete();
            $contact->delete();
        });
    }
}
