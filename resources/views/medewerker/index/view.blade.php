{{-- medewerker.view — Overzicht medewerkers (MVC View-laag) --}}
@extends('layouts.app')

@section('title', 'Medewerker Overzicht')

@section('content')
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-users me-3"></i>Overzicht Medewerker</h1>
        <p class="mb-0 mt-2 opacity-90">Beheer alle medewerkers van Tiko Barbershop</p>
    </div>
</div>

<div class="container">
    @include('medewerker.partials.alerts')

    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4 page-toolbar">
        <div>
            <h2 class="mb-0" style="color: var(--text-primary);">Alle Medewerkers</h2>
            <p class="text-muted mb-0">Totaal: {{ $medewerkers->count() }} medewerkers</p>
        </div>
        <a href="{{ route('medewerkers.create') }}" class="btn btn-primary w-100 w-sm-auto">
            <i class="fas fa-plus me-2"></i>Medewerker toevoegen
        </a>
    </div>

    @if($medewerkers->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Geen medewerkers gevonden</h4>
                <p class="text-muted mb-4">Er zijn geen medewerker geregistreerd.</p>
                <a href="{{ route('medewerkers.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Medewerker toevoegen
                </a>
            </div>
        </div>
    @else
        {{-- Desktop tabel --}}
        <div class="card d-none d-lg-block">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th><i class="fas fa-user me-2"></i>Naam</th>
                            <th><i class="fas fa-envelope me-2"></i>E-mailadres</th>
                            <th><i class="fas fa-phone me-2"></i>Telefoonnummer</th>
                            <th><i class="fas fa-info-circle me-2"></i>Status</th>
                            <th><i class="fas fa-scissors me-2"></i>Specialisatie</th>
                            <th><i class="fas fa-calendar-week me-2"></i>Beschikbaarheid</th>
                            <th class="text-end"><i class="fas fa-cog me-2"></i>Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($medewerkers as $medewerker)
                            @include('medewerker.partials.rij-desktop', [
                                'medewerker' => $medewerker,
                                'beschikbaarheidSamenvattingen' => $beschikbaarheidSamenvattingen ?? [],
                            ])
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Mobiele kaarten --}}
        <div class="medewerker-mobile-list d-lg-none">
            @foreach($medewerkers as $medewerker)
                @include('medewerker.partials.kaart-mobiel', [
                    'medewerker' => $medewerker,
                    'beschikbaarheidSamenvattingen' => $beschikbaarheidSamenvattingen ?? [],
                ])
            @endforeach
        </div>
    @endif
</div>
@endsection
