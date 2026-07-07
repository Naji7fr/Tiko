<?php

/**
 * Genereert database/sql/toki_empty.sql uit toki.sql (schema + views, zonder seeddata).
 * Gebruik: php database/build_toki_empty_sql.php
 */

$source = __DIR__ . '/sql/toki.sql';
$target = __DIR__ . '/sql/toki_empty.sql';
$emptyDbName = 'toki_empty';

if (! file_exists($source)) {
    fwrite(STDERR, "Bronbestand niet gevonden: {$source}\n");
    exit(1);
}

$content = file_get_contents($source);

$content = preg_replace(
    '/CREATE DATABASE IF NOT EXISTS `toki`/i',
    "CREATE DATABASE IF NOT EXISTS `{$emptyDbName}`",
    $content,
    1
);
$content = preg_replace('/USE `toki`;/i', "USE `{$emptyDbName}`;", $content, 1);

$marker = '-- 10. STARTDATA';
$cutAt = strpos($content, $marker);
if ($cutAt !== false) {
    $content = substr($content, 0, $cutAt);
}

$header = <<<SQL
-- =============================================================================
-- Tiko Barbershop — Lege database (alleen schema + views, geen seeddata)
-- =============================================================================
-- Gegenereerd door database/build_toki_empty_sql.php — niet handmatig bewerken.
-- Import: php database/setup_toki_empty.php
-- =============================================================================

SQL;

$content = $header . ltrim($content);

file_put_contents($target, rtrim($content) . "\n");

echo "Geschreven: {$target}\n";
