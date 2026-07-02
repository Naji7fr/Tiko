<?php

namespace Tests\Feature;

use App\Models\Medewerker\MedewerkerModel;
use App\Models\Medewerker\SpecialisatieModel;
use App\Models\User;
use Database\Seeders\TikoSeeder;
use Tests\Concerns\RefreshesTestDatabase;
use Tests\TestCase;

/** Feature tests: medewerker CRUD, validatie en verwijderregels. */
class MedewerkerOverzichtTest extends TestCase
{
    use RefreshesTestDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(TikoSeeder::class);

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'Actief',
        ]);
    }

    public function test_medewerker_overzicht_wordt_succesvol_geladen(): void
    {
        $medewerker = MedewerkerModel::factory()->actief()->create([
            'naam' => 'Jan de Vries',
            'email' => 'jan@example.com',
        ]);

        $response = $this->actingAs($this->admin)->get(route('medewerkers.index'));

        $response->assertOk();
        $response->assertSee('Jan de Vries');
        $response->assertSee('jan@example.com');
        $response->assertSee('Actief');
    }

    public function test_geen_medewerker_beschikbaar_melding(): void
    {
        $response = $this->actingAs($this->admin)->get(route('medewerkers.index'));

        $response->assertOk();
        $response->assertSee('Er zijn geen medewerker geregistreerd.');
    }

    public function test_medewerker_wordt_succesvol_toegevoegd(): void
    {
        $specialisatie = SpecialisatieModel::where('naam', 'Fade')->first();

        $response = $this->actingAs($this->admin)->post(route('medewerkers.store'), [
            'voornaam' => 'Piet',
            'achternaam' => 'Jansen',
            'email' => 'piet@example.com',
            'specialisatie_id' => $specialisatie->id,
            'is_actief' => '1',
        ]);

        $response->assertRedirect(route('medewerkers.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('contact_gegevens', ['email' => 'piet@example.com']);
        $this->assertDatabaseHas('gebruikers', ['volledig_naam' => 'Piet Jansen']);

        $overview = $this->actingAs($this->admin)->get(route('medewerkers.index'));
        $overview->assertSee('Piet Jansen');
        $overview->assertSee('piet@example.com');
    }

    public function test_medewerker_wordt_niet_toegevoegd_bij_dubbele_email(): void
    {
        MedewerkerModel::factory()->create(['email' => 'bestaand@example.com']);

        $specialisatie = SpecialisatieModel::where('naam', 'Fade')->first();

        $response = $this->actingAs($this->admin)->post(route('medewerkers.store'), [
            'voornaam' => 'Nieuwe',
            'achternaam' => 'Medewerker',
            'email' => 'bestaand@example.com',
            'specialisatie_id' => $specialisatie->id,
            'is_actief' => '1',
        ]);

        $response->assertSessionHasErrors(['email']);
        $response->assertSessionHasErrors(['email' => 'Deze e-mail bestaat al.']);
    }

    /**
     * Scenario: medewerker wordt succesvol gewijzigd
     * Home → overzicht → wijzigen → telefoon aanpassen → opslaan → zichtbaar in overzicht
     */
    public function test_medewerker_wordt_succesvol_gewijzigd(): void
    {
        $medewerker = MedewerkerModel::factory()->actief()->create([
            'naam' => 'Jan de Vries',
            'telefoonnummer' => '0612345678',
        ]);

        $this->actingAs($this->admin)->get(route('home'))->assertOk();
        $this->actingAs($this->admin)->get(route('medewerkers.index'))->assertOk();
        $this->actingAs($this->admin)->get(route('medewerkers.edit', $medewerker))
            ->assertOk()
            ->assertSee('Medewerker wijzigen');

        $response = $this->actingAs($this->admin)->put(route('medewerkers.update', $medewerker), [
            'voornaam' => $medewerker->gebruiker->voornaam,
            'achternaam' => $medewerker->gebruiker->achternaam,
            'email' => $medewerker->email,
            'telefoon' => '0687654321',
            'specialisatie_id' => $medewerker->specialisatie_id,
            'is_actief' => '1',
        ]);

        $response->assertRedirect(route('medewerkers.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('contact_gegevens', [
            'id' => $medewerker->gebruiker->contact_gegevens_id,
            'telefoon' => '0687654321',
        ]);

        $overview = $this->actingAs($this->admin)->get(route('medewerkers.index'));
        $overview->assertOk();
        $overview->assertSee('Jan de Vries');
        $overview->assertSee('0687654321');
    }

    /**
     * Scenario: medewerker wordt niet gewijzigd (bestaand telefoonnummer)
     */
    public function test_medewerker_wordt_niet_gewijzigd_bij_bestaand_telefoonnummer(): void
    {
        MedewerkerModel::factory()->actief()->create([
            'telefoonnummer' => '0611111111',
        ]);

        $medewerker = MedewerkerModel::factory()->actief()->create([
            'telefoonnummer' => '0622222222',
        ]);

        $this->actingAs($this->admin)->get(route('home'))->assertOk();
        $this->actingAs($this->admin)->get(route('medewerkers.index'))->assertOk();
        $this->actingAs($this->admin)->get(route('medewerkers.edit', $medewerker))
            ->assertOk()
            ->assertSee('Medewerker wijzigen');

        $response = $this->actingAs($this->admin)->put(route('medewerkers.update', $medewerker), [
            'voornaam' => $medewerker->gebruiker->voornaam,
            'achternaam' => $medewerker->gebruiker->achternaam,
            'email' => $medewerker->email,
            'telefoon' => '0611111111',
            'specialisatie_id' => $medewerker->specialisatie_id,
            'is_actief' => '1',
        ]);

        $response->assertSessionHasErrors(['telefoon']);
        $response->assertSessionHasErrors(['telefoon' => 'Dit telefoonnummer bestaat al.']);

        $this->assertDatabaseHas('contact_gegevens', [
            'id' => $medewerker->gebruiker->contact_gegevens_id,
            'telefoon' => '0622222222',
        ]);
    }

    public function test_medewerker_wordt_niet_gewijzigd_bij_ongeldig_telefoonnummer(): void
    {
        $medewerker = MedewerkerModel::factory()->actief()->create([
            'telefoonnummer' => '0612345678',
        ]);

        $response = $this->actingAs($this->admin)->put(route('medewerkers.update', $medewerker), [
            'voornaam' => $medewerker->gebruiker->voornaam,
            'achternaam' => $medewerker->gebruiker->achternaam,
            'email' => $medewerker->email,
            'telefoon' => 'ongeldig',
            'specialisatie_id' => $medewerker->specialisatie_id,
            'is_actief' => '1',
        ]);

        $response->assertSessionHasErrors(['telefoon']);
        $response->assertSessionHasErrors(['telefoon' => 'Ongeldig telefoonnummer.']);

        $this->assertDatabaseHas('contact_gegevens', [
            'id' => $medewerker->gebruiker->contact_gegevens_id,
            'telefoon' => '0612345678',
        ]);
    }

    public function test_inactieve_medewerker_wordt_succesvol_verwijderd(): void
    {
        $medewerker = MedewerkerModel::factory()->inactief()->create([
            'naam' => 'Te Verwijderen',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('medewerkers.destroy', $medewerker));

        $response->assertRedirect(route('medewerkers.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('medewerkers', ['id' => $medewerker->id]);

        $overview = $this->actingAs($this->admin)->get(route('medewerkers.index'));
        $overview->assertDontSee('Te Verwijderen');
    }

    public function test_actieve_medewerker_kan_niet_worden_verwijderd(): void
    {
        $medewerker = MedewerkerModel::factory()->actief()->create([
            'naam' => 'Actieve Medewerker',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('medewerkers.destroy', $medewerker));

        $response->assertRedirect(route('medewerkers.index'));
        $response->assertSessionHas('error', 'Actieve medewerkers kunnen niet worden verwijderd');

        $this->assertDatabaseHas('medewerkers', ['id' => $medewerker->id]);

        $overview = $this->actingAs($this->admin)->get(route('medewerkers.index'));
        $overview->assertSee('Actieve Medewerker');
    }
}
