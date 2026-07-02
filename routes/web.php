<?php

/**
 * Web routes — Tiko Barbershop
 *
 * Publiek:     home, login, register
 * Klant:       /klant/*        (role: klant)
 * Eigenaar:    /eigenaar/*     (role: admin)
 * Medewerker:  /medewerkers/*  (role: admin, medewerker)
 */

use App\Http\Controllers\Eigenaar\AccountController;
use App\Http\Controllers\Eigenaar\EigenaarDashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
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

// --- Klant-portaal ---
Route::middleware(['auth', 'role:klant'])->prefix('klant')->name('klant.')->group(function () {
    Route::get('/dashboard', [KlantController::class, 'dashboard'])->name('dashboard');
});

// --- Eigenaar-dashboard (alleen admin) ---
Route::middleware(['auth', 'role:admin'])->prefix('eigenaar')->name('eigenaar.')->group(function () {
    Route::get('/dashboard', [EigenaarDashboardController::class, 'index'])->name('dashboard');
    Route::get('/rapportages', [EigenaarDashboardController::class, 'rapportages'])->name('rapportages');
    Route::resource('accounts', AccountController::class)->except(['show']);
    // Placeholder-modules (klanten, afspraken, …) — nog in ontwikkeling
    Route::get('/{module}', [EigenaarDashboardController::class, 'modulePlaceholder'])
        ->where('module', 'klanten|afspraken|behandelingen|producten|bestellingen')
        ->name('module');
});

// --- Medewerkerbeheer (admin + medewerker) ---
Route::middleware(['auth', 'role:admin,medewerker'])->group(function () {
    Route::resource('medewerkers', MedewerkerController::class);
});
