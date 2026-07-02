<?php

/**
 * Web routes — Tiko Barbershop
 *
 * Publiek:     home, login, register
 * Klant:       /klant/*        (role: klant)
 * Eigenaar:    /eigenaar/*     (role: admin)
 * Medewerker:  /medewerkers/*  (role: admin, medewerker)
 */

use App\Http\Controllers\Eigenaar\EigenaarDashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Eigenaar\AccountController as EigenaarAccountController;
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
    Route::get('/account/details', [KlantAccountController::class, 'details'])->name('account.details');
    Route::get('/account/details/edit', [KlantAccountController::class, 'edit'])->name('account.details.edit');
    Route::put('/account/details', [KlantAccountController::class, 'update'])->name('account.details.update');
    Route::delete('/account/details', [KlantAccountController::class, 'destroy'])->name('account.details.destroy');

    Route::get('/account/settings', [KlantAccountController::class, 'settings'])->name('account.settings');
});
Route::middleware(['auth', 'role:klant'])->prefix('klant')->name('klant.')->group(function () {
    Route::get('/dashboard', [KlantController::class, 'dashboard'])->name('dashboard');
});

// --- Eigenaar-dashboard (alleen admin) ---
Route::middleware(['auth', 'role:admin'])->prefix('eigenaar')->name('eigenaar.')->group(function () {
    Route::get('/dashboard', [EigenaarDashboardController::class, 'index'])->name('dashboard');
    Route::get('/rapportages', [EigenaarDashboardController::class, 'rapportages'])->name('rapportages');
    Route::resource('accounts', EigenaarAccountController::class)->except(['show']);
    // Placeholder-modules (klanten, afspraken, …) — nog in ontwikkeling
    Route::get('/{module}', [EigenaarDashboardController::class, 'modulePlaceholder'])
        ->where('module', 'klanten|afspraken|behandelingen|producten|bestellingen')
        ->name('module');
});

// --- Medewerkerbeheer (admin + medewerker) ---
Route::middleware(['auth', 'role:admin,medewerker'])->group(function () {
    Route::get('/product-overzicht', [ProductController::class, 'index'])->name('producten.index');
    Route::get('/product-toevoegen', [ProductController::class, 'create'])->name('producten.create');
    Route::post('/product-toevoegen', [ProductController::class, 'store'])->name('producten.store');
    Route::get('/product-wijzigen/{id}', [ProductController::class, 'edit'])->name('producten.edit');
    Route::put('/product-wijzigen/{id}', [ProductController::class, 'update'])->name('producten.update');
    Route::resource('medewerkers', MedewerkerController::class);
});
