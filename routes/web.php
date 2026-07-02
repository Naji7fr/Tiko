<?php

use App\Http\Controllers\Eigenaar\EigenaarDashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KlantController;
use App\Http\Controllers\Medewerker\MedewerkerController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout.get');

Route::middleware(['auth', 'role:klant'])->prefix('klant')->name('klant.')->group(function () {
    Route::get('/dashboard', [KlantController::class, 'dashboard'])->name('dashboard');
});

Route::middleware(['auth', 'role:admin'])->prefix('eigenaar')->name('eigenaar.')->group(function () {
    Route::get('/dashboard', [EigenaarDashboardController::class, 'index'])->name('dashboard');
    Route::get('/rapportages', [EigenaarDashboardController::class, 'rapportages'])->name('rapportages');
    Route::get('/{module}', [EigenaarDashboardController::class, 'modulePlaceholder'])
        ->where('module', 'klanten|afspraken|behandelingen|producten|bestellingen')
        ->name('module');
});

Route::middleware(['auth', 'role:admin,manager'])->group(function () {
    Route::resource('medewerkers', MedewerkerController::class);
});
