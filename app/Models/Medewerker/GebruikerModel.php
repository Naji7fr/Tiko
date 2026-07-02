<?php

namespace App\Models\Medewerker;

use App\Models\Klant;
use App\Models\Klant\AdresModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * medewerker.model — Gebruiker (persoonsgegevens).
 *
 * MVC Model-laag: gekoppeld aan ContactGegevensModel.
 * ERD: ContactGegevens → Gebruiker → Medewerker
 */
class GebruikerModel extends Model
{
    use HasFactory;

    protected $table = 'gebruikers';

    /** @var list<string> */
    protected $fillable = [
        'contact_gegevens_id',
        'voornaam',
        'tussenvoegsel',
        'achternaam',
        'volledig_naam',
    ];

    /**
     * Bouwt de volledige weergavenaam uit losse velden.
     */
    public static function bouwVolledigNaam(string $voornaam, ?string $tussenvoegsel, string $achternaam): string
    {
        return implode(' ', array_filter([
            trim($voornaam),
            trim($tussenvoegsel ?? ''),
            trim($achternaam),
        ]));
    }

    /** JOIN-relatie: contactgegevens van deze gebruiker. */
    public function contactGegevens(): BelongsTo
    {
        return $this->belongsTo(ContactGegevensModel::class, 'contact_gegevens_id');
    }

    /** Relatie: adres van deze gebruiker. */
    public function adres(): BelongsTo
    {
        return $this->belongsTo(AdresModel::class, 'adres_id');
    }

    /** Relatie: medewerkerprofiel (indien van toepassing). */
    public function medewerker(): HasOne
    {
        return $this->hasOne(MedewerkerModel::class, 'gebruiker_id');
    }

    /** Relatie: klantprofiel (indien van toepassing). */
    public function klant(): HasOne
    {
        return $this->hasOne(Klant::class, 'gebruiker_id');
    }
}
