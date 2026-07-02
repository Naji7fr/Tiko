-- =============================================================================
-- MEDEWERKER — Stored Procedures (MySQL)
-- =============================================================================
-- Import na toki.sql of voeg toe aan hoofdscript.
-- Gebruikt INNER JOINs binnen de procedures (ERD-koppelingen).
-- =============================================================================

DELIMITER //

DROP PROCEDURE IF EXISTS sp_medewerker_overzicht//
DROP PROCEDURE IF EXISTS sp_medewerker_toevoegen//
DROP PROCEDURE IF EXISTS sp_medewerker_wijzigen//
DROP PROCEDURE IF EXISTS sp_medewerker_verwijderen//

-- Overzicht met JOINs: medewerkers + gebruikers + contact_gegevens + specialisaties
CREATE PROCEDURE sp_medewerker_overzicht()
BEGIN
    SELECT
        m.id            AS medewerker_id,
        m.is_actief,
        g.volledig_naam,
        cg.email,
        cg.telefoon,
        s.naam          AS specialisatie_naam
    FROM medewerkers m
    INNER JOIN gebruikers g ON m.gebruiker_id = g.id
    INNER JOIN contact_gegevens cg ON g.contact_gegevens_id = cg.id
    INNER JOIN specialisaties s ON m.specialisatie_id = s.id
    ORDER BY g.volledig_naam ASC;
END//

-- Nieuwe medewerker toevoegen (transactie in procedure)
CREATE PROCEDURE sp_medewerker_toevoegen(
    IN p_voornaam         VARCHAR(50),
    IN p_tussenvoegsel    VARCHAR(20),
    IN p_achternaam       VARCHAR(50),
    IN p_volledig_naam    VARCHAR(121),
    IN p_email            VARCHAR(254),
    IN p_telefoon         VARCHAR(25),
    IN p_specialisatie_id BIGINT UNSIGNED,
    IN p_is_actief        TINYINT(1),
    OUT p_medewerker_id   BIGINT UNSIGNED
)
BEGIN
    DECLARE v_contact_id   BIGINT UNSIGNED;
    DECLARE v_gebruiker_id BIGINT UNSIGNED;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    INSERT INTO contact_gegevens (email, telefoon, created_at, updated_at)
    VALUES (p_email, p_telefoon, NOW(), NOW());

    SET v_contact_id = LAST_INSERT_ID();

    INSERT INTO gebruikers (
        contact_gegevens_id, voornaam, tussenvoegsel, achternaam,
        volledig_naam, created_at, updated_at
    ) VALUES (
        v_contact_id, p_voornaam, p_tussenvoegsel, p_achternaam,
        p_volledig_naam, NOW(), NOW()
    );

    SET v_gebruiker_id = LAST_INSERT_ID();

    INSERT INTO medewerkers (specialisatie_id, gebruiker_id, is_actief, created_at, updated_at)
    VALUES (p_specialisatie_id, v_gebruiker_id, p_is_actief, NOW(), NOW());

    SET p_medewerker_id = LAST_INSERT_ID();

    COMMIT;
END//

-- Bestaande medewerker wijzigen
CREATE PROCEDURE sp_medewerker_wijzigen(
    IN p_medewerker_id    BIGINT UNSIGNED,
    IN p_voornaam          VARCHAR(50),
    IN p_tussenvoegsel     VARCHAR(20),
    IN p_achternaam        VARCHAR(50),
    IN p_volledig_naam     VARCHAR(121),
    IN p_email             VARCHAR(254),
    IN p_telefoon          VARCHAR(25),
    IN p_specialisatie_id  BIGINT UNSIGNED,
    IN p_is_actief         TINYINT(1)
)
BEGIN
    DECLARE v_gebruiker_id BIGINT UNSIGNED;
    DECLARE v_contact_id   BIGINT UNSIGNED;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    SELECT gebruiker_id INTO v_gebruiker_id
    FROM medewerkers WHERE id = p_medewerker_id;

    SELECT contact_gegevens_id INTO v_contact_id
    FROM gebruikers WHERE id = v_gebruiker_id;

    UPDATE contact_gegevens
    SET email = p_email, telefoon = p_telefoon, updated_at = NOW()
    WHERE id = v_contact_id;

    UPDATE gebruikers
    SET voornaam = p_voornaam,
        tussenvoegsel = p_tussenvoegsel,
        achternaam = p_achternaam,
        volledig_naam = p_volledig_naam,
        updated_at = NOW()
    WHERE id = v_gebruiker_id;

    UPDATE medewerkers
    SET specialisatie_id = p_specialisatie_id,
        is_actief = p_is_actief,
        updated_at = NOW()
    WHERE id = p_medewerker_id;

    COMMIT;
END//

-- Inactieve medewerker verwijderen (cascade via FK)
CREATE PROCEDURE sp_medewerker_verwijderen(
    IN p_medewerker_id BIGINT UNSIGNED
)
BEGIN
    DECLARE v_is_actief TINYINT(1);

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    SELECT is_actief INTO v_is_actief
    FROM medewerkers WHERE id = p_medewerker_id;

    IF v_is_actief = 1 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Actieve medewerkers kunnen niet worden verwijderd';
    END IF;

    START TRANSACTION;

    DELETE FROM medewerkers WHERE id = p_medewerker_id;

    COMMIT;
END//

DELIMITER ;
