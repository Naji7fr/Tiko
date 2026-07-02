{{-- klant.view — Klantenoverzicht eigenaar --}}
@extends('layouts.app')

@section('title', 'Klanten')

@section('content')
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-user-friends me-3"></i>Klanten</h1>
        <p class="mb-0 mt-2 opacity-90">Overzicht van alle klantgegevens en loginaccounts</p>
    </div>
</div>

<div class="container">
    @include('eigenaar.partials.alerts')

    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4 page-toolbar">
        <div>
            <h2 class="mb-0" style="color: var(--text-primary);">Alle klanten</h2>
            <p class="text-muted mb-0">Totaal: {{ $klanten->count() }} klanten</p>
        </div>
        <a href="{{ route('eigenaar.accounts.create') }}" class="btn btn-primary w-100 w-sm-auto">
            <i class="fas fa-plus me-2"></i>Klantaccount aanmaken
        </a>
    </div>

    @if($klanten->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-user-friends fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Geen klanten gevonden</h4>
                <p class="text-muted mb-4">Klanten kunnen zich registreren of je maakt een account aan.</p>
                <a href="{{ route('eigenaar.accounts.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Klantaccount aanmaken
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
                            <th>Account</th>
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
                                <td>
                                    @if($klant->user_id)
                                        @if(strtolower((string) $klant->account_status) === 'actief')
                                            <span class="badge bg-success">Actief</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $klant->account_status ?? 'Onbekend' }}</span>
                                        @endif
                                    @else
                                        <span class="badge bg-warning text-dark">Geen login</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($klant->user_id)
                                        <a href="{{ route('eigenaar.accounts.edit', $klant->user_id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit me-1"></i>Account beheren
                                        </a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
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
