<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DatabaseStatusCommand extends Command
{
    protected $signature = 'db:status';

    protected $description = 'Toont welke database actief is (toki of toki_empty)';

    public function handle(): int
    {
        $useEmpty = config('database.use_empty_database');
        $database = config('database.connections.mysql.database');

        $this->line('DB_USE_EMPTY: ' . ($useEmpty ? 'true (lege modus)' : 'false (normale modus)'));
        $this->line('Actieve database: ' . $database);

        try {
            $medewerkers = DB::table('medewerkers')->count();
            $this->line('Medewerkers in actieve database: ' . $medewerkers);
        } catch (\Throwable $exception) {
            $this->error('Kon niet verbinden: ' . $exception->getMessage());
            $this->line('');
            $this->line('Tip: voer uit → php database/setup_toki_empty.php');

            return self::FAILURE;
        }

        if ($useEmpty && $medewerkers > 0) {
            $this->warn('Lege modus staat aan, maar er staan nog medewerkers in de database.');
        }

        if (! $useEmpty) {
            $this->comment('Zet DB_USE_EMPTY=true in .env en run: php artisan config:clear');
        }

        return self::SUCCESS;
    }
}
