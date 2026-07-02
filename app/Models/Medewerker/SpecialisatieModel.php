<?php

namespace App\Models\Medewerker;

use Database\Factories\SpecialisatieFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * medewerker.model — Specialisatie (vakgebied kapper).
 *
 * Voorbeelden: Fade, Baard, Kleuren.
 */
class SpecialisatieModel extends Model
{
    use HasFactory;

    protected $table = 'specialisaties';

    /** @var list<string> */
    protected $fillable = [
        'naam',
        'beschrijving',
    ];

    protected static function newFactory(): SpecialisatieFactory
    {
        return SpecialisatieFactory::new();
    }

    /** Relatie: medewerkers met deze specialisatie. */
    public function medewerkers(): HasMany
    {
        return $this->hasMany(MedewerkerModel::class, 'specialisatie_id');
    }
}
