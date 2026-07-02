<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Tests\Concerns\RefreshesTestDatabase;
use Tests\TestCase;

/** Feature tests: accountbeheer door eigenaar. */
class AccountBeheerTest extends TestCase
{
    use RefreshesTestDatabase;

    private User $eigenaar;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);

        $this->eigenaar = User::where('email', 'admin@tiko.nl')->first();
    }

    public function test_eigenaar_kan_accounts_overzicht_bekijken(): void
    {
        User::factory()->create([
            'name' => 'medewerker.test',
            'voornaam' => 'Test',
            'achternaam' => 'Medewerker',
            'email' => 'medewerker@tiko.nl',
            'role' => 'medewerker',
            'status' => 'Actief',
        ]);

        $response = $this->actingAs($this->eigenaar)->get(route('eigenaar.accounts.index'));

        $response->assertOk();
        $response->assertSee('Accounts');
        $response->assertSee('medewerker@tiko.nl');
        $response->assertSee('admin@tiko.nl');
    }

    public function test_eigenaar_kan_nieuw_account_aanmaken(): void
    {
        $response = $this->actingAs($this->eigenaar)->post(route('eigenaar.accounts.store'), [
            'voornaam' => 'Nieuwe',
            'achternaam' => 'Medewerker',
            'email' => 'nieuw.medewerker@tiko.nl',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'medewerker',
            'status' => 'Actief',
        ]);

        $response->assertRedirect(route('eigenaar.accounts.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'nieuw.medewerker@tiko.nl',
            'role' => 'medewerker',
            'status' => 'Actief',
        ]);
    }

    public function test_eigenaar_kan_account_wijzigen(): void
    {
        $account = User::factory()->create([
            'name' => 'te.wijzigen',
            'voornaam' => 'Te',
            'achternaam' => 'Wijzigen',
            'email' => 'wijzig@tiko.nl',
            'role' => 'medewerker',
            'status' => 'Actief',
        ]);

        $response = $this->actingAs($this->eigenaar)->put(route('eigenaar.accounts.update', $account), [
            'voornaam' => 'Gewijzigd',
            'achternaam' => 'Account',
            'email' => 'wijzig@tiko.nl',
            'role' => 'medewerker',
            'status' => 'Inactief',
        ]);

        $response->assertRedirect(route('eigenaar.accounts.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $account->id,
            'voornaam' => 'Gewijzigd',
            'status' => 'Inactief',
        ]);
    }

    public function test_eigenaar_kan_account_verwijderen(): void
    {
        $account = User::factory()->create([
            'name' => 'te.verwijderen',
            'email' => 'delete@tiko.nl',
            'role' => 'medewerker',
            'status' => 'Actief',
        ]);

        $response = $this->actingAs($this->eigenaar)->delete(route('eigenaar.accounts.destroy', $account));

        $response->assertRedirect(route('eigenaar.accounts.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('users', ['id' => $account->id]);
    }

    public function test_eigenaar_kan_eigen_account_niet_verwijderen(): void
    {
        $response = $this->actingAs($this->eigenaar)->delete(route('eigenaar.accounts.destroy', $this->eigenaar));

        $response->assertRedirect(route('eigenaar.accounts.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $this->eigenaar->id]);
    }

    public function test_eigenaar_kan_klant_account_aanmaken(): void
    {
        $response = $this->actingAs($this->eigenaar)->post(route('eigenaar.accounts.store'), [
            'voornaam' => 'Nieuwe',
            'achternaam' => 'Klant',
            'email' => 'nieuw.klant@tiko.nl',
            'telefoon' => '0612345678',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'klant',
            'status' => 'Actief',
        ]);

        $response->assertRedirect(route('eigenaar.accounts.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'nieuw.klant@tiko.nl',
            'role' => 'klant',
        ]);

        $this->assertDatabaseHas('contact_gegevens', [
            'email' => 'nieuw.klant@tiko.nl',
            'telefoon' => '0612345678',
        ]);

        $this->assertDatabaseHas('klanten', [
            'user_id' => User::where('email', 'nieuw.klant@tiko.nl')->value('id'),
        ]);
    }

    public function test_eigenaar_kan_klant_account_verwijderen(): void
    {
        $this->actingAs($this->eigenaar)->post(route('eigenaar.accounts.store'), [
            'voornaam' => 'Te',
            'achternaam' => 'Verwijderen',
            'email' => 'klant.delete@tiko.nl',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'klant',
            'status' => 'Actief',
        ]);

        $account = User::where('email', 'klant.delete@tiko.nl')->first();

        $response = $this->actingAs($this->eigenaar)->delete(route('eigenaar.accounts.destroy', $account));

        $response->assertRedirect(route('eigenaar.accounts.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('users', ['id' => $account->id]);
        $this->assertDatabaseMissing('klanten', ['user_id' => $account->id]);
    }

    public function test_medewerker_heeft_geen_toegang_tot_accountbeheer(): void
    {
        $medewerker = User::factory()->create([
            'role' => 'medewerker',
            'status' => 'Actief',
        ]);

        $response = $this->actingAs($medewerker)->get(route('eigenaar.accounts.index'));

        $response->assertRedirect(route('home'));
    }
}
