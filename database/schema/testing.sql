PRAGMA foreign_keys = OFF;

DROP TABLE IF EXISTS klanten;
DROP TABLE IF EXISTS medewerkers;
DROP TABLE IF EXISTS gebruikers;
DROP TABLE IF EXISTS specialisaties;
DROP TABLE IF EXISTS contact_gegevens;
DROP TABLE IF EXISTS users;

PRAGMA foreign_keys = ON;

CREATE TABLE users (
    id                INTEGER PRIMARY KEY AUTOINCREMENT,
    name              VARCHAR(255)    NOT NULL UNIQUE,
    voornaam          VARCHAR(255)    NULL,
    achternaam        VARCHAR(255)    NULL,
    email             VARCHAR(255)    NOT NULL UNIQUE,
    email_verified_at DATETIME        NULL,
    password          VARCHAR(255)    NOT NULL,
    role              VARCHAR(255)    NOT NULL DEFAULT 'klant',
    status            VARCHAR(255)    NOT NULL DEFAULT 'Actief',
    remember_token    VARCHAR(100)    NULL,
    created_at        DATETIME        NULL,
    updated_at        DATETIME        NULL
);

CREATE TABLE contact_gegevens (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    email      VARCHAR(254)    NOT NULL UNIQUE,
    telefoon   VARCHAR(25)     NULL,
    opmerking  VARCHAR(225)    NULL,
    created_at DATETIME        NULL,
    updated_at DATETIME        NULL
);

CREATE TABLE gebruikers (
    id                   INTEGER PRIMARY KEY AUTOINCREMENT,
    contact_gegevens_id  INTEGER         NOT NULL,
    voornaam             VARCHAR(50)     NOT NULL,
    tussenvoegsel        VARCHAR(20)     NULL,
    achternaam           VARCHAR(50)     NOT NULL,
    volledig_naam        VARCHAR(121)    NOT NULL,
    geboortedatum        DATE            NULL,
    created_at           DATETIME        NULL,
    updated_at           DATETIME        NULL,
    FOREIGN KEY (contact_gegevens_id) REFERENCES contact_gegevens (id) ON DELETE CASCADE
);

CREATE TABLE specialisaties (
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    naam          VARCHAR(50)     NOT NULL UNIQUE,
    beschrijving  VARCHAR(225)    NULL,
    created_at    DATETIME        NULL,
    updated_at    DATETIME        NULL
);

CREATE TABLE medewerkers (
    id                INTEGER PRIMARY KEY AUTOINCREMENT,
    specialisatie_id  INTEGER         NOT NULL,
    gebruiker_id      INTEGER         NOT NULL UNIQUE,
    is_actief         INTEGER         NOT NULL DEFAULT 1,
    created_at        DATETIME        NULL,
    updated_at        DATETIME        NULL,
    FOREIGN KEY (specialisatie_id) REFERENCES specialisaties (id),
    FOREIGN KEY (gebruiker_id) REFERENCES gebruikers (id) ON DELETE CASCADE
);

CREATE TABLE medewerker_beschikbaarheid (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    medewerker_id   INTEGER         NOT NULL,
    dag_van_week    INTEGER         NOT NULL,
    start_tijd      TIME            NOT NULL,
    eind_tijd       TIME            NOT NULL,
    is_beschikbaar  INTEGER         NOT NULL DEFAULT 1,
    created_at      DATETIME        NULL,
    updated_at      DATETIME        NULL,
    UNIQUE (medewerker_id, dag_van_week),
    FOREIGN KEY (medewerker_id) REFERENCES medewerkers (id) ON DELETE CASCADE
);

CREATE TABLE klanten (
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id       INTEGER         NULL UNIQUE,
    gebruiker_id  INTEGER         NOT NULL UNIQUE,
    created_at    DATETIME        NULL,
    updated_at    DATETIME        NULL,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    FOREIGN KEY (gebruiker_id) REFERENCES gebruikers (id) ON DELETE CASCADE
);

CREATE TABLE behandelingen (
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    naam          VARCHAR(50)     NOT NULL UNIQUE,
    duur_minuten  INTEGER         NOT NULL DEFAULT 30,
    prijs         DECIMAL(8, 2)   NOT NULL,
    created_at    DATETIME        NULL,
    updated_at    DATETIME        NULL
);

CREATE TABLE afspraken (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    klant_id        INTEGER         NOT NULL,
    medewerker_id   INTEGER         NOT NULL,
    behandeling_id  INTEGER         NOT NULL,
    afspraak_datum  DATE            NOT NULL,
    afspraak_tijd   TIME            NOT NULL,
    opmerking       VARCHAR(225)    NULL,
    created_at      DATETIME        NULL,
    updated_at      DATETIME        NULL,
    FOREIGN KEY (klant_id) REFERENCES klanten (id),
    FOREIGN KEY (medewerker_id) REFERENCES medewerkers (id),
    FOREIGN KEY (behandeling_id) REFERENCES behandelingen (id)
);
