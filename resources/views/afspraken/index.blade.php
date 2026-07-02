@extends('layouts.app')

@section('title', 'Afsprakenoverzicht')

@section('content')
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-calendar-alt me-3"></i>Afsprakenoverzicht</h1>
        <p class="mb-0 mt-2 opacity-90">Alle afspraken van vandaag in één overzicht.</p>
    </div>
</div>

<div class="container">
    @include('medewerker.partials.alerts')

    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4 page-toolbar">
        <div>
            <h2 class="mb-0" style="color: var(--text-primary);">Planning vandaag</h2>
            <p class="text-muted mb-0">Totaal: {{ $afspraken->count() }} afspraak{{ $afspraken->count() === 1 ? '' : 'en' }}</p>
        </div>
        <a href="{{ route('afspraken.create') }}" class="btn btn-primary w-100 w-sm-auto">
            <i class="fas fa-plus me-2"></i>Nieuwe afspraak
        </a>
    </div>

    @if($afspraken->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Er zijn geen afspraken gevonden voor deze dag</h4>
            </div>
        </div>
    @else
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Tijd</th>
                            <th>Klant</th>
                            <th>Behandeling</th>
                            <th>Specialist</th>
                            <th>Opmerking</th>
                            <th class="text-end">Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($afspraken as $afspraak)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($afspraak->afspraak_datum . ' ' . $afspraak->afspraak_tijd)->format('H:i') }}</td>
                                <td>{{ $afspraak->klant?->gebruiker?->volledig_naam ?? 'Onbekend' }}</td>
                                <td>{{ $afspraak->behandeling?->naam ?? 'Onbekend' }}</td>
                                <td>{{ $afspraak->medewerker?->gebruiker?->volledig_naam ?? 'Onbekend' }}</td>
                                <td>{{ $afspraak->opmerking }}</td>
                                <td class="text-end">
                                    <a href="{{ route('afspraken.edit', $afspraak) }}" class="btn btn-sm btn-outline-primary me-2">Wijzigen</a>
                                    <form action="{{ route('afspraken.destroy', $afspraak) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Annuleren</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
