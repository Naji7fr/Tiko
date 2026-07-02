-- =============================================================================
-- KLANT — Stored Procedures (MySQL)
-- =============================================================================
-- Import na toki.sql of voeg toe aan hoofdscript.
-- Gebruikt INNER/LEFT JOINs binnen de procedures (ERD-koppelingen).
--
-- ERD: users ← klanten → gebruikers → contact_gegevens
--                    └→ adressen (optioneel via gebruikers.adres_id)
-- =============================================================================

DELIMITER //

DROP PROCEDURE IF EXISTS sp_klant_overzicht//
DROP PROCEDURE IF EXISTS sp_klant_toevoegen//
DROP PROCEDURE IF EXISTS sp_klant_wijzigen//
DROP PROCEDURE IF EXISTS sp_klant_verwijderen//

-- Overzicht met JOINs: klanten + gebruikers + contact_gegevens + adressen + users
CREATE PROCEDURE sp_klant_overzicht()
BEGIN
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
        u.status            AS account_status,
        k.created_at,
        k.updated_at
    FROM klanten k
    INNER JOIN gebruikers g
        ON k.gebruiker_id = g.id
    INNER JOIN contact_gegevens cg
        ON g.contact_gegevens_id = cg.id
    LEFT JOIN adressen a
        ON g.adres_id = a.id
    LEFT JOIN users u
        ON k.user_id = u.id
    ORDER BY g.volledig_naam ASC;
END//

-- Nieuwe klant toevoegen (transactie: contact → optioneel adres → gebruiker → klant)
CREATE PROCEDURE sp_klant_toevoegen(
    IN p_voornaam         VARCHAR(50),
    IN p_tussenvoegsel    VARCHAR(20),
    IN p_achternaam       VARCHAR(50),
    IN p_volledig_naam    VARCHAR(121),
    IN p_email            VARCHAR(254),
    IN p_telefoon         VARCHAR(25),
    IN p_user_id          BIGINT UNSIGNED,
    IN p_straat           VARCHAR(100),
    IN p_huisnummer       VARCHAR(10),
    IN p_postcode         VARCHAR(10),
    IN p_plaats           VARCHAR(50),
    IN p_land             VARCHAR(50),
    OUT p_klant_id        BIGINT UNSIGNED
)
BEGIN
    DECLARE v_contact_id    BIGINT UNSIGNED;
    DECLARE v_gebruiker_id  BIGINT UNSIGNED;
    DECLARE v_adres_id      BIGINT UNSIGNED DEFAULT NULL;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    INSERT INTO contact_gegevens (email, telefoon, created_at, updated_at)
    VALUES (p_email, p_telefoon, NOW(), NOW());

    SET v_contact_id = LAST_INSERT_ID();

    IF p_straat IS NOT NULL AND TRIM(p_straat) <> '' THEN
        INSERT INTO adressen (
            straat, huisnummer, postcode, plaats, land, created_at, updated_at
        ) VALUES (
            p_straat,
            IFNULL(p_huisnummer, ''),
            IFNULL(p_postcode, ''),
            IFNULL(p_plaats, ''),
            IFNULL(NULLIF(TRIM(p_land), ''), 'Nederland'),
            NOW(),
            NOW()
        );

        SET v_adres_id = LAST_INSERT_ID();
    END IF;

    INSERT INTO gebruikers (
        contact_gegevens_id, adres_id, voornaam, tussenvoegsel, achternaam,
        volledig_naam, created_at, updated_at
    ) VALUES (
        v_contact_id, v_adres_id, p_voornaam, p_tussenvoegsel, p_achternaam,
        p_volledig_naam, NOW(), NOW()
    );

    SET v_gebruiker_id = LAST_INSERT_ID();

    INSERT INTO klanten (user_id, gebruiker_id, created_at, updated_at)
    VALUES (p_user_id, v_gebruiker_id, NOW(), NOW());

    SET p_klant_id = LAST_INSERT_ID();

    COMMIT;
END//

-- Bestaande klant wijzigen (contact, gebruiker en optioneel adres)
CREATE PROCEDURE sp_klant_wijzigen(
    IN p_klant_id          BIGINT UNSIGNED,
    IN p_voornaam          VARCHAR(50),
    IN p_tussenvoegsel     VARCHAR(20),
    IN p_achternaam        VARCHAR(50),
    IN p_volledig_naam     VARCHAR(121),
    IN p_email             VARCHAR(254),
    IN p_telefoon          VARCHAR(25),
    IN p_straat            VARCHAR(100),
    IN p_huisnummer        VARCHAR(10),
    IN p_postcode          VARCHAR(10),
    IN p_plaats            VARCHAR(50),
    IN p_land              VARCHAR(50)
)
BEGIN
    DECLARE v_gebruiker_id BIGINT UNSIGNED;
    DECLARE v_contact_id   BIGINT UNSIGNED;
    DECLARE v_adres_id     BIGINT UNSIGNED DEFAULT NULL;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    SELECT gebruiker_id INTO v_gebruiker_id
    FROM klanten WHERE id = p_klant_id;

    SELECT contact_gegevens_id, adres_id
    INTO v_contact_id, v_adres_id
    FROM gebruikers WHERE id = v_gebruiker_id;

    UPDATE contact_gegevens
    SET email = p_email, telefoon = p_telefoon, updated_at = NOW()
    WHERE id = v_contact_id;

    IF p_straat IS NOT NULL AND TRIM(p_straat) <> '' THEN
        IF v_adres_id IS NULL THEN
            INSERT INTO adressen (
                straat, huisnummer, postcode, plaats, land, created_at, updated_at
            ) VALUES (
                p_straat,
                IFNULL(p_huisnummer, ''),
                IFNULL(p_postcode, ''),
                IFNULL(p_plaats, ''),
                IFNULL(NULLIF(TRIM(p_land), ''), 'Nederland'),
                NOW(),
                NOW()
            );

            SET v_adres_id = LAST_INSERT_ID();

            UPDATE gebruikers
            SET adres_id = v_adres_id, updated_at = NOW()
            WHERE id = v_gebruiker_id;
        ELSE
            UPDATE adressen
            SET straat = p_straat,
                huisnummer = IFNULL(p_huisnummer, ''),
                postcode = IFNULL(p_postcode, ''),
                plaats = IFNULL(p_plaats, ''),
                land = IFNULL(NULLIF(TRIM(p_land), ''), 'Nederland'),
                updated_at = NOW()
            WHERE id = v_adres_id;
        END IF;
    END IF;

    UPDATE gebruikers
    SET voornaam = p_voornaam,
        tussenvoegsel = p_tussenvoegsel,
        achternaam = p_achternaam,
        volledig_naam = p_volledig_naam,
        updated_at = NOW()
    WHERE id = v_gebruiker_id;

    COMMIT;
END//

-- Klant verwijderen (blokkeert bij komende afspraken)
CREATE PROCEDURE sp_klant_verwijderen(
    IN p_klant_id BIGINT UNSIGNED
)
BEGIN
    DECLARE v_gebruiker_id      BIGINT UNSIGNED;
    DECLARE v_contact_id          BIGINT UNSIGNED;
    DECLARE v_adres_id            BIGINT UNSIGNED DEFAULT NULL;
    DECLARE v_komende_afspraken   INT DEFAULT 0;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    SELECT COUNT(*) INTO v_komende_afspraken
    FROM afspraken
    WHERE klant_id = p_klant_id
      AND afspraak_datum >= CURDATE();

    IF v_komende_afspraken > 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Klanten met komende afspraken kunnen niet worden verwijderd';
    END IF;

    SELECT gebruiker_id INTO v_gebruiker_id
    FROM klanten WHERE id = p_klant_id;

    SELECT contact_gegevens_id, adres_id
    INTO v_contact_id, v_adres_id
    FROM gebruikers WHERE id = v_gebruiker_id;

    START TRANSACTION;

    DELETE FROM klanten WHERE id = p_klant_id;
    DELETE FROM gebruikers WHERE id = v_gebruiker_id;

    IF v_adres_id IS NOT NULL THEN
        DELETE FROM adressen WHERE id = v_adres_id;
    END IF;

    DELETE FROM contact_gegevens WHERE id = v_contact_id;

    COMMIT;
END//

DELIMITER ;
