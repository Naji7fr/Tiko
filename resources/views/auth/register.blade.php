@extends('layouts.app')

@section('title', 'Klantaccount aanmaken')

@section('content')
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-user-plus me-3"></i>Registreren</h1>
        <p class="mb-0 mt-2 opacity-90">Maak een klantaccount aan bij Tiko Barbershop</p>
    </div>
</div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card">
                <div class="card-body p-4">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Dit is een <strong>klantaccount</strong>. Je kunt je profiel bekijken en later afspraken maken.
                        Beheerders hebben een apart account.
                    </div>

                    <form action="{{ route('register') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="voornaam" class="form-label">Voornaam <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('voornaam') is-invalid @enderror"
                                       id="voornaam" name="voornaam" value="{{ old('voornaam') }}" required autofocus>
                                @error('voornaam')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="achternaam" class="form-label">Achternaam <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('achternaam') is-invalid @enderror"
                                       id="achternaam" name="achternaam" value="{{ old('achternaam') }}" required>
                                @error('achternaam')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">E-mailadres <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email') }}" placeholder="jouw@email.nl" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="telefoon" class="form-label">Telefoonnummer</label>
                            <input type="text" class="form-control @error('telefoon') is-invalid @enderror"
                                   id="telefoon" name="telefoon" value="{{ old('telefoon') }}" placeholder="0612345678">
                            @error('telefoon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Wachtwoord <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Bevestig wachtwoord <span class="text-danger">*</span></label>
                            <input type="password" class="form-control"
                                   id="password_confirmation" name="password_confirmation" required>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-user-plus me-2"></i>Account aanmaken
                            </button>
                        </div>
                    </form>

                    <p class="text-center text-muted small mb-0 mt-3">
                        Al een account? <a href="{{ route('login') }}" class="fw-bold">Log hier in</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
