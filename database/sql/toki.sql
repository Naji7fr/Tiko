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
-- =============================================================================

CREATE DATABASE IF NOT EXISTS `toki`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `toki`;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS
    `afspraken`, `bestelling_producten`, `bestellingen`,
    `behandeling_producten`, `klant_allergieen`, `allergieen`,
    `klanten`, `medewerkers`, `voorraad`, `producten`,
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
-- 10. TECHNISCHE LOG + STORED PROCEDURES (medewerker-module)
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
-- 9. STARTDATA (medewerker + overig)
-- =============================================================================

-- Medewerker-module
INSERT INTO `specialisaties` (`naam`, `created_at`, `updated_at`) VALUES
    ('Fade',    NOW(), NOW()),
    ('Baard',   NOW(), NOW()),
    ('Kleuren', NOW(), NOW());

INSERT INTO `behandelingen` (`naam`, `duur_minuten`, `prijs`, `created_at`, `updated_at`) VALUES
    ('Knippen',         30, 25.00, NOW(), NOW()),
    ('Baard trimmen',   20, 15.00, NOW(), NOW()),
    ('Hot towel shave', 45, 35.00, NOW(), NOW());

INSERT INTO `categorieen` (`naam`, `created_at`, `updated_at`) VALUES
    ('Haarproducten', NOW(), NOW()),
    ('Baardverzorging', NOW(), NOW()),
    ('Verzorging', NOW(), NOW());

INSERT INTO `users` (
    `name`, `voornaam`, `achternaam`, `email`,
    `email_verified_at`, `password`, `role`, `status`,
    `created_at`, `updated_at`
) VALUES (
    'admin', 'Beheerder', 'Tiko', 'admin@tiko.nl',
    NOW(), '$2y$12$i.drMAcWW6u0rQsAGXkpA.FE0.uZfW9/CyTfHn8fI4Cj08rcNDZSq',
    'admin', 'Actief', NOW(), NOW()
);
