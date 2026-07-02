<?php

namespace App\Models\Medewerker;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * medewerker.model — ContactGegevens (e-mail en telefoon).
 *
 * MVC Model-laag: gedeeld contactrecord per medewerker/gebruiker.
 */
class ContactGegevensModel extends Model
{
    protected $table = 'contact_gegevens';

    /** @var list<string> */
    protected $fillable = [
        'email',
        'telefoon',
        'opmerking',
    ];

    /** Relatie: gebruikers die dit contactrecord gebruiken. */
    public function gebruikers(): HasMany
    {
        return $this->hasMany(GebruikerModel::class, 'contact_gegevens_id');
    }
}
