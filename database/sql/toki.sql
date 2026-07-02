-- =============================================================================
-- Tiko Barbershop — Volledig database script (complete ERD)
-- =============================================================================
-- Import: phpMyAdmin → Import
-- Login:  admin@tiko.nl / password
--
-- ERD-tabellen:
--   [MEDEWERKER] ContactGegevens, Gebruiker, Specialisatie, Medewerker
--   [KLANT]      Adres, Klant, Allergie, KlantAllergie
--   Afspraken, Behandeling, BehandelingProducten
--   Bestelling, BestellingProduct, Product, Voorraad, Categorie
--   Leverancier, LeverancierGegevens
--
-- JOIN-views (sectie 9): v_medewerker_overzicht, v_klant_overzicht, …
-- Stored procedures: database/sql/medewerker_stored_procedures.sql
--                  database/sql/klant_stored_procedures.sql
-- =============================================================================

CREATE DATABASE IF NOT EXISTS `toki`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `toki`;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP VIEW IF EXISTS
    `v_producten_overzicht`,
    `v_bestellingen_overzicht`,
    `v_afspraken_overzicht`,
    `v_klant_overzicht`,
    `v_medewerker_overzicht`;

DROP TABLE IF EXISTS
    `afspraken`, `bestelling_producten`, `bestellingen`,
    `behandeling_producten`, `klant_allergieen`, `allergieen`,
    `klanten`, `medewerker_beschikbaarheid`, `medewerkers`, `voorraad`, `producten`,
    `behandelingen`, `leveranciers`, `leverancier_gegevens`,
    `categorieen`, `gebruikers`, `specialisaties`,
    `adressen`, `contact_gegevens`, `technische_logs`,
    `failed_jobs`, `job_batches`, `jobs`, `cache_locks`, `cache`,
    `sessions`, `password_reset_tokens`, `users`;

SET FOREIGN_KEY_CHECKS = 1;


-- =============================================================================
-- 1. LOGIN (Laravel — admin + klant)
-- =============================================================================

CREATE TABLE `users` (
    `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`              VARCHAR(255)    NOT NULL,
    `voornaam`          VARCHAR(255)    NULL,
    `achternaam`        VARCHAR(255)    NULL,
    `email`             VARCHAR(255)    NOT NULL,
    `email_verified_at` TIMESTAMP       NULL,
    `password`          VARCHAR(255)    NOT NULL,
    `role`              VARCHAR(255)    NOT NULL DEFAULT 'klant',
    `status`            VARCHAR(255)    NOT NULL DEFAULT 'Actief',
    `remember_token`    VARCHAR(100)    NULL,
    `created_at`        TIMESTAMP       NULL,
    `updated_at`        TIMESTAMP       NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_email_unique` (`email`),
    UNIQUE KEY `users_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_reset_tokens` (
    `email`      VARCHAR(255) NOT NULL,
    `token`      VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP    NULL,
    PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sessions` (
    `id`            VARCHAR(255)    NOT NULL,
    `user_id`       BIGINT UNSIGNED NULL,
    `ip_address`    VARCHAR(45)     NULL,
    `user_agent`    TEXT            NULL,
    `payload`       LONGTEXT        NOT NULL,
    `last_activity` INT             NOT NULL,
    PRIMARY KEY (`id`),
    KEY `sessions_user_id_index` (`user_id`),
    KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================================
-- 2. LARAVEL (cache & wachtrij)
-- =============================================================================

CREATE TABLE `cache` (
    `key`        VARCHAR(255) NOT NULL,
    `value`      MEDIUMTEXT   NOT NULL,
    `expiration` INT          NOT NULL,
    PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache_locks` (
    `key`        VARCHAR(255) NOT NULL,
    `owner`      VARCHAR(255) NOT NULL,
    `expiration` INT          NOT NULL,
    PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `jobs` (
    `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `queue`        VARCHAR(255)    NOT NULL,
    `payload`      LONGTEXT        NOT NULL,
    `attempts`     TINYINT UNSIGNED NOT NULL,
    `reserved_at`  INT UNSIGNED    NULL,
    `available_at` INT UNSIGNED    NOT NULL,
    `created_at`   INT UNSIGNED    NOT NULL,
    PRIMARY KEY (`id`),
    KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `job_batches` (
    `id`             VARCHAR(255) NOT NULL,
    `name`           VARCHAR(255) NOT NULL,
    `total_jobs`     INT          NOT NULL,
    `pending_jobs`   INT          NOT NULL,
    `failed_jobs`    INT          NOT NULL,
    `failed_job_ids` LONGTEXT     NOT NULL,
    `options`        MEDIUMTEXT   NULL,
    `cancelled_at`   INT          NULL,
    `created_at`     INT          NOT NULL,
    `finished_at`    INT          NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `failed_jobs` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `uuid`       VARCHAR(255)    NOT NULL,
    `connection` TEXT            NOT NULL,
    `queue`      TEXT            NOT NULL,
    `payload`    LONGTEXT        NOT NULL,
    `exception`  LONGTEXT        NOT NULL,
    `failed_at`  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================================
-- 3. MEDEWERKER 
-- =============================================================================
-- ERD:  ContactGegevens → Gebruiker → Medewerker → Specialisatie
--
--   contact_gegevens ──► gebruikers ◄── medewerkers ──► specialisaties
-- =============================================================================

-- 3.1 ContactGegevens
CREATE TABLE `contact_gegevens` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `email`      VARCHAR(254)    NOT NULL,
    `telefoon`   VARCHAR(25)     NULL,
    `opmerking`  VARCHAR(225)    NULL,
    `created_at` TIMESTAMP       NULL,
    `updated_at` TIMESTAMP       NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `contact_gegevens_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3.2 Gebruiker
CREATE TABLE `gebruikers` (
    `id`                   BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `contact_gegevens_id`  BIGINT UNSIGNED NOT NULL,
    `voornaam`             VARCHAR(50)     NOT NULL,
    `tussenvoegsel`        VARCHAR(20)     NULL,
    `achternaam`           VARCHAR(50)     NOT NULL,
    `volledig_naam`        VARCHAR(121)    NOT NULL,
    `geboortedatum`        DATE            NULL,
    `created_at`           TIMESTAMP       NULL,
    `updated_at`           TIMESTAMP       NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_gebruikers_contact`
        FOREIGN KEY (`contact_gegevens_id`) REFERENCES `contact_gegevens` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3.3 Specialisatie
CREATE TABLE `specialisaties` (
    `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `naam`          VARCHAR(50)     NOT NULL,
    `beschrijving`  VARCHAR(225)    NULL,
    `created_at`    TIMESTAMP       NULL,
    `updated_at`    TIMESTAMP       NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `specialisaties_naam_unique` (`naam`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3.4 Medewerker
CREATE TABLE `medewerkers` (
    `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `specialisatie_id`  BIGINT UNSIGNED NOT NULL,
    `gebruiker_id`      BIGINT UNSIGNED NOT NULL,
    `is_actief`         TINYINT(1)      NOT NULL DEFAULT 1,
    `created_at`        TIMESTAMP       NULL,
    `updated_at`        TIMESTAMP       NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `medewerkers_gebruiker_id_unique` (`gebruiker_id`),
    CONSTRAINT `fk_medewerkers_specialisatie`
        FOREIGN KEY (`specialisatie_id`) REFERENCES `specialisaties` (`id`),
    CONSTRAINT `fk_medewerkers_gebruiker`
        FOREIGN KEY (`gebruiker_id`) REFERENCES `gebruikers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3.5 Medewerker beschikbaarheid (weekrooster per medewerker)
CREATE TABLE `medewerker_beschikbaarheid` (
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


-- =============================================================================
-- 4. KLANT (andere developer — Adres, Klant, Allergie)
-- =============================================================================

-- 4.1 Adres (klant-module; koppeling via gebruikers.adres_id)
CREATE TABLE `adressen` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `straat`      VARCHAR(100)    NOT NULL,
    `huisnummer`  VARCHAR(10)     NOT NULL,
    `postcode`    VARCHAR(10)     NOT NULL,
    `plaats`      VARCHAR(50)     NOT NULL,
    `land`        VARCHAR(50)     NOT NULL DEFAULT 'Nederland',
    `created_at`  TIMESTAMP       NULL,
    `updated_at`  TIMESTAMP       NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `gebruikers`
    ADD COLUMN `adres_id` BIGINT UNSIGNED NULL AFTER `contact_gegevens_id`,
    ADD CONSTRAINT `fk_gebruikers_adres`
        FOREIGN KEY (`adres_id`) REFERENCES `adressen` (`id`) ON DELETE SET NULL;

-- 4.2 Klant
CREATE TABLE `klanten` (
    `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`       BIGINT UNSIGNED NULL,
    `gebruiker_id`  BIGINT UNSIGNED NOT NULL,
    `created_at`    TIMESTAMP       NULL,
    `updated_at`    TIMESTAMP       NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `klanten_user_id_unique` (`user_id`),
    UNIQUE KEY `klanten_gebruiker_id_unique` (`gebruiker_id`),
    CONSTRAINT `fk_klanten_user`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_klanten_gebruiker`
        FOREIGN KEY (`gebruiker_id`) REFERENCES `gebruikers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `allergieen` (
    `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `klant_id`      BIGINT UNSIGNED NOT NULL,
    `naam`          VARCHAR(100)    NOT NULL,
    `beschrijving`  VARCHAR(225)    NULL,
    `created_at`    TIMESTAMP       NULL,
    `updated_at`    TIMESTAMP       NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_allergieen_klant`
        FOREIGN KEY (`klant_id`) REFERENCES `klanten` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `klant_allergieen` (
    `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `klant_id`      BIGINT UNSIGNED NOT NULL,
    `allergie_id`   BIGINT UNSIGNED NOT NULL,
    `created_at`    TIMESTAMP       NULL,
    `updated_at`    TIMESTAMP       NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `klant_allergieen_unique` (`klant_id`, `allergie_id`),
    CONSTRAINT `fk_klant_allergieen_klant`
        FOREIGN KEY (`klant_id`) REFERENCES `klanten` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_klant_allergieen_allergie`
        FOREIGN KEY (`allergie_id`) REFERENCES `allergieen` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================================
-- 5. BEHANDELING — Behandeling, BehandelingProducten
-- =============================================================================

CREATE TABLE `behandelingen` (
    `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `naam`          VARCHAR(50)     NOT NULL,
    `duur_minuten`  INT UNSIGNED    NOT NULL DEFAULT 30,
    `prijs`         DECIMAL(8, 2)   NOT NULL,
    `created_at`    TIMESTAMP       NULL,
    `updated_at`    TIMESTAMP       NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `behandelingen_naam_unique` (`naam`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================================
-- 6. PRODUCT — Categorie, Leverancier, Product, Voorraad
-- =============================================================================

CREATE TABLE `categorieen` (
    `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `naam`          VARCHAR(100)    NOT NULL,
    `beschrijving`  VARCHAR(225)    NULL,
    `created_at`    TIMESTAMP       NULL,
    `updated_at`    TIMESTAMP       NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `categorieen_naam_unique` (`naam`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `leverancier_gegevens` (
    `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `bedrijfsnaam`  VARCHAR(100)    NOT NULL,
    `email`         VARCHAR(254)    NULL,
    `telefoon`      VARCHAR(25)     NULL,
    `kvk_nummer`    VARCHAR(20)     NULL,
    `created_at`    TIMESTAMP       NULL,
    `updated_at`    TIMESTAMP       NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `leveranciers` (
    `id`                        BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `leverancier_gegevens_id`   BIGINT UNSIGNED NOT NULL,
    `created_at`                TIMESTAMP       NULL,
    `updated_at`                TIMESTAMP       NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_leveranciers_gegevens`
        FOREIGN KEY (`leverancier_gegevens_id`) REFERENCES `leverancier_gegevens` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `producten` (
    `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `categorie_id`      BIGINT UNSIGNED NOT NULL,
    `leverancier_id`    BIGINT UNSIGNED NOT NULL,
    `naam`              VARCHAR(100)    NOT NULL,
    `ean_code`          VARCHAR(13)     NOT NULL,
    `beschrijving`      VARCHAR(225)    NULL,
    `prijs`             DECIMAL(8, 2)   NOT NULL,
    `created_at`        TIMESTAMP       NULL,
    `updated_at`        TIMESTAMP       NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_producten_categorie`
        FOREIGN KEY (`categorie_id`) REFERENCES `categorieen` (`id`),
    CONSTRAINT `fk_producten_leverancier`
        FOREIGN KEY (`leverancier_id`) REFERENCES `leveranciers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `voorraad` (
    `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `product_id`    BIGINT UNSIGNED NOT NULL,
    `categorie_id`  BIGINT UNSIGNED NOT NULL,
    `aantal`        INT UNSIGNED    NOT NULL DEFAULT 0,
    `minimum`       INT UNSIGNED    NOT NULL DEFAULT 5,
    `created_at`    TIMESTAMP       NULL,
    `updated_at`    TIMESTAMP       NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `voorraad_product_unique` (`product_id`),
    CONSTRAINT `fk_voorraad_product`
        FOREIGN KEY (`product_id`) REFERENCES `producten` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_voorraad_categorie`
        FOREIGN KEY (`categorie_id`) REFERENCES `categorieen` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `behandeling_producten` (
    `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `behandeling_id`    BIGINT UNSIGNED NOT NULL,
    `product_id`        BIGINT UNSIGNED NOT NULL,
    `aantal`            INT UNSIGNED    NOT NULL DEFAULT 1,
    `created_at`        TIMESTAMP       NULL,
    `updated_at`        TIMESTAMP       NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `behandeling_producten_unique` (`behandeling_id`, `product_id`),
    CONSTRAINT `fk_behandeling_producten_behandeling`
        FOREIGN KEY (`behandeling_id`) REFERENCES `behandelingen` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_behandeling_producten_product`
        FOREIGN KEY (`product_id`) REFERENCES `producten` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================================
-- 7. BESTELLING — Bestelling, BestellingProduct
-- =============================================================================

CREATE TABLE `bestellingen` (
    `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `klant_id`      BIGINT UNSIGNED NOT NULL,
    `bestel_datum`  DATE            NOT NULL,
    `status`        VARCHAR(50)     NOT NULL DEFAULT 'Nieuw',
    `totaal_prijs`  DECIMAL(10, 2)  NOT NULL DEFAULT 0.00,
    `created_at`    TIMESTAMP       NULL,
    `updated_at`    TIMESTAMP       NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_bestellingen_klant`
        FOREIGN KEY (`klant_id`) REFERENCES `klanten` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `bestelling_producten` (
    `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `bestelling_id`     BIGINT UNSIGNED NOT NULL,
    `product_id`        BIGINT UNSIGNED NOT NULL,
    `aantal`            INT UNSIGNED    NOT NULL DEFAULT 1,
    `stuk_prijs`        DECIMAL(8, 2)   NOT NULL,
    `created_at`        TIMESTAMP       NULL,
    `updated_at`        TIMESTAMP       NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_bestelling_producten_bestelling`
        FOREIGN KEY (`bestelling_id`) REFERENCES `bestellingen` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_bestelling_producten_product`
        FOREIGN KEY (`product_id`) REFERENCES `producten` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================================
-- 8. AFSPRAAK — Afspraken
-- =============================================================================

CREATE TABLE `afspraken` (
    `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `klant_id`        BIGINT UNSIGNED NOT NULL,
    `medewerker_id`   BIGINT UNSIGNED NOT NULL,
    `behandeling_id`  BIGINT UNSIGNED NOT NULL,
    `afspraak_datum`  DATE            NOT NULL,
    `afspraak_tijd`   TIME            NOT NULL,
    `opmerking`       VARCHAR(225)    NULL,
    `created_at`      TIMESTAMP       NULL,
    `updated_at`      TIMESTAMP       NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_afspraken_klant`
        FOREIGN KEY (`klant_id`) REFERENCES `klanten` (`id`),
    CONSTRAINT `fk_afspraken_medewerker`
        FOREIGN KEY (`medewerker_id`) REFERENCES `medewerkers` (`id`),
    CONSTRAINT `fk_afspraken_behandeling`
        FOREIGN KEY (`behandeling_id`) REFERENCES `behandelingen` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================================
-- 8. TECHNISCHE LOG
-- =============================================================================

CREATE TABLE `technische_logs` (
    `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `niveau`        VARCHAR(20)     NOT NULL,
    `module`        VARCHAR(50)     NOT NULL,
    `actie`         VARCHAR(100)    NOT NULL,
    `bericht`       TEXT            NOT NULL,
    `gebruiker_id`  BIGINT UNSIGNED NULL,
    `ip_adres`      VARCHAR(45)     NULL,
    `created_at`    TIMESTAMP       NULL,
    PRIMARY KEY (`id`),
    KEY `technische_logs_module_index` (`module`),
    KEY `technische_logs_niveau_index` (`niveau`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================================
-- 9. VIEWS MET INNER JOINs (ERD-koppelingen)
-- =============================================================================
-- Herbruikbare overzichten via JOINs over gerelateerde tabellen.
-- Gebruik: SELECT * FROM v_medewerker_overzicht;
-- =============================================================================

-- Medewerker: medewerkers → gebruikers → contact_gegevens + specialisaties
CREATE VIEW `v_medewerker_overzicht` AS
SELECT
    m.id                AS medewerker_id,
    m.is_actief,
    m.specialisatie_id,
    m.gebruiker_id,
    g.voornaam,
    g.tussenvoegsel,
    g.achternaam,
    g.volledig_naam,
    cg.email,
    cg.telefoon,
    s.naam              AS specialisatie_naam,
    m.created_at,
    m.updated_at
FROM medewerkers m
INNER JOIN gebruikers g
    ON m.gebruiker_id = g.id
INNER JOIN contact_gegevens cg
    ON g.contact_gegevens_id = cg.id
INNER JOIN specialisaties s
    ON m.specialisatie_id = s.id;

-- Klant: klanten → gebruikers → contact_gegevens (+ optioneel adres)
CREATE VIEW `v_klant_overzicht` AS
SELECT
    k.id                AS klant_id,
    k.user_id,
    g.id                AS gebruiker_id,
    g.volledig_naam,
    g.voornaam,
    g.achternaam,
    cg.email,
    cg.telefoon,
    a.straat,
    a.huisnummer,
    a.postcode,
    a.plaats,
    a.land,
    k.created_at,
    k.updated_at
FROM klanten k
INNER JOIN gebruikers g
    ON k.gebruiker_id = g.id
INNER JOIN contact_gegevens cg
    ON g.contact_gegevens_id = cg.id
LEFT JOIN adressen a
    ON g.adres_id = a.id;

-- Afspraken: afspraken → klant, medewerker, behandeling (dubbele join op gebruikers)
CREATE VIEW `v_afspraken_overzicht` AS
SELECT
    a.id                AS afspraak_id,
    a.afspraak_datum,
    a.afspraak_tijd,
    a.opmerking,
    kg.volledig_naam    AS klant_naam,
    cg_klant.email      AS klant_email,
    mg.volledig_naam    AS medewerker_naam,
    s.naam              AS medewerker_specialisatie,
    b.naam              AS behandeling_naam,
    b.duur_minuten,
    b.prijs             AS behandeling_prijs,
    a.created_at,
    a.updated_at
FROM afspraken a
INNER JOIN klanten k
    ON a.klant_id = k.id
INNER JOIN gebruikers kg
    ON k.gebruiker_id = kg.id
INNER JOIN contact_gegevens cg_klant
    ON kg.contact_gegevens_id = cg_klant.id
INNER JOIN medewerkers m
    ON a.medewerker_id = m.id
INNER JOIN gebruikers mg
    ON m.gebruiker_id = mg.id
INNER JOIN specialisaties s
    ON m.specialisatie_id = s.id
INNER JOIN behandelingen b
    ON a.behandeling_id = b.id;

-- Bestellingen: bestellingen → klanten → gebruikers
CREATE VIEW `v_bestellingen_overzicht` AS
SELECT
    bs.id               AS bestelling_id,
    bs.bestel_datum,
    bs.status,
    bs.totaal_prijs,
    k.id                AS klant_id,
    g.volledig_naam     AS klant_naam,
    cg.email            AS klant_email,
    bs.created_at,
    bs.updated_at
FROM bestellingen bs
INNER JOIN klanten k
    ON bs.klant_id = k.id
INNER JOIN gebruikers g
    ON k.gebruiker_id = g.id
INNER JOIN contact_gegevens cg
    ON g.contact_gegevens_id = cg.id;

-- Producten: producten → categorieen + leveranciers → leverancier_gegevens
CREATE VIEW `v_producten_overzicht` AS
SELECT
    p.id                AS product_id,
    p.naam              AS product_naam,
    p.ean_code,
    p.beschrijving,
    p.prijs,
    c.id                AS categorie_id,
    c.naam              AS categorie_naam,
    l.id                AS leverancier_id,
    lg.bedrijfsnaam     AS leverancier_naam,
    lg.email            AS leverancier_email,
    lg.telefoon         AS leverancier_telefoon,
    v.aantal            AS voorraad_aantal,
    v.minimum           AS voorraad_minimum,
    p.created_at,
    p.updated_at
FROM producten p
INNER JOIN categorieen c
    ON p.categorie_id = c.id
INNER JOIN leveranciers l
    ON p.leverancier_id = l.id
INNER JOIN leverancier_gegevens lg
    ON l.leverancier_gegevens_id = lg.id
LEFT JOIN voorraad v
    ON v.product_id = p.id;


-- =============================================================================
-- 10. STARTDATA — minimaal 5 realistische records per tabel
-- =============================================================================
-- Wachtwoord alle accounts: password
-- Hash: $2y$12$i.drMAcWW6u0rQsAGXkpA.FE0.uZfW9/CyTfHn8fI4Cj08rcNDZSq
-- =============================================================================

SET @pwd = '$2y$12$i.drMAcWW6u0rQsAGXkpA.FE0.uZfW9/CyTfHn8fI4Cj08rcNDZSq';
SET @nu = NOW();

-- --- Specialisaties (5) ---
INSERT INTO `specialisaties` (`naam`, `beschrijving`, `created_at`, `updated_at`) VALUES
    ('Fade',           'Skin fades, tapers en modern kort haar',        @nu, @nu),
    ('Baard',          'Baard trimmen, contouren en hot towel shave',   @nu, @nu),
    ('Kleuren',        'Grey blending, highlights en kleurverzorging',  @nu, @nu),
    ('Styling',        'Pompadour, slick back en klassieke coupes',     @nu, @nu),
    ('Huidverzorging', 'Gezichtsbehandelingen en scrub voor heren',    @nu, @nu);

-- --- Behandelingen (5) ---
INSERT INTO `behandelingen` (`naam`, `duur_minuten`, `prijs`, `created_at`, `updated_at`) VALUES
    ('Knippen',              30, 27.50, @nu, @nu),
    ('Baard trimmen',        20, 17.50, @nu, @nu),
    ('Hot towel shave',      45, 35.00, @nu, @nu),
    ('Fade + lijn',          45, 32.50, @nu, @nu),
    ('Grey blending',        60, 45.00, @nu, @nu);

-- --- Categorieën (5) ---
INSERT INTO `categorieen` (`naam`, `beschrijving`, `created_at`, `updated_at`) VALUES
    ('Haarproducten',   'Pomades, wax en styling producten',           @nu, @nu),
    ('Baardverzorging', 'Oliën, balsems en baardkits',                 @nu, @nu),
    ('Verzorging',      'Aftershave, scrub en dagelijkse verzorging',  @nu, @nu),
    ('Styling tools',   'Kammen, borstels en tondeuses',               @nu, @nu),
    ('Giftsets',        'Cadeausets voor herenverzorging',             @nu, @nu);

-- --- Leveranciers (5 + gegevens) ---
INSERT INTO `leverancier_gegevens` (`bedrijfsnaam`, `email`, `telefoon`, `kvk_nummer`, `created_at`, `updated_at`) VALUES
    ('Barber Supply NL',              'info@barbersupply.nl',       '020-1234567', '12345678', @nu, @nu),
    ('Dutch Grooming Co',             'orders@dutchgrooming.nl',    '030-7654321', '23456789', @nu, @nu),
    ('Amsterdam Barbershop Wholesale','sales@amsterdambarber.nl',   '020-9876543', '34567890', @nu, @nu),
    ('Northern Scissors BV',          'inkoop@northscissors.nl',    '050-2468135', '45678901', @nu, @nu),
    ('ProHair Nederland',             'support@prohair.nl',         '088-1357924', '56789012', @nu, @nu);

INSERT INTO `leveranciers` (`leverancier_gegevens_id`, `created_at`, `updated_at`) VALUES
    (1, @nu, @nu), (2, @nu, @nu), (3, @nu, @nu), (4, @nu, @nu), (5, @nu, @nu);

-- --- Producten (5) ---
INSERT INTO `producten` (`categorie_id`, `leverancier_id`, `naam`, `ean_code`, `beschrijving`, `prijs`, `created_at`, `updated_at`) VALUES
    (1, 1, 'Reuzel Green Pomade',       '8711000123456', 'Medium hold, high shine pomade',              14.95, @nu, @nu),
    (2, 2, 'Mr. Bear Family Beard Oil','8711000234567', 'Verzorgende baardolie met sandelhout',        19.95, @nu, @nu),
    (3, 3, 'Proraso Aftershave Balm',   '8711000345678', 'Kalmerende balm na het scheren',              12.50, @nu, @nu),
    (1, 4, 'Uppercut Clay Wax',         '8711000456789', 'Sterke hold matte finish wax',                16.50, @nu, @nu),
    (3, 5, 'American Crew Shampoo',     '8711000567890', 'Dagelijkse reiniging voor alle haartypes',    18.00, @nu, @nu);

-- --- Voorraad (5) ---
INSERT INTO `voorraad` (`product_id`, `categorie_id`, `aantal`, `minimum`, `created_at`, `updated_at`) VALUES
    (1, 1, 42, 10, @nu, @nu),
    (2, 2, 28, 8,  @nu, @nu),
    (3, 3, 35, 10, @nu, @nu),
    (4, 1, 19, 6,  @nu, @nu),
    (5, 3, 51, 12, @nu, @nu);

-- --- Loginaccounts (8: 1 eigenaar, 2 medewerkers, 5 klanten) ---
INSERT INTO `users` (
    `name`, `voornaam`, `achternaam`, `email`,
    `email_verified_at`, `password`, `role`, `status`, `created_at`, `updated_at`
) VALUES
    ('admin',              'Beheerder', 'Tiko',      'admin@tiko.nl',           @nu, @pwd, 'admin',      'Actief', @nu, @nu),
    ('sophie.de.vries',    'Sophie',    'de Vries',  'sophie.devries@tiko.nl',  @nu, @pwd, 'medewerker', 'Actief', @nu, @nu),
    ('marco.jansen',       'Marco',     'Jansen',    'marco.jansen@tiko.nl',    @nu, @pwd, 'medewerker', 'Actief', @nu, @nu),
    ('jan.jansen',         'Jan',       'Jansen',    'jan.jansen@gmail.com',    @nu, @pwd, 'klant',      'Actief', @nu, @nu),
    ('sarah.bakker',       'Sarah',     'Bakker',    'sarah.bakker@gmail.com',  @nu, @pwd, 'klant',      'Actief', @nu, @nu),
    ('youssef.el.amrani',  'Youssef',   'El Amrani', 'youssef.elamrani@gmail.com', @nu, @pwd, 'klant', 'Actief', @nu, @nu),
    ('lisa.van.den.berg',  'Lisa',      'van den Berg','lisa.vandenberg@gmail.com', @nu, @pwd, 'klant',  'Actief', @nu, @nu),
    ('tom.de.boer',        'Tom',       'de Boer',   'tom.deboer@gmail.com',    @nu, @pwd, 'klant',      'Actief', @nu, @nu);

-- --- Contactgegevens (10: 5 medewerkers + 5 klanten) ---
INSERT INTO `contact_gegevens` (`email`, `telefoon`, `opmerking`, `created_at`, `updated_at`) VALUES
    ('sophie.devries@tiko.nl',       '06-12345678', 'Vaste kapper op dinsdag en donderdag', @nu, @nu),
    ('marco.jansen@tiko.nl',         '06-23456789', 'Specialist baard en klassieke shave',  @nu, @nu),
    ('daan.bakker@tiko.nl',          '06-34567890', 'Kleur-specialist',                     @nu, @nu),
    ('fatima.hassan@tiko.nl',        '06-45678901', 'Styling en trouwkapsels heren',        @nu, @nu),
    ('rick.vermeulen@tiko.nl',       '06-56789012', 'Huidverzorging en gezichtsbehandeling',@nu, @nu),
    ('jan.jansen@gmail.com',           '06-67890123', 'Vaste klant sinds 2022',               @nu, @nu),
    ('sarah.bakker@gmail.com',         '06-78901234', 'Prefereert afspraak in de ochtend',    @nu, @nu),
    ('youssef.elamrani@gmail.com',     '06-89012345', NULL,                                   @nu, @nu),
    ('lisa.vandenberg@gmail.com',      '06-90123456', 'Allergisch voor bepaalde producten',   @nu, @nu),
    ('tom.deboer@gmail.com',           '06-01234567', 'Komt elke 4 weken voor fade',          @nu, @nu);

-- --- Adressen Amsterdam (5) ---
INSERT INTO `adressen` (`straat`, `huisnummer`, `postcode`, `plaats`, `land`, `created_at`, `updated_at`) VALUES
    ('Overtoom',           '123', '1054 HE', 'Amsterdam', 'Nederland', @nu, @nu),
    ('Ferdinand Bolstraat','45',  '1072 LL', 'Amsterdam', 'Nederland', @nu, @nu),
    ('Javastraat',         '88',  '1094 HN', 'Amsterdam', 'Nederland', @nu, @nu),
    ('Haarlemmerdijk',     '67',  '1013 KE', 'Amsterdam', 'Nederland', @nu, @nu),
    ('Van Woustraat',      '12',  '1073 LL', 'Amsterdam', 'Nederland', @nu, @nu);

-- --- Gebruikers (10: medewerkers 1-5, klanten 6-10 met adres) ---
INSERT INTO `gebruikers` (
    `contact_gegevens_id`, `adres_id`, `voornaam`, `tussenvoegsel`, `achternaam`,
    `volledig_naam`, `geboortedatum`, `created_at`, `updated_at`
) VALUES
    (1,  NULL, 'Sophie', NULL, 'de Vries',    'Sophie de Vries',    '1992-03-14', @nu, @nu),
    (2,  NULL, 'Marco',  NULL, 'Jansen',      'Marco Jansen',       '1988-07-22', @nu, @nu),
    (3,  NULL, 'Daan',   NULL, 'Bakker',      'Daan Bakker',        '1995-11-08', @nu, @nu),
    (4,  NULL, 'Fatima', NULL, 'Hassan',      'Fatima Hassan',      '1990-01-30', @nu, @nu),
    (5,  NULL, 'Rick',   NULL, 'Vermeulen',   'Rick Vermeulen',     '1986-09-17', @nu, @nu),
    (6,  1,    'Jan',    NULL, 'Jansen',      'Jan Jansen',         '1998-05-03', @nu, @nu),
    (7,  2,    'Sarah',  NULL, 'Bakker',      'Sarah Bakker',       '1994-12-19', @nu, @nu),
    (8,  3,    'Youssef',NULL, 'El Amrani',   'Youssef El Amrani',  '1991-08-25', @nu, @nu),
    (9,  4,    'Lisa',   'van den', 'Berg',      'Lisa van den Berg',  '1996-02-11', @nu, @nu),
    (10, 5,    'Tom',    'de', 'Boer',        'Tom de Boer',        '1999-06-28', @nu, @nu);

-- --- Medewerkers (5) ---
INSERT INTO `medewerkers` (`specialisatie_id`, `gebruiker_id`, `is_actief`, `created_at`, `updated_at`) VALUES
    (1, 1, 1, @nu, @nu),
    (2, 2, 1, @nu, @nu),
    (3, 3, 1, @nu, @nu),
    (4, 4, 1, @nu, @nu),
    (5, 5, 0, @nu, @nu);

-- --- Medewerker beschikbaarheid (weekrooster; ma–vr 09:00–18:00, za 08:00–17:00, zo gesloten) ---
INSERT INTO `medewerker_beschikbaarheid` (`medewerker_id`, `dag_van_week`, `start_tijd`, `eind_tijd`, `is_beschikbaar`, `created_at`, `updated_at`) VALUES
    (1, 1, '09:00:00', '18:00:00', 1, @nu, @nu), (1, 2, '09:00:00', '18:00:00', 1, @nu, @nu), (1, 3, '09:00:00', '18:00:00', 1, @nu, @nu),
    (1, 4, '09:00:00', '18:00:00', 1, @nu, @nu), (1, 5, '09:00:00', '18:00:00', 1, @nu, @nu), (1, 6, '08:00:00', '17:00:00', 1, @nu, @nu),
    (1, 7, '09:00:00', '17:00:00', 0, @nu, @nu),
    (2, 1, '09:00:00', '18:00:00', 1, @nu, @nu), (2, 2, '09:00:00', '18:00:00', 1, @nu, @nu), (2, 3, '09:00:00', '18:00:00', 1, @nu, @nu),
    (2, 4, '09:00:00', '18:00:00', 1, @nu, @nu), (2, 5, '09:00:00', '18:00:00', 1, @nu, @nu), (2, 6, '08:00:00', '17:00:00', 1, @nu, @nu),
    (2, 7, '09:00:00', '17:00:00', 0, @nu, @nu),
    (3, 1, '09:00:00', '18:00:00', 1, @nu, @nu), (3, 2, '09:00:00', '18:00:00', 1, @nu, @nu), (3, 3, '09:00:00', '18:00:00', 1, @nu, @nu),
    (3, 4, '09:00:00', '18:00:00', 1, @nu, @nu), (3, 5, '09:00:00', '18:00:00', 1, @nu, @nu), (3, 6, '08:00:00', '17:00:00', 1, @nu, @nu),
    (3, 7, '09:00:00', '17:00:00', 0, @nu, @nu),
    (4, 1, '09:00:00', '18:00:00', 1, @nu, @nu), (4, 2, '09:00:00', '18:00:00', 1, @nu, @nu), (4, 3, '09:00:00', '18:00:00', 1, @nu, @nu),
    (4, 4, '09:00:00', '18:00:00', 1, @nu, @nu), (4, 5, '09:00:00', '18:00:00', 1, @nu, @nu), (4, 6, '08:00:00', '17:00:00', 1, @nu, @nu),
    (4, 7, '09:00:00', '17:00:00', 0, @nu, @nu),
    (5, 1, '09:00:00', '18:00:00', 0, @nu, @nu), (5, 2, '09:00:00', '18:00:00', 0, @nu, @nu), (5, 3, '09:00:00', '18:00:00', 0, @nu, @nu),
    (5, 4, '09:00:00', '18:00:00', 0, @nu, @nu), (5, 5, '09:00:00', '18:00:00', 0, @nu, @nu), (5, 6, '08:00:00', '17:00:00', 0, @nu, @nu),
    (5, 7, '09:00:00', '17:00:00', 0, @nu, @nu);

-- --- Klanten (5) ---
INSERT INTO `klanten` (`user_id`, `gebruiker_id`, `created_at`, `updated_at`) VALUES
    (4, 6,  @nu, @nu),
    (5, 7,  @nu, @nu),
    (6, 8,  @nu, @nu),
    (7, 9,  @nu, @nu),
    (8, 10, @nu, @nu);

-- --- Allergieën per klant (5) ---
INSERT INTO `allergieen` (`klant_id`, `naam`, `beschrijving`, `created_at`, `updated_at`) VALUES
    (1, 'Parfum',           'Huidirritatie bij sterk geparfumeerde producten', @nu, @nu),
    (2, 'Notenolie',        'Reactie op amandel- en arganolie in baardproducten', @nu, @nu),
    (3, 'Alcohol in aftershave', 'Branding en roodheid na alcoholhoudende producten', @nu, @nu),
    (4, 'Lanoline',         'Allergie voor wolvet in sommige pomades', @nu, @nu),
    (5, 'Citroenextract',   'Lichte uitslag bij citrusgeur in shampoo', @nu, @nu);

-- --- Klant–allergie koppeling (5) ---
INSERT INTO `klant_allergieen` (`klant_id`, `allergie_id`, `created_at`, `updated_at`) VALUES
    (1, 1, @nu, @nu),
    (2, 2, @nu, @nu),
    (3, 3, @nu, @nu),
    (4, 4, @nu, @nu),
    (5, 5, @nu, @nu);

-- --- Behandeling–product koppeling (5) ---
INSERT INTO `behandeling_producten` (`behandeling_id`, `product_id`, `aantal`, `created_at`, `updated_at`) VALUES
    (1, 1, 1, @nu, @nu),
    (2, 2, 1, @nu, @nu),
    (3, 3, 1, @nu, @nu),
    (4, 4, 1, @nu, @nu),
    (5, 5, 1, @nu, @nu);

-- --- Bestellingen (5) ---
INSERT INTO `bestellingen` (`klant_id`, `bestel_datum`, `status`, `totaal_prijs`, `created_at`, `updated_at`) VALUES
    (1, '2026-06-10', 'Afgerond',  34.90, @nu, @nu),
    (2, '2026-06-15', 'Verzonden', 19.95, @nu, @nu),
    (3, '2026-06-20', 'Nieuw',     12.50, @nu, @nu),
    (4, '2026-06-25', 'Afgerond',  33.00, @nu, @nu),
    (5, '2026-06-28', 'In behandeling', 32.50, @nu, @nu);

-- --- Bestelregels (5) ---
INSERT INTO `bestelling_producten` (`bestelling_id`, `product_id`, `aantal`, `stuk_prijs`, `created_at`, `updated_at`) VALUES
    (1, 1, 1, 14.95, @nu, @nu),
    (2, 2, 1, 19.95, @nu, @nu),
    (3, 3, 1, 12.50, @nu, @nu),
    (4, 4, 2, 16.50, @nu, @nu),
    (5, 5, 1, 18.00, @nu, @nu);

-- --- Afspraken (5) ---
INSERT INTO `afspraken` (
    `klant_id`, `medewerker_id`, `behandeling_id`,
    `afspraak_datum`, `afspraak_tijd`, `opmerking`, `created_at`, `updated_at`
) VALUES
    (1, 1, 1, '2026-07-02', '09:30:00', 'Reguliere knipbeurt',                    @nu, @nu),
    (2, 2, 2, '2026-07-02', '11:00:00', 'Baard bijwerken voor bruiloft',          @nu, @nu),
    (3, 1, 4, '2026-07-03', '14:00:00', 'Low fade met lijn',                      @nu, @nu),
    (4, 3, 5, '2026-07-04', '10:15:00', 'Eerste grey blending sessie',            @nu, @nu),
    (5, 2, 3, '2026-07-05', '16:30:00', 'Hot towel shave — let op allergieën',    @nu, @nu);

-- --- Technische logs (5) ---
INSERT INTO `technische_logs` (`niveau`, `module`, `actie`, `bericht`, `gebruiker_id`, `ip_adres`, `created_at`) VALUES
    ('info',  'account',   'inloggen',        'Eigenaar ingelogd via dashboard',           1, '127.0.0.1', @nu),
    ('info',  'medewerker','overzicht_ophalen','Medewerkeroverzicht geladen (5 records)', 1, '127.0.0.1', @nu),
    ('info',  'klanten',   'overzicht_ophalen','Klantenoverzicht geladen via JOIN',       1, '127.0.0.1', @nu),
    ('warning','product',  'voorraad_laag',   'Uppercut Clay Wax onder minimum (19 stuks)', NULL, NULL, @nu),
    ('error', 'account',   'inloggen',        'Mislukte loginpoging voor onbekend account', NULL, '192.168.1.50', @nu);

-- --- Laravel: password reset tokens (5) ---
INSERT INTO `password_reset_tokens` (`email`, `token`, `created_at`) VALUES
    ('jan.jansen@gmail.com',       SHA2('reset-jan-2026', 256),   '2026-06-01 08:00:00'),
    ('sarah.bakker@gmail.com',     SHA2('reset-sarah-2026', 256), '2026-06-02 09:15:00'),
    ('youssef.elamrani@gmail.com', SHA2('reset-youssef-2026', 256), '2026-06-03 10:30:00'),
    ('lisa.vandenberg@gmail.com',  SHA2('reset-lisa-2026', 256),  '2026-06-04 11:45:00'),
    ('tom.deboer@gmail.com',       SHA2('reset-tom-2026', 256),   '2026-06-05 12:00:00');

-- --- Laravel: cache (5) ---
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
    ('tiko:dashboard:stats',     's:45:"{\"klanten\":5,\"medewerkers\":4,\"afspraken\":5}";', UNIX_TIMESTAMP() + 3600),
    ('tiko:specialisaties:all',  's:120:"Fade,Baard,Kleuren,Styling,Huidverzorging";',       UNIX_TIMESTAMP() + 7200),
    ('tiko:producten:index',       's:80:"Reuzel,Mr. Bear,Proraso,Uppercut,American Crew";',  UNIX_TIMESTAMP() + 3600),
    ('tiko:behandelingen:prijzen', 's:65:"Knippen:27.50,Baard:17.50,Shave:35.00";',          UNIX_TIMESTAMP() + 1800),
    ('tiko:config:barbershop',     's:30:"Tiko Barbershop Amsterdam";',                      UNIX_TIMESTAMP() + 86400);

-- --- Laravel: cache locks (5) ---
INSERT INTO `cache_locks` (`key`, `owner`, `expiration`) VALUES
    ('lock:import:producten',   'console:setup-toki', UNIX_TIMESTAMP() + 300),
    ('lock:sync:voorraad',      'job:VoorraadSync',   UNIX_TIMESTAMP() + 600),
    ('lock:report:omzet',       'job:OmzetRapport',   UNIX_TIMESTAMP() + 900),
    ('lock:mail:herinnering',   'job:AfspraakMail',   UNIX_TIMESTAMP() + 120),
    ('lock:backup:database',    'cron:mysql-backup',  UNIX_TIMESTAMP() + 1800);

-- --- Laravel: jobs wachtrij (5) ---
INSERT INTO `jobs` (`queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
    ('default', '{"displayName":"App\\\\Jobs\\\\StuurAfspraakHerinnering","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call"}', 0, NULL, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
    ('default', '{"displayName":"App\\\\Jobs\\\\SyncVoorraad","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call"}',             0, NULL, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
    ('mail',    '{"displayName":"App\\\\Jobs\\\\StuurWelkomstmail","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call"}',        0, NULL, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
    ('reports', '{"displayName":"App\\\\Jobs\\\\GenereerMaandrapport","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call"}',     0, NULL, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
    ('default', '{"displayName":"App\\\\Jobs\\\\VerwerkBestelling","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call"}',        1, NULL, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- --- Laravel: job batches (5) ---
INSERT INTO `job_batches` (
    `id`, `name`, `total_jobs`, `pending_jobs`, `failed_jobs`,
    `failed_job_ids`, `options`, `cancelled_at`, `created_at`, `finished_at`
) VALUES
    ('batch-afspraken-001', 'Afspraakherinneringen juli 2026', 5, 0, 0, '[]', NULL, NULL, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
    ('batch-voorraad-001',  'Voorraad synchronisatie',         5, 2, 0, '[]', NULL, NULL, UNIX_TIMESTAMP(), NULL),
    ('batch-mail-001',      'Welkomstmails nieuwe klanten',    5, 5, 0, '[]', NULL, NULL, UNIX_TIMESTAMP(), NULL),
    ('batch-report-001',    'Maandrapport juni 2026',          3, 0, 0, '[]', NULL, NULL, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
    ('batch-import-001',    'Productimport leverancier',       10, 0, 1, '[7]', NULL, NULL, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- --- Laravel: failed jobs (5) ---
INSERT INTO `failed_jobs` (`uuid`, `connection`, `queue`, `payload`, `exception`, `failed_at`) VALUES
    ('a1b2c3d4-e5f6-7890-abcd-ef1234567890', 'database', 'default', '{"job":"SyncVoorraad"}',     'PDOException: Connection timed out',           '2026-06-20 08:12:00'),
    ('b2c3d4e5-f6a7-8901-bcde-f12345678901', 'database', 'mail',    '{"job":"StuurWelkomstmail"}', 'Swift_TransportException: Could not connect',  '2026-06-21 09:30:00'),
    ('c3d4e5f6-a7b8-9012-cdef-123456789012', 'database', 'reports', '{"job":"GenereerRapport"}',   'RuntimeException: Onvoldoende geheugen',       '2026-06-22 14:00:00'),
    ('d4e5f6a7-b8c9-0123-def0-234567890123', 'database', 'default', '{"job":"VerwerkBestelling"}', 'InvalidArgumentException: Product niet gevonden','2026-06-23 11:45:00'),
    ('e5f6a7b8-c9d0-1234-ef01-345678901234', 'database', 'default', '{"job":"ImportProducten"}',   'PDOException: Duplicate entry EAN code',       '2026-06-24 16:20:00');

-- --- Laravel: sessies (5) ---
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
    ('sess001admin000000000000000000000000000000000000000000000000', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDo...', UNIX_TIMESTAMP()),
    ('sess002sophie000000000000000000000000000000000000000000000000', 2, '192.168.1.10', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDo...', UNIX_TIMESTAMP()),
    ('sess003marco0000000000000000000000000000000000000000000000000', 3, '192.168.1.11', 'Mozilla/5.0 (Macintosh; Intel Mac OS X)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDo...', UNIX_TIMESTAMP()),
    ('sess004jan000000000000000000000000000000000000000000000000000', 4, '10.0.0.55', 'Mozilla/5.0 (Android 14; Mobile)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDo...', UNIX_TIMESTAMP()),
    ('sess005sarah0000000000000000000000000000000000000000000000000', 5, '10.0.0.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDo...', UNIX_TIMESTAMP());

-- Migratie: oude rolnaam 'manager' → 'medewerker' (bestaande databases)
UPDATE `users` SET `role` = 'medewerker' WHERE `role` = 'manager';
