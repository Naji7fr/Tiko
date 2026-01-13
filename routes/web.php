<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MedewerkerController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Patient routes (authenticated)
Route::middleware('auth')->group(function () {
    Route::get('/patient/dashboard', [PatientController::class, 'dashboard'])->name('patient.dashboard');
    Route::get('/patient/appointments', [PatientController::class, 'appointments'])->name('patient.appointments');
    Route::post('/patient/appointments/create', [PatientController::class, 'storeAppointment'])->name('patient.appointments.create');
    Route::get('/patient/invoices', [PatientController::class, 'invoices'])->name('patient.invoices');
    Route::get('/patient/messages', [PatientController::class, 'messages'])->name('patient.messages');
    Route::post('/patient/messages/send', [PatientController::class, 'sendMessage'])->name('patient.messages.send');
});

// Admin/Praktijkmanager routes (role-based)
Route::middleware(['auth', 'role:admin,praktijkmanager'])->group(function () {
    Route::resource('medewerkers', MedewerkerController::class);
    Route::resource('accounts', AccountController::class);
    Route::resource('berichten', MessageController::class);
    Route::resource('facturen', InvoiceController::class);
    Route::resource('afspraken', AppointmentController::class);
    Route::get('statistieken', [StatisticsController::class, 'index'])->name('statistieken.index');
});
