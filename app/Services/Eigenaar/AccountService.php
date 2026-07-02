<?php

namespace App\Services\Eigenaar;

use App\Models\Klant;
use App\Models\Medewerker\ContactGegevensModel;
use App\Models\Medewerker\GebruikerModel;
use App\Models\Medewerker\TechnischeLogModel;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * account.service — Beheer van loginaccounts (eigenaar / medewerker / klant).
 */
class AccountService
{
    /** @var list<string> */
    public const BEHEER_ROLLEN = ['admin', 'medewerker', 'klant'];

    /** @return Collection<int, User> */
    public function haalAccountsOp(): Collection
    {
        return User::query()
            ->whereIn('role', self::BEHEER_ROLLEN)
            ->orderBy('voornaam')
            ->orderBy('achternaam')
            ->get();
    }

    /** @param array<string, mixed> $gevalideerdeData */
    public function voegAccountToe(array $gevalideerdeData): User
    {
        return DB::transaction(function () use ($gevalideerdeData): User {
            $user = User::create([
                'name' => $this->genereerUniekeGebruikersnaam(
                    $gevalideerdeData['voornaam'],
                    $gevalideerdeData['achternaam']
                ),
                'voornaam' => $gevalideerdeData['voornaam'],
                'achternaam' => $gevalideerdeData['achternaam'],
                'email' => $gevalideerdeData['email'],
                'password' => $gevalideerdeData['password'],
                'role' => $gevalideerdeData['role'],
                'status' => $gevalideerdeData['status'],
                'email_verified_at' => now(),
            ]);

            if ($gevalideerdeData['role'] === 'klant') {
                $this->maakKlantProfiel($user, $gevalideerdeData);
            }

            TechnischeLogModel::registreer(
                'info',
                'account',
                'toevoegen',
                "Account #{$user->id} ({$user->email}) aangemaakt"
            );

            return $user;
        });
    }

    /** @param array<string, mixed> $gevalideerdeData */
    public function wijzigAccount(User $account, array $gevalideerdeData): User
    {
        return DB::transaction(function () use ($account, $gevalideerdeData): User {
            $oudeRol = $account->role;
            $nieuweRol = $gevalideerdeData['role'];

            if ($oudeRol === 'klant' && $nieuweRol !== 'klant') {
                $this->verwijderKlantProfiel($account);
            }

            $account->update([
                'voornaam' => $gevalideerdeData['voornaam'],
                'achternaam' => $gevalideerdeData['achternaam'],
                'email' => $gevalideerdeData['email'],
                'role' => $nieuweRol,
                'status' => $gevalideerdeData['status'],
            ]);

            if (! empty($gevalideerdeData['password'])) {
                $account->update(['password' => $gevalideerdeData['password']]);
            }

            if ($nieuweRol === 'klant' && $oudeRol !== 'klant') {
                $this->maakKlantProfiel($account->fresh(), $gevalideerdeData);
            } elseif ($nieuweRol === 'klant') {
                $this->wijzigKlantProfiel($account->fresh(), $gevalideerdeData);
            }

            TechnischeLogModel::registreer(
                'info',
                'account',
                'wijzigen',
                "Account #{$account->id} ({$account->email}) gewijzigd"
            );

            return $account->fresh();
        });
    }

    public function verwijderAccount(User $account): void
    {
        DB::transaction(function () use ($account): void {
            if ($account->role === 'klant') {
                $this->verwijderKlantProfiel($account);
            }

            TechnischeLogModel::registreer(
                'info',
                'account',
                'verwijderen',
                "Account #{$account->id} ({$account->email}) verwijderd"
            );

            $account->delete();
        });
    }

    /** @param array<string, mixed> $gevalideerdeData */
    private function maakKlantProfiel(User $user, array $gevalideerdeData): void
    {
        $contact = ContactGegevensModel::create([
            'email' => $gevalideerdeData['email'],
            'telefoon' => $gevalideerdeData['telefoon'] ?? null,
        ]);

        $gebruiker = GebruikerModel::create([
            'contact_gegevens_id' => $contact->id,
            'voornaam' => $gevalideerdeData['voornaam'],
            'achternaam' => $gevalideerdeData['achternaam'],
            'volledig_naam' => GebruikerModel::bouwVolledigNaam(
                $gevalideerdeData['voornaam'],
                null,
                $gevalideerdeData['achternaam']
            ),
        ]);

        Klant::create([
            'user_id' => $user->id,
            'gebruiker_id' => $gebruiker->id,
        ]);
    }

    /** @param array<string, mixed> $gevalideerdeData */
    private function wijzigKlantProfiel(User $user, array $gevalideerdeData): void
    {
        $klant = $user->klant()->with('gebruiker.contactGegevens')->first();

        if (! $klant?->gebruiker) {
            $this->maakKlantProfiel($user, $gevalideerdeData);

            return;
        }

        $klant->gebruiker->update([
            'voornaam' => $gevalideerdeData['voornaam'],
            'achternaam' => $gevalideerdeData['achternaam'],
            'volledig_naam' => GebruikerModel::bouwVolledigNaam(
                $gevalideerdeData['voornaam'],
                null,
                $gevalideerdeData['achternaam']
            ),
        ]);

        $klant->gebruiker->contactGegevens?->update([
            'email' => $gevalideerdeData['email'],
            'telefoon' => $gevalideerdeData['telefoon'] ?? null,
        ]);
    }

    private function verwijderKlantProfiel(User $user): void
    {
        $klant = $user->klant()->with('gebruiker')->first();

        if (! $klant) {
            return;
        }

        $contactId = $klant->gebruiker?->contact_gegevens_id;

        $klant->delete();
        $klant->gebruiker?->delete();

        if ($contactId) {
            ContactGegevensModel::where('id', $contactId)->delete();
        }
    }

    private function genereerUniekeGebruikersnaam(string $voornaam, string $achternaam): string
    {
        $base = Str::slug($voornaam . '.' . $achternaam, '.');
        $username = $base;
        $counter = 1;

        while (User::where('name', $username)->exists()) {
            $username = $base . $counter;
            $counter++;
        }

        return $username;
    }
}
