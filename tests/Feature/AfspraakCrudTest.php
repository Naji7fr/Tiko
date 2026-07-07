<?php

namespace Tests\Feature;

use App\Models\Afspraak;
use App\Models\Klant;
use App\Models\Medewerker\ContactGegevensModel;
use App\Models\Medewerker\GebruikerModel;
use App\Models\Medewerker\MedewerkerModel;
use App\Models\Medewerker\SpecialisatieModel;
use App\Models\User;
use Illuminate\Support\Carbon;
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

    private function volgendeWerkdag(int $dagenVooruit = 3): string
    {
        $datum = now()->addDays($dagenVooruit);

        while ($datum->isSunday()) {
            $datum->addDay();
        }

        return $datum->toDateString();
    }

    private function maakAfspraak(
        int $klantId,
        int $medewerkerId,
        int $behandelingId,
        string $datum,
        string $tijd,
        string $opmerking = 'Test'
    ): int {
        return \DB::table('afspraken')->insertGetId([
            'klant_id' => $klantId,
            'medewerker_id' => $medewerkerId,
            'behandeling_id' => $behandelingId,
            'afspraak_datum' => $datum,
            'afspraak_tijd' => $tijd,
            'opmerking' => $opmerking,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_medewerker_ziet_alle_afspraken_gesorteerd_op_datum_en_tijd(): void
    {
        $klant = $this->maakKlant('klant1@example.com');
        $medewerker = $this->maakMedewerker('medewerker1@example.com');
        $behandeling = $this->maakBehandeling();
        $klantId = $klant->klant()->first()->id;
        $vandaag = now()->toDateString();
        $volgendeWeek = now()->addWeek()->toDateString();

        $this->maakAfspraak($klantId, $medewerker->id, $behandeling, $vandaag, '14:00:00', 'Later vandaag');
        $this->maakAfspraak($klantId, $medewerker->id, $behandeling, $vandaag, '10:00:00', 'Vroeg vandaag');
        $this->maakAfspraak($klantId, $medewerker->id, $behandeling, $volgendeWeek, '09:00:00', 'Volgende week');

        $response = $this->actingAs($this->medewerkerUser)->get(route('afspraken.index'));

        $response->assertOk();
        $response->assertSee('Klant Tester');
        $response->assertSee('Mieke Jansen');
        $response->assertSee('Kapsel');
        $response->assertSee('Vroeg vandaag');
        $response->assertSee('Later vandaag');
        $response->assertSee('Volgende week');
        $response->assertSeeInOrder(['10:00', '14:00', '09:00']);
    }

    public function test_overzicht_toont_lege_melding_zonder_afspraken(): void
    {
        $response = $this->actingAs($this->medewerkerUser)->get(route('afspraken.index'));

        $response->assertOk();
        $response->assertSee('Er zijn nog geen afspraken ingepland');
    }

    public function test_medewerker_kan_afspraak_inplannen_voor_gekozen_klant(): void
    {
        $klant = $this->maakKlant('klant-plan@example.com');
        $medewerker = $this->maakMedewerker('medewerker-plan@example.com');
        $behandeling = $this->maakBehandeling();
        $klantRecord = $klant->klant()->first();

        $response = $this->actingAs($this->medewerkerUser)->post(route('afspraken.store'), [
            'klant_id' => $klantRecord->id,
            'behandeling_id' => $behandeling,
            'medewerker_id' => $medewerker->id,
            'afspraak_datum' => now()->addDays(2)->toDateString(),
            'afspraak_tijd' => '11:00:00',
            'opmerking' => 'Ingepland door medewerker',
        ]);

        $response->assertRedirect(route('afspraken.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('afspraken', [
            'klant_id' => $klantRecord->id,
            'medewerker_id' => $medewerker->id,
            'opmerking' => 'Ingepland door medewerker',
        ]);
    }

    public function test_medewerker_ziet_klant_keuze_op_aanmaakformulier(): void
    {
        $this->maakKlant('klant-form@example.com');

        $response = $this->actingAs($this->medewerkerUser)->get(route('afspraken.create'));

        $response->assertOk();
        $response->assertSee('Kies een klant');
        $response->assertSee('Klant Tester');
    }

    public function test_klant_krijgt_bevestiging_met_details_bij_nieuwe_afspraak(): void
    {
        $klant = $this->maakKlant('klant2@example.com');
        $medewerker = $this->maakMedewerker('medewerker2@example.com');
        $behandeling = $this->maakBehandeling();
        $datum = now()->addDay()->toDateString();

        $response = $this->actingAs($klant)->post(route('afspraken.store'), [
            'behandeling_id' => $behandeling,
            'medewerker_id' => $medewerker->id,
            'afspraak_datum' => $datum,
            'afspraak_tijd' => '10:00:00',
            'opmerking' => 'Nieuwe afspraak',
        ]);

        $response->assertRedirect(route('afspraken.create'));
        $response->assertSessionHas('success');

        $formattedDatum = Carbon::parse($datum)->format('d-m-Y');
        $response->assertSessionHas('success', "Afspraak ingepland: Kapsel bij Mieke Jansen op {$formattedDatum} om 10:00.");

        $this->assertDatabaseHas('afspraken', [
            'medewerker_id' => $medewerker->id,
            'behandeling_id' => $behandeling,
            'afspraak_tijd' => '10:00:00',
        ]);
    }

    public function test_klant_krijgt_foutmelding_bij_bezet_tijdstip(): void
    {
        $klant = $this->maakKlant('klant-conflict@example.com');
        $medewerker = $this->maakMedewerker('medewerker-conflict@example.com');
        $behandeling = $this->maakBehandeling();
        $datum = now()->addDay()->toDateString();

        $this->actingAs($klant)->post(route('afspraken.store'), [
            'behandeling_id' => $behandeling,
            'medewerker_id' => $medewerker->id,
            'afspraak_datum' => $datum,
            'afspraak_tijd' => '10:00:00',
        ])->assertSessionHas('success');

        $conflictResponse = $this->actingAs($klant)->post(route('afspraken.store'), [
            'behandeling_id' => $behandeling,
            'medewerker_id' => $medewerker->id,
            'afspraak_datum' => $datum,
            'afspraak_tijd' => '10:00:00',
        ]);

        $conflictResponse->assertSessionHasErrors(['afspraak_tijd']);
        $conflictResponse->assertSessionHasErrors([
            'afspraak_tijd' => 'Dit tijdstip is niet meer beschikbaar, kies een andere tijd',
        ]);
    }

    public function test_medewerker_kan_afspraak_wijzigen_naar_beschikbaar_moment(): void
    {
        $klant = $this->maakKlant('klant-wijzig@example.com');
        $medewerker = $this->maakMedewerker('medewerker-wijzig@example.com');
        $behandeling = $this->maakBehandeling();
        $klantId = $klant->klant()->first()->id;
        $datum = $this->volgendeWerkdag(3);

        $afspraakId = $this->maakAfspraak($klantId, $medewerker->id, $behandeling, $datum, '10:00:00');
        $afspraak = Afspraak::findOrFail($afspraakId);

        $response = $this->actingAs($this->medewerkerUser)->put(route('afspraken.update', $afspraak), [
            'klant_id' => $klantId,
            'behandeling_id' => $behandeling,
            'medewerker_id' => $medewerker->id,
            'afspraak_datum' => $datum,
            'afspraak_tijd' => '12:00:00',
            'opmerking' => 'Verplaatst',
        ]);

        $response->assertRedirect(route('afspraken.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('afspraken', [
            'id' => $afspraakId,
            'afspraak_tijd' => '12:00:00',
            'opmerking' => 'Verplaatst',
        ]);

        $this->assertDatabaseMissing('afspraken', [
            'id' => $afspraakId,
            'afspraak_tijd' => '10:00:00',
        ]);
    }

    public function test_medewerker_kan_afspraak_niet_wijzigen_naar_bezet_tijdstip(): void
    {
        $klant = $this->maakKlant('klant-wijzig-conflict@example.com');
        $medewerker = $this->maakMedewerker('medewerker-wijzig-conflict@example.com');
        $behandeling = $this->maakBehandeling();
        $klantId = $klant->klant()->first()->id;
        $datum = $this->volgendeWerkdag(4);

        $this->maakAfspraak($klantId, $medewerker->id, $behandeling, $datum, '10:00:00', 'Eerste');
        $afspraakId = $this->maakAfspraak($klantId, $medewerker->id, $behandeling, $datum, '11:00:00', 'Tweede');
        $afspraak = Afspraak::findOrFail($afspraakId);

        $response = $this->actingAs($this->medewerkerUser)->put(route('afspraken.update', $afspraak), [
            'klant_id' => $klantId,
            'behandeling_id' => $behandeling,
            'medewerker_id' => $medewerker->id,
            'afspraak_datum' => $datum,
            'afspraak_tijd' => '10:00:00',
            'opmerking' => 'Tweede',
        ]);

        $response->assertSessionHasErrors([
            'afspraak_tijd' => 'Dit tijdstip is niet meer beschikbaar, kies een andere tijd',
        ]);

        $this->assertDatabaseHas('afspraken', [
            'id' => $afspraakId,
            'afspraak_tijd' => '11:00:00',
        ]);
    }

    public function test_medewerker_kan_toekomstige_afspraak_annuleren(): void
    {
        $klant = $this->maakKlant('klant-delete@example.com');
        $medewerker = $this->maakMedewerker('medewerker-delete@example.com');
        $behandeling = $this->maakBehandeling();
        $klantId = $klant->klant()->first()->id;
        $datum = now()->addDays(2)->toDateString();

        $afspraakId = $this->maakAfspraak($klantId, $medewerker->id, $behandeling, $datum, '10:00:00');
        $afspraak = Afspraak::findOrFail($afspraakId);

        $response = $this->actingAs($this->medewerkerUser)->delete(route('afspraken.destroy', $afspraak));

        $response->assertRedirect(route('afspraken.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('afspraken', ['id' => $afspraakId]);
    }

    public function test_verstreken_afspraak_kan_niet_worden_verwijderd(): void
    {
        $klant = $this->maakKlant('klant3@example.com');
        $medewerker = $this->maakMedewerker('medewerker3@example.com');
        $behandeling = $this->maakBehandeling();

        $afspraakId = $this->maakAfspraak(
            $klant->klant()->first()->id,
            $medewerker->id,
            $behandeling,
            now()->subDay()->toDateString(),
            '10:00:00',
            'Oud'
        );

        $response = $this->actingAs($this->medewerkerUser)->delete(route('afspraken.destroy', $afspraakId));

        $response->assertRedirect(route('afspraken.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('afspraken', ['id' => $afspraakId]);
    }

    public function test_lopende_afspraak_kan_niet_worden_geannuleerd(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-07-02 10:20:00'));

        $klant = $this->maakKlant('klant-lopend@example.com');
        $medewerker = $this->maakMedewerker('medewerker-lopend@example.com');
        $behandeling = $this->maakBehandeling();
        $klantId = $klant->klant()->first()->id;

        $afspraakId = $this->maakAfspraak(
            $klantId,
            $medewerker->id,
            $behandeling,
            '2026-07-02',
            '10:00:00',
            'Lopend'
        );

        $response = $this->actingAs($this->medewerkerUser)->delete(route('afspraken.destroy', $afspraakId));

        $response->assertRedirect(route('afspraken.index'));
        $response->assertSessionHasErrors([
            'afspraak' => 'Een lopende afspraak kan niet meer worden geannuleerd',
        ]);
        $this->assertDatabaseHas('afspraken', ['id' => $afspraakId]);

        Carbon::setTestNow();
    }

    public function test_klant_heeft_geen_toegang_tot_afsprakenoverzicht(): void
    {
        $klant = $this->maakKlant('geen-overzicht@example.com');

        $response = $this->actingAs($klant)->get(route('afspraken.index'));

        $response->assertForbidden();
    }

    public function test_create_formulier_toont_dynamische_planning_velden(): void
    {
        $response = $this->actingAs($this->medewerkerUser)->get(route('afspraken.create'));

        $response->assertOk();
        $response->assertSee('Kies een behandeling');
        $response->assertSee('Kies eerst een specialist', false);
        $response->assertSee('afspraak-planning.js', false);
    }

    public function test_beschikbare_medewerkers_worden_gefilterd_op_behandeling(): void
    {
        $fadeMedewerker = $this->maakMedewerker('fade-specialist@example.com');
        $baardSpecialisatie = SpecialisatieModel::where('naam', 'Baard')->first();
        $baardMedewerker = $this->maakMedewerkerMetSpecialisatie('baard-specialist@example.com', $baardSpecialisatie->id);

        $fadeBehandeling = \DB::table('behandelingen')->insertGetId([
            'naam' => 'Fade + lijn',
            'duur_minuten' => 45,
            'prijs' => 32.50,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $baardBehandeling = \DB::table('behandelingen')->insertGetId([
            'naam' => 'Baard trimmen',
            'duur_minuten' => 20,
            'prijs' => 17.50,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $fadeResponse = $this->actingAs($this->medewerkerUser)->getJson(route('afspraken.beschikbare-medewerkers', [
            'behandeling_id' => $fadeBehandeling,
        ]));

        $fadeResponse->assertOk();
        $fadeIds = collect($fadeResponse->json('medewerkers'))->pluck('id')->all();
        $this->assertContains($fadeMedewerker->id, $fadeIds);
        $this->assertNotContains($baardMedewerker->id, $fadeIds);

        $baardResponse = $this->actingAs($this->medewerkerUser)->getJson(route('afspraken.beschikbare-medewerkers', [
            'behandeling_id' => $baardBehandeling,
        ]));

        $baardResponse->assertOk();
        $baardIds = collect($baardResponse->json('medewerkers'))->pluck('id')->all();
        $this->assertContains($baardMedewerker->id, $baardIds);
        $this->assertNotContains($fadeMedewerker->id, $baardIds);
    }

    public function test_beschikbare_datums_endpoint_geeft_toekomstige_dagen(): void
    {
        $medewerker = $this->maakMedewerker('beschikbaar@example.com');
        $behandeling = $this->maakBehandeling();
        $datum = $this->volgendeWerkdag(2);

        $response = $this->actingAs($this->medewerkerUser)->getJson(route('afspraken.beschikbare-datums', [
            'behandeling_id' => $behandeling,
            'medewerker_id' => $medewerker->id,
        ]));

        $response->assertOk();
        $response->assertJsonStructure(['datums']);
        $this->assertContains($datum, $response->json('datums'));
    }

    public function test_beschikbare_tijden_sluiten_geboekte_momenten_uit(): void
    {
        $klant = $this->maakKlant('beschikbaar-tijd@example.com');
        $medewerker = $this->maakMedewerker('beschikbaar-tijd-med@example.com');
        $behandeling = $this->maakBehandeling();
        $datum = $this->volgendeWerkdag(3);

        $this->maakAfspraak($klant->klant()->first()->id, $medewerker->id, $behandeling, $datum, '10:00:00');

        $response = $this->actingAs($this->medewerkerUser)->getJson(route('afspraken.beschikbare-tijden', [
            'behandeling_id' => $behandeling,
            'medewerker_id' => $medewerker->id,
            'datum' => $datum,
        ]));

        $response->assertOk();
        $tijden = $response->json('tijden');
        $this->assertNotContains('10:00', $tijden);
        $this->assertNotEmpty($tijden);
    }

    public function test_beschikbare_tijden_melding_bij_geen_vrije_momenten(): void
    {
        $medewerker = $this->maakMedewerker('geen-momenten@example.com');
        $behandeling = $this->maakBehandeling();

        $zondag = now()->addWeek()->startOfWeek()->addDays(6)->toDateString();

        $response = $this->actingAs($this->medewerkerUser)->getJson(route('afspraken.beschikbare-tijden', [
            'behandeling_id' => $behandeling,
            'medewerker_id' => $medewerker->id,
            'datum' => $zondag,
        ]));

        $response->assertOk();
        $response->assertJson([
            'tijden' => [],
            'melding' => 'Geen beschikbare tijden gevonden',
        ]);
    }

    private function maakMedewerkerMetSpecialisatie(string $email, int $specialisatieId): MedewerkerModel
    {
        $contact = ContactGegevensModel::create([
            'email' => $email,
            'telefoon' => '0698765432',
        ]);

        $gebruiker = GebruikerModel::create([
            'contact_gegevens_id' => $contact->id,
            'voornaam' => 'Spec',
            'achternaam' => 'Tester',
            'volledig_naam' => 'Spec Tester',
        ]);

        return MedewerkerModel::create([
            'gebruiker_id' => $gebruiker->id,
            'specialisatie_id' => $specialisatieId,
            'is_actief' => true,
        ]);
    }
}
