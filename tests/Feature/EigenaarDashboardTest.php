<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Tests\Concerns\RefreshesTestDatabase;
use Tests\TestCase;

/** Feature tests: eigenaar-dashboard, rapportages en toegangscontrole. */
class EigenaarDashboardTest extends TestCase
{
    use RefreshesTestDatabase;

    public function test_eigenaar_kan_dashboard_bekijken(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::where('email', 'admin@tiko.nl')->first();

        $response = $this->actingAs($admin)->get(route('eigenaar.dashboard'));

        $response->assertOk();
        $response->assertSee('Eigenaar Dashboard');
        $response->assertSee('Medewerkers');
        $response->assertSee('Rapportages');
    }

    public function test_eigenaar_kan_rapportages_bekijken(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'Actief']);

        $response = $this->actingAs($admin)->get(route('eigenaar.rapportages'));

        $response->assertOk();
        $response->assertSee('Rapportages');
    }

    public function test_medewerker_heeft_geen_toegang_tot_eigenaar_dashboard(): void
    {
        $medewerker = User::factory()->create(['role' => 'medewerker', 'status' => 'Actief']);

        $response = $this->actingAs($medewerker)->get(route('eigenaar.dashboard'));

        $response->assertRedirect(route('home'));
    }

    public function test_klant_heeft_geen_toegang_tot_eigenaar_dashboard(): void
    {
        $klant = User::factory()->create(['role' => 'klant', 'status' => 'Actief']);

        $response = $this->actingAs($klant)->get(route('eigenaar.dashboard'));

        $response->assertRedirect(route('home'));
    }
}
