<?php

namespace Database\Factories;

use App\Models\Medewerker\ContactGegevensModel;
use App\Models\Medewerker\GebruikerModel;
use App\Models\Medewerker\MedewerkerModel;
use App\Models\Medewerker\SpecialisatieModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends Factory<MedewerkerModel>
 *
 * Factory voor testdata: maakt volledige ERD-keten aan
 * (contact_gegevens → gebruikers → medewerkers).
 */
class MedewerkerFactory extends Factory
{
    protected $model = MedewerkerModel::class;

    /** Standaard state: actieve medewerker. */
    public function definition(): array
    {
        return [
            'is_actief' => true,
        ];
    }

    /** State: medewerker met is_actief = true. */
    public function actief(): static
    {
        return $this->state(fn () => ['is_actief' => true]);
    }

    /** State: medewerker met is_actief = false (verwijderbaar in tests). */
    public function inactief(): static
    {
        return $this->state(fn () => ['is_actief' => false]);
    }

    /**
     * Override: bouwt contact + gebruiker + medewerker in één aanroep.
     * Ondersteunt virtual attributes: naam, email, telefoonnummer.
     */
    public function create($attributes = [], ?Model $parent = null)
    {
        $resolved = array_merge($this->definition(), $attributes);
        foreach ($this->states as $state) {
            $resolved = array_merge($resolved, is_callable($state) ? $state($resolved) : $state);
        }

        $isActief = $resolved['is_actief'] ?? true;
        unset($resolved['is_actief']);

        $email = $resolved['email'] ?? fake()->unique()->safeEmail();
        unset($resolved['email']);

        $telefoon = $resolved['telefoonnummer'] ?? $resolved['telefoon'] ?? ('06' . fake()->numerify('########'));
        unset($resolved['telefoonnummer'], $resolved['telefoon']);

        if (isset($resolved['naam'])) {
            $parts = explode(' ', $resolved['naam'], 2);
            $voornaam = $resolved['voornaam'] ?? $parts[0];
            $achternaam = $resolved['achternaam'] ?? ($parts[1] ?? 'Test');
            unset($resolved['naam']);
        } else {
            $voornaam = $resolved['voornaam'] ?? fake()->firstName();
            $achternaam = $resolved['achternaam'] ?? fake()->lastName();
        }
        unset($resolved['voornaam'], $resolved['achternaam']);

        $tussenvoegsel = $resolved['tussenvoegsel'] ?? null;
        unset($resolved['tussenvoegsel']);

        $specialisatieId = $resolved['specialisatie_id']
            ?? SpecialisatieModel::query()->inRandomOrder()->value('id')
            ?? SpecialisatieModel::factory()->create()->id;
        unset($resolved['specialisatie_id']);

        $contact = ContactGegevensModel::create([
            'email' => $email,
            'telefoon' => $telefoon,
        ]);

        $gebruiker = GebruikerModel::create([
            'contact_gegevens_id' => $contact->id,
            'voornaam' => $voornaam,
            'tussenvoegsel' => $tussenvoegsel,
            'achternaam' => $achternaam,
            'volledig_naam' => GebruikerModel::bouwVolledigNaam($voornaam, $tussenvoegsel, $achternaam),
        ]);

        return MedewerkerModel::create(array_merge([
            'gebruiker_id' => $gebruiker->id,
            'specialisatie_id' => $specialisatieId,
            'is_actief' => $isActief,
        ], $resolved));
    }
}
