<?php

/**
 * Eenmalig script: maakt database `toki` aan en importeert toki.sql
 * Gebruik: php database/setup_toki.php
 */

$host = '127.0.0.1';
$port = 3306;
$user = 'root';
$pass = '';
$database = 'toki';
$sqlFile = __DIR__ . '/sql/toki.sql';

if (! file_exists($sqlFile)) {
    fwrite(STDERR, "SQL-bestand niet gevonden: {$sqlFile}\n");
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

    $sql = file_get_contents($sqlFile);
    $sql = preg_replace('/^--.*$/m', '', $sql);

    $pdo->exec($sql);

    echo "Database `{$database}` aangemaakt en geïmporteerd.\n";

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

    echo "Login: admin@tiko.nl / password\n";
} catch (Throwable $e) {
    fwrite(STDERR, 'Fout: ' . $e->getMessage() . "\n");
    exit(1);
}
