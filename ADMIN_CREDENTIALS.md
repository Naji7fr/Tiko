# BNG – Admin login

**Manager operations account (na het uitvoeren van de database seeder):**

| Veld      | Waarde          |
|----------|------------------|
| **E-mail**   | `admin@bng.nl`   |
| **Wachtwoord** | `password`     |
| **Rol** | Manager operations |

Na inloggen kom je op je profielpagina en heb je toegang tot alle beheerfuncties (Overzicht medewerker, Overzicht klant, Overzicht accounts, Berichten, Facturen, Statistieken, Afspraken).

**Seeder uitvoeren (als de admin nog niet bestaat):**
```bash
php artisan db:seed
```

Of alleen de DatabaseSeeder:
```bash
php artisan db:seed --class=DatabaseSeeder
```
