<?php

/**
 * Maakt de lege database aan (schema + views, geen seeddata).
 * Gebruik: php database/setup_toki_empty.php
 *
 * Leest DB_* uit .env. SQL-pad via DB_EMPTY_SQL_PATH of database/sql/toki_empty.sql.
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$host = config('database.connections.mysql.host', '127.0.0.1');
$port = config('database.connections.mysql.port', 3306);
$user = config('database.connections.mysql.username', 'root');
$pass = config('database.connections.mysql.password', '');
$database = config('database.empty_database', 'toki_empty');
$sqlFile = config('database.empty_sql_path');

if (! file_exists($sqlFile)) {
    fwrite(STDERR, "SQL-bestand niet gevonden: {$sqlFile}\n");
    fwrite(STDERR, "Voer eerst uit: php database/build_toki_empty_sql.php\n");
    exit(1);
}

try {
    $pdo = new PDO(
        "mysql:host={$host};port={$port};charset=utf8mb4",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    echo "Verbinding met MySQL OK\n";
    echo "Lege database: `{$database}`\n";
    echo "SQL: {$sqlFile}\n";

    $sql = file_get_contents($sqlFile);
    $sql = preg_replace('/^--.*$/m', '', $sql);

    $pdo->exec($sql);

    echo "Database `{$database}` aangemaakt (schema + views, geen seeddata).\n";

    putenv('TIKO_IMPORT_DATABASE=' . $database);

    $spScripts = [
        __DIR__ . '/import_medewerker_sp.php',
        __DIR__ . '/import_klant_sp.php',
    ];

    foreach ($spScripts as $spScript) {
        if (! file_exists($spScript)) {
            continue;
        }

        passthru(PHP_BINARY . ' ' . escapeshellarg($spScript), $spExitCode);
        if ($spExitCode !== 0) {
            fwrite(STDERR, "Waarschuwing: stored procedures konden niet worden geïmporteerd ({$spScript}).\n");
        }
    }

    echo "Zet in .env: DB_USE_EMPTY=true om deze database te gebruiken.\n";
} catch (Throwable $e) {
    fwrite(STDERR, 'Fout: ' . $e->getMessage() . "\n");
    exit(1);
}
