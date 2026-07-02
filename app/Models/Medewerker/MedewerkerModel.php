<?php

namespace App\Models\Medewerker;

use Database\Factories\MedewerkerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * medewerker.model — Medewerker (kapper bij Tiko).
 *
 * MVC Model-laag: koppelt GebruikerModel aan SpecialisatieModel.
 * Bevat JOIN-queries voor overzicht (Eloquent + raw JOIN).
 */
class MedewerkerModel extends Model
{
    use HasFactory;

    protected $table = 'medewerkers';

    /** @var list<string> */
    protected $fillable = [
        'specialisatie_id',
        'gebruiker_id',
        'is_actief',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'is_actief' => 'boolean',
        ];
    }

    protected static function newFactory(): MedewerkerFactory
    {
        return MedewerkerFactory::new();
    }

    /** Relatie: persoonsgegevens van deze medewerker. */
    public function gebruiker(): BelongsTo
    {
        return $this->belongsTo(GebruikerModel::class, 'gebruiker_id');
    }

    /** Relatie: vakgebied van de medewerker. */
    public function specialisatie(): BelongsTo
    {
        return $this->belongsTo(SpecialisatieModel::class, 'specialisatie_id');
    }

    /**
     * Haalt alle medewerkers op met INNER JOINs (ERD-koppelingen).
     *
     * @return Collection<int, MedewerkerModel>
     */
    public static function haalOverzichtMetJoins(): Collection
    {
        return static::query()
            ->select([
                'medewerkers.id',
                'medewerkers.specialisatie_id',
                'medewerkers.gebruiker_id',
                'medewerkers.is_actief',
                'medewerkers.created_at',
                'medewerkers.updated_at',
                'gebruikers.volledig_naam',
                'contact_gegevens.email',
                'contact_gegevens.telefoon',
                'specialisaties.naam as specialisatie_naam',
            ])
            ->join('gebruikers', 'medewerkers.gebruiker_id', '=', 'gebruikers.id')
            ->join('contact_gegevens', 'gebruikers.contact_gegevens_id', '=', 'contact_gegevens.id')
            ->join('specialisaties', 'medewerkers.specialisatie_id', '=', 'specialisaties.id')
            ->orderBy('gebruikers.volledig_naam')
            ->get();
    }

    /**
     * Haalt één medewerker op via JOINs (detailweergave).
     */
    public static function haalDetailMetJoins(int $medewerkerId): ?self
    {
        return static::query()
            ->select('medewerkers.*')
            ->join('gebruikers', 'medewerkers.gebruiker_id', '=', 'gebruikers.id')
            ->join('contact_gegevens', 'gebruikers.contact_gegevens_id', '=', 'contact_gegevens.id')
            ->join('specialisaties', 'medewerkers.specialisatie_id', '=', 'specialisaties.id')
            ->where('medewerkers.id', $medewerkerId)
            ->with(['gebruiker.contactGegevens', 'specialisatie'])
            ->first();
    }

    /**
     * Overzicht via MySQL stored procedure (productie).
     *
     * @return array<int, object>
     */
    public static function haalOverzichtViaStoredProcedure(): array
    {
        return DB::select('CALL sp_medewerker_overzicht()');
    }

    /** Accessor: volledige naam voor de view. */
    public function getNaamAttribute(): ?string
    {
        return $this->gebruiker?->volledig_naam
            ?? $this->attributes['volledig_naam'] ?? null;
    }

    /** Accessor: e-mailadres voor de view. */
    public function getEmailAttribute(): ?string
    {
        return $this->gebruiker?->contactGegevens?->email
            ?? $this->attributes['email'] ?? null;
    }

    /** Accessor: telefoonnummer voor de view. */
    public function getTelefoonnummerAttribute(): ?string
    {
        return $this->gebruiker?->contactGegevens?->telefoon
            ?? $this->attributes['telefoon'] ?? null;
    }

    /** Accessor: leesbare status (Actief / Inactief). */
    public function getStatusAttribute(): string
    {
        return $this->is_actief ? 'Actief' : 'Inactief';
    }

    /** Accessor: specialisatienaam als type-label. */
    public function getTypeAttribute(): ?string
    {
        return $this->specialisatie?->naam
            ?? $this->attributes['specialisatie_naam'] ?? null;
    }
}
