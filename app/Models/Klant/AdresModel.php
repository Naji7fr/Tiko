<?php

namespace App\Models\Klant;

use App\Models\Medewerker\GebruikerModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * klant.adres.model — Adresgegevens voor klanten (optioneel).
 *
 * ERD: adressen ← gebruikers.adres_id
 * Eén adres kan aan meerdere gebruikers gekoppeld zijn (HasMany).
 */
class AdresModel extends Model
{
    protected $table = 'adressen';

    /** @var list<string> */
    protected $fillable = [
        'straat',
        'huisnummer',
        'postcode',
        'plaats',
        'land',
    ];

    /** Relatie: gebruikers die dit adres gebruiken. */
    public function gebruikers(): HasMany
    {
        return $this->hasMany(GebruikerModel::class, 'adres_id');
    }
}
