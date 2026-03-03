<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class EnsureAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:ensure {--password= : Set a custom password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ensure admin user exists with email admin@jamin.nl and is active';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = 'admin@jamin.nl';
        $password = $this->option('password') ?? 'password';

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Admin',
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'admin',
                'status' => 'actief',
                'email_verified_at' => now(),
            ]
        );

        // Ensure status is active (case-insensitive check)
        if (strtolower($user->status) !== 'actief') {
            $user->update(['status' => 'actief']);
            $this->info('Updated user status to actief');
        }

        $this->info("Admin user ensured: {$email}");
        $this->info("Password: {$password}");
        $this->info("Status: {$user->status}");
        $this->info("Role: {$user->role}");

        return Command::SUCCESS;
    }
}
