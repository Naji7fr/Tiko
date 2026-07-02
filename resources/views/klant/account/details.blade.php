@extends('layouts.app')

@section('title', 'Account details')

@section('content')
<div class="container py-5" style="padding-top: 6rem;">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-md-5">
                    @if(session('status'))
                        <div class="alert alert-success border-0 mb-4">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3 mb-4">
                        <div>
                            <span class="text-uppercase text-muted small fw-semibold">Account details</span>
                            <h1 class="h3 mb-2">{{ trim(($user->voornaam ?? '') . ' ' . ($user->achternaam ?? '')) ?: $user->name }}</h1>
                            <p class="text-muted mb-0">Bekijk de belangrijkste gegevens van dit account.</p>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('logout.get') }}" class="btn btn-outline-danger">
                                <i class="fas fa-right-from-bracket me-2"></i>Uitloggen
                            </a>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100">
                                <div class="text-muted small text-uppercase fw-semibold mb-1">First name</div>
                                <div class="fw-semibold">{{ $user->voornaam ?: 'Niet ingevuld' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100">
                                <div class="text-muted small text-uppercase fw-semibold mb-1">Last name</div>
                                <div class="fw-semibold">{{ $user->achternaam ?: 'Niet ingevuld' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100">
                                <div class="text-muted small text-uppercase fw-semibold mb-1">Email</div>
                                <div class="fw-semibold">{{ $klant?->gebruiker?->contactGegevens?->email ?? $user->email }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100">
                                <div class="text-muted small text-uppercase fw-semibold mb-1">Telefoon</div>
                                <div class="fw-semibold">{{ $klant?->gebruiker?->contactGegevens?->telefoon ?: 'Niet ingevuld' }}</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="border rounded-3 p-3 h-100">
                                <div class="text-muted small text-uppercase fw-semibold mb-1">Adres</div>
                                <div class="fw-semibold">
                                    @php
                                        $adres = $klant?->gebruiker?->adres;
                                    @endphp
                                    {{ $adres ? trim($adres->straat . ' ' . $adres->huisnummer . ', ' . $adres->postcode . ' ' . $adres->plaats . ', ' . $adres->land) : 'Niet ingevuld' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('home') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Terug
                        </a>
                        <a href="{{ route('account.details.edit') }}" class="btn btn-primary ms-auto">
                            <i class="fas fa-pen me-2"></i>Update details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection