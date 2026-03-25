@extends('layouts.app')

@section('title', 'Registreren')

@section('content')
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-user-plus me-3"></i>Registreren</h1>
        <p class="mb-0 mt-2 opacity-90">Maak een nieuw account aan</p>
    </div>
</div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7">
            <div class="card">
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('register') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">
                                <i class="fas fa-at me-2"></i>Gebruikersnaam <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name"
                                   value="{{ old('name') }}"
                                   placeholder="Kies een unieke gebruikersnaam" required autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="voornaam" class="form-label">
                                    <i class="fas fa-user me-2"></i>Voornaam <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('voornaam') is-invalid @enderror"
                                       id="voornaam" name="voornaam"
                                       value="{{ old('voornaam') }}"
                                       placeholder="Uw voornaam" required>
                                @error('voornaam')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="achternaam" class="form-label">
                                    <i class="fas fa-user me-2"></i>Achternaam <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('achternaam') is-invalid @enderror"
                                       id="achternaam" name="achternaam"
                                       value="{{ old('achternaam') }}"
                                       placeholder="Uw achternaam" required>
                                @error('achternaam')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-2"></i>E-mailadres <span class="text-danger">*</span>
                            </label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email"
                                   value="{{ old('email') }}"
                                   placeholder="uw@email.nl" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-2"></i>Wachtwoord <span class="text-danger">*</span>
                            </label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   id="password" name="password"
                                   placeholder="Minimaal 8 tekens" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">
                                <i class="fas fa-lock me-2"></i>Bevestig Wachtwoord <span class="text-danger">*</span>
                            </label>
                            <input type="password" class="form-control"
                                   id="password_confirmation" name="password_confirmation"
                                   placeholder="Herhaal uw wachtwoord" required>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-user-plus me-2"></i>Registreren
                            </button>
                        </div>
                    </form>

                    <hr class="my-4">

                    <div class="text-center">
                        <p class="mb-0">Al een account? <a href="{{ route('login') }}" class="text-primary fw-bold">Log hier in</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

