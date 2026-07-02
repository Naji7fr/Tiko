<?php

namespace App\Models;

use App\Models\Klant\AdresModel;
use App\Models\Medewerker\GebruikerModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * klant.model — Klant (barbershop-klant met optioneel loginaccount).
 *
 * MVC Model-laag: koppelt User (login) aan GebruikerModel (persoonsgegevens).
 * Bevat JOIN-queries voor overzicht (Eloquent + stored procedure op MySQL).
 *
 * ERD: users ← klanten → gebruikers → contact_gegevens
 *                    └→ adressen (optioneel)
 */
class Klant extends Model
{
    protected $table = 'klanten';

    /** @var list<string> */
    protected $fillable = [
        'user_id',
        'gebruiker_id',
    ];

    /** Relatie: Laravel loginaccount van deze klant (kan null zijn). */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Relatie: persoonsgegevens incl. contact en optioneel adres. */
    public function gebruiker(): BelongsTo
    {
        return $this->belongsTo(GebruikerModel::class, 'gebruiker_id');
    }

    /**
     * Haalt alle klanten op met INNER/LEFT JOINs (ERD-koppelingen).
     *
     * @return Collection<int, Klant>
     */
    public static function haalOverzichtMetJoins(): Collection
    {
        return static::query()
            ->select([
                'klanten.id as klant_id',
                'klanten.user_id',
                'klanten.gebruiker_id',
                'klanten.created_at',
                'klanten.updated_at',
                'gebruikers.volledig_naam',
                'gebruikers.voornaam',
                'gebruikers.achternaam',
                'contact_gegevens.email',
                'contact_gegevens.telefoon',
                'adressen.straat',
                'adressen.huisnummer',
                'adressen.postcode',
                'adressen.plaats',
                'adressen.land',
                'users.status as account_status',
            ])
            ->join('gebruikers', 'klanten.gebruiker_id', '=', 'gebruikers.id')
            ->join('contact_gegevens', 'gebruikers.contact_gegevens_id', '=', 'contact_gegevens.id')
            ->leftJoin('adressen', 'gebruikers.adres_id', '=', 'adressen.id')
            ->leftJoin('users', 'klanten.user_id', '=', 'users.id')
            ->orderBy('gebruikers.volledig_naam')
            ->get();
    }

    /**
     * Haalt één klant op via JOINs (detailweergave).
     */
    public static function haalDetailMetJoins(int $klantId): ?self
    {
        return static::query()
            ->select('klanten.*')
            ->join('gebruikers', 'klanten.gebruiker_id', '=', 'gebruikers.id')
            ->join('contact_gegevens', 'gebruikers.contact_gegevens_id', '=', 'contact_gegevens.id')
            ->leftJoin('adressen', 'gebruikers.adres_id', '=', 'adressen.id')
            ->where('klanten.id', $klantId)
            ->with(['user', 'gebruiker.contactGegevens', 'gebruiker.adres'])
            ->first();
    }

    /**
     * Overzicht via MySQL stored procedure sp_klant_overzicht (productie).
     *
     * @return array<int, object>
     */
    public static function haalOverzichtViaStoredProcedure(): array
    {
        return DB::select('CALL sp_klant_overzicht()');
    }

    /** Accessor: volledige naam voor de view. */
    public function getVolledigNaamAttribute(): ?string
    {
        return $this->gebruiker?->volledig_naam
            ?? $this->attributes['volledig_naam'] ?? null;
    }

    /** Accessor: e-mailadres voor de view. */
    public function getEmailAttribute(): ?string
    {
        return $this->gebruiker?->contactGegevens?->email
            ?? $this->user?->email
            ?? $this->attributes['email'] ?? null;
    }

    /** Accessor: telefoonnummer voor de view. */
    public function getTelefoonAttribute(): ?string
    {
        return $this->gebruiker?->contactGegevens?->telefoon
            ?? $this->attributes['telefoon'] ?? null;
    }

    /** Accessor: adres als leesbare string. */
    public function getAdresLabelAttribute(): ?string
    {
        $adres = $this->gebruiker?->adres;

        if ($adres instanceof AdresModel) {
            return trim("{$adres->straat} {$adres->huisnummer}, {$adres->postcode} {$adres->plaats}");
        }

        if (! empty($this->attributes['straat'])) {
            return trim(
                ($this->attributes['straat'] ?? '') . ' ' .
                ($this->attributes['huisnummer'] ?? '') . ', ' .
                ($this->attributes['postcode'] ?? '') . ' ' .
                ($this->attributes['plaats'] ?? '')
            );
        }

        return null;
    }
}
