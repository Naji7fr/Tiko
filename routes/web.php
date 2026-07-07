<?php

/**
 * Web routes — Tiko Barbershop
 *
 * Publiek:     home, login, register
 * Klant:       /klant/*        (role: klant)
 * Eigenaar:    /eigenaar/*     (role: admin)
 * Medewerkerbeheer: /medewerkers/*  (role: admin — alleen eigenaar)
 * Producten:       /product-*       (role: admin, medewerker)
 * Afspraken:       /afspraken/*     (auth)
 */

use App\Http\Controllers\AfspraakController;
use App\Http\Controllers\Eigenaar\EigenaarDashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Eigenaar\AccountController as EigenaarAccountController;
use App\Http\Controllers\Eigenaar\KlantenController;
use App\Http\Controllers\Klant\AccountController as KlantAccountController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KlantController;
use App\Http\Controllers\Medewerker\MedewerkerController;
use App\Http\Controllers\Medewerker\ProductController;
use Illuminate\Support\Facades\Route;

// --- Publieke pagina's ---
Route::get('/', [HomeController::class, 'index'])->name('home');

// --- Authenticatie (login / registratie / logout) ---
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout.get');

Route::middleware('auth')->group(function () {
    Route::get('/klant/overzicht', [KlantAccountController::class, 'details'])->name('klant.overzicht');
    Route::get('/klant/overzicht/bewerken', [KlantAccountController::class, 'edit'])->name('klant.overzicht.edit');
    Route::put('/klant/overzicht', [KlantAccountController::class, 'update'])->name('klant.overzicht.update');
    Route::delete('/klant/overzicht', [KlantAccountController::class, 'destroy'])->name('klant.overzicht.destroy');

    Route::get('/klant/instellingen', [KlantAccountController::class, 'settings'])->name('klant.instellingen');

    Route::get('/afspraken/beschikbare-medewerkers', [AfspraakController::class, 'beschikbareMedewerkers'])->name('afspraken.beschikbare-medewerkers');
    Route::get('/afspraken/beschikbare-datums', [AfspraakController::class, 'beschikbareDatums'])->name('afspraken.beschikbare-datums');
    Route::get('/afspraken/beschikbare-tijden', [AfspraakController::class, 'beschikbareTijden'])->name('afspraken.beschikbare-tijden');
    Route::resource('afspraken', AfspraakController::class)->parameters([
        'afspraken' => 'afspraak',
    ]);
});

Route::middleware(['auth', 'role:klant'])->prefix('klant')->name('klant.')->group(function () {
    Route::get('/dashboard', [KlantController::class, 'dashboard'])->name('dashboard');
});

// --- Eigenaar-dashboard (alleen admin) ---
Route::middleware(['auth', 'role:admin'])->prefix('eigenaar')->name('eigenaar.')->group(function () {
    Route::get('/dashboard', [EigenaarDashboardController::class, 'index'])->name('dashboard');
    Route::get('/rapportages', [EigenaarDashboardController::class, 'rapportages'])->name('rapportages');
    Route::resource('accounts', EigenaarAccountController::class)->except(['show']);
    Route::get('/klanten', [KlantenController::class, 'index'])->name('klanten.index');
    Route::get('/klanten/toevoegen', [KlantenController::class, 'create'])->name('klanten.create');
    Route::post('/klanten', [KlantenController::class, 'store'])->name('klanten.store');
    Route::get('/klanten/{klant}/bewerken', [KlantenController::class, 'edit'])->name('klanten.edit');
    Route::put('/klanten/{klant}', [KlantenController::class, 'update'])->name('klanten.update');
    Route::delete('/klanten/{klant}', [KlantenController::class, 'destroy'])->name('klanten.destroy');
    // Placeholder-modules (behandelingen, …) — nog in ontwikkeling
    Route::get('/{module}', [EigenaarDashboardController::class, 'modulePlaceholder'])
        ->where('module', 'behandelingen|bestellingen')
        ->name('module');
});

// --- Medewerkerbeheer (alleen eigenaar / admin) ---
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('medewerkers', MedewerkerController::class);
});

// --- Productbeheer (eigenaar + medewerker-account) ---
Route::middleware(['auth', 'role:admin,medewerker'])->group(function () {
    Route::get('/product-overzicht', [ProductController::class, 'index'])->name('producten.index');
    Route::get('/product-toevoegen', [ProductController::class, 'create'])->name('producten.create');
    Route::post('/product-toevoegen', [ProductController::class, 'store'])->name('producten.store');
    Route::get('/product-wijzigen/{id}', [ProductController::class, 'edit'])->name('producten.edit');
    Route::put('/product-wijzigen/{id}', [ProductController::class, 'update'])->name('producten.update');
    Route::delete('/producten/{id}', [ProductController::class, 'destroy'])->name('producten.destroy');
});
