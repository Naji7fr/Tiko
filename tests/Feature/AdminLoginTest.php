<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\RefreshesTestDatabase;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshesTestDatabase;

    public function test_admin_kan_inloggen_met_tiko_email(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->post(route('login'), [
            'email' => 'admin@tiko.nl',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('eigenaar.dashboard'));
        $this->assertAuthenticatedAs(User::where('email', 'admin@tiko.nl')->first());
    }

    public function test_admin_login_fails_with_wrong_password(): void
    {
        User::factory()->create([
            'email' => 'admin@tiko.nl',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'Actief',
        ]);

        $response = $this->post(route('login'), [
            'email' => 'admin@tiko.nl',
            'password' => 'wrong',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
