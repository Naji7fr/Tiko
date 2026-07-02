-- =============================================================================
-- Patch: medewerker_beschikbaarheid (bestaande toki-database)
-- Voer uit in phpMyAdmin of: mysql -u root toki < database/sql/medewerker_beschikbaarheid_patch.sql
-- =============================================================================

USE `toki`;

CREATE TABLE IF NOT EXISTS `medewerker_beschikbaarheid` (
    `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `medewerker_id`   BIGINT UNSIGNED NOT NULL,
    `dag_van_week`    TINYINT UNSIGNED NOT NULL COMMENT '1=maandag … 7=zondag (ISO)',
    `start_tijd`      TIME            NOT NULL,
    `eind_tijd`       TIME            NOT NULL,
    `is_beschikbaar`  TINYINT(1)      NOT NULL DEFAULT 1,
    `created_at`      TIMESTAMP       NULL,
    `updated_at`      TIMESTAMP       NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `medewerker_beschikbaarheid_medewerker_dag_unique` (`medewerker_id`, `dag_van_week`),
    CONSTRAINT `fk_medewerker_beschikbaarheid_medewerker`
        FOREIGN KEY (`medewerker_id`) REFERENCES `medewerkers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Standaard weekrooster voor medewerkers die nog geen rooster hebben
INSERT INTO `medewerker_beschikbaarheid`
    (`medewerker_id`, `dag_van_week`, `start_tijd`, `eind_tijd`, `is_beschikbaar`, `created_at`, `updated_at`)
SELECT m.id, d.dag,
    CASE WHEN d.dag = 6 THEN '08:00:00' ELSE '09:00:00' END,
    CASE WHEN d.dag = 6 THEN '17:00:00' WHEN d.dag = 7 THEN '17:00:00' ELSE '18:00:00' END,
    CASE WHEN d.dag = 7 OR m.is_actief = 0 THEN 0 ELSE 1 END,
    NOW(), NOW()
FROM `medewerkers` m
CROSS JOIN (
    SELECT 1 AS dag UNION SELECT 2 UNION SELECT 3 UNION SELECT 4
    UNION SELECT 5 UNION SELECT 6 UNION SELECT 7
) d
WHERE NOT EXISTS (
    SELECT 1 FROM `medewerker_beschikbaarheid` mb
    WHERE mb.medewerker_id = m.id AND mb.dag_van_week = d.dag
);
