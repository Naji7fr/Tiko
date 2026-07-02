{{-- medewerker.view — Medewerker detail --}}
@extends('layouts.app')

@section('title', $medewerker->naam)

@section('content')
@php
    use App\Services\Medewerker\MedewerkerBeschikbaarheidService;
@endphp
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-user me-3"></i>{{ $medewerker->naam }}</h1>
    </div>
</div>

<div class="container">
    @include('medewerker.partials.alerts')

    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-envelope me-2"></i>Contactgegevens</div>
        <div class="card-body">
            <p class="mb-1"><strong>E-mail:</strong> {{ $medewerker->email }}</p>
            <p class="mb-0"><strong>Telefoon:</strong> {{ $medewerker->telefoonnummer ?? '—' }}</p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-user me-2"></i>Gebruiker</div>
        <div class="card-body">
            <p class="mb-1"><strong>Voornaam:</strong> {{ $medewerker->gebruiker->voornaam }}</p>
            <p class="mb-1"><strong>Tussenvoegsel:</strong> {{ $medewerker->gebruiker->tussenvoegsel ?? '—' }}</p>
            <p class="mb-0"><strong>Achternaam:</strong> {{ $medewerker->gebruiker->achternaam }}</p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-scissors me-2"></i>Medewerker</div>
        <div class="card-body">
            <p class="mb-1"><strong>Specialisatie:</strong> {{ $medewerker->specialisatie->naam }}</p>
            <p class="mb-0"><strong>Status:</strong> {{ $medewerker->status }}</p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-calendar-week me-2"></i>Beschikbaarheid</div>
        <div class="card-body">
            <p class="mb-3"><strong>Overzicht:</strong> {{ $beschikbaarheidSamenvatting ?? '—' }}</p>
            <ul class="list-unstyled mb-0">
                @foreach($beschikbaarheidPerDag ?? [] as $dagNummer => $dag)
                    <li class="mb-1">
                        <strong>{{ MedewerkerBeschikbaarheidService::DAG_NAMEN[$dagNummer] ?? $dagNummer }}:</strong>
                        @if($dag['actief'] ?? false)
                            {{ $dag['start'] }} – {{ $dag['eind'] }}
                        @else
                            <span class="text-muted">Gesloten</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <a href="{{ route('medewerkers.edit', $medewerker) }}" class="btn btn-warning">
        <i class="fas fa-edit me-2"></i>Wijzigen
    </a>
    <a href="{{ route('medewerkers.index') }}" class="btn btn-secondary">Terug</a>
</div>
@endsection
