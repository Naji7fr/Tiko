@extends('layouts.app')

@section('title', 'Overzicht Klant')

@section('content')
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-user-friends me-3"></i>Overzicht klant</h1>
        <p class="mb-0 mt-2 opacity-90">Overzicht van alle klanten</p>
    </div>
</div>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0" style="color: var(--text-primary);">Alle Klanten</h2>
            <p class="text-muted mb-0">Totaal: {{ $klanten->count() }} klanten</p>
        </div>
    </div>

    @if($klanten->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-user-friends fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Geen klanten gevonden</h4>
                <p class="text-muted mb-4">Er zijn geen klanten geregistreerd.</p>
            </div>
        </div>
    @else
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th><i class="fas fa-user me-2"></i>Naam</th>
                            <th><i class="fas fa-envelope me-2"></i>E-mailadres</th>
                            <th><i class="fas fa-info-circle me-2"></i>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($klanten as $klant)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-3" style="width: 40px; height: 40px; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;">
                                            {{ strtoupper(substr($klant->name ?? 'K', 0, 1)) }}
                                        </div>
                                        <strong>{{ $klant->name }}</strong>
                                    </div>
                                </td>
                                <td>
                                    <a href="mailto:{{ $klant->email }}" class="text-decoration-none">
                                        <i class="fas fa-envelope me-2 text-muted"></i>{{ $klant->email }}
                                    </a>
                                </td>
                                <td>
                                    @if(strtolower($klant->status ?? '') === 'actief')
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Actief
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-pause-circle me-1"></i>{{ ucfirst($klant->status ?? 'Inactief') }}
                                        </span>
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
