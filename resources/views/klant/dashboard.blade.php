{{-- klant.view — Klant-dashboard (Mijn account) --}}
@extends('layouts.app')

@section('title', 'Mijn account')

@section('content')
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-user me-3"></i>Mijn account</h1>
        <p class="mb-0 mt-2 opacity-90">Welkom terug, {{ $user->voornaam ?? $user->name }}!</p>
    </div>
</div>

<div class="container">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- Profiel --}}
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header">
                    <i class="fas fa-id-card me-2"></i>Mijn gegevens
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-muted">Naam</dt>
                        <dd class="col-sm-8">{{ $klant?->gebruiker?->volledig_naam ?? ($user->voornaam . ' ' . $user->achternaam) }}</dd>

                        <dt class="col-sm-4 text-muted">E-mail</dt>
                        <dd class="col-sm-8">{{ $user->email }}</dd>

                        <dt class="col-sm-4 text-muted">Telefoon</dt>
                        <dd class="col-sm-8">
                            {{ $klant?->gebruiker?->contactGegevens?->telefoon ?? '—' }}
                        </dd>

                        <dt class="col-sm-4 text-muted">Accounttype</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-secondary">
                                <i class="fas fa-user me-1"></i>Klant
                            </span>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        {{-- Toekomstige features --}}
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header">
                    <i class="fas fa-calendar-check me-2"></i>Mijn afspraken
                </div>
                <div class="card-body text-center py-5">
                    <i class="fas fa-calendar-plus fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Binnenkort beschikbaar</h4>
                    <p class="text-muted mb-0">
                        Hier kun je straks je afspraken bekijken en boeken.
                        Deze functie wordt toegevoegd wanneer het boekingssysteem klaar is.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-1">
        <div class="col-md-4">
            <div class="card text-center h-100">
                <div class="card-body py-4">
                    <i class="fas fa-scissors fa-2x text-primary mb-3"></i>
                    <h5>Diensten</h5>
                    <p class="text-muted small mb-3">Fade, baard & kleurbehandelingen</p>
                    <a href="{{ route('home') }}#diensten" class="btn btn-sm btn-outline-primary">Bekijk diensten</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center h-100">
                <div class="card-body py-4">
                    <i class="fas fa-clock fa-2x text-primary mb-3"></i>
                    <h5>Openingstijden</h5>
                    <p class="text-muted small mb-3">Ma–Za geopend, zo gesloten</p>
                    <a href="{{ route('home') }}#contact" class="btn btn-sm btn-outline-primary">Contact & tijden</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center h-100">
                <div class="card-body py-4">
                    <i class="fas fa-phone fa-2x text-primary mb-3"></i>
                    <h5>Bel ons</h5>
                    <p class="text-muted small mb-3">Walk-ins altijd welkom</p>
                    <a href="tel:0201234567" class="btn btn-sm btn-outline-primary">020 - 123 4567</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
