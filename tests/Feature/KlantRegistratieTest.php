<?php

namespace Tests\Feature;

use App\Models\Klant;
use App\Models\Medewerker\ContactGegevensModel;
use App\Models\Medewerker\GebruikerModel;
use App\Models\User;
use Database\Seeders\TikoSeeder;
use Tests\Concerns\RefreshesTestDatabase;
use Tests\TestCase;

/** Feature tests: klant-registratie, dashboard en rol-scheiding. */
class KlantRegistratieTest extends TestCase
{
    use RefreshesTestDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TikoSeeder::class);
    }

    public function test_klant_kan_zich_registreren(): void
    {
        $response = $this->post(route('register'), [
            'voornaam' => 'Jan',
            'achternaam' => 'Jansen',
            'email' => 'jan@klant.nl',
            'telefoon' => '0612345678',
            'password' => 'wachtwoord123',
            'password_confirmation' => 'wachtwoord123',
        ]);

        $response->assertRedirect(route('klant.dashboard'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'jan@klant.nl',
            'role' => 'klant',
            'status' => 'Actief',
        ]);

        $this->assertDatabaseHas('contact_gegevens', ['email' => 'jan@klant.nl']);
        $this->assertDatabaseHas('gebruikers', ['volledig_naam' => 'Jan Jansen']);
        $this->assertDatabaseHas('klanten', [
            'user_id' => User::where('email', 'jan@klant.nl')->value('id'),
        ]);
    }

    public function test_registratie_weigert_namen_met_cijfers(): void
    {
        $response = $this->from(route('register'))->post(route('register'), [
            'voornaam' => 'Jan123',
            'achternaam' => 'Jansen',
            'email' => 'jan@klant.nl',
            'password' => 'wachtwoord123',
            'password_confirmation' => 'wachtwoord123',
        ]);

        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors('voornaam');
        $this->assertDatabaseMissing('users', ['email' => 'jan@klant.nl']);
    }

    public function test_registratie_weigert_ongeldig_emailadres(): void
    {
        $response = $this->from(route('register'))->post(route('register'), [
            'voornaam' => 'Jan',
            'achternaam' => 'Jansen',
            'email' => 'geen-at-email',
            'password' => 'wachtwoord123',
            'password_confirmation' => 'wachtwoord123',
        ]);

        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors('email');
        $this->assertDatabaseMissing('users', ['email' => 'geen-at-email']);
    }

    public function test_ingelogde_klant_heeft_geen_toegang_tot_medewerkerbeheer(): void
    {
        $user = User::factory()->create(['role' => 'klant', 'status' => 'Actief']);

        $contact = ContactGegevensModel::create([
            'email' => $user->email,
            'telefoon' => '0612345678',
        ]);

        $gebruiker = GebruikerModel::create([
            'contact_gegevens_id' => $contact->id,
            'voornaam' => 'Test',
            'achternaam' => 'Klant',
            'volledig_naam' => 'Test Klant',
        ]);

        Klant::create([
            'user_id' => $user->id,
            'gebruiker_id' => $gebruiker->id,
        ]);

        $response = $this->actingAs($user)->get(route('medewerkers.index'));

        $response->assertRedirect(route('home'));
    }

    public function test_klant_dashboard_is_toegankelijk_voor_klant(): void
    {
        $user = User::factory()->create(['role' => 'klant', 'status' => 'Actief']);

        $contact = ContactGegevensModel::create([
            'email' => $user->email,
        ]);

        $gebruiker = GebruikerModel::create([
            'contact_gegevens_id' => $contact->id,
            'voornaam' => 'Piet',
            'achternaam' => 'Pietersen',
            'volledig_naam' => 'Piet Pietersen',
        ]);

        Klant::create([
            'user_id' => $user->id,
            'gebruiker_id' => $gebruiker->id,
        ]);

        $response = $this->actingAs($user)->get(route('klant.dashboard'));

        $response->assertOk();
        $response->assertSee('Klant Overzicht');
        $response->assertSee('Piet Pietersen');
    }

    public function test_admin_heeft_geen_toegang_tot_klant_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'Actief']);

        $response = $this->actingAs($admin)->get(route('klant.dashboard'));

        $response->assertRedirect(route('home'));
    }
}
