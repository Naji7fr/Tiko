<?php

namespace Tests\Feature;

use App\Models\Klant;
use App\Models\Medewerker\ContactGegevensModel;
use App\Models\Medewerker\GebruikerModel;
use App\Models\User;
use Tests\Concerns\RefreshesTestDatabase;
use Tests\TestCase;

/** Feature tests: klant kan eigen account verwijderen. */
class KlantAccountVerwijderenTest extends TestCase
{
    use RefreshesTestDatabase;

    public function test_klant_kan_eigen_account_verwijderen(): void
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

        $response = $this->actingAs($user)->delete(route('klant.overzicht.destroy'));

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('status', 'Je account is verwijderd.');

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('klanten', ['user_id' => $user->id]);
        $this->assertDatabaseMissing('gebruikers', ['id' => $gebruiker->id]);
        $this->assertDatabaseMissing('contact_gegevens', ['id' => $contact->id]);

        $this->assertGuest();
    }

    public function test_niet_klant_kan_account_verwijderen_route_niet_gebruiken(): void
    {
        $user = User::factory()->create([
            'name' => 'Admin Gebruiker',
            'voornaam' => 'Admin',
            'achternaam' => 'Gebruiker',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'status' => 'Actief',
        ]);

        $response = $this->actingAs($user)->delete(route('klant.overzicht.destroy'));

        $response->assertForbidden();
        $this->assertAuthenticatedAs($user);
    }
}