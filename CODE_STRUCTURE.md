# Code Structure Documentation

## Project Organization

This Laravel application follows the standard MVC (Model-View-Controller) architecture with all code properly organized into dedicated folders.

## 📁 Directory Structure

```
laravel/
├── app/
│   ├── Http/
│   │   └── Controllers/              ← ALL CONTROLLERS TOGETHER
│   │       ├── AccountController.php
│   │       ├── AppointmentController.php
│   │       ├── HomeController.php
│   │       ├── InvoiceController.php
│   │       ├── MedewerkerController.php
│   │       ├── MessageController.php
│   │       └── StatisticsController.php
│   │
│   └── Models/                       ← ALL MODELS TOGETHER
│       ├── Appointment.php
│       ├── Invoice.php
│       ├── Medewerker.php
│       ├── Message.php
│       └── User.php
│
├── resources/
│   └── views/                        ← ALL VIEWS TOGETHER
│       ├── layouts/
│       │   └── app.blade.php
│       ├── accounts/
│       │   ├── create.blade.php
│       │   └── index.blade.php
│       ├── afspraken/
│       │   ├── create.blade.php
│       │   └── index.blade.php
│       ├── berichten/
│       │   ├── create.blade.php
│       │   ├── index.blade.php
│       │   └── show.blade.php
│       ├── facturen/
│       │   ├── create.blade.php
│       │   └── index.blade.php
│       ├── medewerkers/
│       │   ├── create.blade.php
│       │   ├── edit.blade.php
│       │   ├── index.blade.php
│       │   └── show.blade.php
│       ├── statistieken/
│       │   └── index.blade.php
│       └── home.blade.php
│
├── public/
│   └── css/
│       └── style.css                 ← ALL CSS IN ONE FILE
│
└── routes/
    └── web.php                       ← ALL ROUTES DEFINED HERE
```

## 🎯 Controllers (All Together)

**Location:** `app/Http/Controllers/`

All controllers are organized in one folder:

- **AccountController** - Manages user accounts
- **AppointmentController** - Handles appointments (afspraken)
  - `index()` - List all appointments
  - `create()` - Show create form
  - `store()` - Save new appointment
- **HomeController** - Homepage
- **InvoiceController** - Manages invoices (facturen)
  - `index()` - List all invoices
  - `create()` - Show create form
  - `store()` - Save new invoice
- **MedewerkerController** - Manages staff (medewerkers)
- **MessageController** - Handles messages (berichten)
  - `index()` - List all messages
  - `create()` - Show create form
  - `store()` - Send new message
  - `show()` - View message details
- **StatisticsController** - Statistics dashboard

## 📊 Models (All Together)

**Location:** `app/Models/`

All models are organized in one folder:

- **Appointment** - Appointment model with patient/dentist relationships
- **Invoice** - Invoice model with patient relationship
- **Medewerker** - Staff member model
- **Message** - Message model with sender/receiver relationships
- **User** - User model (patients, dentists, admins)

## 🎨 Views (All Together)

**Location:** `resources/views/`

All views are organized by feature in subdirectories:

- **layouts/** - Main layout template
- **accounts/** - Account management views
- **afspraken/** - Appointment views (index, create)
- **berichten/** - Message views (index, create, show)
- **facturen/** - Invoice views (index, create)
- **medewerkers/** - Staff management views
- **statistieken/** - Statistics dashboard
- **home.blade.php** - Homepage

## 🎨 Styles (All Together)

**Location:** `public/css/style.css`

All CSS styles are centralized in one file:
- Base styles
- Navigation
- Buttons
- Cards
- Tables
- Forms
- Homepage specific styles
- Responsive design

## ✅ Features Implemented

### ✅ Afspraken (Appointments)
- ✅ List all appointments
- ✅ Create new appointment
- ✅ View appointment statistics

### ✅ Facturen (Invoices)
- ✅ List all invoices
- ✅ Create new invoice
- ✅ View invoice statistics

### ✅ Berichten (Messages)
- ✅ List all messages
- ✅ Create new message
- ✅ View message details
- ✅ Mark as read

## 🔗 Routes

All routes are defined in `routes/web.php`:

```php
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::resource('medewerkers', MedewerkerController::class);
Route::resource('accounts', AccountController::class);
Route::resource('berichten', MessageController::class);
Route::resource('facturen', InvoiceController::class);
Route::resource('afspraken', AppointmentController::class);
Route::get('statistieken', [StatisticsController::class, 'index'])->name('statistieken.index');
```

## 📝 Notes

- All controllers follow RESTful conventions
- All models use Eloquent ORM
- All views use Blade templating
- CSS is centralized for easy maintenance
- Code follows Laravel best practices
- Everything is properly organized and grouped together

