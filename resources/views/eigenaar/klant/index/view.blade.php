{{-- klant.view — Klantenoverzicht eigenaar --}}
@extends('layouts.app')

@section('title', 'Klanten')

@section('content')
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-user-friends me-3"></i>Klanten</h1>
        <p class="mb-0 mt-2 opacity-90">Overzicht van alle klantgegevens</p>
    </div>
</div>

<div class="container">
    @include('eigenaar.partials.alerts')

    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4 page-toolbar">
        <div>
            <h2 class="mb-0" style="color: var(--text-primary);">Alle klanten</h2>
            @if(!empty($zoekterm))
                <p class="text-muted mb-0">Resultaten: {{ $klanten->count() }} van {{ $totaalKlanten }} klanten</p>
            @else
                <p class="text-muted mb-0">Totaal: {{ $klanten->count() }} klanten</p>
            @endif
        </div>
        <div class="d-flex flex-column gap-2 w-100 w-sm-auto">
            <form method="GET" action="{{ route('eigenaar.klanten.index') }}" class="d-flex flex-column flex-sm-row gap-2">
                <input
                    type="search"
                    name="zoek"
                    class="form-control"
                    placeholder="Zoek op naam"
                    value="{{ $zoekterm ?? '' }}"
                    maxlength="121">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i>Zoeken
                    </button>
                    @if(!empty($zoekterm))
                        <a href="{{ route('eigenaar.klanten.index') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </form>

            <a href="{{ route('eigenaar.klanten.create') }}" class="btn btn-success align-self-end">
                <i class="fas fa-plus me-1"></i>Klant toevoegen
            </a>
        </div>
    </div>

    @if($klanten->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-user-friends fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Geen klanten gevonden</h4>
                <p class="text-muted mb-0">Er zijn nog geen klantgegevens beschikbaar.</p>
                <a href="{{ route('eigenaar.klanten.create') }}" class="btn btn-success mt-3">
                    <i class="fas fa-plus me-1"></i>Klant toevoegen
                </a>
            </div>
        </div>
    @else
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Naam</th>
                            <th>E-mail</th>
                            <th>Telefoon</th>
                            <th>Adres</th>
                            <th class="text-end">Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($klanten as $klant)
                            <tr>
                                <td><strong>{{ $klant->volledig_naam }}</strong></td>
                                <td>{{ $klant->email }}</td>
                                <td>{{ $klant->telefoon ?: '—' }}</td>
                                <td>
                                    @if($klant->straat)
                                        {{ $klant->straat }} {{ $klant->huisnummer }}<br>
                                        <small class="text-muted">{{ $klant->postcode }} {{ $klant->plaats }}</small>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-2">
                                        <a href="{{ route('eigenaar.klanten.edit', $klant->klant_id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit me-1"></i>Bewerken
                                        </a>
                                        <form method="POST" action="{{ route('eigenaar.klanten.destroy', $klant->klant_id) }}" class="d-inline"
                                              onsubmit="return confirm('Weet je zeker dat je deze klant wilt verwijderen?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash-alt me-1"></i>Verwijderen
                                            </button>
                                        </form>
                                    </div>
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
