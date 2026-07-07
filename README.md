# Tiko Medewerker

Laravel-applicatie voor medewerkerbeheer bij **Tiko Barbershop** (MVC-architectuur).

## MVC-mapstructuur (medewerker-module)

```
app/Models/Medewerker/          →  medewerker.model
  MedewerkerModel.php
  GebruikerModel.php
  ContactGegevensModel.php
  SpecialisatieModel.php
  TechnischeLogModel.php

app/Http/Controllers/Medewerker/  →  medewerker.controller
  MedewerkerController.php

app/Http/Requests/Medewerker/     →  server-side validatie
  StoreMedewerkerRequest.php
  UpdateMedewerkerRequest.php

app/Services/Medewerker/          →  businesslogica + stored procedures
  MedewerkerService.php

resources/views/medewerker/       →  medewerker.view
  index/view.blade.php            →  medewerker.index.view
  create/view.blade.php           →  medewerker.create.view
  edit/view.blade.php             →  medewerker.edit.view
  show/view.blade.php             →  medewerker.show.view
  partials/form.blade.php         →  medewerker.partials.form
  partials/alerts.blade.php       →  medewerker.partials.alerts

public/js/medewerker/             →  client-side validatie
  medewerker.validation.js

database/sql/
  toki.sql                        →  volledige database
  medewerker_stored_procedures.sql →  JOINs + CRUD procedures
```

## Technische eisen (geïmplementeerd)

| Eis | Implementatie |
|-----|---------------|
| Commentaar | PHPDoc in models, controller, service, views |
| Joins | `MedewerkerModel::haalOverzichtMetJoins()` + SP `sp_medewerker_overzicht` |
| Try-catch | `MedewerkerController` + `MedewerkerService` |
| PSR-12 | Consistente naamgeving, namespaces, structuur |
| Stored Procedures | `database/sql/medewerker_stored_procedures.sql` |
| Naamgeving | Nederlandse methodenamen (`haalMedewerkersOp`, `voegMedewerkerToe`) |
| MVC | View ← Controller ← Service ← Model |
| Security | Auth, role middleware, CSRF, FormRequest, authorize() |
| Validatie | Client (HTML5 + JS) + server (FormRequest) |
| Technische log | `technische_logs` tabel + `TechnischeLogModel` |
| Meldingen | Flash alerts via `medewerker.alerts.partial` |

## Database (MySQL)

### Normale database (met seeddata)

1. Importeer `database/sql/toki.sql`
2. Importeer `database/sql/medewerker_stored_procedures.sql` en `database/sql/klant_stored_procedures.sql`

```bash
php database/setup_toki.php
```

```env
DB_USE_EMPTY=false
DB_DATABASE=toki
```

**Admin (eigenaar):** `admin@tiko.nl` / `password`

### Lege database (alleen schema, geen seeddata)

Naast `toki` kun je een lege database `toki_empty` gebruiken — handig om zelf data toe te voegen zonder voorbeeldrecords.

```bash
php database/build_toki_empty_sql.php   # genereert database/sql/toki_empty.sql
php database/setup_toki_empty.php       # maakt toki_empty aan in MySQL
```

Schakel in `.env`:

```env
DB_USE_EMPTY=true
DB_EMPTY_DATABASE=toki_empty
DB_EMPTY_SQL_PATH=database/sql/toki_empty.sql
```

Zet `DB_USE_EMPTY=false` om terug te schakelen naar de normale database (`DB_DATABASE`).

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=toki
DB_USERNAME=root
DB_PASSWORD=
```

## Installatie

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan serve
```

**Admin (eigenaar):** `admin@tiko.nl` / `password` → [Eigenaar Dashboard](http://localhost:8000/eigenaar/dashboard)

*(Alleen beschikbaar wanneer `DB_USE_EMPTY=false` en seeddata is geïmporteerd.)*

Na inloggen als eigenaar (`admin`) zie je het dashboard met overzicht van medewerkers, klanten, afspraken, producten, behandelingen, bestellingen en rapportages.

## Tests

```bash
php artisan test
```

Tests gebruiken SQLite + JOIN-fallback (geen stored procedures).
