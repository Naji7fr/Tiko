<?php

namespace Tests\Feature;

use App\Models\Klant;
use App\Models\Medewerker\ContactGegevensModel;
use App\Models\Medewerker\GebruikerModel;
use App\Models\Medewerker\MedewerkerModel;
use App\Models\Medewerker\SpecialisatieModel;
use App\Models\User;
use Tests\Concerns\RefreshesTestDatabase;
use Tests\TestCase;

class AfspraakCrudTest extends TestCase
{
    use RefreshesTestDatabase;

    private User $medewerkerUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\TikoSeeder::class);

        $this->medewerkerUser = User::factory()->create([
            'role' => 'medewerker',
            'status' => 'Actief',
        ]);
    }

    private function maakKlant(string $email = 'klant@example.com'): User
    {
        $user = User::factory()->create([
            'role' => 'klant',
            'email' => $email,
            'status' => 'Actief',
        ]);

        $contact = ContactGegevensModel::create([
            'email' => $email,
            'telefoon' => '0612345678',
        ]);

        $gebruiker = GebruikerModel::create([
            'contact_gegevens_id' => $contact->id,
            'voornaam' => 'Klant',
            'achternaam' => 'Tester',
            'volledig_naam' => 'Klant Tester',
        ]);

        Klant::create([
            'user_id' => $user->id,
            'gebruiker_id' => $gebruiker->id,
        ]);

        return $user;
    }

    private function maakMedewerker(string $email = 'medewerker@example.com'): MedewerkerModel
    {
        $contact = ContactGegevensModel::create([
            'email' => $email,
            'telefoon' => '0698765432',
        ]);

        $gebruiker = GebruikerModel::create([
            'contact_gegevens_id' => $contact->id,
            'voornaam' => 'Mieke',
            'achternaam' => 'Jansen',
            'volledig_naam' => 'Mieke Jansen',
        ]);

        $specialisatie = SpecialisatieModel::where('naam', 'Fade')->first();

        return MedewerkerModel::create([
            'gebruiker_id' => $gebruiker->id,
            'specialisatie_id' => $specialisatie->id,
            'is_actief' => true,
        ]);
    }

    private function maakBehandeling(): int
    {
        return \DB::table('behandelingen')->insertGetId([
            'naam' => 'Kapsel',
            'duur_minuten' => 45,
            'prijs' => 35.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_medewerker_ziet_overzicht_van_afspraken_vandaag(): void
    {
        $klant = $this->maakKlant('klant1@example.com');
        $medewerker = $this->maakMedewerker('medewerker1@example.com');
        $behandeling = $this->maakBehandeling();

        \DB::table('afspraken')->insert([
            'klant_id' => $klant->klant()->first()->id,
            'medewerker_id' => $medewerker->id,
            'behandeling_id' => $behandeling,
            'afspraak_datum' => now()->toDateString(),
            'afspraak_tijd' => '10:00:00',
            'opmerking' => 'Testafspraak',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($this->medewerkerUser)->get(route('afspraken.index'));

        $response->assertOk();
        $response->assertSee('Kapsel');
        $response->assertSee('Mieke Jansen');
    }

    public function test_klant_kan_afspraak_toevoegen_en_conflict_wordt_afgevangen(): void
    {
        $klant = $this->maakKlant('klant2@example.com');
        $medewerker = $this->maakMedewerker('medewerker2@example.com');
        $behandeling = $this->maakBehandeling();

        $response = $this->actingAs($klant)->post(route('afspraken.store'), [
            'behandeling_id' => $behandeling,
            'medewerker_id' => $medewerker->id,
            'afspraak_datum' => now()->addDay()->toDateString(),
            'afspraak_tijd' => '10:00:00',
            'opmerking' => 'Nieuwe afspraak',
        ]);

        $response->assertRedirect(route('afspraken.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('afspraken', [
            'medewerker_id' => $medewerker->id,
            'behandeling_id' => $behandeling,
            'afspraak_tijd' => '10:00:00',
        ]);

        $conflictResponse = $this->actingAs($klant)->post(route('afspraken.store'), [
            'behandeling_id' => $behandeling,
            'medewerker_id' => $medewerker->id,
            'afspraak_datum' => now()->addDay()->toDateString(),
            'afspraak_tijd' => '10:00:00',
            'opmerking' => 'Dubbel',
        ]);

        $conflictResponse->assertSessionHas('error');
        $conflictResponse->assertSessionHasErrors();
    }

    public function test_verstreken_afspraak_kan_niet_worden_verwijderd(): void
    {
        $klant = $this->maakKlant('klant3@example.com');
        $medewerker = $this->maakMedewerker('medewerker3@example.com');
        $behandeling = $this->maakBehandeling();

        $afspraak = \DB::table('afspraken')->insertGetId([
            'klant_id' => 1,
            'medewerker_id' => $medewerker->id,
            'behandeling_id' => $behandeling,
            'afspraak_datum' => now()->subDay()->toDateString(),
            'afspraak_tijd' => '10:00:00',
            'opmerking' => 'Oud',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($this->medewerkerUser)->delete(route('afspraken.destroy', $afspraak));

        $response->assertRedirect(route('afspraken.index'));
        $response->assertSessionHas('error');
    }
}
