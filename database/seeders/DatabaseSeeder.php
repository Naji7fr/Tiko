<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Hoofdseeder: Tiko-stamgegevens + standaard admin-account.
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call(TikoSeeder::class);

        User::updateOrCreate(
            ['email' => 'admin@tiko.nl'],
            [
                'name' => 'admin',
                'voornaam' => 'Beheerder',
                'achternaam' => 'Tiko',
                'email' => 'admin@tiko.nl',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'Actief',
                'email_verified_at' => now(),
            ]
        );
    }
}
