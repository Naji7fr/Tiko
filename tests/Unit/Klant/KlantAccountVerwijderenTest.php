<?php

namespace Tests\Unit\Klant;

use App\Models\Klant;
use App\Models\Medewerker\ContactGegevensModel;
use App\Models\Medewerker\GebruikerModel;
use App\Models\User;
use App\Services\Klant\KlantAccountService;
use Tests\Concerns\RefreshesTestDatabase;
use Tests\TestCase;

/**
 * Unit tests: KlantAccountService::verwijderProfiel (zonder HTTP/routes).
 */
class KlantAccountVerwijderenTest extends TestCase
{
    use RefreshesTestDatabase;

    private KlantAccountService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new KlantAccountService;
    }

    public function test_verwijder_profiel_verwijdert_user_klant_gebruiker_en_contact(): void
    {
        $user = User::factory()->create([
            'name' => 'Jan Jansen',
            'voornaam' => 'Jan',
            'achternaam' => 'Jansen',
            'email' => 'jan@example.com',
            'role' => 'klant',
            'status' => 'Actief',
        ]);

        $contact = ContactGegevensModel::create([
            'email' => 'jan@example.com',
            'telefoon' => '0612345678',
        ]);

        $gebruiker = GebruikerModel::create([
            'contact_gegevens_id' => $contact->id,
            'voornaam' => 'Jan',
            'achternaam' => 'Jansen',
            'volledig_naam' => 'Jan Jansen',
        ]);

        Klant::create([
            'user_id' => $user->id,
            'gebruiker_id' => $gebruiker->id,
        ]);

        $this->service->verwijderProfiel($user);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('klanten', ['user_id' => $user->id]);
        $this->assertDatabaseMissing('gebruikers', ['id' => $gebruiker->id]);
        $this->assertDatabaseMissing('contact_gegevens', ['id' => $contact->id]);
    }

    public function test_is_klant_identificeert_alleen_klant_rol(): void
    {
        $klant = User::factory()->make(['role' => 'klant']);
        $admin = User::factory()->make(['role' => 'admin']);
        $medewerker = User::factory()->make(['role' => 'medewerker']);

        $this->assertTrue($klant->isKlant());
        $this->assertFalse($admin->isKlant());
        $this->assertFalse($medewerker->isKlant());
    }
}
