<?php

namespace App\Services\Klant;

use App\Models\Klant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * klant.account.service — Verwijderen van klantprofielgegevens.
 *
 * Gebruikt door Klant\AccountController; testbaar als unit (zonder HTTP).
 */
class KlantAccountService
{
    /**
     * Verwijdert klantprofiel: adres, gebruiker, contact_gegevens en user-record.
     *
     * @throws \Throwable bij databasefouten
     */
    public function verwijderProfiel(User $user): void
    {
        $klantQuery = Klant::query()->where('user_id', $user->id);

        if (Schema::hasTable('adressen')) {
            $klantQuery->with(['gebruiker.contactGegevens', 'gebruiker.adres']);
        } else {
            $klantQuery->with('gebruiker.contactGegevens');
        }

        $klant = $klantQuery->first();

        DB::transaction(function () use ($user, $klant): void {
            $gebruikerId = $klant?->gebruiker_id;
            $contactGegevensId = $klant?->gebruiker?->contact_gegevens_id;
            $adresId = Schema::hasTable('adressen') ? $klant?->gebruiker?->adres_id : null;

            if ($adresId) {
                DB::table('adressen')->where('id', $adresId)->delete();
            }

            if ($gebruikerId) {
                DB::table('gebruikers')->where('id', $gebruikerId)->delete();
            }

            if ($contactGegevensId) {
                DB::table('contact_gegevens')->where('id', $contactGegevensId)->delete();
            }

            DB::table('users')->where('id', $user->id)->delete();
        });
    }
}
