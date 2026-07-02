<?php

/**
 * Importeert medewerker stored procedures in database `toki`.
 * Gebruik: php database/import_medewerker_sp.php
 */

$host = '127.0.0.1';
$user = 'root';
$pass = '';
$database = 'toki';
$sqlFile = __DIR__ . '/sql/medewerker_stored_procedures.sql';

if (! file_exists($sqlFile)) {
    fwrite(STDERR, "SQL-bestand niet gevonden: {$sqlFile}\n");
    exit(1);
}

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$database};charset=utf8mb4",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $sql = file_get_contents($sqlFile);
    $sql = preg_replace('/^--.*$/m', '', $sql);
    $sql = preg_replace('/^DELIMITER.*$/m', '', $sql);
    $sql = str_replace('//', ';', $sql);

    preg_match_all('/DROP PROCEDURE IF EXISTS \w+;/', $sql, $drops);
    foreach ($drops[0] as $drop) {
        $pdo->exec($drop);
    }

    $parts = preg_split('/(?=CREATE PROCEDURE)/', $sql);
    foreach ($parts as $part) {
        $part = trim($part);
        if (! str_starts_with($part, 'CREATE PROCEDURE')) {
            continue;
        }

        $lastEnd = strrpos($part, 'END;');
        if ($lastEnd === false) {
            continue;
        }

        $pdo->exec(substr($part, 0, $lastEnd + 4));
    }

    echo "Stored procedures geïmporteerd in `{$database}`.\n";
} catch (Throwable $e) {
    fwrite(STDERR, 'Fout: ' . $e->getMessage() . "\n");
    exit(1);
}
