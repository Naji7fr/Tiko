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

1. Importeer `database/sql/toki.sql`
2. Importeer `database/sql/medewerker_stored_procedures.sql`

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

Na inloggen als eigenaar (`admin`) zie je het dashboard met overzicht van medewerkers, klanten, afspraken, producten, behandelingen, bestellingen en rapportages.

## Tests

```bash
php artisan test
```

Tests gebruiken SQLite + JOIN-fallback (geen stored procedures).
