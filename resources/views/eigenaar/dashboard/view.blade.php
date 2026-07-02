{{-- eigenaar.view — Eigenaar dashboard --}}
@extends('layouts.app')

@section('title', 'Eigenaar Dashboard')

@section('content')
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-crown me-3"></i>Eigenaar Dashboard</h1>
        <p class="mb-0 mt-2 opacity-90">
            Welkom, {{ $gebruiker->voornaam ?? $gebruiker->name }} — volledig overzicht van Tiko Barbershop
        </p>
    </div>
</div>

<div class="container eigenaar-dashboard">
    @include('eigenaar.partials.alerts')

    {{-- Kerncijfers --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-lg-2">
            <div class="eigenaar-stat-card">
                <i class="fas fa-users"></i>
                <strong>{{ $statistieken['medewerkers_actief'] }}</strong>
                <span>Actieve medewerkers</span>
                <small class="text-muted">{{ $statistieken['medewerkers_totaal'] }} totaal</small>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="eigenaar-stat-card">
                <i class="fas fa-user-friends"></i>
                <strong>{{ $statistieken['klanten_totaal'] }}</strong>
                <span>Klanten</span>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="eigenaar-stat-card">
                <i class="fas fa-calendar-check"></i>
                <strong>{{ $statistieken['afspraken_vandaag'] }}</strong>
                <span>Afspraken vandaag</span>
                <small class="text-muted">{{ $statistieken['afspraken_komende_week'] }} deze week</small>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="eigenaar-stat-card">
                <i class="fas fa-cut"></i>
                <strong>{{ $statistieken['behandelingen_totaal'] }}</strong>
                <span>Behandelingen</span>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="eigenaar-stat-card">
                <i class="fas fa-box"></i>
                <strong>{{ $statistieken['producten_totaal'] }}</strong>
                <span>Producten</span>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="eigenaar-stat-card">
                <i class="fas fa-euro-sign"></i>
                <strong>€ {{ number_format($statistieken['omzet_maand'], 2, ',', '.') }}</strong>
                <span>Omzet deze maand</span>
                <small class="text-muted">€ {{ number_format($statistieken['omzet_totaal'], 2, ',', '.') }} totaal</small>
            </div>
        </div>
    </div>

    {{-- Beheermodules --}}
    <h2 class="h4 mb-3"><i class="fas fa-th-large me-2"></i>Beheer</h2>
    <div class="row g-4 mb-5">
        @foreach($modules as $module)
            <div class="col-md-6 col-lg-4">
                <a href="{{ $module['route'] }}" class="eigenaar-module-card text-decoration-none">
                    <div class="eigenaar-module-card__icon">
                        <i class="fas {{ $module['icoon'] }}"></i>
                    </div>
                    <div>
                        <h3>{{ $module['titel'] }}</h3>
                        <p>{{ $module['beschrijving'] }}</p>
                        @if($module['actief'])
                            <span class="badge bg-success">Beschikbaar</span>
                        @else
                            <span class="badge bg-secondary">Binnenkort</span>
                        @endif
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="row g-4">
        {{-- Komende afspraken --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-calendar-alt me-2"></i>Komende afspraken</span>
                    <a href="{{ route('afspraken.index') }}" class="btn btn-sm btn-light">Alles bekijken</a>
                </div>
                <div class="card-body p-0">
                    @if($komendeAfspraken->isEmpty())
                        <p class="text-muted text-center py-4 mb-0">Geen komende afspraken.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Datum</th>
                                        <th>Klant</th>
                                        <th>Medewerker</th>
                                        <th>Behandeling</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($komendeAfspraken as $afspraak)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($afspraak->afspraak_datum)->format('d-m-Y') }}<br>
                                                <small class="text-muted">{{ substr($afspraak->afspraak_tijd, 0, 5) }}</small></td>
                                            <td>{{ $afspraak->klant_naam }}</td>
                                            <td>{{ $afspraak->medewerker_naam }}</td>
                                            <td>{{ $afspraak->behandeling_naam }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Recente bestellingen --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-shopping-cart me-2"></i>Recente bestellingen</span>
                    <a href="{{ route('eigenaar.module', 'bestellingen') }}" class="btn btn-sm btn-light">Alles bekijken</a>
                </div>
                <div class="card-body p-0">
                    @if($recenteBestellingen->isEmpty())
                        <p class="text-muted text-center py-4 mb-0">Geen bestellingen gevonden.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Datum</th>
                                        <th>Klant</th>
                                        <th>Status</th>
                                        <th class="text-end">Bedrag</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recenteBestellingen as $bestelling)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($bestelling->bestel_datum)->format('d-m-Y') }}</td>
                                            <td>{{ $bestelling->klant_naam }}</td>
                                            <td><span class="badge bg-primary">{{ $bestelling->status }}</span></td>
                                            <td class="text-end">€ {{ number_format($bestelling->totaal_prijs, 2, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
