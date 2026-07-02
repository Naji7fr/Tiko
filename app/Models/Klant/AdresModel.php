<?php

namespace App\Models\Klant;

use App\Models\Medewerker\GebruikerModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function gebruikers(): HasMany
    {
        return $this->hasMany(GebruikerModel::class, 'adres_id');
    }
}