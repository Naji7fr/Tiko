{{-- eigenaar.view — Rapportages --}}
@extends('layouts.app')

@section('title', 'Rapportages')

@section('content')
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-chart-bar me-3"></i>Rapportages</h1>
        <p class="mb-0 mt-2 opacity-90">Omzet, statistieken en analyses</p>
    </div>
</div>

<div class="container">
    @include('eigenaar.partials.alerts')

    <div class="mb-3">
        <a href="{{ route('eigenaar.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Terug naar dashboard
        </a>
    </div>

    @php $stats = $rapportage['statistieken']; @endphp

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-primary mb-0">{{ $stats['bestellingen_totaal'] }}</h3>
                    <p class="text-muted mb-0">Bestellingen</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-primary mb-0">{{ $stats['afspraken_totaal'] }}</h3>
                    <p class="text-muted mb-0">Afspraken totaal</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-primary mb-0">€ {{ number_format($stats['omzet_maand'], 2, ',', '.') }}</h3>
                    <p class="text-muted mb-0">Omzet deze maand</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-primary mb-0">€ {{ number_format($stats['omzet_totaal'], 2, ',', '.') }}</h3>
                    <p class="text-muted mb-0">Omzet totaal</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><i class="fas fa-cut me-2"></i>Populairste behandelingen</div>
                <div class="card-body p-0">
                    @if($rapportage['top_behandelingen']->isEmpty())
                        <p class="text-muted text-center py-4 mb-0">Nog geen afspraakdata beschikbaar.</p>
                    @else
                        <table class="table mb-0">
                            <thead><tr><th>Behandeling</th><th class="text-end">Afspraken</th></tr></thead>
                            <tbody>
                                @foreach($rapportage['top_behandelingen'] as $rij)
                                    <tr>
                                        <td>{{ $rij->naam }}</td>
                                        <td class="text-end">{{ $rij->aantal }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><i class="fas fa-shopping-bag me-2"></i>Bestellingen per status</div>
                <div class="card-body p-0">
                    @if($rapportage['bestellingen_per_status']->isEmpty())
                        <p class="text-muted text-center py-4 mb-0">Nog geen bestellingdata beschikbaar.</p>
                    @else
                        <table class="table mb-0">
                            <thead><tr><th>Status</th><th class="text-end">Aantal</th><th class="text-end">Omzet</th></tr></thead>
                            <tbody>
                                @foreach($rapportage['bestellingen_per_status'] as $rij)
                                    <tr>
                                        <td>{{ $rij->status }}</td>
                                        <td class="text-end">{{ $rij->aantal }}</td>
                                        <td class="text-end">€ {{ number_format($rij->omzet ?? 0, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
