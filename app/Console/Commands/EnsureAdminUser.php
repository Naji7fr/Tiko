<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

/**
 * Maakt of werkt het standaard admin-account bij.
 */
class EnsureAdminUser extends Command
{
    protected $signature = 'admin:ensure {--password= : Optioneel wachtwoord}';

    protected $description = 'Zorg dat admin@tiko.nl bestaat en actief is';

    public function handle(): int
    {
        $email = 'admin@tiko.nl';
        $password = $this->option('password') ?? 'password';

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'admin',
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'admin',
                'status' => 'Actief',
                'email_verified_at' => now(),
            ]
        );

        $this->info("Admin account: {$email}");
        $this->info("Wachtwoord: {$password}");
        $this->info("Status: {$user->status}");

        return Command::SUCCESS;
    }
}
