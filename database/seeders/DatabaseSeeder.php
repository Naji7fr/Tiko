<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin (for beheer / backoffice)
        User::updateOrCreate(
            ['email' => 'admin@bng.nl'],
            [
                'name'       => 'admin',
                'voornaam'   => 'Beheerder',
                'achternaam' => 'BNG',
                'email'      => 'admin@bng.nl',
                'password'   => Hash::make('password'),
                'role'       => 'admin',
                'status'     => 'Actief',
                'email_verified_at' => now(),
            ]
        );

        User::factory()->create([
            'name'       => 'testuser',
            'voornaam'   => 'Test',
            'achternaam' => 'Gebruiker',
            'email'      => 'test@example.com',
            'status'     => 'Actief',
        ]);
    }
}
