@extends('layouts.app')

@section('title', 'Overzicht reizen')

@section('content')
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-plane-departure me-3"></i>Overzicht reizen</h1>
        <p class="mb-0 mt-2 opacity-90">Bekijk alle beschikbare reizen en reisgegevens</p>
    </div>
</div>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0" style="color: var(--text-primary);">Alle reizen</h2>
        <p class="text-muted mb-0">Totaal: {{ $reizen->count() }} reizen</p>
    </div>

    @if($reizen->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-plane-departure fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Geen reizen gevonden</h4>
                <p class="text-muted mb-0">Er zijn geen reizen beschikbaar.</p>
            </div>
        </div>
    @else
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th><i class="fas fa-heading me-2"></i>Titel</th>
                            <th><i class="fas fa-map-marker-alt me-2"></i>Bestemming</th>
                            <th><i class="fas fa-calendar me-2"></i>Startdatum</th>
                            <th><i class="fas fa-calendar-check me-2"></i>Einddatum</th>
                            <th><i class="fas fa-euro-sign me-2"></i>Prijs</th>
                            <th><i class="fas fa-info-circle me-2"></i>Status</th>
                            <th><i class="fas fa-align-left me-2"></i>Beschrijving</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reizen as $reis)
                            <tr>
                                <td><strong>{{ $reis->titel }}</strong></td>
                                <td>{{ $reis->bestemming }}</td>
                                <td>{{ $reis->start_datum->format('d-m-Y') }}</td>
                                <td>{{ $reis->eind_datum->format('d-m-Y') }}</td>
                                <td>@if($reis->prijs) €{{ number_format($reis->prijs, 2, ',', '.') }} @else — @endif</td>
                                <td>
                                    @if(strtolower($reis->status ?? '') === 'beschikbaar')
                                        <span class="badge bg-success">{{ ucfirst($reis->status) }}</span>
                                    @elseif(strtolower($reis->status ?? '') === 'voltooid')
                                        <span class="badge bg-secondary">{{ ucfirst($reis->status) }}</span>
                                    @else
                                        <span class="badge bg-warning">{{ ucfirst($reis->status ?? 'Onbekend') }}</span>
                                    @endif
                                </td>
                                <td><span class="text-muted">{{ Str::limit($reis->beschrijving, 40) }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
